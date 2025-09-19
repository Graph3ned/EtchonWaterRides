# Current Database Structure - Bogac Rides Management System

## Overview
This document outlines the current database structure based on the existing migration files in the project.

## Core Tables

### 1. Users Table
**Migration:** `2024_12_08_073253_users.php`
**Purpose:** Stores user accounts (staff and admin users)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    userType VARCHAR(255) DEFAULT '0',  -- 0 = Staff, 1 = Admin
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Key Features:**
- `userType`: 0 = Staff, 1 = Admin (role-based access)
- Email must be unique
- Password hashing support
- Remember token for persistent login

### 2. Rides Rental Database Table
**Migration:** `2024_11_30_115019_create_rides-rental-dbs_table.php`
**Purpose:** Stores individual ride rental transactions

```sql
CREATE TABLE rides_rental_dbs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user VARCHAR(255) NOT NULL,                    -- Staff member name
    rideType VARCHAR(255) NOT NULL,                -- Type of ride
    classification VARCHAR(255) NOT NULL,          -- Classification within ride type
    note VARCHAR(255) NULL,                        -- Additional notes
    duration INTEGER NOT NULL,                     -- Duration in minutes
    life_jacket_usage INTEGER NOT NULL,            -- Number of life jackets used
    pricePerHour DECIMAL(8,2) NOT NULL,            -- Price per hour
    totalPrice DECIMAL(8,2) NOT NULL,              -- Total calculated price
    status INTEGER NOT NULL,                       -- Status of the ride
    timeStart TIME NOT NULL,                       -- Start time of ride
    timeEnd TIME NOT NULL,                         -- End time of ride
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Key Features:**
- Tracks individual ride transactions
- Links to staff member via `user` field (string-based relationship)
- Calculates total price based on duration and hourly rate
- Tracks safety equipment usage (life jackets)
- Time tracking for rides
- Status field for ride state management

### 3. Prices Table
**Migration:** `2024_12_08_051828_create_prices_table.php`
**Purpose:** Stores pricing configuration for different ride types and classifications

```sql
CREATE TABLE prices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ride_type VARCHAR(255) NOT NULL,               -- Type of ride
    classification VARCHAR(255) NOT NULL,          -- Classification within ride type
    price_per_hour INTEGER NOT NULL,               -- Price per hour in PHP
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Key Features:**
- Centralized pricing management
- Different rates for different ride types and classifications
- Easy price updates without code changes
- Implicit relationship with rides via `rideType` + `classification`

### 4. Sessions Table
**Migration:** `2025_05_14_183251_create_sessions_table.php`
**Purpose:** Manages user sessions

```sql
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,                   -- Session ID
    user_id BIGINT UNSIGNED NULL,                  -- Foreign key to users
    ip_address VARCHAR(45) NOT NULL,               -- User's IP address
    user_agent TEXT NOT NULL,                      -- Browser information
    payload LONGTEXT NOT NULL,                     -- Session data
    last_activity INT UNSIGNED NOT NULL,           -- Last activity timestamp
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

**Key Features:**
- Only table with proper foreign key relationship
- Links to users table for authentication
- Tracks IP address and user agent for security
- Stores session payload data
- Automatic cleanup when user is deleted

## Data Relationships

### Primary Relationships
1. **Users → Sessions**: One-to-Many (FK relationship)
   - One user can have multiple sessions
   - Foreign key: `sessions.user_id → users.id`
   - Cascade: SET NULL on user deletion

2. **Users → Rides**: One-to-Many (String-based relationship)
   - One user (staff member) can have many rides
   - Relationship via `user` field in `rides_rental_dbs`
   - No foreign key constraint

3. **Prices → Rides**: One-to-Many (Implicit relationship)
   - Price configuration determines pricing for rides
   - Relationship via `rideType` + `classification` fields
   - No foreign key constraint

## Business Logic

### User Types
- **Staff (userType = '0')**: Can create and manage ride transactions
- **Admin (userType = '1')**: Can manage prices, view all data, manage staff

### Ride Management
- Each ride must have a valid `rideType` and `classification` combination
- Pricing is calculated based on duration and hourly rate
- Life jacket usage is tracked for safety compliance
- Status field allows for ride state management

### Data Integrity
- Email addresses must be unique across all users
- Ride durations are stored in minutes
- Prices are stored with 2 decimal precision
- All timestamps use Laravel's standard format

## Design Patterns

### Loose Coupling Strategy
The system uses a **loose coupling** approach for business tables:

- **String-based relationships**: `rides_rental_dbs.user` → `users.name`
- **Implicit relationships**: `rides_rental_dbs.rideType + classification` → `prices.ride_type + classification`
- **No foreign key constraints** for business data

### Benefits of This Approach
1. **Data Preservation**: Historical ride data remains intact even if users are deactivated
2. **Flexibility**: User names can change without breaking historical data
3. **Performance**: No FK constraint checks during high-volume inserts
4. **Simplicity**: Easier to understand and maintain for small-medium applications

### Application-Level Validation
Instead of database constraints, validation happens in code:
- User existence validation before creating rides
- Price consistency validation
- Business rule enforcement in models

## Indexes and Performance

### Recommended Indexes
```sql
-- Users table
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_userType ON users(userType);

-- Rides table
CREATE INDEX idx_rides_user ON rides_rental_dbs(user);
CREATE INDEX idx_rides_type ON rides_rental_dbs(rideType);
CREATE INDEX idx_rides_classification ON rides_rental_dbs(classification);
CREATE INDEX idx_rides_created_at ON rides_rental_dbs(created_at);
CREATE INDEX idx_rides_status ON rides_rental_dbs(status);

-- Prices table
CREATE INDEX idx_prices_ride_type ON prices(ride_type);
CREATE INDEX idx_prices_classification ON prices(classification);
CREATE UNIQUE INDEX idx_prices_type_classification ON prices(ride_type, classification);

-- Sessions table
CREATE INDEX idx_sessions_user_id ON sessions(user_id);
CREATE INDEX idx_sessions_last_activity ON sessions(last_activity);
```

## Data Flow

### 1. Ride Creation Process
1. Staff member logs in (session created)
2. Staff selects ride type and classification
3. System looks up pricing from `prices` table
4. Staff enters ride details (duration, life jackets, etc.)
5. System calculates total price
6. Ride record created in `rides_rental_dbs`

### 2. Price Management Process
1. Admin logs in
2. Admin views/modifies prices in `prices` table
3. New rides automatically use updated pricing
4. Historical rides maintain original pricing

### 3. Reporting Process
1. System queries `rides_rental_dbs` with filters
2. Joins with `users` table for staff information
3. Aggregates data by date, staff, ride type, etc.
4. Generates sales reports and analytics

## Security Considerations

1. **Password Security**: All passwords are hashed using Laravel's built-in hashing
2. **Session Security**: Sessions are stored securely with IP and user agent tracking
3. **Data Validation**: All inputs are validated before database insertion
4. **Access Control**: Role-based access via `userType` field
5. **SQL Injection**: Protected by Eloquent ORM and prepared statements

## Current Status

This database structure supports:
- ✅ User management and authentication
- ✅ Ride transaction tracking
- ✅ Dynamic pricing system
- ✅ Sales reporting and analytics
- ✅ Staff performance tracking
- ✅ Safety equipment monitoring
- ✅ Session management
- ✅ Data preservation for historical records

The design prioritizes data preservation and business flexibility over strict normalization, making it appropriate for a water ride rental business system.
