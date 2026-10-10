# Installation

## Prerequisites

A Linux host with Docker Engine and Compose, a domain and TLS reverse proxy, Python 3 for interactive configuration, and a Supabase project. For installation without Docker, use PHP 8.3 with PDO MySQL, cURL, mbstring and fileinfo plus MySQL 8.0. The Apache web root must be `public/`; never expose the repository root.

## Supabase

1. Create a project in the appropriate data region. Enable email/password authentication and TOTP MFA.
2. Disable public user registration if this is an invitation-only client portal. Set the authentication site's URL to the production HTTPS origin.
3. Run `supabase/setup.sql` with an administrator in the Supabase SQL editor. Confirm the bucket is **private** and no existing Storage policies allow anonymous/client access to it.
4. Obtain the project URL, legacy anon JWT API key and service-role JWT key. The server-only service role can access all bucket data: treat it as a high-privilege secret. This client implementation expects JWT API keys, not the newer opaque publishable/secret key formats.
5. Create the owner's Supabase account through its administration interface, using a unique strong password. Retain its Auth UUID. Password resets and MFA factor recovery currently use Supabase administration; there is no application reset-password screen.

## Application

Run `python3 bin/configure.py` from the source root. It prompts for origin and Supabase keys without printing the keys, and generates random database credentials and the application key. It refuses to overwrite an existing `.env`. Store a secure copy with your secret manager.

Run:

```sh
docker compose up -d --build
docker compose exec web php bin/console.php migrate
docker compose exec web php bin/console.php check
```

MySQL is not exposed to the host network. Apache binds only to localhost port 8080. Configure your HTTPS reverse proxy to forward the website to this port. Do not expose that clear-text port directly.

Grant the account using actual values in the argument positions:

```text
docker compose exec web php bin/console.php grant-user AUTH_UUID EMAIL "FULL NAME" admin
```

The uppercase argument labels above describe required CLI inputs, not installed accounts. `grant-user` also changes an existing account's role or re-enables it. It does not create Supabase accounts or send email. Use roles `admin`, `editor` or `client`.

Visit `/admin`, sign in, enrol the TOTP secret in an authenticator, and verify the code. Populate branding, approved privacy/terms, real business contact data and evidence-backed content before launch. Test private project isolation with two client accounts.

## Local-only testing

If using plain HTTP on a local test host, set `APP_ENV=development`, `SESSION_SECURE=false` and an appropriate HTTP `APP_URL`. Never use those settings in production. Keep MFA enabled. Containers load environment at creation: recreate the web container after environment changes.

## Migration precautions

Migrations are ordered SQL files tracked in `migrations`. MySQL DDL is not transactionally rolled back. Run only on a fresh database for first installation, and take a verified backup before subsequent schema changes. If a migration fails partway, inspect and repair the partial state; do not blindly rerun or drop a populated database.
