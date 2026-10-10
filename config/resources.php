<?php
return [
 'pages'=>['permission'=>'cms.write','fields'=>['slug'=>'slug','title'=>'text','summary'=>'long','body'=>'body','status'=>'draft|published','seo_title'=>'optional','seo_description'=>'optional']],
 'services'=>['permission'=>'cms.write','fields'=>['slug'=>'slug','title'=>'text','summary'=>'long','body'=>'body','status'=>'draft|published']],
 'blog'=>['permission'=>'cms.write','fields'=>['slug'=>'slug','title'=>'text','summary'=>'long','body'=>'body','status'=>'draft|published']],
 'testimonials'=>['permission'=>'cms.write','fields'=>['client_id'=>'id','name'=>'text','company'=>'text','quote'=>'long','status'=>'draft|published']],
 'portfolio'=>['permission'=>'cms.write','fields'=>['project_id'=>'id','title'=>'text','summary'=>'long','body'=>'body','status'=>'draft|published']],
 'projects'=>['permission'=>'projects.write','fields'=>['client_id'=>'id','slug'=>'slug','title'=>'text','summary'=>'long','body'=>'body','location'=>'text','category'=>'text','budget'=>'money','currency'=>'currency','progress'=>'progress','state'=>'planning|active|completed|on-hold','completion_date'=>'date?','public'=>'bool']],
 'clients'=>['permission'=>'projects.write','fields'=>['name'=>'text','email'=>'email','company'=>'optional']],
 'milestones'=>['permission'=>'projects.write','fields'=>['project_id'=>'id','title'=>'text','due_date'=>'date','completed'=>'bool']],
 'invoices'=>['permission'=>'projects.write','fields'=>['project_id'=>'id','number'=>'text','currency'=>'currency','due_date'=>'date','status'=>'draft|issued|paid|void']],
 'invoice_items'=>['permission'=>'projects.write','fields'=>['invoice_id'=>'id','description'=>'text','quantity'=>'positive','unit_price'=>'money','tax_percent'=>'progress']],
 'media'=>['permission'=>'cms.write','fields'=>['title'=>'text','url'=>'url','alt_text'=>'text','kind'=>'photo|video|panorama']],
 'categories'=>['permission'=>'cms.write','fields'=>['name'=>'text','slug'=>'slug']],
 'tags'=>['permission'=>'cms.write','fields'=>['name'=>'text','slug'=>'slug']],
 'seo'=>['permission'=>'cms.write','fields'=>['path'=>'path','title'=>'text','description'=>'long','noindex'=>'bool']],
];
