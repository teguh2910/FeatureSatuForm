# Example App (No Main Repo Needed)

This folder helps developers work on FeatureSatuForm without cloning the confidential main repository.

## Quick Start (Windows / PowerShell)

1. Open PowerShell in this folder.
2. Run setup script:

   powershell -ExecutionPolicy Bypass -File setup.ps1

3. Move into generated app:

   cd app

4. The script will automatically:
   - start Docker containers for nginx and sqlserver
   - configure .env to use sqlsrv on localhost:14330 with user sa

5. Run migrations:

   php artisan migrate

6. Run app:

   php artisan serve

## What setup.ps1 does

- Creates a clean Laravel 10 app in example-app/app
- Adds local package repository (path to this package root)
- Requires teguh/feature-satu-form in the example app
- Creates docker-compose.yml (nginx + sqlserver) if missing
- Runs docker compose up -d automatically
- Sets Laravel DB_* values in .env for SQL Server container
- Generates APP_KEY

## Optional Parameters

- Use custom SA password:

   powershell -ExecutionPolicy Bypass -File setup.ps1 -SqlSaPassword "YourStrong!Passw0rd2026"

- Use custom ports:

   powershell -ExecutionPolicy Bypass -File setup.ps1 -NginxPort 8081 -SqlPort 14330

Default ports: nginx 18080, sqlserver 14330.

## Notes

- This app is for package development/testing only.
- Do not commit the generated app folder unless needed.
