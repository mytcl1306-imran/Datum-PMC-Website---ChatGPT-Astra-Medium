<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { exit(1); }
require dirname(__DIR__).'/app/bootstrap.php';
use Datum\Core\Db;
$command=$argv[1] ?? 'help';
try {
    if ($command==='migrate') {
        Db::run('CREATE TABLE IF NOT EXISTS migrations (name VARCHAR(100) PRIMARY KEY, applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');
        foreach(glob(dirname(__DIR__).'/database/*.sql') as $file) {
            if (Db::one('SELECT name FROM migrations WHERE name=?',[basename($file)])) { continue; }
            Db::connection()->exec(file_get_contents($file));
            Db::run('INSERT INTO migrations(name) VALUES(?)',[basename($file)]); echo 'Applied '.basename($file).PHP_EOL;
        }
    } elseif ($command==='grant-user') {
        [$script,$cmd,$uuid,$email,$name,$role]=array_pad($argv,6,'');
        if (!preg_match('/^[a-f0-9-]{36}$/Di',$uuid) || !filter_var($email,FILTER_VALIDATE_EMAIL) || !$name || !in_array($role,['admin','editor','client'],true)) { throw new RuntimeException('Usage: grant-user AUTH_UUID EMAIL NAME admin|editor|client'); }
        $roleId=Db::one('SELECT id FROM roles WHERE name=?',[$role])['id'];
        Db::run('INSERT INTO users(auth_id,email,name,role_id) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE email=VALUES(email),name=VALUES(name),role_id=VALUES(role_id),active=1',[$uuid,$email,$name,$roleId]);
        Db::audit(null,'cli-grant-user','users'); echo 'Portal access updated.'.PHP_EOL;
    } elseif ($command==='disable-user') {
        $id=filter_var($argv[2] ?? null,FILTER_VALIDATE_INT); if (!$id) { throw new RuntimeException('Numeric user ID required'); }
        Db::run('UPDATE users SET active=0 WHERE id=?',[$id]); Db::audit(null,'cli-disable-user','users',$id); echo 'Access disabled.'.PHP_EOL;
    } elseif ($command==='revoke-client') {
        $client=filter_var($argv[2] ?? null,FILTER_VALIDATE_INT); $user=filter_var($argv[3] ?? null,FILTER_VALIDATE_INT);
        if (!$client || !$user) { throw new RuntimeException('Usage: revoke-client CLIENT_ID USER_ID'); }
        Db::run('DELETE FROM client_users WHERE client_id=? AND user_id=?',[$client,$user]); Db::audit(null,'cli-revoke-client','users',$user); echo 'Client access revoked.'.PHP_EOL;
    } elseif ($command==='cleanup') { Db::run('DELETE FROM rate_limits WHERE expires_at<?',[time()]); echo 'Expired rate counters removed.'.PHP_EOL; }
    elseif ($command==='check') {
        $required=['APP_URL','APP_KEY','DB_HOST','DB_DATABASE','DB_USERNAME','DB_PASSWORD','SUPABASE_URL','SUPABASE_ANON_KEY','SUPABASE_SERVICE_KEY'];$errors=[];
        foreach($required as $key) { if (!getenv($key)) { $errors[]=$key.' is missing'; } }
        if (strlen(getenv('APP_KEY') ?: '')<32) { $errors[]='APP_KEY must contain at least 32 random characters'; }
        if (!str_starts_with(getenv('APP_URL') ?: '', 'https://') && getenv('APP_ENV')==='production') { $errors[]='Production APP_URL must use HTTPS'; }
        foreach($errors as $error) { fwrite(STDERR,$error.PHP_EOL); }
        if ($errors) { exit(1); }
        Db::one('SELECT 1'); echo 'Configuration and database connection passed. External services have not been tested.'.PHP_EOL;
    } else { echo "Commands: migrate, check, grant-user, disable-user, revoke-client, cleanup\n"; }
} catch(Throwable $error) { fwrite(STDERR,'Command failed: '.$error->getMessage().PHP_EOL); exit(1); }
