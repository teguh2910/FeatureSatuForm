# Example App (No Main Repo Needed)

This folder helps developers work on FeatureSatuForm without cloning the confidential main repository.

## Quick Start (Windows / PowerShell)

1. Open PowerShell in this folder.
2. Run setup script:

   powershell -ExecutionPolicy Bypass -File setup.ps1

3. Move into generated app:

   cd app

4. The script will automatically:
   - build PHP-FPM image (with sqlsrv/pdo_sqlsrv)
   - start Docker containers for php, nginx, and sqlserver
   - configure .env to use sqlsrv on host sqlserver:1433

5. Run migrations:

   docker compose exec php php artisan migrate

6. Run app:

   open http://localhost:18080

## What setup.ps1 does

- Creates a clean Laravel 10 app in example-app/app
- Adds local package repository (path to this package root)
- Requires teguh/feature-satu-form in the example app
- Creates docker-compose.yml (php + nginx + sqlserver)
- Runs docker compose up -d automatically
- Sets Laravel DB_* values in .env for SQL Server container
- Generates APP_KEY

## Quick Start (macOS / Apple Silicon)

1. Open Terminal in this folder.
2. Run the macOS script:

   bash setup-mac.sh

3. Move into generated app:

   cd app

4. The script will automatically:
   - build PHP-FPM image (with sqlsrv/pdo_sqlsrv)
   - start Docker containers for php, nginx, and azure-sql-edge
   - configure .env to use sqlsrv on host sqlserver:1433

5. Run migrations:

   docker compose exec php php artisan migrate

6. Run app:

   open http://localhost:18080

## What setup-mac.sh does

- Creates a clean Laravel 10 app in example-app/app
- Adds local package repository (path to this package root)
- Requires teguh/feature-satu-form in the example app
- Creates docker-compose.yml (php + nginx + azure-sql-edge)
- Runs docker compose up -d automatically
- Sets Laravel DB_* values in .env for SQL Edge container
- Generates APP_KEY

## Optional Parameters

- Use custom SA password:

   powershell -ExecutionPolicy Bypass -File setup.ps1 -SqlSaPassword "YourStrong!Passw0rd2026"

- Use custom ports:

   powershell -ExecutionPolicy Bypass -File setup.ps1 -NginxPort 8081 -SqlPort 14330

Default ports: nginx 18080, sqlserver 14330.

Useful commands:

- Start services:

   docker compose up -d

- Run Artisan from container:

   docker compose exec php php artisan <command>

## Notes

- This app is for package development/testing only.
- Do not commit the generated app folder unless needed.
