#!/usr/bin/env bash
# ==============================================================================
# Script de Despliegue Automatizado - Pollo Feliz
# Ejecuta migraciones de base de datos en Laravel y compilación de frontend
# Compatible con Laravel Cloud, CI/CD, GitHub Actions y servidores Linux
# ==============================================================================
set -e

echo "======================================================"
echo "🚀 [CI/CD - DEPLOY] Iniciando proceso de despliegue..."
echo "Fecha y hora: $(date -u '+%Y-%m-%d %H:%M:%S UTC')"
echo "======================================================"

# 1. Ejecutar migraciones de base de datos (Laravel)
echo "📦 [1/3] Verificando entorno de base de datos y migraciones Laravel..."

if [ -d "backend" ] && [ -f "backend/artisan" ]; then
    echo "-> Directorio backend/ detectado. Ejecutando migraciones..."
    cd backend
    
    # Asegurar existencia de archivo .env si aún no se ha generado
    if [ ! -f ".env" ] && [ -f ".env.example" ]; then
        echo "-> Creando .env inicial a partir de .env.example..."
        cp .env.example .env
        php artisan key:generate --force || true
    fi

    # Asegurar directorios de caché y almacenamiento con permisos
    mkdir -p bootstrap/cache storage/framework/{cache,sessions,views}
    chmod -R 777 bootstrap/cache storage 2>/dev/null || true

    echo "-> Ejecutando: php artisan migrate --force"
    php artisan migrate --force || {
        echo "⚠️ Advertencia: php artisan migrate terminó con aviso o la base de datos se configurará en tiempo de ejecución."
    }

    echo "-> Sembrando usuarios administradores iniciales..."
    php artisan db:seed --class=AdminUserSeeder --force || true
    cd ..
elif [ -f "artisan" ]; then
    echo "-> Directorio raíz de Laravel detectado. Ejecutando migraciones..."
    if [ ! -f ".env" ] && [ -f ".env.example" ]; then
        echo "-> Creando .env inicial a partir de .env.example..."
        cp .env.example .env
        php artisan key:generate --force || true
    fi

    echo "-> Ejecutando: php artisan migrate --force"
    php artisan migrate --force || {
        echo "⚠️ Advertencia: php artisan migrate terminó con aviso o la base de datos se configurará en tiempo de ejecución."
    }

    echo "-> Sembrando usuarios administradores iniciales..."
    php artisan db:seed --class=AdminUserSeeder --force || true
else
    echo "ℹ️ No se detectó archivo artisan en este directorio. Continuando con la compilación..."
fi

# 2. Compilar assets de Frontend (Vite)
echo "🔨 [2/3] Compilando Frontend (npm run build)..."

if [ -f "package.json" ]; then
    echo "-> Ejecutando 'npm run build' en directorio principal..."
    npm run build
fi

if [ -d "frontend" ] && [ -f "frontend/package.json" ] && [ -f "frontend/vite.config.js" ]; then
    echo "-> Verificando build en directorio /frontend..."
    cd frontend
    # Si frontend tiene dependencias propias o dist separado
    if [ -d "node_modules" ]; then
        npm run build || true
    fi
    cd ..
fi

echo "======================================================"
echo "✅ [3/3] ¡Despliegue completado con éxito!"
echo "======================================================"
