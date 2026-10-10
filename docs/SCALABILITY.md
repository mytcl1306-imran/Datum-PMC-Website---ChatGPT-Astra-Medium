# Future scalability plan

The delivered topology is single-instance. Serving thousands of concurrent users has not been demonstrated.

1. Measure the initial workload: anonymous page requests, concurrent portal sessions, peak uploads, dataset size and p95 API latency. Verify Supabase rate/connection limits for the actual plan.
2. Add public page caching that never includes session-dependent or private content. Fingerprint static assets and introduce CDN compression/image variants. Remove unnecessary session creation on cacheable anonymous pages after separating CSRF delivery.
3. Move sessions to a secure shared Redis-compatible store before multiple PHP instances; otherwise login continuity depends on sticky sessions. Run stateless PHP workers behind health-checked load balancing.
4. Use a managed MySQL service with backups, monitoring and appropriate pooling. Profile query plans and add indexes based on measured selectivity. Replace LIKE search with a dedicated full-text index/search service as needed.
5. Implement a durable worker queue for email, exports, virus scanning and provider synchronization, with bounded retries, dead-letter handling and idempotency. The current outbox is only a persistence primitive.
6. Cache validated identity appropriately only after defining revocation and risk requirements; the current server verification on every protected request can become a latency bottleneck. Preserve server MFA and local membership checks.
7. Add resumable uploads, object lifecycle/retention policies and evidence reconciliation. Keep private authorization in the application even when distributing signed downloads.
8. Split services only when operational measurements justify it. Avoid dual-writing business data to MySQL and Supabase Postgres without a durable event/outbox replication design and explicit ownership.

Release each stage with repeatable load tests, an availability objective, capacity margin and tested rollback. A technology list alone does not establish enterprise scale.
