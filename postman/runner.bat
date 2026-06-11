@echo off
REM =====================================================
REM Postman Test Runner for Team 13 Crowdfunding API
REM =====================================================
REM 
REM This script automates API testing using Newman (Postman CLI)
REM 
REM Usage:
REM   runner.bat                    - Run all tests with defaults
REM   runner.bat --delay 500        - Custom request delay (ms)
REM   runner.bat --timeout 8000     - Custom timeout (ms)
REM   runner.bat --stop-on-error    - Stop on first error
REM   runner.bat --insecure         - Allow self-signed certificates
REM

setlocal enabledelayedexpansion

REM Check if Node.js is installed
where /q node
if errorlevel 1 (
    echo.
    echo =====================================================
    echo ERROR: Node.js is not installed or not in PATH
    echo =====================================================
    echo.
    echo Please install Node.js from: https://nodejs.org/
    echo.
    pause
    exit /b 1
)

REM Check if Newman is installed
npm list -g newman >nul 2>&1
if errorlevel 1 (
    echo.
    echo =====================================================
    echo INFO: Installing Newman (Postman CLI)...
    echo =====================================================
    echo.
    call npm install -g newman
    if errorlevel 1 (
        echo ERROR: Failed to install Newman
        pause
        exit /b 1
    )
)

REM Get script directory
set SCRIPT_DIR=%~dp0
set COLLECTION=%SCRIPT_DIR%Team-13-Crowdfunding.postman_collection.json
set ENVIRONMENT=%SCRIPT_DIR%Team-13-Local.postman_environment.json

REM Check if collection file exists
if not exist "%COLLECTION%" (
    echo.
    echo =====================================================
    echo ERROR: Collection file not found
    echo =====================================================
    echo.
    echo Expected: %COLLECTION%
    echo.
    pause
    exit /b 1
)

REM Check if environment file exists
if not exist "%ENVIRONMENT%" (
    echo.
    echo =====================================================
    echo ERROR: Environment file not found
    echo =====================================================
    echo.
    echo Expected: %ENVIRONMENT%
    echo.
    pause
    exit /b 1
)

REM Display header
echo.
echo =====================================================
echo Team 13 Crowdfunding API - Postman Test Runner
echo =====================================================
echo.
echo Collection:  %COLLECTION%
echo Environment: %ENVIRONMENT%
echo.
echo Command: node %SCRIPT_DIR%runner.js %*
echo.
echo =====================================================
echo.

REM Run the test
call node "%SCRIPT_DIR%runner.js" %*

REM Capture exit code
set EXIT_CODE=%ERRORLEVEL%

REM Display footer
echo.
if %EXIT_CODE% equ 0 (
    echo =====================================================
    echo SUCCESS: All tests passed!
    echo =====================================================
) else (
    echo =====================================================
    echo WARNING: Some tests failed or had errors
    echo =====================================================
)
echo.
echo View detailed results: %SCRIPT_DIR%test-results.json
echo.

REM Exit with the same code as Newman
exit /b %EXIT_CODE%
