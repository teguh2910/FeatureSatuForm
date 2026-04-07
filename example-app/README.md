# Example App (No Main Repo Needed)

This folder helps developers work on FeatureSatuForm without cloning the confidential main repository.

## Quick Start (Windows / PowerShell)

1. Open PowerShell in this folder.
2. Run setup script:

   powershell -ExecutionPolicy Bypass -File setup.ps1

3. Move into generated app:

   cd app

4. Set database credentials in .env.
5. Run migrations:

   php artisan migrate

6. Run app:

   php artisan serve

## What setup.ps1 does

- Creates a clean Laravel 10 app in example-app/app
- Adds local package repository (path to this package root)
- Requires teguh/feature-satu-form in the example app
- Generates APP_KEY

## Notes

- This app is for package development/testing only.
- Do not commit the generated app folder unless needed.
