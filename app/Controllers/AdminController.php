<?php
declare(strict_types=1);
namespace Datum\Controllers;
use Datum\Core\{Db,Http};
use Datum\Services\Auth;
final class AdminController
{
    public static function resources(): array { return require dirname(__DIR__,2).'/config/resources.php'; }
    public static function resource(string $resource, ?int $id, string $method): never
    {
        $definition=self::resources()[$resource] ?? null;
        if (!$definition) { Http::fail('Resource not found',404); }
        $user=Auth::permission($definition['permission']);
        if ($method==='GET') {
            $offset=max(0,(int)($_GET['offset'] ?? 0));
            Http::json($id ? Db::one("SELECT * FROM `$resource` WHERE id=?",[$id]) : ['rows'=>Db::all("SELECT * FROM `$resource` ORDER BY id DESC LIMIT 50 OFFSET $offset"),'fields'=>$definition['fields']]);
        }
        if (!in_array($method,['POST','PUT','DELETE'],true)) { Http::fail('Method not allowed',405); }
        $db=Db::connection(); $db->beginTransaction();
        $existing=$id ? Db::one("SELECT * FROM `$resource` WHERE id=? FOR UPDATE",[$id]) : null;
        if ($id && !$existing) { $db->rollBack(); Http::fail('Record not found',404); }
        if ($method==='DELETE') {
            if (!$id) { $db->rollBack(); Http::fail('Record ID required'); }
            Db::run("DELETE FROM `$resource` WHERE id=?",[$id]);
            Db::audit((int)$user['id'],'delete',$resource,$id); $db->commit(); Http::json(['deleted'=>true]);
        }
        $input=Http::input(); $values=[];
        foreach ($definition['fields'] as $field=>$type) { $values[$field]=self::validate($input,$field,$type); }
        if ($existing && isset($existing['version'])) {
            if ((int)($input['version'] ?? 0)!==(int)$existing['version']) { $db->rollBack(); Http::fail('This record has changed. Reload before saving.',409); }
            Db::run('INSERT INTO content_versions(resource,record_id,version,snapshot,user_id) VALUES(?,?,?,?,?)',[$resource,$id,$existing['version'],json_encode($existing,JSON_THROW_ON_ERROR),$user['id']]);
            $values['version']=(int)$existing['version']+1;
        }
        if ($id) {
            $sets=implode(',',array_map(static fn($key)=>"`$key`=?",array_keys($values)));
            Db::run("UPDATE `$resource` SET $sets WHERE id=?",[...array_values($values),$id]);
        } else {
            $columns=implode('`,`',array_keys($values)); $marks=implode(',',array_fill(0,count($values),'?'));
            Db::run("INSERT INTO `$resource` (`$columns`) VALUES ($marks)",array_values($values)); $id=(int)$db->lastInsertId();
        }
        Db::audit((int)$user['id'],$existing?'update':'create',$resource,$id); $db->commit();
        Http::json(Db::one("SELECT * FROM `$resource` WHERE id=?",[$id]),$existing?200:201);
    }
    private static function validate(array $input,string $field,string $type): mixed
    {
        if ($type==='bool') { return ($input[$field] ?? false) ? 1 : 0; }
        if (in_array($type,['id','progress','money','positive'],true)) {
            $value=$input[$field] ?? null;
            if (!is_numeric($value) || !is_finite((float)$value) || $value<0 || (in_array($type,['id','positive']) && $value<=0) || ($type==='progress' && $value>100) || (in_array($type,['id','progress']) && (float)$value !== (float)(int)$value)) { Http::fail('Invalid number: '.$field,422); }
            return $value;
        }
        $value=Http::text($input,$field,$type==='body'?60000:($type==='long'?10000:($type==='url'?500:($type==='email'?254:160))),!in_array($type,['optional','date?'],true));
        if ($type==='slug' && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D',$value)) { Http::fail('Use a lowercase URL slug',422); }
        if ($type==='currency' && !preg_match('/^[A-Z]{3}$/D',$value)) { Http::fail('Use a three-letter currency code',422); }
        if ($type==='path' && !preg_match('#^/[a-z0-9/-]*$#D',$value)) { Http::fail('Invalid URL path',422); }
        if ($type==='email' && !filter_var($value,FILTER_VALIDATE_EMAIL)) { Http::fail('Invalid email',422); }
        if ($type==='url' && (!filter_var($value,FILTER_VALIDATE_URL) || parse_url($value,PHP_URL_SCHEME)!=='https')) { Http::fail('HTTPS URL required',422); }
        if (str_contains($type,'|') && !in_array($value,explode('|',$type),true)) { Http::fail('Invalid option: '.$field,422); }
        if (str_starts_with($type,'date')) {
            if ($value==='' && $type==='date?') { return null; }
            $date=\DateTimeImmutable::createFromFormat('!Y-m-d',$value);
            if (!$date || $date->format('Y-m-d')!==$value) { Http::fail('Invalid date',422); }
        }
        return $value;
    }
}
