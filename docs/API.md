# API reference

Base path `/api`. Requests and responses are JSON except document upload (`multipart/form-data`). Send cookies with same-origin requests. Obtain the CSRF token from the HTML meta element or `GET /session` and send `X-CSRF-Token` for every write. No cross-origin API access is enabled.

Common failures: 400 invalid JSON/provider rejection; 401 missing/expired login; 403 CSRF/role/MFA denial; 404 unknown or inaccessible resource; 409 content version conflict; 413 oversized request; 422 validation; 429 rate limit; 500 application failure; 503 configuration/provider failure. Errors use `{"error":"message"}` and may include a reference. Provider errors do not reveal raw provider data.

| Method and path | Request / result | Authorization |
|---|---|---|
| GET /session | `{csrf}` | Public |
| POST /login | email, password → local identity, role, mfa_required | CSRF, rate limit |
| GET /me | local identity, MFA status | Signed-in account |
| POST /logout | empty object → ok | CSRF |
| GET /mfa | verified factors | Signed-in account, AAL1 allowed |
| POST /mfa/enroll | empty object → factor ID, TOTP data | Signed-in account, rate limit |
| POST /mfa/verify | factor_id, six-digit code → identity | Signed-in account, rate limit |
| POST /leads | name, email, phone, service, message, consent=true, website empty → reference | Public, CSRF, rate limit |
| POST /newsletter | email, consent=true → pending-interest message | Public, CSRF, rate limit |
| GET /projects | up to 100 accessible project summaries | MFA, project membership; admin sees all |
| GET /projects/{id} | project with milestones, records, documents, issued invoices | Project membership or admin |
| POST /projects/{id}/records | kind, title, body → ID | Clients: rfi/snag only; admins: all types |
| PUT /records/{id} | status → ok | projects.write |
| POST /projects/{id}/documents | multipart file → ok | projects.write and project access |
| GET /documents/{id}/download | `{url}` valid for 60 seconds | Project access |
| GET /admin/resources/{resource}?offset=0 | `{rows,fields}`, 50 rows | Registry permission |
| GET /admin/resources/{resource}/{id} | Record | Registry permission |
| POST /admin/resources/{resource} | All allowlisted fields → record | Registry permission |
| PUT /admin/resources/{resource}/{id} | All fields plus current version if versioned | Registry permission |
| DELETE /admin/resources/{resource}/{id} | deleted=true; relational constraints may block | Registry permission |
| GET /admin/versions?resource=pages&id=1 | Prior snapshots | Original resource permission |
| GET /admin/overview | Content and project counts | cms.write |
| GET /admin/leads | Latest 100 leads | leads.write |
| PUT /admin/leads/{id} | status, assigned_to local admin ID or null | leads.write |
| GET /admin/settings | Key/value list | users.write |
| PUT /admin/settings | key, value | users.write |
| GET /admin/users | Local account list | users.write |
| POST /admin/client-access | client_id, user_id | users.write |
| GET /admin/audit | Latest 100 events | users.write |

The complete resource field/type registry is `config/resources.php`. There is no generic SQL endpoint. There is no implemented public payment, AI, appointment or calendar endpoint.
