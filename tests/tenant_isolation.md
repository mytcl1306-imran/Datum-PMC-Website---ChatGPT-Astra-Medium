# Required authenticated integration test

Use a fresh test database and a separate Supabase project. This test is not executed in the supplied environment.

1. Create two Supabase users A and B, complete TOTP setup, and provision each as `client`.
2. Create clients A and B and one private project per client. Associate only matching users.
3. Add one document, invoice, milestone and RFI to each project as administrator.
4. Sign in as A. `/api/projects` must contain only A's project.
5. Request B's project and B's document download ID as A: both must return 404. Attempt RFI creation on B: 404, no insert.
6. Before TOTP completion, repeat project/list/document operations: 403.
7. As A, try every `/api/admin/*` endpoint: 403. Try a status update and upload: 403.
8. As A, create an RFI on A: 201; try a report or approval: 403.
9. Disable A with the CLI; repeat a request with the existing session: 403.
10. Revoke A's client membership, re-enable the account and verify no project access remains.
11. Change a signed-in user's Supabase password and revoke all sessions. Confirm the local application rejects its token once Supabase considers it invalid; document provider token revocation timing.
12. Ensure no raw storage paths or service-role keys appear in public page/API responses.
13. Repeat access cases with sequentially guessed IDs and concurrent requests. Record actual results and retain evidence before launch.
