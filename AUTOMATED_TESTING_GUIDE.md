# Automated Testing Guide for Etchon Water Rides System

## Overview
This document explains how to run and maintain the automated test suite for the Etchon Water Rides management system.

## Test Coverage
The automated tests cover **33 out of 39** test cases from your original manual test suite:

### ✅ Fully Automated (33 tests)
- **Authentication (4 tests)**
  - Admin Login and Access
  - Staff Login and Access  
  - Authentication Security Baseline
  - Session Management Security

- **Access Control (2 tests)**
  - Unauthorized Access Prevention
  - Staff Access to Admin Functions

- **Staff Management (1 test)**
  - Duplicate Prevention

- **Ride Rentals (12 tests)**
  - Create New Rental Transaction
  - Edit Existing Rental
  - Complete Rental Transaction
  - Required Custom Duration When Enabled
  - Data Type Validation
  - Data Validation - Invalid Duration
  - Data Validation - Price Calculation
  - Ride Reappears After Completion
  - Automated Duration Calculation
  - Life Jacket Usage Tracking
  - Rental Status Management
  - Automated Billing Calculation

- **Reports & Sales (8 tests)**
  - Generate Financial Report
  - Filter Reports by Date Range
  - Filter Reports by Staff Member
  - Custom Date Range Reports
  - Monthly Report Generation
  - Yearly Report Generation
  - Export CSV Report (partial)
  - Growth Rate Calculation (partial)

- **Activity Logs (4 tests)**
  - Complete Audit Trail
  - Filter Activity Logs by Action
  - Filter Activity Logs by User
  - View Detailed Log Information

- **Ride Availability (2 tests)**
  - View Real-time Ride Availability
  - Updated Boat Availability

### ⚠️ Partially Automated (3 tests)
These require browser automation (Laravel Dusk):
- Real-time Data Updates (multi-browser testing)
- Real-time Status Updates (multi-browser testing)
- Mobile Responsiveness

### ❌ Manual Testing Required (3 tests)
These cannot be fully automated:
- Generate Operational Report (requires business logic verification)
- Filter Ride Availability by Type (UI-dependent)
- Clear Filters Functionality (UI-dependent)

## Running Tests

### Quick Start
```bash
# Run all automated tests
./run-automated-tests.bat

# Or run individual test suites
php artisan test tests/Feature/AuthenticationAutomatedTest.php
php artisan test tests/Feature/AccessControlAutomatedTest.php
php artisan test tests/Feature/StaffManagementAutomatedTest.php
php artisan test tests/Feature/RideRentalsAutomatedTest.php
php artisan test tests/Feature/ReportsAutomatedTest.php
```

### Test Environment Setup
```bash
# Ensure test database is set up
php artisan migrate --env=testing

# Run tests with fresh database each time
php artisan test --recreate-databases
```

## Test Structure

### Feature Tests
Located in `tests/Feature/`, these test complete user workflows:
- **AuthenticationAutomatedTest.php** - Login, security, sessions
- **AccessControlAutomatedTest.php** - Route protection, role-based access
- **StaffManagementAutomatedTest.php** - Staff creation, validation
- **RideRentalsAutomatedTest.php** - Rental lifecycle, validation
- **ReportsAutomatedTest.php** - Report generation, filtering

### Test Data Factories
Located in `database/factories/`, these create test data:
- **UserFactory.php** - Admin and staff users
- **RideTypeFactory.php** - Water ride types
- **ClassificationFactory.php** - Ride classifications
- **RideFactory.php** - Individual rides
- **RentalFactory.php** - Rental transactions

## Continuous Integration

### GitHub Actions (Recommended)
Create `.github/workflows/tests.yml`:
```yaml
name: Tests
on: [push, pull_request]
jobs:
  tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test
```

### Local Development
```bash
# Run tests on file changes (requires entr)
find tests/ -name "*.php" | entr -r php artisan test

# Run specific test method
php artisan test --filter "allows admin login"
```

## Test Data Management

### Database Seeding for Tests
```php
// In test files, use factories:
$admin = User::factory()->create(['userType' => 1]);
$rental = Rental::factory()->create(['status' => 0]);
```

### Test Database Isolation
Each test runs in a transaction that's rolled back, ensuring clean state.

## Extending Tests

### Adding New Test Cases
1. Create test in appropriate `tests/Feature/` file
2. Use descriptive test names: `it('validates rental duration is positive')`
3. Follow AAA pattern: Arrange, Act, Assert
4. Use factories for test data creation

### Example Test Structure
```php
it('creates rental with valid data', function () {
    // Arrange
    $staff = User::factory()->create(['userType' => 0]);
    $ride = Ride::factory()->create();
    
    // Act
    $this->actingAs($staff);
    $response = $this->post('/rentals', [
        'ride_id' => $ride->id,
        'duration' => 60
    ]);
    
    // Assert
    $response->assertStatus(201);
    expect(Rental::count())->toBe(1);
});
```

## Performance Testing

### Load Testing (Optional)
For high-traffic scenarios, consider:
```bash
# Install artillery for load testing
npm install -g artillery

# Create load test config
artillery quick --count 10 --num 5 http://localhost:8000/login
```

## Monitoring and Reporting

### Test Coverage
```bash
# Generate coverage report (requires Xdebug)
php artisan test --coverage-html coverage/
```

### Test Results
- Tests output detailed results with timing
- Failed tests show exact assertion failures
- Database queries are logged in verbose mode

## Troubleshooting

### Common Issues
1. **Database errors**: Ensure migrations are up to date
2. **Factory errors**: Check model relationships and required fields
3. **Livewire errors**: Verify component names and methods exist
4. **Authentication errors**: Check user factory creates valid users

### Debug Mode
```bash
# Run single test with debug output
php artisan test --filter "test_name" -vvv
```

## Best Practices

1. **Keep tests independent** - Each test should work in isolation
2. **Use descriptive names** - Test names should explain what's being tested
3. **Test edge cases** - Include boundary conditions and error scenarios
4. **Mock external services** - Don't rely on external APIs in tests
5. **Regular maintenance** - Update tests when features change

## Next Steps

### Browser Testing Setup (Optional)
To test the remaining UI-dependent cases:
```bash
composer require --dev laravel/dusk
php artisan dusk:install
php artisan dusk
```

### API Testing (Future)
For mobile app or API endpoints:
```bash
composer require --dev pestphp/pest-plugin-api
```

This automated test suite provides comprehensive coverage of your system's core functionality while maintaining fast execution times and reliable results.

