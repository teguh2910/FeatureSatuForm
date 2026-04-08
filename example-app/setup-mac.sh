#!/usr/bin/env bash

set -euo pipefail

APP_DIR="app"
SQL_SA_PASSWORD="YourStrong!Passw0rd2026"
NGINX_PORT="18080"
SQL_PORT="14330"

while [[ $# -gt 0 ]]; do
    case "$1" in
        --app-dir)
            APP_DIR="$2"
            shift 2
            ;;
        --sql-sa-password)
            SQL_SA_PASSWORD="$2"
            shift 2
            ;;
        --nginx-port)
            NGINX_PORT="$2"
            shift 2
            ;;
        --sql-port)
            SQL_PORT="$2"
            shift 2
            ;;
        *)
            echo "Unknown argument: $1" >&2
            exit 1
            ;;
    esac
done

SCRIPT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PACKAGE_ROOT="$(cd "$SCRIPT_ROOT/.." && pwd)"
COMPOSE_FILE="$SCRIPT_ROOT/docker-compose.yml"
APP_PATH="$SCRIPT_ROOT/$APP_DIR"
APP_COMPOSER_FILE="$APP_PATH/composer.json"
DOCKER_PATH="$SCRIPT_ROOT/docker"
DOCKER_PHP_PATH="$DOCKER_PATH/php"
DOCKER_NGINX_PATH="$DOCKER_PATH/nginx"
DOCKER_PHP_DOCKERFILE="$DOCKER_PHP_PATH/Dockerfile"
DOCKER_NGINX_CONF="$DOCKER_NGINX_PATH/default.conf"

ensure_command() {
    local command_name="$1"
    if ! command -v "$command_name" >/dev/null 2>&1; then
        echo "$command_name is required but was not found in PATH." >&2
        exit 1
    fi
}

set_env_value() {
    local file_path="$1"
    local key="$2"
    local value="$3"
    local tmp_file
    tmp_file="$(mktemp)"

    if [[ -f "$file_path" ]]; then
        awk -v key="$key" -v value="$value" '
            BEGIN { found = 0 }
            $0 ~ "^" key "=" {
                print key "=" value
                found = 1
                next
            }
            { print }
            END {
                if (!found) {
                    print key "=" value
                }
            }
        ' "$file_path" > "$tmp_file"
        mv "$tmp_file" "$file_path"
    else
        printf '%s=%s\n' "$key" "$value" > "$file_path"
        rm -f "$tmp_file"
    fi
}

update_app_composer_json() {
    local file_path="$1"
    local package_root="$2"
    php -r '
        $path = $argv[1];
        $packageRoot = $argv[2];
        $composerJson = json_decode(file_get_contents($path), true);
        if (!is_array($composerJson)) {
            fwrite(STDERR, "Failed to parse composer.json\n");
            exit(1);
        }

        $composerJson["repositories"] = [[
            "type" => "path",
            "url" => $packageRoot,
            "options" => ["symlink" => true],
        ]];
        $composerJson["minimum-stability"] = "dev";
        $composerJson["prefer-stable"] = true;

        file_put_contents($path, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
    ' "$file_path" "$package_root"
}

ensure_sqlsrv_config() {
    local file_path="$1"
    if [[ ! -f "$file_path" ]]; then
        return
    fi

    sed -i '' -E "s#^[[:space:]]*// 'encrypt' => env\('DB_ENCRYPT', 'yes'\),#            'encrypt' => env('DB_ENCRYPT', 'yes'),#" "$file_path"
    sed -i '' -E "s#^[[:space:]]*// 'trust_server_certificate' => env\('DB_TRUST_SERVER_CERTIFICATE', 'false'\),#            'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),#" "$file_path"
}

ensure_command docker
ensure_command composer
ensure_command php

mkdir -p "$DOCKER_PHP_PATH" "$DOCKER_NGINX_PATH"

cat > "$DOCKER_PHP_DOCKERFILE" <<'EOF'
FROM php:8.1-fpm-bookworm

RUN apt-get update \
        && apt-get install -y --no-install-recommends \
                curl \
                gnupg2 \
        ca-certificates \
                git \
                unzip \
                zip \
                libzip-dev \
                unixodbc-dev \
    && ARCH="$(dpkg --print-architecture)" \
        && curl -sSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && echo "deb [arch=${ARCH} signed-by=/usr/share/keyrings/microsoft-prod.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list \
        && apt-get update \
        && ACCEPT_EULA=Y apt-get install -y --no-install-recommends msodbcsql18 \
        && pecl install sqlsrv-5.12.0 pdo_sqlsrv-5.12.0 \
        && docker-php-ext-enable sqlsrv pdo_sqlsrv \
        && docker-php-ext-install zip \
        && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
        && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
EOF

cat > "$DOCKER_NGINX_CONF" <<'EOF'
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
EOF

cat > "$COMPOSE_FILE" <<EOF
services:
    php:
        build:
            context: ./docker/php
            dockerfile: Dockerfile
        volumes:
            - ./app:/var/www/html
            - ${PACKAGE_ROOT}:${PACKAGE_ROOT}
        restart: unless-stopped

    nginx:
        image: nginx:1.27-alpine
        depends_on:
            - php
        ports:
            - "${NGINX_PORT}:80"
        volumes:
            - ./app:/var/www/html:ro
            - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
        restart: unless-stopped

    sqlserver:
        image: mcr.microsoft.com/azure-sql-edge:latest
        environment:
            ACCEPT_EULA: "Y"
            MSSQL_SA_PASSWORD: "${SQL_SA_PASSWORD}"
        ports:
            - "${SQL_PORT}:1433"
        volumes:
            - mssql-data:/var/opt/mssql
        restart: unless-stopped

volumes:
    mssql-data:
EOF

cd "$SCRIPT_ROOT"

if [[ ! -f "$APP_COMPOSER_FILE" ]]; then
    if [[ -d "$APP_PATH" ]]; then
        echo "Found incomplete app directory in $APP_DIR. Recreating ..."
        rm -rf "$APP_PATH"
    fi

    echo "Creating Laravel app in $APP_DIR ..."
    composer create-project laravel/laravel "$APP_DIR" "^10.0" --no-interaction
fi

if docker compose version >/dev/null 2>&1; then
    docker compose -f "$COMPOSE_FILE" down --remove-orphans
    docker compose -f "$COMPOSE_FILE" up -d
elif command -v docker-compose >/dev/null 2>&1; then
    docker-compose -f "$COMPOSE_FILE" down --remove-orphans
    docker-compose -f "$COMPOSE_FILE" up -d
else
    echo "Docker Compose is required but was not found." >&2
    exit 1
fi

update_app_composer_json "$APP_COMPOSER_FILE" "$PACKAGE_ROOT"

if [[ -d "$APP_PATH/vendor/teguh/feature-satu-form" ]]; then
    rm -rf "$APP_PATH/vendor/teguh/feature-satu-form"
fi

composer --working-dir "$APP_DIR" require teguh/feature-satu-form:@dev --no-interaction --prefer-install=dist

cd "$APP_PATH"

if [[ ! -f ".env" ]]; then
    cp .env.example .env
fi

set_env_value ".env" "DB_CONNECTION" "sqlsrv"
set_env_value ".env" "DB_HOST" "sqlserver"
set_env_value ".env" "DB_PORT" "1433"
set_env_value ".env" "DB_DATABASE" "master"
set_env_value ".env" "DB_USERNAME" "sa"
set_env_value ".env" "DB_PASSWORD" "$SQL_SA_PASSWORD"
set_env_value ".env" "DB_ENCRYPT" "yes"
set_env_value ".env" "DB_TRUST_SERVER_CERTIFICATE" "true"
set_env_value ".env" "APP_URL" "http://localhost:$NGINX_PORT"
ensure_sqlsrv_config "config/database.php"

php artisan key:generate

echo "Done. Next steps:"
echo "1. Open app: http://localhost:${NGINX_PORT}"
echo "2. Run migrations in container: docker compose exec php php artisan migrate"
echo "3. Verify containers: docker compose ps"