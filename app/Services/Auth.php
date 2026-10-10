<?php
declare(strict_types=1);
namespace Datum\Services;
use Datum\Core\{Db,Http};
final class Auth
{
    public static function login(array $data): array
    {
        Http::limit('login',10);
        $email=Http::text($data,'email',254); $password=Http::text($data,'password',1024);
        $session=Supabase::request('POST','/auth/v1/token?grant_type=password',['email'=>$email,'password'=>$password]);
        session_regenerate_id(true);
        $_SESSION['access_token']=$session['access_token']; $_SESSION['refresh_token']=$session['refresh_token']; $_SESSION['last_active']=time(); $_SESSION['started']=time();
        return self::user(false);
    }
    public static function user(bool $enforceMfa = true): array
    {
        if (empty($_SESSION['access_token'])) { Http::fail('Sign in to continue',401); }
        if (time()-($_SESSION['last_active'] ?? 0)>1800 || time()-($_SESSION['started'] ?? 0)>28800) { self::logout(); Http::fail('Session expired',401); }
        $_SESSION['last_active']=time();
        $token=$_SESSION['access_token'];
        $claims=self::claims($token);
        if (($claims['exp'] ?? 0) < time()+60) {
            $session=Supabase::request('POST','/auth/v1/token?grant_type=refresh_token',['refresh_token'=>$_SESSION['refresh_token']]);
            $_SESSION['access_token']=$token=$session['access_token']; $_SESSION['refresh_token']=$session['refresh_token'];
        }
        // getUser verifies the exact token before any decoded claim is trusted.
        $remote=Supabase::request('GET','/auth/v1/user',null,$token);
        $claims=self::claims($token);
        if (($claims['sub'] ?? '') !== ($remote['id'] ?? '') || ($claims['exp'] ?? 0)<time()) { Http::fail('Invalid session',401); }
        $user=Db::one('SELECT u.id,u.auth_id,u.name,u.email,r.name role FROM users u JOIN roles r ON r.id=u.role_id WHERE u.auth_id=? AND u.active=1',[$remote['id']]);
        if (!$user) { Http::fail('Account has not been granted portal access',403); }
        $user['mfa_required']=getenv('REQUIRE_MFA') !== 'false' && ($claims['aal'] ?? '') !== 'aal2';
        if ($enforceMfa && $user['mfa_required']) { Http::fail('Complete two-factor verification',403); }
        return $user;
    }
    private static function claims(string $token): array
    {
        $parts=explode('.',$token);
        return json_decode(base64_decode(strtr($parts[1] ?? '', '-_', '+/')),true) ?: [];
    }
    public static function permission(string $permission): array
    {
        $user=self::user();
        if (!Db::one('SELECT p.id FROM permissions p JOIN role_permissions rp ON rp.permission_id=p.id JOIN users u ON u.role_id=rp.role_id WHERE u.id=? AND p.name=?',[$user['id'],$permission])) { Http::fail('Access denied',403); }
        return $user;
    }
    public static function project(int $id): array
    {
        $user=self::user();
        $project=Db::one('SELECT * FROM projects WHERE id=?',[$id]);
        if (!$project || ($user['role']!=='admin' && !Db::one('SELECT client_id FROM client_users WHERE client_id=? AND user_id=?',[$project['client_id'],$user['id']]))) { Http::fail('Project not found',404); }
        return $project;
    }
    public static function logout(): void { $_SESSION=[]; session_regenerate_id(true); $_SESSION['csrf']=bin2hex(random_bytes(32)); }
}
