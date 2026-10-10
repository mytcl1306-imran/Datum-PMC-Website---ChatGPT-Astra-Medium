# Deployment guide

The included Compose configuration deploys one Apache/PHP process group and one MySQL container. It is suitable for initial integration validation; it is not a high-availability topology. The available ChatGPT Sites hosting is a JavaScript Worker runtime and cannot run this stack, so this source package has not been published there.

## Production host

1. Install Docker/Compose using your operating system's supported procedure. Apply host updates and configure a firewall exposing only HTTPS and restricted administrative access.
2. Configure a domain and a certificate with your reverse proxy. Forward HTTPS traffic to `127.0.0.1:8080`. Verify the certificate chain and redirects before enabling public access.
3. Set `APP_URL` to the exact HTTPS origin, `APP_ENV=production`, `SESSION_SECURE=true`, `REQUIRE_MFA=true`. Check `php bin/console.php check` inside the web container.
4. Run migrations on the intended database; verify the migration table. Provision the first administrator, complete MFA and publish reviewed content.
5. Apply Supabase region/access policies and private bucket setup. Test both allowed and forbidden downloads.
6. Run the tests in TESTING.md, backup/restore rehearsal and an independent security review before launch.

## Reverse proxies and Cloudflare

The application rate limits by `REMOTE_ADDR` and intentionally does not trust arbitrary forwarded headers. Behind a proxy, configure Apache `mod_remoteip` or the equivalent Nginx trusted-proxy handling with an explicit allowlist of your actual proxy addresses. Do not trust the entire internet. Without this, all users may share a rate bucket. This configuration is deployment-specific and is not included automatically.

For Cloudflare, use strict end-to-end TLS and verify the origin certificate. Bypass caching for `/api/*`, `/admin`, `/portal` and any session-dependent HTML. Do not cache responses carrying session cookies. The application currently sends `Cache-Control: no-store` on HTML/API responses. Static assets can be cached separately; use conservative cache lifetimes until filename fingerprinting is implemented. Enable appropriate Brotli/gzip compression at the proxy after testing.

## Alternative Nginx installation

`deploy/nginx.conf` describes a PHP-FPM web root at `/var/www/datum/public`. It is an alternative to Apache, not a second layer inside the current container. Install PHP 8.3 FPM and required extensions; set environment/secrets for the worker, session directory permissions and database connectivity. Add the host-specific TLS server block. Verify sensitive repository files are outside the web root.

## Release discipline

Tag the source, review dependency/base-image updates, build an immutable image, execute migrations in staging, run acceptance tests and retain a verified rollback image/database plan. The supplied image tags are version-family tags, not pinned digests; pin approved digests after your vulnerability review. Never bake `.env` into the image. The Docker ignore file excludes environment files.

Container logs go to stdout/stderr. Aggregate them with retention controls and alert on error rate, failed authentication, storage errors, database saturation and failed backups. Add an internal health/readiness endpoint before orchestrated deployment; one is not included in this build.
