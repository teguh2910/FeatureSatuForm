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
$dockerPath = Join-Path $scriptRoot "docker"
$dockerPhpPath = Join-Path $dockerPath "php"
$dockerNginxPath = Join-Path $dockerPath "nginx"
$dockerPhpDockerfile = Join-Path $dockerPhpPath "Dockerfile"
$dockerNginxConf = Join-Path $dockerNginxPath "default.conf"

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

    function Update-AppComposerJson {
        param(
            [string]$Path
        )

        $composerJson = Get-Content -Path $Path -Raw | ConvertFrom-Json
        $repo = [PSCustomObject]@{
            type = "path"
            url = "../.."
            options = [PSCustomObject]@{
                symlink = $false
            }
        }

        $composerJson.repositories = @($repo)
        $composerJson."minimum-stability" = "dev"
        $composerJson."prefer-stable" = $true

        $composerJson | ConvertTo-Json -Depth 20 | Set-Content -Path $Path
    }

    function Ensure-SqlSrvConfig {
        param(
            [string]$Path
        )

        if (-not (Test-Path $Path)) {
            return
        }

        $content = Get-Content -Path $Path -Raw
        $content = $content -replace "\s*// 'encrypt' => env\('DB_ENCRYPT', 'yes'\),", "            'encrypt' => env('DB_ENCRYPT', 'yes'),"
        $content = $content -replace "\s*// 'trust_server_certificate' => env\('DB_TRUST_SERVER_CERTIFICATE', 'false'\),", "            'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),"
        Set-Content -Path $Path -Value $content
    }

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
        throw "Docker is required but was not found in PATH."
}

if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    throw "Composer is required but was not found in PATH."
}

New-Item -ItemType Directory -Force -Path $dockerPhpPath | Out-Null
New-Item -ItemType Directory -Force -Path $dockerNginxPath | Out-Null

@'
FROM php:8.1-fpm-bookworm

RUN apt-get update \
        && apt-get install -y --no-install-recommends \
                curl \
                gnupg2 \
                git \
                unzip \
                zip \
                libzip-dev \
                unixodbc-dev \
        && curl -sSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
        && echo "deb [arch=amd64 signed-by=/usr/share/keyrings/microsoft-prod.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list \
        && apt-get update \
        && ACCEPT_EULA=Y apt-get install -y --no-install-recommends msodbcsql18 \
        && pecl install sqlsrv-5.12.0 pdo_sqlsrv-5.12.0 \
        && docker-php-ext-enable sqlsrv pdo_sqlsrv \
        && docker-php-ext-install zip \
        && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
'@ | Set-Content -Path $dockerPhpDockerfile

@'
server {
        listen 80;
        server_name _;
        root /var/www/html/public;
        index index.php index.html;

        location / {
                try_files $uri $uri/ /index.php?$query_string;
        }

        location ~ \.php$ {
                include fastcgi_params;
                fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
                fastcgi_index index.php;
                fastcgi_pass php:9000;
        }

        location ~ /\.ht {
                deny all;
        }
}
'@ | Set-Content -Path $dockerNginxConf

@"
services:
    php:
        build:
            context: .
            dockerfile: ./docker/php/Dockerfile
        volumes:
            - ./app:/var/www/html
        restart: unless-stopped

    nginx:
        image: nginx:1.27-alpine
        depends_on:
            - php
        ports:
            - "${NginxPort}:80"
        volumes:
            - ./app:/var/www/html:ro
            - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
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

Update-AppComposerJson -Path $appComposerFile

if (Test-Path (Join-Path $appPath "vendor/teguh/feature-satu-form")) {
    Remove-Item -Path (Join-Path $appPath "vendor/teguh/feature-satu-form") -Recurse -Force
}

composer --working-dir $AppDir require teguh/feature-satu-form:@dev --no-interaction --prefer-install=dist
Assert-LastExitCode -Action "composer require"

Set-Location $AppDir

if (-not (Test-Path ".env")) {
    Copy-Item .env.example .env
}

Set-EnvValue -Path ".env" -Key "DB_CONNECTION" -Value "sqlsrv"
Set-EnvValue -Path ".env" -Key "DB_HOST" -Value "sqlserver"
Set-EnvValue -Path ".env" -Key "DB_PORT" -Value "1433"
Set-EnvValue -Path ".env" -Key "DB_DATABASE" -Value "master"
Set-EnvValue -Path ".env" -Key "DB_USERNAME" -Value "sa"
Set-EnvValue -Path ".env" -Key "DB_PASSWORD" -Value "$SqlSaPassword"
Set-EnvValue -Path ".env" -Key "DB_ENCRYPT" -Value "yes"
Set-EnvValue -Path ".env" -Key "DB_TRUST_SERVER_CERTIFICATE" -Value "true"
Set-EnvValue -Path ".env" -Key "APP_URL" -Value "http://localhost:$NginxPort"
Ensure-SqlSrvConfig -Path "config/database.php"

php artisan key:generate
Assert-LastExitCode -Action "php artisan key:generate"

Write-Host "Done. Next steps:"
Write-Host "1. Open app: http://localhost:$NginxPort"
Write-Host "2. Run migrations in container: docker compose exec php php artisan migrate"
Write-Host "3. Verify containers: docker compose ps"
