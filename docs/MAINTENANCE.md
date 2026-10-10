# Maintenance plan

Daily: review new enquiries and failed requests; check backup completion, disk usage and database health. Run `php bin/console.php cleanup` from an authenticated server scheduler to prune expired rate-limit buckets. No scheduler is created automatically.

Weekly: review client memberships and outstanding RFIs/snags; inspect outbox accumulation. This build does not consume the outbox. Do not interpret pending events as successful notifications. Reconcile object storage against document records until automatic orphan management is built.

Monthly: test a database restore in isolation; review administrative accounts, MFA recovery procedures, base-image updates and external provider changes. Exercise the configured incident-alert pathway. Review dead links, unpublished legal/content pages and actual search/index status.

Quarterly: rotate secrets according to policy, test least-privilege access, perform an accessibility/content review, and repeat load tests when traffic or features change. Confirm data regions and retention policies meet current business requirements.

Before deleting client data, identify related projects, records, invoices, payments, documents, memberships, logs and object storage. Foreign keys deliberately prevent blind cascades. Implement a reviewed retention/deletion workflow instead of ad hoc destructive SQL.
