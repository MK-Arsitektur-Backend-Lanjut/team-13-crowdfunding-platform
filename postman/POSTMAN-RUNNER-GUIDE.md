# Postman Runner Guide - Team 13 Crowdfunding API

## Overview
Automated test execution for Campaign Management and Donation Processing modules using Newman (Postman CLI).

---

## Quick Start

### 1. Prerequisites
- Node.js v14+ installed
- npm package manager available
- Laravel app running on `http://localhost:8000`
- MySQL database seeded with test data

### 2. Installation
```bash
# Install Newman globally (recommended)
npm install -g newman

# Or install locally in the project
npm install newman
```

### 3. Run Tests
```bash
# Using global Newman
node postman/runner.js

# Using local Newman (if installed locally)
npx newman run postman/Team-13-Crowdfunding.postman_collection.json \
  -e postman/Team-13-Local.postman_environment.json
```

---

## Test Execution Flow

### Sequential Execution Order
The runner executes requests in this order to ensure proper test dependencies:

```
1. LOGIN (Auth)
   └─ Generates JWT token for protected endpoints
   
2. GET CAMPAIGNS (Campaign Management)
   └─ Retrieves list of all campaigns
   
3. CREATE CAMPAIGN (Campaign Management)
   └─ Creates new campaign, stores campaign_id for next tests
   
4. GET CAMPAIGN BY ID (Campaign Management)
   └─ Retrieves the newly created campaign details
   
5. UPDATE CAMPAIGN (Campaign Management)
   └─ Updates campaign description and status
   
6. CREATE DONATION (Public Endpoint)
   └─ Creates donation for campaign, stores donation_id
   
7. GET CAMPAIGN TOTAL (Public Endpoint)
   └─ Retrieves aggregated donation total for campaign
   
8. GET DONATION STATS (Admin Endpoint)
   └─ Retrieves stats: active_donors, seeded_active_donors, total_success_donations
   
9. REFRESH TOKEN (Auth)
   └─ Refreshes JWT token for continued protected access
   
10. GET DONATION HISTORY (Protected Endpoint)
    └─ Retrieves user's donation history using refreshed token
    
11. GET SINGLE DONATION (Protected Endpoint)
    └─ Retrieves details of specific donation by ID
    
12. CHANGE CAMPAIGN STATUS (Admin Endpoint)
    └─ Changes campaign status to "selesai" (completed)
    
13. GET CAMPAIGNS BY STATUS (Public Endpoint)
    └─ Filters campaigns by status (aktif/selesai)
    
14. DELETE DONATION (Protected Endpoint)
    └─ Deletes the donation created in step 6
    
15. LOGOUT (Auth)
    └─ Invalidates JWT token
```

### Test Assertions
Each request includes automatic assertions:
- ✅ HTTP Status Code validation
- ✅ Response JSON structure validation
- ✅ Variable storage for dependent requests
- ✅ Test result logging

---

## Configuration Options

### Command Line Options
```bash
# Custom request delay (milliseconds)
node postman/runner.js --delay 500

# Custom timeout per request (milliseconds)
node postman/runner.js --timeout 10000

# Stop on first error
node postman/runner.js --stop-on-error

# Allow self-signed certificates
node postman/runner.js --insecure

# Combine options
node postman/runner.js --delay 200 --timeout 8000 --stop-on-error
```

### Environment Variables
The runner uses variables defined in `postman/Team-13-Local.postman_environment.json`:

| Variable | Default | Description |
|----------|---------|-------------|
| `base_url` | `http://localhost:8000` | API base URL |
| `token` | _(empty)_ | JWT token (auto-populated after login) |
| `campaign_id` | `1` | Campaign ID for tests |
| `donation_id` | `1` | Donation ID for tests |
| `test_email` | `personal@test.local` | Test user email |
| `test_password` | `Test12345!` | Test user password |

---

## Test Results

### Console Output
After execution, displays:
- ✅ Pass/Fail status per request
- 📊 Summary: Total requests, tests, pass rate
- ⏱️ Total execution duration
- 📈 Test statistics

### JSON Report
Detailed test results are exported to `postman/test-results.json`:
```json
{
  "info": { "name": "Team 13 Crowdfunding API", ... },
  "stats": {
    "total": 15,
    "passed": 15,
    "failed": 0
  },
  "run": {
    "stats": {
      "tests": { "total": 25, "pending": 0, "failed": 0 },
      "assertions": { "total": 25, "failed": 0 },
      "requests": { "total": 15, "pending": 0, "failed": 0 }
    }
  }
}
```

---

## Troubleshooting

### Issue: "Cannot find module 'newman'"
**Solution:** Install Newman globally
```bash
npm install -g newman
```

### Issue: "Collection file not found"
**Solution:** Ensure you're running the script from project root:
```bash
cd your-project-root
node postman/runner.js
```

### Issue: "Failed to connect to http://localhost:8000"
**Solution:** Start the Laravel development server:
```bash
docker compose exec app php artisan serve --host=0.0.0.0 --port=8000
# OR
php artisan serve
```

### Issue: "401 Unauthorized on login"
**Solution:** Check test user credentials in environment file:
```bash
# Verify user exists and password is correct
php artisan tinker
>>> User::where('email', 'personal@test.local')->first()
```

### Issue: "Tests timeout or run slowly"
**Solution:** Increase timeout or delay:
```bash
node postman/runner.js --delay 200 --timeout 8000
```

### Issue: "Certificate validation error"
**Solution:** Allow self-signed certificates:
```bash
node postman/runner.js --insecure
```

---

## Integration with CI/CD

### GitHub Actions Example
```yaml
name: API Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: crowdfunding
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
    
    steps:
      - uses: actions/checkout@v2
      - uses: actions/setup-node@v2
        with:
          node-version: '18'
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      
      - name: Install dependencies
        run: |
          composer install
          npm install -g newman
      
      - name: Run migrations
        run: php artisan migrate --env=testing
      
      - name: Seed database
        run: php artisan db:seed --env=testing
      
      - name: Start Laravel server
        run: php artisan serve &
      
      - name: Run Postman tests
        run: node postman/runner.js --timeout 8000
```

---

## Windows Batch Script

For Windows users, use the provided `runner.bat` file:

```batch
@echo off
REM Postman Runner for Windows
node postman/runner.js %*
```

Run tests:
```cmd
runner.bat
runner.bat --delay 500 --timeout 8000
```

---

## Docker Integration

Run tests inside Docker container:
```bash
docker compose exec app bash -c "npm install -g newman && node postman/runner.js"
```

Or add to your docker-compose.yml:
```yaml
test:
  command: bash -c "npm install -g newman && npm test"
```

---

## Manual Testing in Postman UI

If you prefer manual testing:

1. **Import Collection:** File → Import → Select `Team-13-Crowdfunding.postman_collection.json`
2. **Import Environment:** File → Import → Select `Team-13-Local.postman_environment.json`
3. **Select Environment:** Top-right dropdown → Select "Team-13-Local"
4. **Run Collection:** Runner button → Select collection → Run
5. **View Results:** Check test results panel on the right

---

## API Endpoints Tested

### Authentication (4 requests)
- POST `/api/auth/login` - User login
- POST `/api/auth/register` - User registration
- POST `/api/auth/refresh` - Refresh JWT token
- POST `/api/auth/logout` - Logout user

### Campaign Management (7 requests)
- GET `/api/campaigns` - List all campaigns
- POST `/api/campaigns` - Create campaign
- GET `/api/campaigns/{id}` - Get campaign details
- PUT `/api/campaigns/{id}` - Update campaign
- DELETE `/api/campaigns/{id}` - Delete campaign
- PATCH `/api/campaigns/{id}/status` - Change status
- GET `/api/campaigns/status/{status}` - Filter by status

### Donation Processing (4 requests)
- POST `/api/donations` - Create donation
- GET `/api/campaigns/{id}/donations/total` - Campaign total
- GET `/api/donations/stats` - Donation statistics
- GET `/api/donations/history` - User donation history (Protected)
- GET `/api/donations/{id}` - Single donation detail (Protected)
- DELETE `/api/donations/{id}` - Delete donation (Protected)

---

## Performance Metrics

Expected execution time on local machine:
- **Without delays:** ~3-5 seconds
- **With 100ms delay:** ~5-7 seconds
- **With 500ms delay:** ~12-15 seconds

Network latency will affect actual times. For CI/CD, use `--timeout 10000` to account for container startup delays.

---

## Support

For issues or questions:
1. Check console output for detailed error messages
2. Review `test-results.json` for detailed failure information
3. Verify Laravel server is running: `curl http://localhost:8000/api/campaigns`
4. Check network connectivity: `ping localhost`
5. Review application logs: `docker compose logs app`

