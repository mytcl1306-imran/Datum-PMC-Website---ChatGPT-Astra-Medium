# Delivery and gap matrix

“Implemented” below means source is present, **not** that runtime acceptance has passed. Empty operational collections are intentional; they are not seeded with fictitious records. No integration is represented as live without credentials and testing.

| Requested area | Delivered implementation | Remaining work |
|---|---|---|
| Identity | Proposed DATUM identity, SVG mark, architectural illustration, tokens, local responsive CSS | Name/trademark clearance, owner approval, real photography |
| Home | Hero, service cards, approach, process, calculators CTA, newsletter-interest form, footer; home editorial strings in settings | Verified statistics, client logos, featured projects, testimonials, live location map, latest-post modules; CMS layout/reordering |
| Pages | Home, about, services, service details, projects, project details, industries, blog/article, contact, FAQ, resources, cookies, search, sitemap, 404 | Career/events/downloads/awards/certifications/clients/gallery begin as draft CMS pages; dedicated workflows/gallery not implemented |
| Portfolio/case studies | Both public URLs currently list public projects; separate editable portfolio table | Distinct case-study and portfolio detail templates, metrics and media presentation |
| Public projects | Title/summary search, category and location filter; public/private flag | Public budget/status/date filters; photo/video/360 viewers; public timelines and charts |
| Services | All 24 requested service records with editable content | Business review and tailoring of each service scope |
| Client access | Supabase password login, TOTP enrolment/verification, 30-minute idle/8-hour maximum session, server token verification | Self-service password reset, invitations, recovery and account management |
| Client portal | Assigned project list, progress, milestones, issued invoices/totals, private signed document downloads, records and RFI/snag submissions | Notifications UI, photo/video galleries, client approval workflow, invoice PDF and online payment |
| Admin | Content CRUD, projects, clients, milestones, invoice lines, metadata, lead status/assignment, settings, audit viewer, membership grant | Charts, email templates, forms builder, role/permission editor, backups UI, full media library |
| CMS | Draft/publish, field allowlists, optimistic version checks, previous snapshots, plain-text content safely escaped | Rich text, autosave, one-click restore, drag-and-drop section builder, media insertion |
| Leads | Validated AJAX form, consent, honeypot, CSRF and shared-database rate limiting, transactional persistence/outbox event | Email/SMS responses, phone/email OTP, reCAPTCHA, external CRM delivery |
| Newsletter | Consent-based interest capture; records remain pending; explicitly reports that no emails are sent | Double opt-in mail, confirmation/unsubscribe endpoints, campaigns and automation |
| Contact links | Email, telephone and WhatsApp click-to-chat appear when real settings are populated | WhatsApp Business API, Google Maps, booking availability, Google Calendar/Meet, Zoom, Teams, Calendly |
| Documents | Admin upload PDF/JPEG/PNG/text; private Supabase bucket; 60-second signed downloads after project authorization | Malware scanning, quarantine/release workflow, deletion/orphan cleanup, Google Drive, Cloudinary |
| Finance | Normalized invoices/items/payment tables and computed invoice total | Stripe, Razorpay, PayPal checkout, webhooks, reconciliation, tax documents and refunds |
| SEO | Server-rendered content, per-page metadata overrides, canonical, OG/Twitter tags, XML sitemap, robots, noindex for workspace/search | Schema.org, breadcrumbs, rich-result validation, OG images, pagination of large public collections, measured SEO audits |
| Marketing | Contact CTAs, interest capture, three transparent calculators | Lead magnet delivery, exit-intent/popups, campaign workflows, before/after imagery, verified social proof |
| AI | None; calculators are explicitly arithmetic | Chat assistant, model-backed estimator, recommendations, semantic document search, voice search |
| Search | MySQL LIKE search over published pages/services/blog | Index-backed ranking, private document text extraction, semantic search |
| Security | PDO parameters, table/field allowlists, HTML escaping, CSP, CSRF, protected sessions, RBAC, project isolation, audit events, MFA enforcement | Independent penetration test, full OWASP assessment, malware controls, hardened infrastructure and incident drills |
| Encryption | TLS-required production configuration; passwords managed by Supabase | Verify infrastructure encryption at rest; field encryption/secret manager integration |
| Performance | Small local assets, no third-party blocking scripts, PHP opcode cache config | Asset compression/hash pipeline, CDN cache policy, image processing, measured Lighthouse and load tests |
| Accessibility | Semantic landmarks, skip link, labelled controls, focus states, status regions, responsive layout, reduced-motion rule | Keyboard/screen-reader testing and full WCAG 2.2 AA audit |
| Analytics | No tracking scripts load | GA4, GTM, Meta, LinkedIn, heatmaps, consent controls, events and funnels |
| Database | 34 business tables plus migration tracking; relational keys/checks/indexes, content seed | Review evolving domain constraints; appointments/forms/media relationships have no full product workflow |
| Supabase Database | Auth/Storage system metadata; business records live in MySQL | No Supabase Postgres business-data replica or two-way sync |
| Deployment | Docker/Apache/MySQL, alternate Nginx config, interactive configuration, migration/provisioning CLI | Actual hosting, TLS/domain setup, CI release pipeline, cloud infrastructure |
| Stack libraries | Native HTML/CSS/JS and PHP; no frontend build dependency | Bootstrap, jQuery, AOS, GSAP, Swiper, Three.js, Particles, Lottie, Chart.js, icon/font libraries not integrated; Font Awesome Pro requires a license |
| Scale | Indexed access patterns, bounded responses, architecture guidance | Distributed sessions, pooling, queues, load tests, failover and capacity planning |

The requested “every feature fully functional, deployable without modification” state has not been reached. Source configuration, real business content, unfinished implementation and runtime verification are required. Do not treat this package as a completed production launch.
