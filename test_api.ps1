$BASE_URL = "http://localhost:8000/api"

function Test-Endpoint {
    param($Method, $Url, $Body = $null, $Headers = @{}, $Label = "")
    Write-Host "`n=== $Label ===" -ForegroundColor Cyan
    try {
        $params = @{ Method = $Method; Uri = $Url; ContentType = "application/json"; Headers = $Headers; ErrorAction = "Stop" }
        if ($Body) { $params["Body"] = $Body }
        $res = Invoke-RestMethod @params
        Write-Host "STATUS: OK 200/201" -ForegroundColor Green
        $res | ConvertTo-Json -Depth 4
    } catch {
        $statusCode = $_.Exception.Response.StatusCode.value__
        Write-Host "STATUS: $statusCode" -ForegroundColor Red
        try {
            $reader = [System.IO.StreamReader]::new($_.Exception.Response.GetResponseStream())
            $body = $reader.ReadToEnd()
            Write-Host $body
        } catch {
            Write-Host $_.Exception.Message
        }
    }
}

# 1. Register
Test-Endpoint -Method POST -Url "$BASE_URL/auth/register" `
    -Body '{"name":"Org User","email":"org@example.com","password":"password123","role":"organization"}' `
    -Label "REGISTER ORG USER"

# 2. Login personal
$loginRes = Invoke-RestMethod -Method POST -Uri "$BASE_URL/auth/login" -ContentType "application/json" -Body '{"email":"test@example.com","password":"password123"}'
$TOKEN = $loginRes.data.token
Write-Host "`n=== LOGIN (personal) ===" -ForegroundColor Cyan
Write-Host "STATUS: OK" -ForegroundColor Green
Write-Host "Token: $($TOKEN.Substring(0,30))..."

$AUTH = @{ Authorization = "Bearer $TOKEN" }

# 3. GET Campaigns
Test-Endpoint -Method GET -Url "$BASE_URL/campaigns" -Label "GET CAMPAIGNS"

# 4. POST Campaign
Test-Endpoint -Method POST -Url "$BASE_URL/campaigns" -Headers $AUTH `
    -Body '{"title":"Bantu Korban Banjir","description":"Kampanye untuk membantu korban banjir","target_amount":5000000}' `
    -Label "CREATE CAMPAIGN"

# 5. GET Donation Categories
Test-Endpoint -Method GET -Url "$BASE_URL/donation-categories" -Label "GET DONATION CATEGORIES"

# 6. POST Donation Category
Test-Endpoint -Method POST -Url "$BASE_URL/donation-categories" `
    -Body '{"name":"Bencana Alam","description":"Kategori untuk bencana alam"}' `
    -Label "CREATE DONATION CATEGORY"

# 7. GET Campaigns again (should have 1)
Test-Endpoint -Method GET -Url "$BASE_URL/campaigns" -Label "GET CAMPAIGNS (after create)"

# 8. POST Donation (campaign_id=1)
Test-Endpoint -Method POST -Url "$BASE_URL/donations" `
    -Body '{"campaign_id":1,"amount":50000,"donor_name":"Test Donor","is_anonymous":false}' `
    -Label "CREATE DONATION"

# 9. Campaign Total
Test-Endpoint -Method GET -Url "$BASE_URL/campaigns/1/donations/total" -Label "GET CAMPAIGN TOTAL"

# 10. Donation Stats
Test-Endpoint -Method GET -Url "$BASE_URL/donations/stats" -Label "GET DONATION STATS"

# 11. Donation History (authenticated)
Test-Endpoint -Method GET -Url "$BASE_URL/donations/history" -Headers $AUTH -Label "GET DONATION HISTORY"

# 12. Logout
Test-Endpoint -Method POST -Url "$BASE_URL/auth/logout" -Headers $AUTH -Label "LOGOUT"

Write-Host "`n=== TEST SELESAI ===" -ForegroundColor Yellow
