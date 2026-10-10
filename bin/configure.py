#!/usr/bin/env python3
"""Create deployment environment interactively without printing credentials."""
from pathlib import Path
import getpass, secrets
root = Path(__file__).resolve().parents[1]
target = root / '.env'
if target.exists():
    raise SystemExit('.env already exists; update it using your secret-management workflow.')
url = input('Canonical HTTPS website URL: ').strip().rstrip('/')
supa = input('Supabase HTTPS project URL: ').strip().rstrip('/')
if not url.startswith('https://') or not supa.startswith('https://'):
    raise SystemExit('HTTPS URLs are required.')
values = {'APP_ENV':'production','APP_URL':url,'APP_KEY':secrets.token_hex(32),'DB_HOST':'db','DB_PORT':'3306','DB_DATABASE':'datum','DB_USERNAME':'datum','DB_PASSWORD':secrets.token_hex(32),'MYSQL_ROOT_PASSWORD':secrets.token_hex(32),'SUPABASE_URL':supa,'SUPABASE_ANON_KEY':getpass.getpass('Supabase anon JWT key: ').strip(),'SUPABASE_SERVICE_KEY':getpass.getpass('Supabase service-role JWT key: ').strip(),'SUPABASE_BUCKET':'project-documents','REQUIRE_MFA':'true','SESSION_SECURE':'true'}
if any('\n' in value or '\r' in value or not value for value in values.values()):
    raise SystemExit('All fields must contain a single non-empty value.')
fd = __import__('os').open(target, __import__('os').O_WRONLY | __import__('os').O_CREAT | __import__('os').O_EXCL, 0o600)
with __import__('os').fdopen(fd, 'w') as output:
    output.write('\n'.join(f'{key}={value}' for key,value in values.items())+'\n')
print('Configuration created. Keep .env private and outside version control.')
