INSERT INTO roles(name) VALUES ('admin'),('editor'),('client');
INSERT INTO permissions(name) VALUES ('cms.write'),('projects.write'),('leads.write'),('users.write');
INSERT INTO role_permissions SELECT 1,id FROM permissions;
INSERT INTO role_permissions SELECT 2,id FROM permissions WHERE name='cms.write';
INSERT INTO settings(`key`,value) VALUES ('brand_name','DATUM');
INSERT INTO settings(`key`,value) VALUES ('brand_descriptor','Project Management Consultancy');
INSERT INTO settings(`key`,value) VALUES ('hero_eyebrow','Clarity at every stage of construction');
INSERT INTO settings(`key`,value) VALUES ('hero_title','Built on precision.
Managed with purpose.');
INSERT INTO settings(`key`,value) VALUES ('hero_description','Independent project management and civil engineering consultancy. Connecting design intent, site execution and commercial control.');
INSERT INTO settings(`key`,value) VALUES ('contact_heading','Let’s build a clearer path forward.');
INSERT INTO settings(`key`,value) VALUES ('footer_line','Technical clarity. Accountable delivery.');
INSERT INTO settings(`key`,value) VALUES ('about_intro','A focused consultancy model for complex construction decisions.');
INSERT INTO settings(`key`,value) VALUES ('about_body','Bring project planning, commercial control and site coordination into one clear conversation. Engagements can be scoped around a specific challenge or a defined stage of delivery.

Start with the project objectives. Establish the evidence. Agree the decisions, owners and next steps.');
INSERT INTO settings(`key`,value) VALUES ('email','');
INSERT INTO settings(`key`,value) VALUES ('phone','');
INSERT INTO settings(`key`,value) VALUES ('whatsapp','');
INSERT INTO settings(`key`,value) VALUES ('address','');
INSERT INTO services(slug,title,summary,body,status) VALUES ('project-management','Project Management','Align scope, programme, cost and responsibilities through a single project control framework.','Align scope, programme, cost and responsibilities through a single project control framework.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('construction-management','Construction Management','Coordinate contractors, interfaces and daily execution against agreed construction priorities.','Coordinate contractors, interfaces and daily execution against agreed construction priorities.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('planning','Planning','Translate the project scope into work packages, dependencies and an actionable delivery plan.','Translate the project scope into work packages, dependencies and an actionable delivery plan.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('scheduling','Scheduling','Build and maintain logic-linked programmes with milestones and critical-path visibility.','Build and maintain logic-linked programmes with milestones and critical-path visibility.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('billing','Billing','Review measurement records and payment applications against agreed contract terms.','Review measurement records and payment applications against agreed contract terms.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('estimation','Estimation','Develop quantity-based estimates with explicit assumptions, exclusions and rate sources.','Develop quantity-based estimates with explicit assumptions, exclusions and rate sources.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('tendering','Tendering','Prepare tender packages, respond to clarifications and compare bids on a consistent basis.','Prepare tender packages, respond to clarifications and compare bids on a consistent basis.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('contracts-management','Contracts Management','Track contractual obligations, notices, changes and commercial correspondence.','Track contractual obligations, notices, changes and commercial correspondence.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('quantity-surveying','Quantity Surveying','Measure works and maintain a traceable quantity and valuation record.','Measure works and maintain a traceable quantity and valuation record.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('cost-control','Cost Control','Monitor commitments, actual costs and forecast exposure against the approved budget.','Monitor commitments, actual costs and forecast exposure against the approved budget.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('project-monitoring','Project Monitoring','Turn site observations and progress data into focused management reporting.','Turn site observations and progress data into focused management reporting.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('qa-qc','QA/QC','Plan inspections, record nonconformities and follow corrective actions through closure.','Plan inspections, record nonconformities and follow corrective actions through closure.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('safety-management','Safety Management','Coordinate safety observations and corrective actions with the appointed site safety team.','Coordinate safety observations and corrective actions with the appointed site safety team.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('vendor-management','Vendor Management','Track procurement submittals, approvals, manufacturing and delivery commitments.','Track procurement submittals, approvals, manufacturing and delivery commitments.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('site-supervision','Site Supervision','Review site execution against drawings, specifications and approved work methods.','Review site execution against drawings, specifications and approved work methods.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('progress-monitoring','Progress Monitoring','Track physical progress with consistent measurement rules and evidence.','Track physical progress with consistent measurement rules and evidence.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('delay-analysis','Delay Analysis','Review programme updates and contemporaneous records to understand delay events.','Review programme updates and contemporaneous records to understand delay events.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('risk-management','Risk Management','Maintain a live register of risks, owners, mitigation actions and residual exposure.','Maintain a live register of risks, owners, mitigation actions and residual exposure.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('value-engineering','Value Engineering','Compare practical alternatives against function, lifecycle cost and constructability.','Compare practical alternatives against function, lifecycle cost and constructability.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('claims-management','Claims Management','Organise entitlement records and quantum evidence for structured claim evaluation.','Organise entitlement records and quantum evidence for structured claim evaluation.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('document-control','Document Control','Maintain revisions, transmittals and review status so teams work from current information.','Maintain revisions, transmittals and review status so teams work from current information.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('project-audits','Project Audits','Review project controls and execution records against the agreed assessment scope.','Review project controls and execution records against the agreed assessment scope.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('feasibility-studies','Feasibility Studies','Evaluate project options, constraints and preliminary delivery assumptions.','Evaluate project options, constraints and preliminary delivery assumptions.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO services(slug,title,summary,body,status) VALUES ('construction-consultancy','Construction Consultancy','Provide focused technical and commercial input at key project decisions.','Provide focused technical and commercial input at key project decisions.

An engagement starts with a review of the available drawings, programme, contract information and project priorities. The agreed scope defines deliverables, reporting cadence and decision responsibilities.

Discuss your project stage and the support required to establish a tailored scope of services.','published');
INSERT INTO pages(slug,title,summary,body,status) VALUES ('about','A considered approach to construction.','A focused consultancy model for complex construction decisions.','Bring project planning, commercial control and site coordination into one clear conversation. Engagements can be scoped around a specific challenge or a defined stage of delivery.

Start with the project objectives. Establish the evidence. Agree the decisions, owners and next steps.','published');
INSERT INTO pages(slug,title,summary,body,status) VALUES ('industries','Different sectors. Shared fundamentals.','Project controls shaped around the realities of each development.','Residential developments
Commercial buildings
Industrial facilities
Infrastructure projects

The scope of each engagement is agreed around project scale, location, design maturity and delivery requirements.','published');
INSERT INTO pages(slug,title,summary,body,status) VALUES ('faq','A little clarity, before we begin.','Answers to common questions about an engagement.','When should a consultant be engaged?
Early involvement helps define the project brief and controls. A focused review can also support projects already under construction.

Can support be limited to one service?
Yes. A scope can cover a specific technical or commercial requirement.

What information is needed for an initial discussion?
Share the project type, location, stage, programme and principal challenge. Avoid sending confidential documents until a secure sharing arrangement is agreed.

Are estimates fixed quotations?
No. Planning calculations depend on the inputs and exclusions. A formal scope and fee proposal follow a project review.','published');
INSERT INTO pages(slug,title,summary,body,status) VALUES ('privacy-policy','Privacy information','Publication requires the operator’s approved privacy notice.','','draft');
INSERT INTO pages(slug,title,summary,body,status) VALUES ('terms','Terms of engagement','Publication requires the operator’s approved website terms.','','draft');
INSERT INTO pages(slug,title,summary,body,status) VALUES ('cookies','Cookies and local storage','How this application uses browser storage.','An essential session cookie supports secure sign-in and form protection. A local browser preference remembers your selected colour theme. This build does not load advertising pixels, analytics trackers or third-party marketing cookies. These disclosures must be reviewed if integrations are added.','published');
INSERT INTO settings(`key`,value) VALUES ('home_copy_1','Discuss your project');
INSERT INTO settings(`key`,value) VALUES ('home_copy_2','↗');
INSERT INTO settings(`key`,value) VALUES ('home_copy_3','Explore our expertise');
INSERT INTO settings(`key`,value) VALUES ('home_copy_4','↓');
INSERT INTO settings(`key`,value) VALUES ('home_copy_5','＋');
INSERT INTO settings(`key`,value) VALUES ('home_copy_6','From first principles');
INSERT INTO settings(`key`,value) VALUES ('home_copy_7','to final delivery.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_8','01 / THE BIGGER PICTURE');
INSERT INTO settings(`key`,value) VALUES ('home_copy_9','Every detail.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_10','One clear direction.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_11','PROJECT STRATEGY');
INSERT INTO settings(`key`,value) VALUES ('home_copy_12','＋');
INSERT INTO settings(`key`,value) VALUES ('home_copy_13','TECHNICAL ASSURANCE');
INSERT INTO settings(`key`,value) VALUES ('home_copy_14','＋');
INSERT INTO settings(`key`,value) VALUES ('home_copy_15','COMMERCIAL CLARITY');
INSERT INTO settings(`key`,value) VALUES ('home_copy_16','＋');
INSERT INTO settings(`key`,value) VALUES ('home_copy_17','SITE COORDINATION');
INSERT INTO settings(`key`,value) VALUES ('home_copy_18','01 — THE APPROACH');
INSERT INTO settings(`key`,value) VALUES ('home_copy_19','A clear line from');
INSERT INTO settings(`key`,value) VALUES ('home_copy_20','vision to execution.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_21','Construction brings many moving parts. A considered project management approach connects them—giving every decision a clear context, owner and next step.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_22','01 / DEFINE');
INSERT INTO settings(`key`,value) VALUES ('home_copy_23','Start with clarity.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_24','Establish the brief, constraints and success criteria before committing resources.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_25','02 / CONNECT');
INSERT INTO settings(`key`,value) VALUES ('home_copy_26','Bring the pieces together.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_27','Align programme, cost and technical information around shared project priorities.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_28','03 / CONTROL');
INSERT INTO settings(`key`,value) VALUES ('home_copy_29','Keep decisions moving.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_30','Use timely records and focused reporting to make progress visible and accountable.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_31','02 — EXPERTISE');
INSERT INTO settings(`key`,value) VALUES ('home_copy_32','Specialist thinking.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_33','Connected delivery.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_34','View all services ↗');
INSERT INTO settings(`key`,value) VALUES ('home_copy_35','↗');
INSERT INTO settings(`key`,value) VALUES ('home_copy_36','03 — THE PROCESS');
INSERT INTO settings(`key`,value) VALUES ('home_copy_37','Structure for');
INSERT INTO settings(`key`,value) VALUES ('home_copy_38','the journey ahead.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_39','A deliberate sequence. A shared understanding. Support shaped around your project.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_40','Our approach ↗');
INSERT INTO settings(`key`,value) VALUES ('home_copy_41','01');
INSERT INTO settings(`key`,value) VALUES ('home_copy_42','Understand the brief');
INSERT INTO settings(`key`,value) VALUES ('home_copy_43','Define objectives, review available information and identify immediate priorities.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_44','02');
INSERT INTO settings(`key`,value) VALUES ('home_copy_45','Establish the baseline');
INSERT INTO settings(`key`,value) VALUES ('home_copy_46','Agree scope, programme, cost assumptions and the reporting framework.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_47','03');
INSERT INTO settings(`key`,value) VALUES ('home_copy_48','Coordinate delivery');
INSERT INTO settings(`key`,value) VALUES ('home_copy_49','Track actions, monitor progress and bring decisions to the right people.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_50','04');
INSERT INTO settings(`key`,value) VALUES ('home_copy_51','Close with confidence');
INSERT INTO settings(`key`,value) VALUES ('home_copy_52','Reconcile records, resolve outstanding items and support an orderly handover.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_53','MAKE INFORMED DECISIONS');
INSERT INTO settings(`key`,value) VALUES ('home_copy_54','Turn assumptions');
INSERT INTO settings(`key`,value) VALUES ('home_copy_55','into a starting point.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_56','Explore transparent planning calculators with inputs you control.');
INSERT INTO settings(`key`,value) VALUES ('home_copy_57','Explore the tools ↗');
INSERT INTO settings(`key`,value) VALUES ('home_copy_58','YOUR NEXT PROJECT');
INSERT INTO settings(`key`,value) VALUES ('home_copy_59','Start a conversation ↗');
INSERT INTO pages(slug,title,summary,body,status) VALUES ('clients','Clients','','','draft');
INSERT INTO pages(slug,title,summary,body,status) VALUES ('certifications','Certifications','','','draft');
INSERT INTO pages(slug,title,summary,body,status) VALUES ('awards','Awards','','','draft');
INSERT INTO pages(slug,title,summary,body,status) VALUES ('career','Careers','','','draft');
INSERT INTO pages(slug,title,summary,body,status) VALUES ('downloads','Downloads','','','draft');
INSERT INTO pages(slug,title,summary,body,status) VALUES ('events','Events','','','draft');
INSERT INTO pages(slug,title,summary,body,status) VALUES ('project-gallery','Project gallery','','','draft');
