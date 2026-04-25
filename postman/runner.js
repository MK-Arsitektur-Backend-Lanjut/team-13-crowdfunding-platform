/**
 * Postman Runner for Team 13 Crowdfunding API
 * 
 * This script automates the sequential execution of API tests using Newman (Postman CLI)
 * It runs comprehensive smoke tests for Campaign Management and Donation Processing modules
 * 
 * Usage:
 *   npm install -g newman
 *   node postman/runner.js
 * 
 * Or with custom options:
 *   node postman/runner.js --delay 500 --timeout 5000
 */

import { spawnSync } from 'child_process';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Configuration
const config = {
  collection: path.join(__dirname, 'Team-13-Crowdfunding.postman_collection.json'),
  environment: path.join(__dirname, 'Team-13-Local.postman_environment.json'),
  delay: 100, // Delay between requests (ms)
  timeout: 5000, // Request timeout (ms)
  stopOnError: false, // Continue even if error occurs
  insecure: false // Allow self-signed certificates
};

// Parse command line arguments
const args = process.argv.slice(2);
args.forEach((arg, idx) => {
  if (arg === '--delay' && args[idx + 1]) {
    config.delay = parseInt(args[idx + 1]);
  }
  if (arg === '--timeout' && args[idx + 1]) {
    config.timeout = parseInt(args[idx + 1]);
  }
  if (arg === '--stop-on-error') {
    config.stopOnError = true;
  }
  if (arg === '--insecure') {
    config.insecure = true;
  }
});

// Verify files exist
if (!fs.existsSync(config.collection)) {
  console.error(`❌ Collection file not found: ${config.collection}`);
  process.exit(1);
}

if (!fs.existsSync(config.environment)) {
  console.error(`❌ Environment file not found: ${config.environment}`);
  process.exit(1);
}

console.log('🚀 Starting Postman Test Runner...\n');
console.log('📋 Configuration:');
console.log(`   Collection: ${path.basename(config.collection)}`);
console.log(`   Environment: ${path.basename(config.environment)}`);
console.log(`   Request Delay: ${config.delay}ms`);
console.log(`   Request Timeout: ${config.timeout}ms`);
console.log(`   Stop on Error: ${config.stopOnError ? 'Yes' : 'No'}`);
console.log('\n' + '='.repeat(70) + '\n');

// Build Newman command
const newmanArgs = [
  'run',
  config.collection,
  '-e', config.environment,
  '--delay-request', config.delay.toString(),
  '--timeout', config.timeout.toString(),
  '-r', 'cli,json',
  '--reporter-json-export', path.join(__dirname, 'test-results.json')
];

if (config.stopOnError) {
  newmanArgs.push('--bail');
}

if (config.insecure) {
  newmanArgs.push('--insecure');
}

// Run Newman via CLI
const result = spawnSync('newman', newmanArgs, {
  stdio: 'inherit',
  shell: process.platform === 'win32' // Use shell on Windows
});

// Handle results
console.log('\n' + '='.repeat(70));

if (result.error) {
  console.error(`\n❌ Error running Newman: ${result.error.message}`);
  if (result.error.code === 'ENOENT') {
    console.error('\nNewman is not installed. Please run:');
    console.error('  npm install -g newman\n');
  }
  process.exit(1);
}

// Check if test results file was created
const resultsFile = path.join(__dirname, 'test-results.json');
if (fs.existsSync(resultsFile)) {
  try {
    const results = JSON.parse(fs.readFileSync(resultsFile, 'utf8'));
    
    if (results.run && results.run.stats) {
      const stats = results.run.stats;
      console.log('\n📈 Test Summary:');
      console.log(`   Total Requests: ${stats.requests?.total || 0}`);
      console.log(`   Failed Requests: ${results.run.failures?.length || 0}`);
      console.log(`   Total Tests: ${stats.tests?.total || 0}`);
      console.log(`   Failed Tests: ${stats.assertions?.failed || 0}`);
      
      if (stats.tests.total > 0) {
        const passRate = Math.round(
          ((stats.tests.total - (stats.assertions?.failed || 0)) / stats.tests.total) * 100
        );
        console.log(`   Pass Rate: ${passRate}%`);
      }
      
      const duration = (stats.timings?.completed - stats.timings?.started) / 1000;
      if (duration) {
        console.log(`   Total Duration: ${duration.toFixed(2)}s`);
      }
    }
  } catch (e) {
    // Results file exists but couldn't be parsed
  }
  
  console.log(`\n✅ Results exported to: ${path.relative(process.cwd(), resultsFile)}`);
}

// Exit with appropriate code
if (result.status === 0) {
  console.log('\n✅ All Tests Passed!\n');
  process.exit(0);
} else {
  console.log('\n⚠️  Some tests failed or encountered errors.\n');
  process.exit(result.status || 1);
}
