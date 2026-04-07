param(
        [string]$AppDir = "app",
        [string]$SqlSaPassword = "YourStrong!Passw0rd2026",
        [int]$NginxPort = 18080,
    [int]$SqlPort = 14330
)

$ErrorActionPreference = "Stop"

$scriptRoot = $PSScriptRoot
$composeFile = Join-Path $scriptRoot "docker-compose.yml"
$appPath = Join-Path $scriptRoot $AppDir
$appComposerFile = Join-Path $appPath "composer.json"

function Assert-LastExitCode {
    param(
        [string]$Action
    )

    if ($LASTEXITCODE -ne 0) {
        throw "$Action failed with exit code $LASTEXITCODE."
    }
}

function Set-EnvValue {
        param(
                [string]$Path,
                [string]$Key,
                [string]$Value
        )

        $pattern = "^(" + [regex]::Escape($Key) + ")=.*$"
        if (Select-String -Path $Path -Pattern $pattern -Quiet) {
                (Get-Content $Path) -replace $pattern, ("$Key=$Value") | Set-Content $Path
        }
        else {
                Add-Content -Path $Path -Value ("$Key=$Value")
        }
}

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
        throw "Docker is required but was not found in PATH."
}

if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    throw "Composer is required but was not found in PATH."
}

@"
services:
    nginx:
        image: nginx:1.27-alpine
        ports:
            - "${NginxPort}:80"
        volumes:
            - ./app/public:/usr/share/nginx/html:ro
        restart: unless-stopped

    sqlserver:
        image: mcr.microsoft.com/mssql/server:2022-latest
        environment:
            ACCEPT_EULA: "Y"
            MSSQL_PID: "Developer"
            MSSQL_SA_PASSWORD: "${SqlSaPassword}"
        ports:
            - "${SqlPort}:1433"
        volumes:
            - mssql-data:/var/opt/mssql
        restart: unless-stopped

volumes:
    mssql-data:
"@ | Set-Content -Path $composeFile

Set-Location $scriptRoot
if (-not (Test-Path $appComposerFile)) {
    if (Test-Path $appPath) {
        Write-Host "Found incomplete app directory in $AppDir. Recreating ..."
        Remove-Item -Path $appPath -Recurse -Force
    }

    Write-Host "Creating Laravel app in $AppDir ..."
    composer create-project laravel/laravel $AppDir "^10.0" --no-interaction
    Assert-LastExitCode -Action "composer create-project"
}

docker compose version *> $null
if ($LASTEXITCODE -eq 0) {
    docker compose -f $composeFile down --remove-orphans
    Assert-LastExitCode -Action "docker compose down"
    docker compose -f $composeFile up -d
    Assert-LastExitCode -Action "docker compose up"
}
elseif (Get-Command docker-compose -ErrorAction SilentlyContinue) {
    docker-compose -f $composeFile down --remove-orphans
    Assert-LastExitCode -Action "docker-compose down"
    docker-compose -f $composeFile up -d
    Assert-LastExitCode -Action "docker-compose up"
}
else {
    throw "Docker Compose is required but was not found."
}

composer --working-dir $AppDir config repositories.feature-satu-form path ../.. --no-interaction
Assert-LastExitCode -Action "composer config repositories"
composer --working-dir $AppDir config minimum-stability dev --no-interaction
Assert-LastExitCode -Action "composer config minimum-stability"
composer --working-dir $AppDir config prefer-stable true --no-interaction
Assert-LastExitCode -Action "composer config prefer-stable"
composer --working-dir $AppDir require teguh/feature-satu-form:@dev --no-interaction
Assert-LastExitCode -Action "composer require"

Set-Location $AppDir

if (-not (Test-Path ".env")) {
    Copy-Item .env.example .env
}

Set-EnvValue -Path ".env" -Key "DB_CONNECTION" -Value "sqlsrv"
Set-EnvValue -Path ".env" -Key "DB_HOST" -Value "127.0.0.1"
Set-EnvValue -Path ".env" -Key "DB_PORT" -Value "$SqlPort"
Set-EnvValue -Path ".env" -Key "DB_DATABASE" -Value "master"
Set-EnvValue -Path ".env" -Key "DB_USERNAME" -Value "sa"
Set-EnvValue -Path ".env" -Key "DB_PASSWORD" -Value "$SqlSaPassword"

php artisan key:generate
Assert-LastExitCode -Action "php artisan key:generate"

Write-Host "Done. Next steps:"
Write-Host "1. Verify Docker containers are running: docker ps"
Write-Host "2. Run: php artisan migrate"
Write-Host "3. Run: php artisan serve"
