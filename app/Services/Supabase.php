<?php
declare(strict_types=1);
namespace Datum\Services;
use Datum\Core\Http;
final class Supabase
{
    public static function request(string $method, string $path, ?array $body = null, ?string $token = null, bool $service = false, ?string $raw = null, string $mime = 'application/json'): array
    {
        $url=rtrim(getenv('SUPABASE_URL') ?: '', '/');
        $key=getenv($service ? 'SUPABASE_SERVICE_KEY' : 'SUPABASE_ANON_KEY');
        if (!str_starts_with($url,'https://') || !$key) { Http::fail('Service is not configured',503); }
        $curl=curl_init($url . $path);
        curl_setopt_array($curl,[CURLOPT_CUSTOMREQUEST=>$method,CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>20,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_HTTPHEADER=>['apikey: '.$key,'Authorization: Bearer '.($token ?: $key),'Content-Type: '.$mime]]);
        if ($body !== null || $raw !== null) { curl_setopt($curl,CURLOPT_POSTFIELDS,$raw ?? json_encode($body === [] ? (object)[] : $body,JSON_THROW_ON_ERROR)); }
        $response=curl_exec($curl); $code=curl_getinfo($curl,CURLINFO_RESPONSE_CODE); curl_close($curl);
        if ($response === false || $code === 0) { Http::fail('Authentication or storage service unavailable',503); }
        $result=json_decode($response,true) ?: [];
        if ($code >= 400) { Http::fail($code===400 || $code===401 || $code===422 ? 'Service rejected the request. Check your details.' : 'External service unavailable', $code===429 ? 429 : ($code>=500 ? 503 : 400)); }
        return $result;
    }
}
