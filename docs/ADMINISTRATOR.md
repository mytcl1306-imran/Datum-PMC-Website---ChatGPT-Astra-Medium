# Administrator manual

## Accounts and roles

Create identities in Supabase, then grant access with `bin/console.php grant-user`. Admins have CMS, project, lead and user permissions. Editors have only CMS permission. Clients see projects belonging to client organisations linked through `client_users`. A Supabase user alone has no portal rights.

From **Users & access**, locate the local user ID and grant its client ID. To revoke, run `php bin/console.php revoke-client CLIENT_ID USER_ID`. To immediately block all application access, run `php bin/console.php disable-user USER_ID`. Both actions are audited. Keep access provisioning restricted to authorised staff. Recovery of an authenticator is an operator task in Supabase, following identity verification.

## Content

Use a resource tab and select **Create record** or **Edit**. Slugs use lowercase letters, numbers and hyphens. Pages, services, blog, testimonials and portfolio have draft/published states. Content is plain text, with blank lines becoming paragraphs; HTML is escaped. Save explicitly—there is no autosave.

For versioned records, the server checks the version before saving. On conflict, reload and reconcile your changes. **Version history** displays previous snapshots. To restore, copy approved values into the current editor and save as a new version. Deletion can be blocked by relational references. There is no soft-delete or recycle bin.

Home editorial text and brand/contact information are under **Settings**. Settings cannot contain executable markup. Contact links render only after real details are supplied. Home copy keys correspond to document order; verify the public result after editing. Layout and navigation remain code-defined.

Legal pages, awards, clients, certifications, careers, downloads, events and gallery start as drafts. Only publish factual, approved material. The generic page renderer does not create application, event registration or document-download workflows automatically.

## Projects and invoices

Create a client, then a project referring to its ID. Set `public` only when the narrative is approved for public disclosure. Public pages never show the project's private records or document paths. Grant client membership separately.

Set progress to an integer from 0 to 100 and maintain milestones. Use the **Client portal** link while signed in as administrator to access project records and document uploads. Clients may add RFIs and snags; only administrators may add the other record types or change statuses.

Create an invoice followed by its line items. Total is quantity × unit price × (1 + tax percentage/100), summed over lines. Draft invoices are hidden from clients. “Paid” is a manually managed status in this build; it is not linked to a payment processor. Do not treat it as reconciled accounting evidence.

## Leads

The lead inbox shows the latest 100 records. Update status and assign an active administrator's ID. Contact enquiries are stored before success is returned. The outbox stores an event reference, but there is no running delivery worker or autoresponse. Staff must monitor the inbox. Avoid treating the pending newsletter table as a confirmed mailing list.

## Documents

Upload PDF, JPEG, PNG or text files up to 10 MB from the project's portal page. Only administrators can upload. Use trusted files; a malware scanner is not included. Private downloads expire after 60 seconds. The current UI does not delete stored files. Coordinate retention and deletion with database/storage operators until lifecycle tooling is implemented.
