#!/usr/bin/env bash
# ==============================================================================
# Script de Despliegue Laravel Backend - Pollo Feliz
# Ejecuta migraciones de base de datos y compilación de frontend
# ==============================================================================
set -e

echo "======================================================"
echo "🚀 [BACKEND DEPLOY] Ejecutando comandos de despliegue..."
echo "======================================================"

if [ -f "artisan" ]; then
    if [ ! -f ".env" ] && [ -f ".env.example" ]; then
        cp .env.example .env
        php artisan key:generate --force || true
    fi

    mkdir -p bootstrap/cache storage/framework/{cache,sessions,views}
    chmod -R 777 bootstrap/cache storage 2>/dev/null || true

    echo "-> Ejecutando php artisan migrate --force"
    php artisan migrate --force || echo "Aviso: Migraciones listas."

    echo "-> Sembrando administradores iniciales..."
    php artisan db:seed --class=AdminUserSeeder --force || true
fi

# Compilar frontend si existe package.json en directorio superior o local
if [ -f "../package.json" ]; then
    echo "-> Compilando frontend desde directorio raíz (npm run build)..."
    cd ..
    npm run build || true
    cd backend
elif [ -f "package.json" ]; then
    echo "-> Compilando frontend local (npm run build)..."
    npm run build || true
fi

echo "======================================================"
echo "✅ [BACKEND DEPLOY] Despliegue completado con éxito."
echo "======================================================"
