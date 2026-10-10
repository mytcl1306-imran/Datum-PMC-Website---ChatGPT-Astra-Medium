# Security design and release gates

## Implemented controls

- Native PDO prepared values and explicit resource/field allowlists.
- Escaped server content and browser `textContent`, no editable raw HTML.
- Same-origin CSP; no third-party script exceptions, no frames or plugins.
- HttpOnly, SameSite Strict, secure-by-default sessions; fixation protection and idle/absolute expiry.
- CSRF token validation on every API write, including login.
- Supabase server verification of the exact access token before trusting JWT identity/AAL.
- MFA required by default across protected APIs; pre-MFA endpoints limited to factor setup/verification and identity.
- Local permissions and client/project relationship checks. Version-history reads inherit resource permissions.
- Private bucket, random file keys, size/MIME restrictions and short signed download lifetime.
- Shared MySQL rate counters for login, forms, MFA and uploads.
- Audit events for administrative writes, provisioning, membership and document uploads.

## Remaining security work

This is not a declaration of OWASP compliance. No penetration test or full source-security audit was run. Test IDOR/tenant isolation, CSRF/session lifecycle, payload validation, upload handling, content injection, concurrent edits, SSRF boundaries and provider failures on staging. Map findings to a selected OWASP ASVS level and track evidence.

Uploads are not virus scanned or sandboxed. Limit uploads to trusted administrators until a quarantine/scanning pipeline exists. Signed URLs can be shared until expiry. Do not place them in public logs or analytics.

MySQL volume encryption is an infrastructure responsibility. The `.env` file contains high-privilege storage and database secrets. Restrict file access, keep it out of Git/backups intended for general distribution, rotate keys and use a secret manager in production. The application does not implement field-level encryption. Session files contain Supabase tokens and need equally strict access controls.

Audit records are in the same database and are not tamper-evident. Export them to a restricted external log service before treating them as compliance evidence. The generic application error logger omits exception details to avoid leaking data; use controlled staging diagnostics to troubleshoot.

Publish a reviewed privacy notice and legal terms, establish retention/deletion practices, and configure regional requirements for the actual business and target clients. This package does not provide legal advice or a globally valid privacy policy.

## Sources consulted

- https://www.php.net/manual/en/session.security.php
- https://www.php.net/manual/en/pdo.prepare.php
- https://supabase.com/docs/reference/javascript/auth-getuser
- https://supabase.com/docs/guides/auth/auth-mfa

These sources inform the design; they are not evidence that this implementation has passed security tests.
