<?php
declare(strict_types=1);
namespace Datum\Controllers;
use Datum\Core\Db;
final class SiteController
{
    public static function render(string $path): void
    {
        $settings=array_column(Db::all('SELECT * FROM settings'),'value','key');
        $services=Db::all("SELECT * FROM services WHERE status='published' ORDER BY id");
        $brand=$settings['brand_name']; $page=null; $rows=[]; $template='page'; $title=$brand; $description=$settings['hero_description'];
        if ($path==='/robots.txt') { header('Content-Type: text/plain'); echo "User-agent: *\nDisallow: /admin\nDisallow: /portal\nDisallow: /api/\nSitemap: ".rtrim(getenv('APP_URL'),'/')."/sitemap.xml\n"; return; }
        if ($path==='/sitemap.xml') {
            header('Content-Type: application/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            $links=['/','/services','/projects','/blog','/contact','/resources'];
            foreach(Db::all("SELECT slug FROM pages WHERE status='published'") as $p) { $links[]='/'.$p['slug']; }
            foreach($services as $s) { $links[]='/services/'.$s['slug']; }
            foreach(Db::all("SELECT slug FROM blog WHERE status='published'") as $p) { $links[]='/blog/'.$p['slug']; }
            foreach(Db::all('SELECT slug FROM projects WHERE public=1') as $p) { $links[]='/projects/'.$p['slug']; }
            foreach(array_unique($links) as $link) { echo '<url><loc>'.e(rtrim(getenv('APP_URL'),'/').$link).'</loc></url>'; } echo '</urlset>'; return;
        }
        if ($path==='/') { $template='home'; }
        elseif ($path==='/contact') { $template='contact'; $title='Discuss your project'; }
        elseif ($path==='/admin' || $path==='/portal') { $template='workspace'; $title=$path==='/admin'?'Administration':'Client workspace'; }
        elseif ($path==='/resources') { $template='resources'; $title='Tools for clearer decisions'; }
        elseif ($path==='/services') { $template='listing'; $title='Expertise, connected.'; $description='From the first feasibility question to the final account.'; $rows=array_map(static fn($r)=>[...$r,'link'=>'/services/'.$r['slug']],$services); }
        elseif (preg_match('#^/(services|blog)/([a-z0-9-]+)$#D',$path,$m)) { $page=Db::one("SELECT * FROM {$m[1]} WHERE slug=? AND status='published'",[$m[2]]); }
        elseif (preg_match('#^/projects/([a-z0-9-]+)$#D',$path,$m)) { $page=Db::one('SELECT title,summary,body FROM projects WHERE slug=? AND public=1',[$m[1]]); }
        elseif (in_array($path,['/projects','/portfolio','/case-studies'],true)) {
            $template='listing'; $title='Work, in perspective.'; $description='Explore published project information.';
            $q=mb_substr((string)($_GET['q'] ?? ''),0,100); $category=mb_substr((string)($_GET['category'] ?? ''),0,80); $location=mb_substr((string)($_GET['location'] ?? ''),0,160);
            $rows=Db::all("SELECT title,summary,slug,category,location FROM projects WHERE public=1 AND (title LIKE ? OR summary LIKE ?) AND (?='' OR category=?) AND (?='' OR location LIKE ?) ORDER BY id DESC LIMIT 60",['%'.$q.'%','%'.$q.'%',$category,$category,$location,'%'.$location.'%']);
            $rows=array_map(static fn($r)=>[...$r,'link'=>'/projects/'.$r['slug']],$rows);
        }
        elseif ($path==='/blog' || $path==='/news') { $template='listing'; $title='Notes from the field.'; $description='Published insights and updates.'; $rows=array_map(static fn($r)=>[...$r,'link'=>'/blog/'.$r['slug']],Db::all("SELECT title,summary,slug FROM blog WHERE status='published' ORDER BY id DESC LIMIT 60")); }
        elseif ($path==='/testimonials') { $template='listing'; $title='Client perspectives'; $rows=Db::all("SELECT name title,quote summary FROM testimonials WHERE status='published' ORDER BY id DESC"); }
        elseif ($path==='/search') {
            $template='listing'; $title='Search'; $description='Find services, pages and articles.'; $q=mb_substr((string)($_GET['q'] ?? ''),0,100);
            if ($q!=='') { foreach(['services','pages','blog'] as $table) { foreach(Db::all("SELECT title,summary,slug FROM $table WHERE status='published' AND (title LIKE ? OR body LIKE ?) LIMIT 20",['%'.$q.'%','%'.$q.'%']) as $r) { $rows[]=[...$r,'link'=>($table==='pages'?'':'/'.$table).'/'.$r['slug']]; } } }
        }
        elseif ($path==='/sitemap') { $template='listing'; $title='Explore DATUM'; foreach(Db::all("SELECT title,summary,slug FROM pages WHERE status='published'") as $r) { $rows[]=[...$r,'link'=>'/'.$r['slug']]; } }
        else { $slug=trim($path,'/'); $page=Db::one("SELECT * FROM pages WHERE slug=? AND status='published'",[$slug]); }
        if ($page) { $title=($page['seo_title'] ?? '') ?: $page['title']; $description=($page['seo_description'] ?? '') ?: $page['summary']; }
        if ($template==='page' && !$page) { http_response_code(404); $template='404'; $title='Page not found'; }
        $seo=Db::one('SELECT * FROM seo WHERE path=?',[$path]);
        if ($seo) { $title=$seo['title']; $description=$seo['description']; }
        $canonical=rtrim(getenv('APP_URL') ?: '', '/').$path;
        $noindex=in_array($template,['workspace','404'],true) || $path==='/search' || ($seo['noindex'] ?? false);
        require dirname(__DIR__).'/Views/layout.php';
    }
}
