'use strict';

function randomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

module.exports = {
  setDynamicDonationPayload,
  setAuthPayload,
  setCampaignReadVars,
};

function setDynamicDonationPayload(userContext, events, done) {
  const campaignId = Number(process.env.STRESS_CAMPAIGN_ID || 1);
  const amountMin = Number(process.env.STRESS_AMOUNT_MIN || 10000);
  const amountMax = Number(process.env.STRESS_AMOUNT_MAX || 1000000);
  const isAnonymous = Math.random() < 0.35;

  userContext.vars.idempotency_key = `load-${Date.now()}-${Math.random().toString(16).slice(2)}`;
  userContext.vars.campaign_id = campaignId;
  userContext.vars.amount = randomInt(amountMin, amountMax);
  userContext.vars.is_anonymous = isAnonymous;
  userContext.vars.donor_name = isAnonymous
    ? 'Anonymous'
    : `LoadUser-${Math.random().toString(36).slice(2, 9)}`;

  return done();
}

function setAuthPayload(userContext, events, done) {
  userContext.vars.test_email = process.env.STRESS_USER_EMAIL || 'personal@test.local';
  userContext.vars.test_password = process.env.STRESS_USER_PASSWORD || 'Test12345!';
  return done();
}

function setCampaignReadVars(userContext, events, done) {
  const maxPage = Number(process.env.STRESS_CAMPAIGN_MAX_PAGE || 1333);
  const statusMaxPage = Number(process.env.STRESS_CAMPAIGN_STATUS_MAX_PAGE || 933);
  const maxCampaignId = Number(process.env.STRESS_CAMPAIGN_MAX_ID || 20000);

  userContext.vars.campaign_page = randomInt(1, maxPage);
  userContext.vars.campaign_status_page = randomInt(1, statusMaxPage);
  userContext.vars.campaign_id = randomInt(1, maxCampaignId);

  return done();
}
