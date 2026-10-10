"use strict";
(() => {
  const $ = selector => document.querySelector(selector);
  const el = (tag, text, className) => { const node = document.createElement(tag); if (text !== undefined) node.textContent = text; if (className) node.className = className; return node; };
  const button = (text, action, className = 'button secondary') => { const b = el('button', text, className); b.type = 'button'; b.addEventListener('click', () => Promise.resolve(action()).catch(showError)); return b; };
  const content = $('#workspace-content'); const admin = $('[data-workspace]').dataset.workspace === 'admin';
  let csrf = $('meta[name=csrf-token]').content, user, factor, currentResource, offset = 0;
  const showError = error => { $('#workspace-message').textContent = error.message; };
  const api = async (path, method = 'GET', body) => {
    const headers = {'X-CSRF-Token': csrf}; if (body && !(body instanceof FormData)) headers['Content-Type'] = 'application/json';
    const response = await fetch('/api' + path, {method, headers, body: body ? body instanceof FormData ? body : JSON.stringify(body) : undefined});
    const data = await response.json(); if (!response.ok) throw new Error(data.error || 'Request failed'); return data;
  };
  const busy = async (form, task) => { const b = form.querySelector('button[type=submit],button:not([type])'); if (b) b.disabled = true; try { await task(); } catch(e) { showError(e); } finally { if (b) b.disabled = false; } };
  const field = (name, type = 'text', value = '') => {
    const label = el('label', name.replaceAll('_', ' ')); let input;
    if (type.includes('|')) { input = el('select'); for (const v of type.split('|')) { const option = el('option', v); option.value = v; input.append(option); } }
    else if (['long','body'].includes(type)) { input = el('textarea'); input.rows = type === 'body' ? 10 : 3; label.classList.add('full'); }
    else { input = el('input'); input.type = type === 'bool' ? 'checkbox' : ['id','money','progress','positive'].includes(type) ? 'number' : type.startsWith('date') ? 'date' : type === 'email' ? 'email' : type === 'url' ? 'url' : 'text'; if (input.type === 'number') { input.min = ['id','positive'].includes(type) ? '1' : '0'; input.step = ['money','positive'].includes(type) ? 'any' : '1'; if (type === 'progress') input.max = '100'; } }
    input.name = name; if (type === 'bool') { input.checked = !!Number(value); label.classList.add('check'); } else input.value = value ?? '';
    input.required = !['optional','date?','bool'].includes(type); label.append(input); return label;
  };
  const table = (rows, columns, actions) => {
    const wrap = el('div', undefined, 'table-scroll'), t = el('table'), head = el('thead'), tr = el('tr');
    for (const name of columns) { const th = el('th', name.replaceAll('_',' ')); th.scope = 'col'; tr.append(th); }
    if (actions) tr.append(el('th', 'Actions')); head.append(tr); t.append(head); const body = el('tbody');
    for (const row of rows) { const r = el('tr'); for (const key of columns) r.append(el('td', row[key] ?? '—')); if (actions) { const td = el('td'); actions(row).forEach(b => td.append(b)); r.append(td); } body.append(r); }
    t.append(body); wrap.append(t); if (!rows.length) wrap.append(el('p', 'No records yet.', 'empty')); return wrap;
  };
  $('#login-form').addEventListener('submit', e => { e.preventDefault(); busy(e.target, async () => { user = await api('/login','POST',Object.fromEntries(new FormData(e.target))); e.target.reset(); await enter(); }); });
  $('#logout').addEventListener('click', async () => { try { await api('/logout','POST',{}); location.reload(); } catch(e) { showError(e); } });
  $('#mfa-form').addEventListener('submit', e => { e.preventDefault(); busy(e.target, async () => { user = await api('/mfa/verify','POST',{factor_id: factor, code: e.target.elements.code.value}); await enter(); }); });
  async function enter() {
    $('#workspace-message').textContent = ''; $('#login-form').hidden = true; $('#logout').hidden = false;
    if (user.mfa_required) {
      $('#mfa-panel').hidden = false; const factors = await api('/mfa');
      const area = $('#mfa-enrollment'); area.replaceChildren();
      if (factors.factors.length) factor = factors.factors[0].id;
      else area.append(button('Set up authenticator', async () => {
        const data = await api('/mfa/enroll','POST',{}); factor = data.id;
        area.replaceChildren(el('p','Add a time-based account in your authenticator using this secret, then enter its six-digit code.'), el('p',data.totp.secret,'secret'));
      })); return;
    }
    $('#mfa-panel').hidden = true; $('#workspace-body').hidden = false;
    if (admin && !['admin','editor'].includes(user.role)) { $('#workspace-body').hidden = true; throw new Error('This account cannot access administration. Use the client portal.'); }
    const nav = $('#workspace-nav'); nav.replaceChildren();
    const tab = (name, action) => { const b = button(name, async () => { nav.querySelectorAll('button').forEach(x => x.classList.remove('active')); b.classList.add('active'); await action(); }, ''); nav.append(b); };
    if (admin) {
      for (const resource of ['pages','services','blog','portfolio','testimonials','media','categories','tags','seo',...(user.role === 'admin' ? ['clients','projects','milestones','invoices','invoice_items'] : [])]) tab(resource.replaceAll('_',' '), () => { offset=0; return listResource(resource); });
      if (user.role === 'admin') { tab('Leads', leads); tab('Settings', settings); tab('Users & access', users); tab('Audit log', async () => { const rows = await api('/admin/audit'); content.replaceChildren(table(rows,['created_at','user_id','action','resource','record_id'])); }); }
      await listResource('pages');
    } else { tab('My projects', projects); await projects(); }
  }
  async function listResource(resource) {
    currentResource = resource; const data = await api(`/admin/resources/${resource}?offset=${offset}`); content.replaceChildren();
    const tools = el('div',undefined,'toolbar'); tools.append(el('h2',resource.replaceAll('_',' ')),button('Create record',() => editor(resource,data.fields))); content.append(tools);
    const columns = ['id', ...Object.keys(data.fields).filter(k => !['body','summary','quote','seo_description'].includes(k)).slice(0,5)];
    content.append(table(data.rows,columns,row => [button('Edit',() => editor(resource,data.fields,row)),button('Delete',async () => { if (confirm('Delete this record? Related records may prevent deletion.')) { await api(`/admin/resources/${resource}/${row.id}`,'DELETE'); await listResource(resource); } })]));
    const pager = el('div',undefined,'actions'); if (offset) pager.append(button('Previous',() => { offset = Math.max(0,offset-50); return listResource(resource); })); if (data.rows.length === 50) pager.append(button('Next',() => { offset+=50; return listResource(resource); })); content.append(pager);
  }
  function editor(resource, fields, row = {}) {
    content.querySelector('.editor')?.remove(); const form = el('form',undefined,'panel editor'); form.append(el('h2',row.id ? `Edit record #${row.id}` : 'Create record'));
    const grid = el('div',undefined,'form-grid'); Object.entries(fields).forEach(([name,type]) => grid.append(field(name,type,row[name] ?? ''))); form.append(grid);
    const save = el('button','Save record','button'); save.type='submit'; form.append(save,button('Cancel',() => form.remove()));
    if (row.version) form.append(button('Version history',async () => { const versions=await api(`/admin/versions?resource=${encodeURIComponent(resource)}&id=${row.id}`); const dialog=el('dialog'); dialog.append(el('h2','Previous versions'),el('p','To restore, copy approved field values into the editor and save a new version.')); versions.forEach(v => { dialog.append(el('h3',`Version ${v.version} · ${v.created_at}`),el('pre',JSON.stringify(JSON.parse(v.snapshot),null,2))); }); dialog.append(button('Close',()=>{dialog.close();dialog.remove();})); document.body.append(dialog); dialog.showModal(); }));
    form.addEventListener('submit',e => { e.preventDefault(); busy(form, async () => { const data=Object.fromEntries(new FormData(form)); for (const [key,type] of Object.entries(fields)) if (type==='bool') data[key]=form.elements[key].checked; if(row.version) data.version=row.version; await api(`/admin/resources/${resource}${row.id ? '/'+row.id : ''}`,row.id?'PUT':'POST',data); $('#workspace-message').textContent='Record saved.'; await listResource(resource); }); });
    content.append(form); form.scrollIntoView({block:'start'}); form.querySelector('input,textarea,select')?.focus();
  }
  async function settings() {
    const rows=await api('/admin/settings'); content.replaceChildren(el('h2','Brand and site settings'));
    for (const row of rows) { const form=el('form',undefined,'panel record'); form.append(field('value','long',row.value)); form.querySelector('label').firstChild.textContent=row.key.replaceAll('_',' '); const b=el('button','Save','button'); b.type='submit'; form.append(b); form.addEventListener('submit',e=>{e.preventDefault();busy(form,async()=>{await api('/admin/settings','PUT',{key:row.key,value:form.elements.value.value});$('#workspace-message').textContent='Setting saved.';});}); content.append(form); }
  }
  async function leads() {
    const rows=await api('/admin/leads'); content.replaceChildren(el('h2','Lead inbox'),table(rows,['id','created_at','name','email','phone','service','message','status','assigned_to'],row=>[button('Update',()=>{
      const form=el('form',undefined,'panel record'); form.append(el('h3',`Lead #${row.id}`),field('status','new|contacted|qualified|won|lost',row.status),field('assigned_to','optional',row.assigned_to)); const b=el('button','Save','button');b.type='submit';form.append(b);
      form.addEventListener('submit',e=>{e.preventDefault();busy(form,async()=>{await api('/admin/leads/'+row.id,'PUT',{status:form.elements.status.value,assigned_to:form.elements.assigned_to.value||null});await leads();});});content.prepend(form);
    })]));
  }
  async function users() {
    const rows=await api('/admin/users'); content.replaceChildren(el('h2','Users and client access'),el('p','Account provisioning and role changes use the server CLI. Associate a provisioned user with a client below.'),table(rows,['id','name','email','role','active']));
    const form=el('form',undefined,'panel record');form.append(field('client_id','id'),field('user_id','id'));const b=el('button','Grant access','button');b.type='submit';form.append(b);form.addEventListener('submit',e=>{e.preventDefault();busy(form,async()=>{await api('/admin/client-access','POST',Object.fromEntries(new FormData(form)));$('#workspace-message').textContent='Client access granted.';form.reset();});});content.append(form);
  }
  async function projects() {
    const rows=await api('/projects'); content.replaceChildren(el('h2','Your projects')); const grid=el('div',undefined,'project-list');
    for(const row of rows){const b=button('',()=>project(row.id),'panel project-card');b.append(el('h2',row.title),el('p',`${row.location} · ${row.state}`),el('p',`${row.progress}% reported progress`));const progress=el('progress',`${row.progress}%`,'progress');progress.value=row.progress;progress.max=100;progress.setAttribute('aria-label',`${row.title} progress`);b.append(progress);grid.append(b);} content.append(grid);if(!rows.length)content.append(el('p','No projects have been assigned to this account.','empty'));
  }
  async function project(id) {
    const p=await api('/projects/'+id);const detail=el('div',undefined,'project-detail');detail.append(button('← All projects',projects),el('h2',p.title),el('p',p.summary),el('p',`${p.progress}% reported progress · ${p.state}`));
    const milestones=el('section');milestones.append(el('h2','Milestones'),table(p.milestones,['title','due_date','completed']));detail.append(milestones);
    const invoices=el('section');invoices.append(el('h2','Invoices'),table(p.invoices,['number','due_date','currency','total','status']));detail.append(invoices);
    const documents=el('section');documents.append(el('h2','Documents'),table(p.documents,['title','mime','size_bytes','created_at'],d=>[button('Download',async()=>{const result=await api(`/documents/${d.id}/download`);const link=el('a','Open secure download ↗','text-link');link.href=result.url;link.target='_blank';link.rel='noopener noreferrer';documents.append(link);})]));
    if(user.role==='admin'){const upload=el('form',undefined,'panel');const label=el('label','Upload PDF, JPEG, PNG or text (10 MB maximum)');const input=el('input');input.type='file';input.name='file';input.required=true;input.accept='.pdf,.jpg,.jpeg,.png,.txt';label.append(input);upload.append(label);const b=el('button','Upload','button');b.type='submit';upload.append(b);upload.addEventListener('submit',e=>{e.preventDefault();busy(upload,async()=>{await api(`/projects/${id}/documents`,'POST',new FormData(upload));await project(id);});});documents.append(upload);}detail.append(documents);
    const records=el('section');records.append(el('h2','Project records'));for(const r of p.records){const card=el('article',undefined,'panel record');card.append(el('span',`${r.kind} · ${r.status}`,'tag'),el('h3',r.title),el('p',r.body));if(user.role==='admin'){const status=field('status','open|in-progress|closed|approved|rejected',r.status);card.append(status,button('Update status',async()=>{await api('/records/'+r.id,'PUT',{status:status.querySelector('select').value});await project(id);}));}records.append(card);}if(!p.records.length)records.append(el('p','No project records yet.'));detail.append(records);
    const form=el('form',undefined,'panel');form.append(el('h3','Add a project record'),field('kind',user.role==='admin'?'report|meeting|rfi|snag|approval|risk':'rfi|snag'),field('title'),field('body','long'));const b=el('button','Submit record','button');b.type='submit';form.append(b);form.addEventListener('submit',e=>{e.preventDefault();busy(form,async()=>{await api(`/projects/${id}/records`,'POST',Object.fromEntries(new FormData(form)));await project(id);});});detail.append(form);content.replaceChildren(detail);
  }
  api('/me').then(async data=>{user=data;await enter();}).catch(error=>{if(!/Sign in/.test(error.message))showError(error);});
})();
