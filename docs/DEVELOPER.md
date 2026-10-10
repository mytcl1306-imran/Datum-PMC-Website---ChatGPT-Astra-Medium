# Developer documentation

## Architecture

`public/index.php` is the front controller. `app/bootstrap.php` provides environment loading, the autoloader and HTML escaping. Core database and HTTP helpers are in `app/Core`. Controllers select server-rendered views or dispatch JSON APIs. Supabase/Auth services isolate network identity and storage operations. The generic admin resource definitions are an explicit table/field/permission allowlist.

MySQL is the only authoritative business database. Supabase owns authentication and the private object store; its Postgres holds their managed metadata. This avoids introducing an unimplemented dual-write consistency model. PHP checks MySQL client membership before returning a signed object URL.

## Directory map

```text
app/Core             PDO, HTTP, CSRF, rate limiting
app/Controllers      public site, API, admin resources
app/Services         Supabase transport, identity and project access
app/Views            server-rendered templates
bin                  install and account administration
config               resource allowlists
public/assets        CSS, JS and vector identity assets
public/index.php     web entry point
database             ordered MySQL schema/content migrations
supabase             private bucket setup
deploy               Apache, Nginx and PHP configuration
scripts              backup and restore commands
tests                smoke tests and tenant acceptance procedure
docs                 operational and product documentation
storage              reserved runtime directory, not public
```

PHP follows namespaces, strict typing and PSR-4-style layout, but no automated PSR-12/style audit was executed. Controllers are compact and should be decomposed into domain services as the application grows. This is not a claim of complete SOLID/enterprise architecture compliance.

## Request boundaries

All writes use a session CSRF token. Browser code never receives Supabase access or refresh tokens; PHP stores them in the server-side session. On protected requests, the exact token is verified through Supabase `/auth/v1/user` before trusting decoded identity or AAL. Token refresh happens server-side. `Auth::permission` checks the local role mapping, and `Auth::project` checks the client/project relationship. Editors cannot access private projects unless explicitly assigned.

Never add a table name from user input directly to SQL. AdminController interpolates only a key from the fixed resource registry. Values use native PDO prepared statements. Text templates use `e()` or `paragraphs()`; browser DOM updates use `textContent`. HTML content is not supported.

## Adding features

Add a numbered migration, domain service, explicit authorization check and endpoint, then a UI consumer. For CMS-only resources, extend the allowlist and validation deliberately. Do not add users, roles, audit logs or payment transactions to generic CRUD. Use dedicated privileged workflows with audit records. For payment providers, implement signed webhooks, currency/amount verification and idempotency before exposing checkout.

## Content versioning

Updates lock the current record inside a transaction, compare the submitted version, snapshot the old state and increment the version. Concurrent edits return 409. Deletes are permanent and not reversible through snapshots. Non-versioned resources such as invoice lines use last-write-wins; add version columns before collaborative finance workflows.

## Known engineering limits

Large content collections are capped; public list pagination is not implemented. API routing is explicit but not generated from OpenAPI. File uploads can leave an orphan object if the later MySQL insert fails. Add compensating cleanup and object reconciliation. Private signed URLs are bearer links for 60 seconds. Database and external network failures return generic errors and a local request reference where available.
