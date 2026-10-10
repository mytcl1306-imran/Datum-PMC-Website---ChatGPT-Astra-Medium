"use strict";
(() => {
  const root = document.documentElement;
  const automatic = () => matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  let saved; try { saved = localStorage.getItem('datum-theme'); } catch {}
  root.dataset.theme = saved || automatic();
  matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => { if (!saved) root.dataset.theme = automatic(); });
  document.querySelector('#theme-toggle')?.addEventListener('click', () => {
    saved = root.dataset.theme === 'dark' ? 'light' : 'dark'; root.dataset.theme = saved;
    try { localStorage.setItem('datum-theme', saved); } catch {}
  });
  const menu = document.querySelector('.menu-toggle');
  menu?.addEventListener('click', () => { const open = menu.getAttribute('aria-expanded') !== 'true'; menu.setAttribute('aria-expanded', String(open)); document.querySelector('#navigation').classList.toggle('open', open); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { menu?.setAttribute('aria-expanded', 'false'); document.querySelector('#navigation')?.classList.remove('open'); } });
  const submit = (id, endpoint) => {
    const form = document.getElementById(id); if (!form) return;
    form.addEventListener('submit', async e => {
      e.preventDefault(); const button = form.querySelector('button'); const message = form.querySelector('.form-message'); button.disabled = true; message.textContent = 'Sending…';
      const body = Object.fromEntries(new FormData(form)); body.consent = form.elements.consent.checked;
      try {
        const response = await fetch(endpoint, {method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').content}, body: JSON.stringify(body)});
        const result = await response.json(); if (!response.ok) throw new Error(result.error || 'Could not send. Please try again.');
        message.textContent = result.message + (result.reference ? ` Reference: ${result.reference}.` : ''); form.reset();
      } catch (error) { message.textContent = error.message; } finally { button.disabled = false; }
    });
  };
  submit('contact-form', '/api/leads'); submit('newsletter', '/api/newsletter');
  const number = n => new Intl.NumberFormat(undefined, {maximumFractionDigits: 2}).format(n);
  const calculations = {
    'cost-calculator': v => `${number(v.area * v.rate * (1 + v.contingency / 100))} total budget`,
    'time-calculator': v => `${number(Math.ceil(v.quantity / (v.productivity * v.crews)))} working days`,
    'roi-calculator': v => `${number((v.savings - v.fee) / v.fee * 100)}% estimated net return`
  };
  for (const [id, calculate] of Object.entries(calculations)) {
    document.getElementById(id)?.addEventListener('submit', e => { e.preventDefault(); const values = Object.fromEntries([...new FormData(e.target)].map(([k,v]) => [k, Number(v)])); const output = calculate(values); e.target.querySelector('output').textContent = /Infinity|NaN/.test(output) ? 'Inputs exceed the calculation range.' : output; });
  }
})();
