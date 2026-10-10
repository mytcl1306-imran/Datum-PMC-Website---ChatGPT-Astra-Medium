-- Execute in the Supabase SQL editor. MySQL remains the transactional source of truth.
-- The private Storage bucket stores project evidence; all object access goes through PHP.
insert into storage.buckets (id, name, public, file_size_limit, allowed_mime_types)
values ('project-documents','project-documents',false,10485760,array['application/pdf','image/jpeg','image/png','text/plain'])
on conflict (id) do update set public=false, file_size_limit=10485760, allowed_mime_types=excluded.allowed_mime_types;
-- Do not add anonymous or authenticated-user object policies for this bucket.
-- The PHP service role alone uploads and signs short-lived download URLs after MySQL access checks.
-- Supabase Postgres is used for Auth/Storage-managed metadata. There is no dual-write business-data mirror.
