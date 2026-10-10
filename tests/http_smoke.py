#!/usr/bin/env python3
"""Run against an isolated installed test environment; this creates one test lead."""
import http.cookiejar, json, os, urllib.request, urllib.error, uuid
base = os.environ['TEST_BASE_URL'].rstrip('/')
jar = http.cookiejar.CookieJar()
client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))
def request(path, body=None, csrf=None):
    headers = {'Content-Type':'application/json'}
    if csrf: headers['X-CSRF-Token'] = csrf
    req = urllib.request.Request(base+path, data=json.dumps(body).encode() if body is not None else None, headers=headers)
    try:
        with client.open(req) as response: return response.status, response.read().decode()
    except urllib.error.HTTPError as error: return error.code, error.read().decode()
status, body = request('/'); assert status == 200 and 'DATUM' in body
status, body = request('/api/session'); assert status == 200
csrf = json.loads(body)['csrf']
assert request('/api/admin/resources/pages')[0] == 401
assert request('/api/projects/1')[0] == 401
assert request('/api/documents/1/download')[0] in (401,404)
assert request('/api/leads', {})[0] == 403
assert request('/api/leads', {'name':'Check'}, csrf)[0] == 422
lead = {'name':'Automated verification','email':f'qa-{uuid.uuid4().hex}@example.invalid','service':'Project Management','message':'Isolated deployment test; delete this lead after validation.','consent':True,'website':''}
status, body = request('/api/leads', lead, csrf); assert status == 201, (status,body)
assert json.loads(body)['reference'] > 0
assert request('/api/leads',dict(lead,website='bot'),csrf)[0] == 422
assert request('/does-not-exist')[0] == 404
assert request('/sitemap.xml')[0] == 200
print('HTTP smoke checks passed; remove the verification lead from the test database.')
