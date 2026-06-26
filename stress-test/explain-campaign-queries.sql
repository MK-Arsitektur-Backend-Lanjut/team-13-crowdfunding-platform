-- EXPLAIN ANALYZE for CampaignRepository list query (GET /api/campaigns)
-- Run: docker compose exec -T mysql mysql -uuser -ppassword crowdfunding < stress-test/explain-campaign-queries.sql

SET SESSION optimizer_trace='enabled=off';

-- Query 1: List data (all campaigns, page 1, per_page 15)
EXPLAIN ANALYZE
SELECT
    campaigns.id,
    campaigns.title,
    campaigns.description,
    campaigns.target_amount,
    campaigns.status,
    campaigns.created_at,
    campaigns.updated_at,
    donation_totals.total_amount AS total_donations
FROM campaigns
LEFT JOIN donation_totals ON campaigns.id = donation_totals.campaign_id
ORDER BY campaigns.created_at DESC
LIMIT 15 OFFSET 0;

-- Query 2: COUNT all campaigns
EXPLAIN ANALYZE
SELECT COUNT(*) AS aggregate FROM campaigns;

-- Query 3: List with status filter (GET /api/campaigns/status/aktif)
EXPLAIN ANALYZE
SELECT
    campaigns.id,
    campaigns.title,
    campaigns.description,
    campaigns.target_amount,
    campaigns.status,
    campaigns.created_at,
    campaigns.updated_at,
    donation_totals.total_amount AS total_donations
FROM campaigns
LEFT JOIN donation_totals ON campaigns.id = donation_totals.campaign_id
WHERE campaigns.status = 'aktif'
ORDER BY campaigns.created_at DESC
LIMIT 15 OFFSET 0;

-- Query 4: COUNT with status filter
EXPLAIN ANALYZE
SELECT COUNT(*) AS aggregate FROM campaigns WHERE status = 'aktif';
