# DATUM · Project Management Consultancy

Start with **docs/README.md** and **docs/FEATURE-MATRIX.md**.

This is a PHP/MySQL application source package, not a fully complete or production-verified system. Required credentials, real business content, missing integrations and runtime acceptance testing are documented explicitly.

Quick path after preparing Supabase and a HTTPS host:

```sh
python3 bin/configure.py
docker compose up -d --build
docker compose exec web php bin/console.php migrate
docker compose exec web php bin/console.php check
```

Then grant an existing Supabase user's UUID a portal role using the command described in docs/INSTALLATION.md. No default account or password is supplied.
