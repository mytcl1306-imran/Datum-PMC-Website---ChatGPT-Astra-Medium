# Testing record and acceptance plan

## Executed during package creation

- Node syntax checks for both browser JavaScript files: passed.
- Python parsing/compilation checks for the configuration and smoke-test scripts: passed.
- Compose YAML and Composer JSON parse checks: passed.
- Shell syntax checks for backup and restore scripts: passed.
- Static asset reference, SVG XML and PHP template-target existence checks: passed.
- SQL seed counts and schema/foreign-key target consistency checks: passed.
- ZIP integrity check: passed before delivery.

## Not executed

PHP syntax/runtime checks, MySQL migration execution, Docker builds, Supabase authentication/MFA/storage requests, browser rendering, accessibility automation/manual checks, Lighthouse/PageSpeed, email/payment/calendar integration, load tests and disaster recovery. PHP, MySQL and Docker were not available in the creation environment. No browser QA was performed. The scripts below are supplied for the deployment team; their inclusion is not a passing result.

## First staging checks

```sh
docker compose exec web sh -c 'find app public bin tests config -name "*.php" -exec php -l {} \;'
docker compose exec web php tests/run.php
docker compose exec web php bin/console.php check
```

Run `TEST_BASE_URL` pointing to the isolated test deployment with `python3 tests/http_smoke.py`. The test intentionally creates one labelled lead; remove it afterwards. Use HTTPS or set insecure cookies only for localhost testing. The smoke test covers public rendering, missing-login denials, CSRF rejection, input errors, honeypot rejection, lead persistence and sitemap/404 availability.

Execute `tests/tenant_isolation.md` with real staging identities. Test MFA enrolment, verified factors, refresh tokens, idle expiration, role changes and revoked memberships. Specifically test that CMS editors cannot read private project version snapshots.

## Product acceptance

Create and publish each content type; test apostrophes, Unicode, HTML injection strings, long input and duplicate slugs. Open the same versioned record in two browsers and ensure the second write receives 409. Verify unpublished pages and projects never appear in search/sitemap. Test signed downloads before and after expiry. Test invalid file types, over-size files and missing storage credentials.

Test invoice totals using decimal quantities and tax rates against independently calculated values. There is no currency conversion or payment reconciliation; never infer it from a manually set paid flag.

## Browser and accessibility

Verify mobile widths 320/375/768 px and desktop widths 1280/1920 px; both colour themes; keyboard-only use; focus visibility; navigation at 200% zoom; screen-reader form labels/status messages; long names/content; browser storage disabled; slow network and errors. Audit actual colour contrast. The implementation uses focus outlines and semantic HTML but has no WCAG certification.

## Performance and capacity

Run Lighthouse on production-like HTTPS pages with final content and imagery. Record device/network settings and all category scores. Profile protected API latency, Supabase verification frequency and database queries. Run staged concurrency tests before committing to thousands of users. Define an error budget and response-time objectives first; the requested score and throughput targets are unverified.
