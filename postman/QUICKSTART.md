# Quick Start - Postman Runner

## 📋 Overview
Run automated API tests for Team 13 Crowdfunding Platform using Newman (Postman CLI).

## 🚀 Quick Steps

### 1. **Install Newman** (One-time setup)
```bash
npm install -g newman
```

### 2. **Ensure Laravel is Running**
```bash
# Docker
docker compose up -d

# Or Local
php artisan serve
```

### 3. **Run Tests** (Choose one)

#### **Option A: Node.js (Recommended)**
```bash
node postman/runner.js
```

#### **Option B: Windows Batch**
```cmd
postman\runner.bat
```

#### **Option C: PowerShell**
```powershell
.\postman\runner.ps1
```

#### **Option D: Newman CLI Directly**
```bash
newman run postman/Team-13-Crowdfunding.postman_collection.json \
  -e postman/Team-13-Local.postman_environment.json
```

---

## ⚙️ Common Options

```bash
# Custom delay between requests (ms)
node postman/runner.js --delay 500

# Custom timeout per request (ms)
node postman/runner.js --timeout 10000

# Stop on first error
node postman/runner.js --stop-on-error

# Allow self-signed certificates
node postman/runner.js --insecure

# Combine options
node postman/runner.js --delay 200 --timeout 8000 --stop-on-error
```

---

## 📊 What Gets Tested

| Category | Tests | Details |
|----------|-------|---------|
| **Authentication** | 4 | Login, Register, Refresh, Logout |
| **Campaign Management** | 7 | CRUD, Status, Filtering |
| **Donation Processing** | 4 | Create, Stats, History, Delete |
| **Total Requests** | **15** | Full smoke test suite |

---

## 📈 Output Example

```
✓ Login                          200 OK
✓ Get Campaigns                  200 OK
✓ Create Campaign                201 Created
✓ Get Campaign by ID             200 OK
✓ Update Campaign                200 OK
✓ Create Donation                200 OK
✓ Get Campaign Total             200 OK
✓ Get Donation Stats             200 OK
✓ Refresh Token                  200 OK
✓ Get Donation History           200 OK
✓ Get Single Donation            200 OK
✓ Change Campaign Status         200 OK
✓ Get Campaigns by Status        200 OK
✓ Delete Donation                200 OK
✓ Logout                         200 OK

===================================================
✓ All Tests Passed!
===================================================

📈 Test Summary:
   Total Requests: 15
   Total Tests: 25
   Pass Rate: 100%
   Total Duration: 4.52s

✅ Results exported to: postman/test-results.json
```

---

## ✅ Test Results

Results are saved to **`postman/test-results.json`** with:
- Request/response pairs
- Test assertions
- Timing information
- Pass/fail status

---

## 🔧 Troubleshooting

### "Cannot find module 'newman'"
```bash
npm install -g newman
```

### "Failed to connect to localhost:8000"
```bash
# Check if server is running
curl http://localhost:8000/api/campaigns

# Start server
docker compose up
# or
php artisan serve
```

### "401 Unauthorized"
- Verify test user exists
- Check `postman/Team-13-Local.postman_environment.json` credentials
- Ensure database is seeded: `php artisan db:seed`

### Tests timeout
Use longer timeout:
```bash
node postman/runner.js --timeout 10000
```

---

## 📖 Full Documentation

For detailed setup, troubleshooting, and CI/CD integration, see [POSTMAN-RUNNER-GUIDE.md](POSTMAN-RUNNER-GUIDE.md)

---

## 🌐 Manual Testing (Postman UI)

If you prefer the Postman application:

1. Open Postman Desktop or web app
2. Click **Import**
3. Select both files:
   - `postman/Team-13-Crowdfunding.postman_collection.json`
   - `postman/Team-13-Local.postman_environment.json`
4. Select environment: Top-right dropdown → **Team-13-Local**
5. Run collection: **Runner** button → Select collection → **Run**

---

## 📞 Support

**Files included:**
- `runner.js` - Node.js runner (recommended)
- `runner.bat` - Windows batch script
- `runner.ps1` - PowerShell script
- `POSTMAN-RUNNER-GUIDE.md` - Full documentation
- `Team-13-Crowdfunding.postman_collection.json` - API requests
- `Team-13-Local.postman_environment.json` - Environment variables

For issues, check:
1. Laravel server status: `curl http://localhost:8000/api/campaigns`
2. Database connectivity: `php artisan tinker`
3. Application logs: `docker compose logs app` or `storage/logs/`

