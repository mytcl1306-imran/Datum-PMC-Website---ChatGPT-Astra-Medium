<?php
declare(strict_types=1);
require dirname(__DIR__).'/app/bootstrap.php';
use Datum\Core\Http;
use Datum\Controllers\{ApiController,SiteController};
Http::start();
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH) ?: '/';
try {
    if (str_starts_with($path,'/api/')) { ApiController::dispatch($path,$_SERVER['REQUEST_METHOD']); }
    if (!in_array($_SERVER['REQUEST_METHOD'],['GET','HEAD'],true)) { http_response_code(405); exit; }
    SiteController::render($path);
} catch (Throwable $error) {
    $requestId=bin2hex(random_bytes(8));
    error_log('DATUM '.$requestId.' '.get_class($error).' '.$error->getCode());
    if (str_starts_with($path,'/api/')) { Http::json(['error'=>'The request could not be completed.','reference'=>$requestId],500); }
    http_response_code(503); header('Retry-After: 120');
    echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>Service temporarily unavailable</title><h1>We’ll be back shortly.</h1><p>Please try again later.</p></html>';
}
