# Backup strategy and disaster recovery

## Coverage

Back up MySQL, private Supabase objects, source releases and the configuration/secret inventory separately. The included `scripts/backup.sh` covers **only MySQL**. It does not back up Supabase Auth or Storage. Arrange provider-supported Auth/Postgres backups and object exports according to the selected Supabase plan; verify restoration coverage, including password/MFA metadata and object bytes.

Use an encrypted backup volume for `BACKUP_DIR`. The script enables restrictive file permissions, runs a consistent transactional dump and tests gzip integrity. Compression is not encryption. Copy backups to a separate failure domain using a managed, authenticated process. Do not put service keys in public backup filenames or logs.

Suggested starting retention: daily for 14 days, weekly for eight weeks, monthly for 12 months, adjusted to the operator's obligations and data minimisation policy. Retention pruning is not automated by the scripts.

## Objectives

Proposed targets: a 24-hour recovery point and a four-hour recovery time for an initial small deployment. These are planning targets, not achieved guarantees. Tighter requirements need MySQL binary-log shipping/PITR, redundant hosting and tested provider recovery.

## Restore rehearsal

1. Provision an isolated host with no production traffic or outbound notification/payment jobs.
2. Restore the matching application release and secrets through the approved secret process.
3. Start an empty MySQL service. Point `RESTORE_FILE` to a verified dump, set `CONFIRM_RESTORE=RESTORE`, then run `scripts/restore.sh`.
4. Restore/verify the private Storage objects and Supabase identities through the provider's supported recovery flow.
5. Confirm migrations, row counts, client memberships, invoice totals and sampled file checksums.
6. Run authentication/MFA, tenant-isolation and download tests. Record elapsed time and data-loss window.
7. Obtain operational sign-off, change traffic routing and monitor. Keep the old environment isolated for investigation.

## Incident response

For compromised credentials, disable application access as appropriate, rotate affected secrets, revoke provider sessions and inspect audit/storage activity. Preserve evidence. Restore from a known clean recovery point only after identifying and closing the cause. Document the decision authority and client communication process before launch.
