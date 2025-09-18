## Bogac Rides — System Overview and Documentation

### Purpose and Audience
Bogac Rides is a rentals management system for a beach/water-rides business. It manages ride inventory, rental lifecycles, availability, pricing, logging, and reporting. This document is intended for developers, admins (owners), and staff to understand how the system works, how data flows, and what each part is responsible for.

### Conceptual Framework
The Boat Rental Management System (BRMS) is grounded in the principle of integrating information technology into rental operations to achieve efficiency, accuracy, and accountability. The framework is based on three main components:

---
config:
  layout: elk
---
flowchart LR
 subgraph Inputs["Inputs"]
        I1["Ride type, classification, ride"]
        I2["Start/end time, duration"]
        I3["Note, life jackets"]
        I4["Admin catalog changes - optional images"]
        I5["Delete reason"]
        I6["Report filters"]
        I7["Staff registration details"]
  end
 subgraph Processes["Processes"]
        P1["1 Add Rental"]
        P2["2 Edit Rental"]
        P3["3 Complete Rental"]
        P4["4 Delete Rental"]
        P5["5 Ride Availability Live"]
        P6["6 Catalog Management"]
        P7["7 Reporting and Sales"]
        P8["8 Logging and Audit"]
        P9["9 Register Staff"]
  end
 subgraph Outputs["Outputs"]
        O1["Availability lists and time-left"]
        O2["Updated rentals with snapshots"]
        O3["Ride states updated"]
        O4["CSV reports and tables"]
        O5["Activity logs with reasons"]
        O6["Images on rates and details"]
        O7["Active staff accounts"]
  end
    I1 --> P1 & P2
    I2 --> P1 & P2
    I3 --> P1 & P2
    I5 --> P4
    I4 --> P6
    I6 --> P7
    I7 --> P9
    P1 --> O2 & O3 & P8
    P2 --> O2 & P8
    P3 --> O3 & P8
    P4 --> O3 & P8
    P5 --> O1
    P6 --> O6
    P7 --> O4
    P9 --> O7 & P8
    P8 --> O5


This framework ensures a continuous cycle of input, processing, and output, creating a reliable management tool that supports decision-making, enhances accountability, maintains data integrity through snapshots, and strengthens the operational efficiency of Bogac Rides water rental business.

### Intended Users and Roles
- **Admin (Owner)**: Full control. Manages ride catalog (types, classifications, rides), uploads optional images, registers new staff, reviews logs/reports, oversees operations.
- **Staff (Field Workers)**: Create/edit/complete/delete rentals, manage daily operations, view live availability with time-left, generate reports.
- **Guest (Public Viewer)**: No login required. Can view public rates and availability pages intended for customers.

### Core Concepts
- **Rides Inventory**: `RideTypes` → `Classifications` (with `price_per_hour` and optional `image_path`) → `Rides` (with `identifier`, `is_active`).
- **Rentals Lifecycle**: Active (ongoing) → Completed (finished). Completing a rental changes only the status; it does not overwrite `end_at`.
- **Snapshot Fields**: Rentals store `_at_time` copies of labels and prices for historical accuracy: `ride_type_name_at_time`, `classification_name_at_time`, `ride_identifier_at_time`, `price_per_hour_at_time`, `user_name_at_time`.
- **Real-Time Status**: Livewire polling and events update staff dashboards and ride availability in near real-time.
- **Audit Logging**: All create/edit/delete actions are recorded in staff logs, including delete reasons.

### Data Model (ERD Summary)
- `USERS`: id, name, email, username, userType, password, timestamps
- `RIDE_TYPES`: id, name, image_path (optional), timestamps
- `CLASSIFICATIONS`: id, ride_type_id, name, price_per_hour, image_path (optional), timestamps, soft-deletes
- `RIDES`: id, classification_id, identifier, is_active (0=Inactive, 1=Available, 2=Used), timestamps, soft-deletes
- `RENTALS`: id, user_id, ride_id, status (Active/Completed), start_at, end_at, duration_minutes, life_jacket_quantity, price_per_hour_at_time, computed_total, note, user_name_at_time, ride_identifier_at_time, classification_name_at_time, ride_type_name_at_time, timestamps
- `STAFF_LOGS`: id, user_id, action, model_type, model_id, old_values, new_values, timestamps
- `PROFILE_CHANGE_REQUESTS`, `SESSIONS`, `PASSWORD_RESET_TOKENS`, `MIGRATIONS`: framework/support tables

### Status Codes
- Ride `is_active`: 0=Inactive, 1=Available, 2=Used
- Rental `status`: Active, Completed

### Key Processes (Input → Process → Output)
- **Add Rental (Staff)**
  - Input: Ride Type, Classification, Ride, Start/End, Duration, Note, Life jackets
  - Process: Validate; compute `computed_total`; create rental with snapshots; set `ride.is_active = Used`; log creation; broadcast refresh
  - Output: Ride shows as Used with time-left; updated dashboards; success message; log entry

- **Edit Rental (Staff)**
  - Input: New classification/ride/times/duration, note
  - Process: Load existing; validate allowing current ride; prevent conflict with other active rentals; update rental and snapshots; recalc total; log edit
  - Output: Updated rental row with correct totals and labels; success message; log entry

- **Complete Rental (Staff)**
  - Input: Complete action
  - Process: Set `status = Completed`; do not change `end_at`; set `ride.is_active = Available`; log edit
  - Output: Rental leaves ongoing views; ride becomes Available; log entry

- **Delete Rental (Staff)**
  - Input: Confirmation + required delete reason
  - Process: Validate reason; log with reason; delete rental; set `ride.is_active = Available`; refresh views
  - Output: Rental removed; availability updated; log entry with reason

- **Ride Availability (Live)**
  - Input: Optional filters (Ride Type, Classification); Clear Filters
  - Process: Query rides and active rentals; compute time-left; sort Used by least time-left; self-heal inconsistent statuses; poll refresh
  - Output: Available / Used / Inactive lists, time-left display, Live indicator, last updated

- **Catalog Management (Admin)**
  - Input: Ride Type (name, optional image), Classification (name, price/hour, optional image), Rides (identifier/status)
  - Process: Validate; store images to `public/storage`; CRUD operations; prevent deactivate/delete if ride is Used; log changes
  - Output: Updated catalog; images on Rates/Details; reflected in Staff workflows

- **Admin Registers Staff**
  - Input: Staff name, email/username, password, role
  - Process: Create user with role `staff`; enforce uniqueness; issue credentials
  - Output: New staff account ready for Staff Dashboard

- **Reporting & Sales**
  - Input: Date filters
  - Process: Query rentals using snapshots; compute totals; export CSV; update tables
  - Output: CSV and on-screen reports using snapshot labels; stable historical view

- **Logs**
  - Input: None (read)
  - Process: Translate raw old/new values; resolve labels from snapshots; include delete reasons; format times
  - Output: Human-readable activity feed for admins/staff

### Security & Access
- **Guest (no login)**: Read-only access to designated public pages (rates, guest availability). No write operations or authenticated routes.
- **Staff (login required)**: Rentals management, live availability, reporting. No admin-only catalog or user management.
- **Admin (login required)**: Full access to catalog management (including images), staff registration, logs, reporting, and administrative settings.
- Enforced via role-based authorization/middleware; uploads restricted to Admin; public assets served through `public/storage` symlink.

---
config:
  layout: elk
---
flowchart LR
 subgraph Inputs["Inputs"]
        I1["Ride type, classification, ride"]
        I2["Start/end time, duration"]
        I3["Note, life jackets"]
        I4["Admin catalog changes - optional images"]
        I5["Delete reason"]
        I6["Report filters"]
        I7["Staff registration details"]
  end
 subgraph Processes["Processes"]
        P1["1 Add Rental"]
        P2["2 Edit Rental"]
        P3["3 Complete Rental"]
        P4["4 Delete Rental"]
        P5["5 Ride Availability Live"]
        P6["6 Catalog Management"]
        P7["7 Reporting and Sales"]
        P8["8 Logging and Audit"]
        P9["9 Register Staff"]
  end
 subgraph Outputs["Outputs"]
        O1["Availability lists and time-left"]
        O2["Updated rentals with snapshots"]
        O3["Ride states updated"]
        O4["CSV reports and tables"]
        O5["Activity logs with reasons"]
        O6["Images on rates and details"]
        O7["Active staff accounts"]
  end
    I1 --> P1 & P2
    I2 --> P1 & P2
    I3 --> P1 & P2
    I5 --> P4
    I4 --> P6
    I6 --> P7
    I7 --> P9
    P1 --> O2 & O3 & P8
    P2 --> O2 & P8
    P3 --> O3 & P8
    P4 --> O3 & P8
    P5 --> O1
    P6 --> O6
    P7 --> O4
    P9 --> O7 & P8
    P8 --> O5


---
config:
  layout: elk
---
flowchart LR
    Guest["Guest"] -- view rates and availability --> PR3["3 Catalog Management"]
    Staff["Staff"] -- sign in out --> PR5["5 Auth and Sessions"]
    Staff -- manage rentals --> PR1["1 Rentals and Billing"]
    Staff -- view availability --> PR2["2 Ride Availability"]
    Staff -- generate reports --> PR4["4 Reporting and Sales"]
    Admin["Admin"] -- manage catalog --> PR3
    Admin -- register staff --> PR8["8 Staff Registration"]
    Admin -- view logs --> PR7["7 Logging and Audit View"]
    PR5 -- write sessions --> D_SESS[("SESSIONS")]
    PR5 -- write reset tokens --> D_TOKENS[("PASSWORD_RESET_TOKENS")]
    PR8 -- create staff user --> D_USERS[("USERS")]
    PR8 -- write log --> D_LOGS[("STAFF_LOGS")]
    PR3 -- write ride types --> D_TYPES[("RIDE_TYPES")]
    PR3 -- write classifications --> D_CLASS[("CLASSIFICATIONS")]
    PR3 -- write rides --> D_RIDES[("RIDES")]
    PR1 -- write rentals snapshots --> D_RENTALS[("RENTALS")]
    PR1 -- update ride status --> D_RIDES
    PR1 -- write log --> D_LOGS
    PR2 -- fix inconsistent status --> D_RIDES
    PR6["6 Profile Change Requests"] -- write requests --> D_PCR[("PROFILE_CHANGE_REQUESTS")]
    PR6 -- write log --> D_LOGS
    D_USERS -- verify credentials --> PR5
    D_USERS -- read user handler --> PR1
    D_USERS -- link users --> PR6
    D_USERS -- read users --> PR4
    D_RIDES -- read rides status --> PR2
    D_RENTALS -- read active rentals --> PR2
    D_RENTALS -- read rentals snapshots --> PR4
    D_TYPES -- read ride types --> PR4
    D_CLASS -- read classifications --> PR4
    D_LOGS -- read logs --> PR7
    D_RENTALS -- resolve labels from snapshots --> PR7
    D_PCR -- read requests --> PR6

    ERD(Entity Relationship Diagram)
    ---
config:
  layout: dagre
---
erDiagram
  USERS ||--o{ RENTALS : has
  USERS ||--o{ STAFF_LOGS : has
  USERS ||--o{ PROFILE_CHANGE_REQUESTS : has
  RIDE_TYPES ||--|{ CLASSIFICATIONS : has
  CLASSIFICATIONS ||--|{ RIDES : has
  RIDES ||--o{ RENTALS : has
  USERS {
    int id PK
    string name
    string email
    string username
    string userType
    string password
    string remember_token
    datetime created_at
    datetime updated_at
    datetime deleted_at
  }
  RIDE_TYPES {
    int id PK
    string name
    string image_path
    datetime created_at
    datetime updated_at
  }
  CLASSIFICATIONS {
    int id PK
    int ride_type_id FK
    string name
    float price_per_hour
    string image_path
    datetime created_at
    datetime updated_at
    datetime deleted_at
  }
  RIDES {
    int id PK
    int classification_id FK
    string identifier
    int is_active
    datetime created_at
    datetime updated_at
    datetime deleted_at
  }
  RENTALS {
    int id PK
    int user_id FK
    int ride_id FK
    int status
    datetime start_at
    datetime end_at
    int duration_minutes
    int life_jacket_quantity
    float price_per_hour_at_time
    float computed_total
    string note
    string user_name_at_time
    string ride_identifier_at_time
    string classification_name_at_time
    string ride_type_name_at_time
    datetime created_at
    datetime updated_at
  }
  STAFF_LOGS {
    int id PK
    int user_id FK
    string action
    string model_type
    int model_id
    string old_values
    string new_values
    datetime created_at
    datetime updated_at
  }
  PROFILE_CHANGE_REQUESTS {
    int id PK
    int user_id FK
    string new_email
    string otp_code_hash
    string payload
    datetime expires_at
    datetime consumed_at
    datetime created_at
    datetime updated_at
  }
  SESSIONS {
    string id PK
    int user_id
    string ip_address
    string user_agent
    string payload
    int last_activity
  }
  PASSWORD_RESET_TOKENS {
    string email PK
    string token
    datetime created_at
  }
  MIGRATIONS {
    int id PK
    string migration
    int batch
  }

USE CASE:
---
config:
  layout: elk
---
flowchart TB
 subgraph GuestUC["Guest Use Cases"]
        UC2["View Ride Availability and Prices"]
  end
 subgraph StaffUC["Staff Use Cases"]
        UC12["Manage Rentals"]
  end
 subgraph AdminUC["Admin Use Cases"]
        UC16["Manage Rides"]
        UC19["View Staff Logs"]
        UC20["Generate Reports"]
        UC21["Manage Staff Accounts"]
        UC3["View Sales"]
  end
 subgraph SharedUC["Shared Use Cases"]
        UC4["Sign In/Out"]
        UC9["View Ride Availability"]
        UC22["Forgot Password"]
        UC1["Manage Profile"]
  end
 subgraph System["Bogac Rides Management System"]
        GuestUC
        StaffUC
        AdminUC
        SharedUC
  end
    Guest["Guest"] --> UC2
    Staff["Staff"] --> UC4 & UC9 & UC12 & UC22 & UC1
    Admin["Admin"] --> UC4 & UC9 & UC19 & UC20 & UC21 & UC22 & UC1 & UC16 & UC3

         

### Components Overview
- `RideAvailability`: Live lists of Available/Used/Inactive with time-left; auto-corrects inconsistent statuses; sorting by least time-left; filters and Clear Filters.
- `StaffDashboard`: Manage rentals with modals; delete requires reason; uses `_at_time` snapshot labels; totals and eager-loading.
- `AddRide`: Cascading selectors; computes totals; snapshots; sets ride to Used; emits refresh.
- `EditRide`: Allows keeping same ride; prevents conflicts; updates snapshots; completes without changing `end_at`.
- `RidesRate` and `ViewDetails`: Display and manage images for `RideTypes` and `Classifications`; responsive image sizing.
- `Logs`: Human-friendly changes; includes delete reasons; robust time parsing.

### Real-Time & Consistency
- Livewire polling (`wire:poll`) and events (`dispatch`) keep views fresh.
- Self-healing ride status: if a ride is marked Used but has no active rental, it is flipped back to Available.

### Reporting
- Uses rental snapshots for consistent historical reporting; tables and CSVs align with updated schema; Identification column present.

### Deployment Notes
- Ensure `php artisan storage:link` for public images.
- Migrations include `ride_type_name_at_time` and `image_path` on both `RIDE_TYPES` and `CLASSIFICATIONS`.


