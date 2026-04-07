param(
    [string]$AppDir = "app"
)

$ErrorActionPreference = "Stop"

if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    throw "Composer is required but was not found in PATH."
}

if (-not (Test-Path $AppDir)) {
    Write-Host "Creating Laravel app in $AppDir ..."
    composer create-project laravel/laravel $AppDir "^10.0"
}

Set-Location $AppDir

composer config repositories.feature-satu-form path ../..
composer config minimum-stability dev
composer config prefer-stable true
composer require teguh/feature-satu-form:@dev

if (-not (Test-Path ".env")) {
    Copy-Item .env.example .env
}

php artisan key:generate

Write-Host "Done. Next steps:"
Write-Host "1. Update DB settings in example-app/$AppDir/.env"
Write-Host "2. Run: php artisan migrate"
Write-Host "3. Run: php artisan serve"
