<?php
declare(strict_types=1);
require dirname(__DIR__).'/app/bootstrap.php';
function check(bool $condition,string $name): void { if(!$condition) { throw new RuntimeException($name); } echo 'PASS '.$name.PHP_EOL; }
check(e('<script>"&')==='&lt;script&gt;&quot;&amp;','HTML escaping');
check(!str_contains(paragraphs('<img src=x onerror=alert(1)>'),'<img'),'content escaping');
$definitions=require dirname(__DIR__).'/config/resources.php';
check(!isset($definitions['users']) && !isset($definitions['audit_logs']) && !isset($definitions['payments']),'sensitive tables excluded from generic CRUD');
check(count($definitions['projects']['fields'])===13,'project field allowlist');
echo "Pure checks passed. Run HTTP and tenant-isolation tests on a configured test deployment.\n";
