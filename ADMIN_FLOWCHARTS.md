# OSDW Scholarship Management System — Admin-Side Flowcharts & Process Specifications
**Institution:** Cagayan State University (CSU) — Lal-lo Campus  
**Office:** Office of Student Development and Welfare (OSDW)  
**System Module:** Administrator Operations & Grantee Management Portal

---

## 1. High-Level Admin System Architecture & Master Flowchart

The following master flowchart depicts the end-to-end operational lifecycle conducted by the OSDW Administrator, spanning foundation configuration, program launching, application screening, scholar monitoring, periodic compliance, student welfare interventions, and regulatory reporting.

```mermaid
flowchart TD
    %% Styling
    classDef startEnd fill:#1e293b,stroke:#0f172a,stroke-width:2px,color:#fff;
    classDef process fill:#0284c7,stroke:#0369a1,stroke-width:2px,color:#fff;
    classDef decision fill:#d97706,stroke:#b45309,stroke-width:2px,color:#fff;
    classDef storage fill:#059669,stroke:#047857,stroke-width:2px,color:#fff;
    classDef notify fill:#7c3aed,stroke:#6d28d9,stroke-width:2px,color:#fff;

    Start([Admin Logs In via CSU Portal]):::startEnd --> AuthCheck{Credentials Valid & Role == 'admin'?}:::decision

    AuthCheck -- No --> AuthFail[Display Error / Redirect to Login]:::process
    AuthFail --> Start

    AuthCheck -- Yes --> Dashboard[Admin Dashboard: Statistics, Metrics & Alerts]:::process

    %% Module Paths
    Dashboard --> NavAcad[1. Academic Year & Semester Setup]:::process
    Dashboard --> NavProg[2. Scholarship Program & Requirements Config]:::process
    Dashboard --> NavApp[3. Application Processing & Document Verification]:::process
    Dashboard --> NavSch[4. Scholar Management & Renewal Tracking]:::process
    Dashboard --> NavComp[5. Periodic Compliance Monitoring]:::process
    Dashboard --> NavWelf[6. Student Welfare Cases & Referrals]:::process
    Dashboard --> NavRep[7. Centralized Reporting, CSV Exports & Audit Logs]:::process

    %% Phase 1
    NavAcad --> AY_Check{Active Academic Year Configured?}:::decision
    AY_Check -- No --> CreateAY[Create / Activate Academic Year & Semesters]:::process
    AY_Check -- Yes --> AY_Ready[Ready for Program Association]:::process

    %% Phase 2
    NavProg --> ProgCreate[Create Scholarship Program]:::process
    ProgCreate --> SetReqs[Define Required Document Checklist & Deadlines]:::process
    SetReqs --> PubProg[Publish Scholarship Status: Open / Active]:::process

    %% Phase 3
    PubProg -.-> StudentAppSubmit[(Students Submit Online Applications)]:::storage
    StudentAppSubmit --> NavApp
    NavApp --> AppQueue[Review Application Intake Queue]:::process
    AppQueue --> VerifyDocs{Review Uploaded Documents}:::decision
    
    VerifyDocs -- "Deficient / Blurred" --> DocReject[Mark: Needs Resubmission & Enter Remarks]:::process
    DocReject --> AppIncomplete[Set Application: Incomplete]:::process
    AppIncomplete --> NotifyStudentDef[Notify Student to Resolve Deficiency]:::notify
    NotifyStudentDef -.-> StudentResubmit[(Student Resubmits Document)]:::storage
    StudentResubmit -.-> VerifyDocs

    VerifyDocs -- "All Documents Valid" --> AppDecision{Admin Decision on Application}:::decision

    AppDecision -- "Reject" --> RejectApp[Set Application: Rejected & Enter Remarks]:::process
    RejectApp --> NotifyAppReject[Notify Student of Rejection]:::notify

    AppDecision -- "Approve" --> ApproveApp[Set Application: Approved]:::process
    ApproveApp --> AutoEnroll[System Auto-Enrolls Student into Active Scholars Table]:::process
    AutoEnroll --> LogAuditApp[Write to SystemLog & Notify Student]:::notify

    %% Phase 4
    AutoEnroll --> NavSch
    NavSch --> MonitorScholars[Monitor Scholar Standings & Roster]:::process
    RenewalEvent{Renewal Evaluation / End of Sem}:::decision
    MonitorScholars --> RenewalEvent
    RenewalEvent -- "Compliant" --> KeepActive[Maintain Active Scholar Standing]:::process
    RenewalEvent -- "Graduated" --> MarkCompleted[Mark Status: Completed]:::process
    RenewalEvent -- "Disqualified / Inactive" --> MarkTerminated[Mark Status: Terminated]:::process

    %% Phase 5
    KeepActive --> NavComp
    NavComp --> CreateCompReq[Broadcast Compliance Request: Midterm / Endterm Grades]:::process
    CreateCompReq --> AutoAssignScholars[System Auto-Assigns Task to Active Scholars]:::process
    AutoAssignScholars --> ReviewCompDocs{Verify Compliance Document}:::decision
    ReviewCompDocs -- "Approved" --> MarkCompDone[Mark Compliance: Completed]:::process
    ReviewCompDocs -- "Needs Revision" --> RequestCompFix[Flag Resubmission / Extend Deadline]:::process

    %% Phase 6
    NavWelf --> IntakeWelfare[Receive Student Welfare Ticket]:::process
    IntakeWelfare --> TriageCase[Triage & Set: Under Assessment]:::process
    TriageCase --> ReferralDecision{Action Required?}:::decision
    ReferralDecision -- "Financial Aid" --> ReferScholarship[Link / Refer to Scholarship Program]:::process
    ReferralDecision -- "Counseling / Medical" --> ReferOffice[Refer to Guidance / Campus Clinic]:::process
    ReferScholarship --> CloseCase[Resolve / Close Case & Print Dossier]:::process
    ReferOffice --> CloseCase

    %% Phase 7
    NavRep --> GenReport[Select Filter: AY, Semester, Scholarship, Demographic]:::process
    GenReport --> PrintOfficial[Print CSU Letterhead Official Report]:::process
    GenReport --> ExportCSV[Download Raw Datasets CSV]:::process
    GenReport --> AuditTrail[Review Audit Logs for Compliance Integrity]:::process

    PrintOfficial --> Finish([End of Operational Cycle]):::startEnd
    ExportCSV --> Finish
```

---

## 2. Detailed Process Flowcharts by Administrative Module

### Flowchart 2.1: Academic Year & Semester Configuration
Controls the system-wide active institutional periods that govern scholarship availability and filtering.

```mermaid
flowchart TD
    classDef startEnd fill:#1e293b,stroke:#0f172a,stroke-width:2px,color:#fff;
    classDef process fill:#0284c7,stroke:#0369a1,stroke-width:2px,color:#fff;
    classDef decision fill:#d97706,stroke:#b45309,stroke-width:2px,color:#fff;

    Start([Start Academic Year Management]):::startEnd --> ViewList[View Existing Academic Years & Semesters]:::process
    ViewList --> ActionChoice{Admin Action}:::decision

    ActionChoice -- "Create New AY" --> InputAY[Input Name e.g. '2026-2027', Start Date, End Date]:::process
    InputAY --> SaveAY[Save Academic Year to Database]:::process
    SaveAY --> ActivatePrompt{Set as Active AY?}:::decision
    ActivatePrompt -- Yes --> DeactivateOld[Deactivate Previous Academic Years]:::process
    DeactivateOld --> SetCurrent[Set is_active = 1]:::process
    ActivatePrompt -- No --> ViewList

    ActionChoice -- "Add Semester" --> SelectAY[Select Associated Academic Year]:::process
    SelectAY --> InputSem[Input Semester: '1st Semester', '2nd Semester', or 'Summer']:::process
    InputSem --> SaveSem[Save Semester Record]:::process
    SaveSem --> SemActive{Set as Active Semester?}:::decision
    SemActive -- Yes --> SetSemActive[Update is_active flag for Semester]:::process
    SemActive -- No --> ViewList

    SetCurrent --> EndAY([Academic Calendar Synchronized]):::startEnd
    SetSemActive --> EndAY
```

---

### Flowchart 2.2: Scholarship Program Creation & Requirement Definition
Defines scholarship programs, funding providers, available slots, coverage types, and document submission checklists.

```mermaid
flowchart TD
    classDef startEnd fill:#1e293b,stroke:#0f172a,stroke-width:2px,color:#fff;
    classDef process fill:#0284c7,stroke:#0369a1,stroke-width:2px,color:#fff;
    classDef decision fill:#d97706,stroke:#b45309,stroke-width:2px,color:#fff;

    Start([Start Program Creation]):::startEnd --> OpenForm[Open Scholarship Creation Form]:::process
    OpenForm --> FillDetails["Input Program Metadata:
    • Name & Provider (e.g. CHED, LGU, Private)
    • School Year & Academic Year ID
    • Semester
    • Slot Capacity & Coverage Type (Full, Continuing)
    • Application Deadline & Eligibility Criteria"]:::process
    
    FillDetails --> ValidateInput{Valid Form Input?}:::decision
    ValidateInput -- No --> ShowErrors[Show Form Validation Errors]:::process
    ShowErrors --> OpenForm

    ValidateInput -- Yes --> SaveProgram[Store Scholarship in Database]:::process
    SaveProgram --> SetupRequirements[Open Requirement Configuration Tab]:::process
    
    SetupRequirements --> AddReqLoop["Add Document Requirement:
    • Name (e.g., Certificate of Indigency, Transcript of Records)
    • File Type Requirements (PDF, JPG, PNG)
    • Mandatory vs. Optional Flag
    • Specific Guidelines / Submission Deadline"]:::process

    AddReqLoop --> MoreReqs{Add Another Requirement?}:::decision
    MoreReqs -- Yes --> AddReqLoop
    MoreReqs -- No --> SetProgStatus{Publish Program?}:::decision

    SetProgStatus -- "Draft" --> StatusDraft[Status = 'draft' — Invisible to Students]:::process
    SetProgStatus -- "Open" --> StatusOpen[Status = 'open' — Available in Student Portal]:::process

    StatusDraft --> EndProg([Scholarship Program Ready]):::startEnd
    StatusOpen --> EndProg
```

---

### Flowchart 2.3: Application Review, Document Verification & Enrollment (Core Engine)
Illustrates how the admin processes student submissions, verifies attached proofs, handles deficient documents, and automatically creates scholar records upon approval.

```mermaid
flowchart TD
    classDef startEnd fill:#1e293b,stroke:#0f172a,stroke-width:2px,color:#fff;
    classDef process fill:#0284c7,stroke:#0369a1,stroke-width:2px,color:#fff;
    classDef decision fill:#d97706,stroke:#b45309,stroke-width:2px,color:#fff;
    classDef notify fill:#7c3aed,stroke:#6d28d9,stroke-width:2px,color:#fff;

    Start([Student Application Intake]):::startEnd --> FilterQueue["Admin Filters Application Queue:
    • By Scholarship Program
    • By Academic Year
    • By Status: 'submitted', 'under_review', 'incomplete'"]:::process

    FilterQueue --> SelectApp[Open Application Review Dossier]:::process
    SelectApp --> InspectAcademic["Review Student Background:
    • Student ID, Course, Year Level
    • General Weighted Average (GPA)
    • Prior Scholar Records / Dual-Scholarship Check"]:::process

    InspectAcademic --> InspectDocs[Inspect Uploaded Verification Documents]:::process

    InspectDocs --> DocLoop{Examine Each Document}:::decision

    DocLoop -- "Illegible / Mismatched / Expired" --> FlagDeficient["Update Document Status:
    'needs_resubmission'
    + Add Specific Defect Remarks"]:::process
    
    FlagDeficient --> TriggerIncomplete["System Automatically Sets Application Status:
    'incomplete'"]:::process
    TriggerIncomplete --> NotifyStudentFast["Dispatch In-App & Email Notification:
    DocumentStatusUpdated"]:::notify
    NotifyStudentFast --> WaitStudentFix[Student Accesses Fast-Resolution Interface to Re-upload]:::process
    WaitStudentFix -.-> InspectDocs

    DocLoop -- "Valid & Compliant" --> FlagVerified["Update Document Status:
    'verified' + Set verified_at Timestamp"]:::process

    FlagVerified --> CheckAllDocs{All Mandatory Documents Verified?}:::decision
    CheckAllDocs -- No --> DocLoop
    CheckAllDocs -- Yes --> FinalEvaluation{Admin Application Decision}:::decision

    FinalEvaluation -- "Under Review" --> SetReviewing[Status = 'under_review']:::process
    
    FinalEvaluation -- "Reject" --> EnterRejection["Input Rejection Reason:
    Status = 'rejected'"]:::process
    EnterRejection --> NotifyReject["Dispatch Notification:
    ApplicationStatusUpdated ('rejected')"]:::notify
    NotifyReject --> AuditReject[Write System Log: Action = 'status_change']:::process

    FinalEvaluation -- "Approve" --> EnterApproval["Set Status = 'approved'"]:::process
    EnterApproval --> SystemTransaction[Execute DB Transaction]:::process
    SystemTransaction --> CreateScholar["Auto-Create Record in 'scholars' Table:
    • student_id, scholarship_id, application_id
    • status = 'active'
    • approved_at = now()"]:::process
    CreateScholar --> NotifyApproval["Dispatch Notification:
    ApplicationStatusUpdated ('approved')"]:::notify
    NotifyApproval --> AuditApprove[Write System Log: Action = 'status_change' + Scholar Created]:::process

    AuditReject --> EndReview([Application Evaluation Completed]):::startEnd
    AuditApprove --> EndReview
```

---

### Flowchart 2.4: Active Scholar Management & Renewal Verification
Tracks students after being granted scholarship privileges, managing renewals, semester qualifications, and status transitions.

```mermaid
flowchart TD
    classDef startEnd fill:#1e293b,stroke:#0f172a,stroke-width:2px,color:#fff;
    classDef process fill:#0284c7,stroke:#0369a1,stroke-width:2px,color:#fff;
    classDef decision fill:#d97706,stroke:#b45309,stroke-width:2px,color:#fff;
    classDef notify fill:#7c3aed,stroke:#6d28d9,stroke-width:2px,color:#fff;

    Start([Scholar Management Roster]):::startEnd --> EntryMethod{Grantee Source}:::decision

    EntryMethod -- "Regular Route" --> AutoFromApp[Enrolled via Approved Application]:::process
    EntryMethod -- "Direct Admin Enrollment" --> ManualAdd["Direct Grantee Store Form:
    • Select Student
    • Select Scholarship
    • Set Status (Active)
    • Enter Justification Remarks"]:::process
    ManualAdd --> CheckDuplicate{Already an Active Grantee?}:::decision
    CheckDuplicate -- Yes --> RejectDuplicate[Abort: Duplicate Grantee Error]:::process
    CheckDuplicate -- No --> DirectCreate[Store Scholar + Audit Log + Notify Student]:::process

    AutoFromApp --> RosterView[Active Scholars Dashboard & Masterlist]:::process
    DirectCreate --> RosterView

    RosterView --> RenewalCycle{Renewal / Semester Evaluation}:::decision

    RenewalCycle -- "Review Renewal Documents" --> ReviewRenewalDoc["Inspect Submitted Renewal:
    • Registration Form / Certificate of Registration
    • Official Grade Slip / GPA Verification"]:::process

    ReviewRenewalDoc --> VerifyRenewalDecision{Admin Evaluation}:::decision
    VerifyRenewalDecision -- "Deficient" --> RejectRenewal[Mark Renewal Rejected + Feedback]:::process
    RejectRenewal --> NotifyRenewalFix[Notify Scholar to Re-submit]:::notify
    
    VerifyRenewalDecision -- "Approved" --> ConfirmRenewal["Mark Renewal 'verified'
    • Maintain Scholar Status = 'active'
    • Record Term Renewal History"]:::process

    RenewalCycle -- "Manual Standing Update" --> ChangeStatusChoice{Select New Status}:::decision
    ChangeStatusChoice -- "Graduated" --> SetCompleted[Status = 'completed']:::process
    ChangeStatusChoice -- "Academic Deficit / Violation" --> SetTerminated[Status = 'terminated']:::process
    ChangeStatusChoice -- "Pending Requirements" --> SetForRenewal[Status = 'for_renewal']:::process

    SetCompleted --> NotifyScholarStanding[Send ScholarStatusUpdated Notification]:::notify
    SetTerminated --> NotifyScholarStanding
    ConfirmRenewal --> NotifyScholarStanding
    SetForRenewal --> NotifyScholarStanding

    NotifyScholarStanding --> EndScholarFlow([Scholar Standing Synchronized]):::startEnd
```

---

### Flowchart 2.5: Periodic Compliance Monitoring (Midterm / Periodic Checkpoints)
Handles administrative requests for ongoing scholars to submit periodic requirements.

```mermaid
flowchart TD
    classDef startEnd fill:#1e293b,stroke:#0f172a,stroke-width:2px,color:#fff;
    classDef process fill:#0284c7,stroke:#0369a1,stroke-width:2px,color:#fff;
    classDef decision fill:#d97706,stroke:#b45309,stroke-width:2px,color:#fff;
    classDef notify fill:#7c3aed,stroke:#6d28d9,stroke-width:2px,color:#fff;

    Start([Initiate Compliance Request]):::startEnd --> FillCompReq["Admin Inputs Compliance Metadata:
    • Target Scholarship Program
    • School Year & Semester
    • Submission Deadline
    • Title & Instructions
    • Required Checkpoints (e.g. Grade Slip, Registration Form)"]:::process

    FillCompReq --> SaveBroadcast[Save Compliance Request]:::process
    SaveBroadcast --> AutoAssignScholars["System Automation:
    • Finds all 'active' Scholars of that program
    • Creates ScholarCompliance record for each
    • Sets status = 'pending'"]:::process

    AutoAssignScholars --> NotifyAllScholars[Dispatch ComplianceRequestedNotification to Scholars]:::notify

    NotifyAllScholars --> MonitorSubmissions[Admin Monitors Compliance Dashboard]:::process
    MonitorSubmissions --> TrackStats["Track Metrics:
    • Assigned Scholars
    • Submitted Documents
    • Pending Reviews
    • Overdue Scholars"]:::process

    TrackStats --> DeadlineCheck{Scholars Need More Time?}:::decision
    DeadlineCheck -- Yes --> ExtendDeadlineAction[Admin Extends Deadline Date]:::process
    ExtendDeadlineAction --> MonitorSubmissions

    DeadlineCheck -- No --> ReviewCompQueue[Review Submitted Compliance Documents]:::process
    ReviewCompQueue --> CompDocEval{Verify Document}:::decision

    CompDocEval -- "Deficient" --> CompNeedsResubmit["Mark: 'needs_resubmission'
    • Enter Specific Feedback
    • ScholarCompliance Status = 'in_review'"]:::process
    CompNeedsResubmit --> NotifyCompDoc[Dispatch ComplianceDocumentVerifiedNotification]:::notify

    CompDocEval -- "Compliant" --> CompDocVerified["Mark: 'verified'"]:::process
    CompDocVerified --> CheckAllCompDocs{All Assigned Requirements Verified?}:::decision
    CheckAllCompDocs -- Yes --> MarkCompComplete["Set ScholarCompliance Status: 'completed'"]:::process
    CheckAllCompDocs -- No --> MonitorSubmissions

    MarkCompComplete --> NotifyCompDoc
    NotifyCompDoc --> EndCompFlow([Compliance Process Concluded]):::startEnd
```

---

### Flowchart 2.6: Student Welfare Case Triage, Referral & Dossier Generation
Manages student development and welfare interventions, emergency student financial assistance, mental health or academic distress referrals, and official dossiers.

```mermaid
flowchart TD
    classDef startEnd fill:#1e293b,stroke:#0f172a,stroke-width:2px,color:#fff;
    classDef process fill:#0284c7,stroke:#0369a1,stroke-width:2px,color:#fff;
    classDef decision fill:#d97706,stroke:#b45309,stroke-width:2px,color:#fff;
    classDef notify fill:#7c3aed,stroke:#6d28d9,stroke-width:2px,color:#fff;

    Start([Student Welfare Case Logged]):::startEnd --> IntakeReview["Admin Views Welfare Desk:
    • Case ID (e.g. WLF-2026-001)
    • Category: Financial, Academic, Health, Counseling
    • Urgency: Low, Medium, High, Critical
    • Attached Evidence Documents"]:::process

    IntakeReview --> SetAssessment[Update Case Status to 'Under Assessment']:::process
    SetAssessment --> AssessCase{Determine Support Action}:::decision

    AssessCase -- "Direct OSDW Counseling / Guidance" --> InternalGuidance[Conduct Case Interview & Document Notes]:::process
    InternalGuidance --> UpdateFollowup[Status = 'For Follow-up']:::process

    AssessCase -- "Scholarship Endorsement" --> ReferScholarshipForm["Create Welfare Referral:
    • Target: Available Scholarship Program
    • Notes: Justification of urgent financial need
    • System links referral ID to Application"]:::process
    ReferScholarshipForm --> NotifyStudentRef[Notify Student: Case Referred to Scholarship]:::notify

    AssessCase -- "External / Campus Office Referral" --> ReferOfficeForm["Create Office Referral:
    • Guidance and Counseling Office
    • Campus Medical Clinic
    • Dean of Student Affairs / External LGU"]:::process
    ReferOfficeForm --> NotifyStudentRef

    UpdateFollowup --> ResolveEvaluation{Issue Addressed?}:::decision
    NotifyStudentRef --> ResolveEvaluation

    ResolveEvaluation -- Yes --> MarkResolved[Status = 'Resolved']:::process
    MarkResolved --> FinalClose[Status = 'Closed']:::process

    FinalClose --> PrintDossierAction[Generate 1-Page Official Student Welfare Case Dossier]:::process
    PrintDossierAction --> ExportOfficialDossier["Export / Print Case Dossier:
    • CSU Letterhead
    • Student Background & Case Narrative
    • Document List & Referral History
    • Official OSDW Officer Signatures"]:::process

    ExportOfficialDossier --> EndWelfareFlow([Welfare Case Successfully Closed]):::startEnd
```

---

### Flowchart 2.7: Reporting, Data Export & Audit Trail Architecture
Outlines how aggregated system data is transformed into compliance reports, printable official documents, and immutable security audit logs.

```mermaid
flowchart TD
    classDef startEnd fill:#1e293b,stroke:#0f172a,stroke-width:2px,color:#fff;
    classDef process fill:#0284c7,stroke:#0369a1,stroke-width:2px,color:#fff;
    classDef decision fill:#d97706,stroke:#b45309,stroke-width:2px,color:#fff;

    Start([Centralized Reports & Analytics]):::startEnd --> SelectTab{Choose Reporting Scope}:::decision

    SelectTab -- "Scholarships & Grantees" --> FilterScholars["Filter Dimensions:
    • Academic Year & Semester
    • Specific Scholarship Program
    • Grantee Standing (Active, Graduated, Terminated)"]:::process

    SelectTab -- "Application Demographics" --> FilterApps["Filter Dimensions:
    • Status (Approved, Rejected, Under Review)
    • College / Degree Program
    • Slot Utilization Rate"]:::process

    SelectTab -- "Welfare Analytics" --> FilterWelfare["Filter Dimensions:
    • Distress Category
    • Resolution Velocity
    • Referral Outcomes"]:::process

    FilterScholars --> OutputChoice{Choose Output Format}:::decision
    FilterApps --> OutputChoice
    FilterWelfare --> OutputChoice

    OutputChoice -- "Web Analytics View" --> RenderHTML[Interactive Summary Cards, Data Tables & Badges]:::process
    
    OutputChoice -- "Printable Official Document" --> RenderPrintView["Render Printable View:
    • CSU Lal-lo Institutional Letterhead & Logo
    • Tabulated Masterlist with Official Details
    • Signatory Block: Prepared by, Reviewed by, Approved by"]:::process
    RenderPrintView --> PrintBrowser[Trigger Print to PDF / Physical Printer]:::process

    OutputChoice -- "Raw Data Export" --> StreamCSV["Stream CSV Download:
    • export/scholars
    • export/applications
    • export/slot-utilization
    • export/compliance
    • export/welfare-cases"]:::process

    OutputChoice -- "Security Audit" --> ViewAuditLog["Access System Logs:
    • User ID, IP Address, Timestamp
    • Module, Action, Record ID
    • Before / After Value Changes"]:::process

    ViewAuditLog --> ExportAudit[Export Audit Trail / Clear Archived Logs]:::process

    PrintBrowser --> EndReportFlow([Report Dispatched]):::startEnd
    StreamCSV --> EndReportFlow
    ExportAudit --> EndReportFlow
```

---

## 3. System State Transition Matrix

| Module | Entity | Current State | Triggering Admin Action | Next State | Automated System Side Effects |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Application** | `Application` | `submitted` | Admin starts reviewing | `under_review` | Updated in queue counters. |
| **Application** | `ApplicationDocument` | `pending` | Admin identifies missing/blurry page | `needs_resubmission` | Application status set to `incomplete`; `DocumentStatusUpdated` notification sent to student. |
| **Application** | `Application` | `incomplete` | Student uploads revised document | `submitted` | Re-enters review queue for admin re-inspection. |
| **Application** | `ApplicationDocument` | `pending` / `needs_resubmission` | Admin validates document authenticity | `verified` | Records `verified_at` timestamp. |
| **Application** | `Application` | `under_review` / `submitted` | Admin approves application | `approved` | **Auto-creates record in `scholars` table** (`status = active`); decrements remaining program slots; sends `ApplicationStatusUpdated` notification; logs in `SystemLog`. |
| **Application** | `Application` | `under_review` / `submitted` | Admin rejects application | `rejected` | If previously enrolled scholar existed, deletes scholar record; sends `ApplicationStatusUpdated` notification with remarks. |
| **Scholar** | `Scholar` | `active` | End of term renewal evaluation | `for_renewal` | Scholar notified to submit renewal documents (Registration Form & Grades). |
| **Scholar** | `Scholar` | `for_renewal` | Admin verifies valid renewal documents | `active` | Renewal history logged for the semester. |
| **Scholar** | `Scholar` | `active` / `for_renewal` | Scholar completes degree | `completed` | Marked as graduate grantee; slot freed for upcoming academic year. |
| **Scholar** | `Scholar` | `active` / `for_renewal` | Grade deficiency / code violation | `terminated` | Disqualified from future disbursements; notification sent. |
| **Compliance** | `ComplianceRequest` | `draft` | Admin publishes request | `active` | System finds all active scholars of program and generates `ScholarCompliance` records with status `pending`; dispatches broadcast notification. |
| **Compliance** | `ScholarCompliance` | `pending` | Scholar submits compliance files | `submitted` | Displays in admin compliance review queue. |
| **Compliance** | `ScholarComplianceDocument`| `pending` | Admin confirms requirement met | `verified` | When all requirements verified, `ScholarCompliance` updates to `completed`. |
| **Welfare** | `WelfareCase` | `Open` | Admin opens case dossier | `Under Assessment` | Staff intake notes recorded. |
| **Welfare** | `WelfareCase` | `Under Assessment` | Admin refers to scholarship or office | `Referred` / `For Follow-up` | Generates `WelfareCaseReferral`; student notified; application linking prepared. |
| **Welfare** | `WelfareCase` | `For Follow-up` | Admin confirms resolution | `Resolved` -> `Closed` | 1-Page Official Case Dossier generated and ready for printing. |

---

## 4. Key Admin Decision Points Summary

1. **Document Verification Gate**:
   - Every uploaded document must be marked as either `verified` or `needs_resubmission`.
   - A single `needs_resubmission` status immediately shifts the entire application to `incomplete`, halting approval until resolved by the student.
2. **Dual-Scholarship Conflict Prevention**:
   - Before approving or manually enrolling a scholar, the system verifies that the student does not hold another active scholarship for the concurrent academic term.
3. **Application Approval Transaction**:
   - The approval of an application is atomic: it updates the application status, registers the scholar grantee, writes the system audit log, and notifies the applicant in a single database transaction.
4. **Welfare Referral to Application Link**:
   - When a student in financial crisis is referred from the Welfare module to a scholarship, the referral ID is tied into the application data (`welfare_case_referral_id`), prioritizing or tracing the scholarship grant back to the welfare case.
