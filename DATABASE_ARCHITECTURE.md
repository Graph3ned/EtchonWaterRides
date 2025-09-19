# Database Architecture - Bogac Rides Management System

## Overview
This document outlines the complete database architecture for the Bogac Rides Management System based on the migration files.

## Core Tables

### 1. Users Table
**Purpose**: Stores user accounts (staff and admin users)
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    userType VARCHAR(255) DEFAULT '0',  -- 0 = Staff, 1 = Admin
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Key Features**:
- `userType`: 0 = Staff, 1 = Admin (role-based access)
- Email verification support
- Password hashing
- Remember token for persistent login

### 2. Rides Rental Database Table
**Purpose**: Stores individual ride rental transactions
```sql
CREATE TABLE rides_rental_dbs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user VARCHAR(255) NOT NULL,                    -- Staff member name
    rideType VARCHAR(255) NOT NULL,                -- Type of ride (e.g., 'water_ride', 'land_ride')
    classification VARCHAR(255) NOT NULL,          -- Classification within ride type
    note TEXT NULL,                                -- Additional notes
    duration INTEGER NOT NULL,                     -- Duration in minutes
    life_jacket_usage INTEGER NOT NULL,            -- Number of life jackets used
    pricePerHour DECIMAL(8,2) NOT NULL,            -- Price per hour
    totalPrice DECIMAL(8,2) NOT NULL,              -- Total calculated price
    timeStart TIME NOT NULL,                       -- Start time of ride
    timeEnd TIME NOT NULL,                         -- End time of ride
    status VARCHAR(255) NULL,                      -- Status of the ride (added later)
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Key Features**:
- Tracks individual ride transactions
- Links to staff member via `user` field
- Calculates total price based on duration and hourly rate
- Tracks safety equipment usage (life jackets)
- Time tracking for rides

### 3. Prices Table
**Purpose**: Stores pricing configuration for different ride types and classifications
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

**Key Features**:
- Centralized pricing management
- Different rates for different ride types and classifications
- Easy price updates without code changes

## System Tables (Laravel Framework)

### 4. Sessions Table
**Purpose**: Manages user sessions
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

### 5. Cache Tables
**Purpose**: Application caching system
```sql
-- Cache storage
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INTEGER NOT NULL
);

-- Cache locks for distributed locking
CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);
```

### 6. Job Queue Tables
**Purpose**: Background job processing
```sql
-- Main jobs table
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX idx_queue (queue)
);

-- Job batches for grouped processing
CREATE TABLE job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INTEGER NULL,
    created_at INTEGER NOT NULL,
    finished_at INTEGER NULL
);

-- Failed jobs tracking
CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Data Relationships

### Primary Relationships
1. **Users → Rides**: One-to-Many
   - One user (staff member) can have many rides
   - Relationship via `user` field in `rides_rental_dbs`

2. **Prices → Rides**: One-to-Many (Implicit)
   - Price configuration determines pricing for rides
   - Relationship via `rideType` and `classification` fields

3. **Users → Sessions**: One-to-Many
   - One user can have multiple sessions
   - Foreign key relationship via `user_id`

### Business Logic Relationships
- **Ride Pricing**: `rides_rental_dbs.pricePerHour` should match `prices.price_per_hour` for corresponding `rideType` and `classification`
- **Staff Management**: Only users with `userType = 0` (staff) can create rides
- **Admin Access**: Users with `userType = 1` (admin) can manage prices and view all data

## Key Business Rules

### 1. User Types
- **Staff (userType = 0)**: Can create and manage ride transactions
- **Admin (userType = 1)**: Can manage prices, view all data, manage staff

### 2. Ride Management
- Each ride must have a valid `rideType` and `classification` combination
- Pricing is calculated based on duration and hourly rate
- Life jacket usage is tracked for safety compliance

### 3. Data Integrity
- Email addresses must be unique across all users
- Ride durations are stored in minutes
- Prices are stored with 2 decimal precision
- All timestamps use Laravel's standard format

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

## Scalability Considerations

1. **Partitioning**: Consider partitioning `rides_rental_dbs` by date for large datasets
2. **Caching**: Use cache tables for frequently accessed pricing data
3. **Indexing**: Proper indexing on commonly queried fields
4. **Archiving**: Consider archiving old ride data to separate tables
5. **Queue Processing**: Use job queues for heavy operations like report generation

This architecture supports a water ride rental business with staff management, pricing configuration, transaction tracking, and comprehensive reporting capabilities.
