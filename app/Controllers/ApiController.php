<?php
declare(strict_types=1);
namespace Datum\Controllers;
use Datum\Core\{Db,Http};
use Datum\Services\{Auth,Supabase};
final class ApiController
{
    public static function dispatch(string $path,string $method): never
    {
        if (!in_array($method,['GET','POST','PUT','DELETE'],true)) { Http::fail('Method not allowed',405); }
        if ($method!=='GET') { Http::csrf(); }
        if ($path==='/api/session' && $method==='GET') { Http::json(['csrf'=>$_SESSION['csrf']]); }
        if ($path==='/api/login' && $method==='POST') { Http::json(Auth::login(Http::input())); }
        if ($path==='/api/logout' && $method==='POST') { Auth::logout(); Http::json(['ok'=>true]); }
        if ($path==='/api/me' && $method==='GET') { Http::json(Auth::user(false)); }
        if ($path==='/api/mfa' && $method==='GET') {
            Auth::user(false); $u=Supabase::request('GET','/auth/v1/user',null,$_SESSION['access_token']);
            Http::json(['factors'=>array_values(array_filter($u['factors'] ?? [],static fn($f)=>$f['status']==='verified'))]);
        }
        if ($path==='/api/mfa/enroll' && $method==='POST') {
            Auth::user(false); Http::limit('mfa-enroll',5);
            Http::json(Supabase::request('POST','/auth/v1/factors',['factor_type'=>'totp','friendly_name'=>'DATUM portal '.bin2hex(random_bytes(3))],$_SESSION['access_token']));
        }
        if ($path==='/api/mfa/verify' && $method==='POST') {
            Auth::user(false); Http::limit('mfa',10); $d=Http::input();
            $factor=Http::text($d,'factor_id',50); $code=Http::text($d,'code',6);
            if (!preg_match('/^[a-f0-9-]{36}$/D',$factor) || !preg_match('/^[0-9]{6}$/D',$code)) { Http::fail('Invalid verification details'); }
            $c=Supabase::request('POST','/auth/v1/factors/'.$factor.'/challenge',[], $_SESSION['access_token']);
            $v=Supabase::request('POST','/auth/v1/factors/'.$factor.'/verify',['challenge_id'=>$c['id'],'code'=>$code],$_SESSION['access_token']);
            $_SESSION['access_token']=$v['access_token']; $_SESSION['refresh_token']=$v['refresh_token']; session_regenerate_id(true);
            Http::json(Auth::user());
        }
        if ($path==='/api/leads' && $method==='POST') { self::lead(); }
        if ($path==='/api/newsletter' && $method==='POST') {
            Http::limit('newsletter',5); $d=Http::input(); $email=Http::text($d,'email',254);
            if (!filter_var($email,FILTER_VALIDATE_EMAIL) || ($d['consent'] ?? false)!==true) { Http::fail('A valid email and consent are required',422); }
            Db::run('INSERT IGNORE INTO newsletter(email,token_hash) VALUES(?,?)',[$email,hash('sha256',random_bytes(32))]);
            Http::json(['message'=>'Your interest has been recorded. No subscription emails are sent by this build.'],201);
        }
        if (preg_match('#^/api/admin/resources/([a-z_]+)(?:/(\d+))?$#D',$path,$m)) { AdminController::resource($m[1],isset($m[2])?(int)$m[2]:null,$method); }
        if ($path==='/api/admin/overview' && $method==='GET') {
            Auth::permission('cms.write'); Http::json(['content'=>Db::one("SELECT COUNT(*) total FROM pages")['total'],'projects'=>Db::one('SELECT COUNT(*) total FROM projects')['total']]);
        }
        if ($path==='/api/admin/leads' && $method==='GET') { Auth::permission('leads.write'); Http::json(Db::all('SELECT * FROM leads ORDER BY id DESC LIMIT 100')); }
        if (preg_match('#^/api/admin/leads/(\d+)$#D',$path,$m) && $method==='PUT') {
            $u=Auth::permission('leads.write'); $d=Http::input();
            if (!in_array($d['status'] ?? '',['new','contacted','qualified','won','lost'],true)) { Http::fail('Invalid lead status',422); }
            $assigned=$d['assigned_to'] ?? null;
            if ($assigned!==null && !Db::one("SELECT u.id FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=? AND u.active=1 AND r.name='admin'",[$assigned])) { Http::fail('Invalid lead owner',422); }
            Db::run('UPDATE leads SET status=?,assigned_to=? WHERE id=?',[$d['status'],$assigned,$m[1]]); Db::audit((int)$u['id'],'update','leads',(int)$m[1]); Http::json(['ok'=>true]);
        }
        if ($path==='/api/admin/settings') {
            $u=Auth::permission('users.write');
            if ($method==='GET') { Http::json(Db::all('SELECT * FROM settings')); }
            if ($method==='PUT') {
                $d=Http::input(); $key=Http::text($d,'key',80); $value=Http::text($d,'value',10000,false);
                if (!Db::one('SELECT `key` FROM settings WHERE `key`=?',[$key])) { Http::fail('Setting not found',404); }
                Db::run('UPDATE settings SET value=? WHERE `key`=?',[$value,$key]); Db::audit((int)$u['id'],'update','settings'); Http::json(['ok'=>true]);
            }
        }
        if ($path==='/api/admin/audit' && $method==='GET') { Auth::permission('users.write'); Http::json(Db::all('SELECT * FROM audit_logs ORDER BY id DESC LIMIT 100')); }
        if ($path==='/api/admin/users' && $method==='GET') { Auth::permission('users.write'); Http::json(Db::all('SELECT u.id,u.name,u.email,u.active,r.name role FROM users u JOIN roles r ON r.id=u.role_id')); }
        if ($path==='/api/admin/client-access' && $method==='POST') {
            $u=Auth::permission('users.write'); $d=Http::input();
            $client=filter_var($d['client_id'] ?? null,FILTER_VALIDATE_INT); $uid=filter_var($d['user_id'] ?? null,FILTER_VALIDATE_INT);
            if (!$client || !$uid) { Http::fail('Valid client and user IDs required',422); }
            Db::run('INSERT IGNORE INTO client_users(client_id,user_id) VALUES(?,?)',[$client,$uid]); Db::audit((int)$u['id'],'grant-client-access','users',(int)$uid); Http::json(['ok'=>true]);
        }
        if ($path==='/api/admin/versions' && $method==='GET') { $resource=(string)($_GET['resource'] ?? ''); $definition=AdminController::resources()[$resource] ?? null; if (!$definition) { Http::fail('Resource not found',404); } Auth::permission($definition['permission']); Http::json(Db::all('SELECT * FROM content_versions WHERE resource=? AND record_id=? ORDER BY version DESC LIMIT 30',[$_GET['resource'] ?? '',(int)($_GET['id'] ?? 0)])); }
        if ($path==='/api/projects' && $method==='GET') {
            $u=Auth::user(); Http::json($u['role']==='admin' ? Db::all('SELECT id,title,location,progress,state FROM projects ORDER BY id DESC LIMIT 100') : Db::all('SELECT p.id,p.title,p.location,p.progress,p.state FROM projects p JOIN client_users cu ON cu.client_id=p.client_id WHERE cu.user_id=? ORDER BY p.id DESC LIMIT 100',[$u['id']]));
        }
        if (preg_match('#^/api/projects/(\d+)$#D',$path,$m) && $method==='GET') {
            $p=Auth::project((int)$m[1]);
            $p['records']=Db::all('SELECT id,kind,title,body,status,due_date,created_at FROM project_records WHERE project_id=? ORDER BY id DESC LIMIT 200',[$p['id']]);
            $p['milestones']=Db::all('SELECT * FROM milestones WHERE project_id=? ORDER BY due_date',[$p['id']]);
            $p['documents']=Db::all('SELECT id,title,mime,size_bytes,created_at FROM documents WHERE project_id=? ORDER BY id DESC',[$p['id']]);
            $p['invoices']=Db::all("SELECT i.*,COALESCE((SELECT SUM(quantity*unit_price*(1+tax_percent/100)) FROM invoice_items WHERE invoice_id=i.id),0) total FROM invoices i WHERE i.project_id=? AND i.status<>'draft'",[$p['id']]);
            Http::json($p);
        }
        if (preg_match('#^/api/projects/(\d+)/records$#D',$path,$m) && $method==='POST') {
            $p=Auth::project((int)$m[1]); $u=Auth::user(); $d=Http::input(); $kind=Http::text($d,'kind',30);
            if (!in_array($kind,['report','meeting','rfi','snag','approval','risk'],true) || ($u['role']!=='admin' && !in_array($kind,['rfi','snag'],true))) { Http::fail('Record type not permitted',403); }
            Db::run('INSERT INTO project_records(project_id,kind,title,body,created_by) VALUES(?,?,?,?,?)',[$p['id'],$kind,Http::text($d,'title',160),Http::text($d,'body',10000),$u['id']]);
            $id=(int)Db::connection()->lastInsertId(); Db::audit((int)$u['id'],'create','project_records',$id); Http::json(['id'=>$id],201);
        }
        if (preg_match('#^/api/records/(\d+)$#D',$path,$m) && $method==='PUT') {
            $u=Auth::permission('projects.write'); $d=Http::input();
            if (!in_array($d['status'] ?? '',['open','in-progress','closed','approved','rejected'],true)) { Http::fail('Invalid status',422); }
            Db::run('UPDATE project_records SET status=? WHERE id=?',[$d['status'],$m[1]]); Db::audit((int)$u['id'],'status','project_records',(int)$m[1]); Http::json(['ok'=>true]);
        }
        if (preg_match('#^/api/projects/(\d+)/documents$#D',$path,$m) && $method==='POST') { self::upload((int)$m[1]); }
        if (preg_match('#^/api/documents/(\d+)/download$#D',$path,$m) && $method==='GET') {
            $doc=Db::one('SELECT * FROM documents WHERE id=?',[$m[1]]); if (!$doc) { Http::fail('Document not found',404); } Auth::project((int)$doc['project_id']);
            $signed=Supabase::request('POST','/storage/v1/object/sign/'.rawurlencode(getenv('SUPABASE_BUCKET') ?: 'project-documents').'/'.$doc['object_path'],['expiresIn'=>60],null,true);
            Http::json(['url'=>rtrim(getenv('SUPABASE_URL'),'/').'/storage/v1'.$signed['signedURL']]);
        }
        Http::fail('Endpoint not found',404);
    }
    private static function lead(): never
    {
        Http::limit('lead',5); $d=Http::input();
        if (!empty($d['website'])) { Http::fail('Unable to submit form',422); }
        $name=Http::text($d,'name',160); $email=Http::text($d,'email',254); $phone=Http::text($d,'phone',40,false); $service=Http::text($d,'service',160); $message=Http::text($d,'message',5000);
        if (!filter_var($email,FILTER_VALIDATE_EMAIL) || ($d['consent'] ?? false)!==true) { Http::fail('A valid email and consent are required',422); }
        $db=Db::connection(); $db->beginTransaction();
        Db::run('INSERT INTO leads(name,email,phone,service,message) VALUES(?,?,?,?,?)',[$name,$email,$phone,$service,$message]); $id=(int)$db->lastInsertId();
        Db::run('INSERT INTO outbox(event_type,payload) VALUES(?,?)',['lead.created',json_encode(['lead_id'=>$id],JSON_THROW_ON_ERROR)]);
        $db->commit(); Http::json(['message'=>'Your enquiry has been received.','reference'=>$id],201);
    }
    private static function upload(int $project): never
    {
        Auth::project($project); $u=Auth::permission('projects.write'); Http::limit('upload',30);
        $file=$_FILES['file'] ?? null;
        if (!$file || $file['error']!==UPLOAD_ERR_OK || $file['size']>10*1024*1024 || $file['size']<1 || !is_uploaded_file($file['tmp_name'])) { Http::fail('Select a file up to 10 MB',422); }
        $mime=(new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $types=['application/pdf'=>'pdf','image/jpeg'=>'jpg','image/png'=>'png','text/plain'=>'txt'];
        if (!isset($types[$mime])) { Http::fail('PDF, JPEG, PNG and text files are accepted',422); }
        $path=$project.'/'.bin2hex(random_bytes(16)).'.'.$types[$mime];
        Supabase::request('POST','/storage/v1/object/'.rawurlencode(getenv('SUPABASE_BUCKET') ?: 'project-documents').'/'.$path,null,null,true,file_get_contents($file['tmp_name']),$mime);
        $title=mb_substr(basename($file['name']),0,160);
        Db::run('INSERT INTO documents(project_id,title,object_path,mime,size_bytes,uploaded_by) VALUES(?,?,?,?,?,?)',[$project,$title,$path,$mime,$file['size'],$u['id']]);
        Db::audit((int)$u['id'],'upload','documents',(int)Db::connection()->lastInsertId()); Http::json(['ok'=>true],201);
    }
}
