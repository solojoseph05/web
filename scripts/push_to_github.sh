#!/usr/bin/env bash
# ==============================================================================
# Script para subir el proyecto Pollo Feliz Durango a tu repositorio de GitHub
# Uso:
#   bash scripts/push_to_github.sh https://github.com/TU_USUARIO/TU_REPOSITORIO.git
# ==============================================================================

set -e

REPO_URL="$1"

if [ -z "$REPO_URL" ]; then
  echo "⚠️ Por favor especifica la URL de tu repositorio de GitHub."
  echo "Ejemplo de uso:"
  echo "  bash scripts/push_to_github.sh https://github.com/tu-usuario/pollo-feliz-durango.git"
  exit 1
fi

echo "🚀 Configurando repositorio remoto de GitHub..."
if git remote | grep -q "origin"; then
  git remote set-url origin "$REPO_URL"
else
  git remote add origin "$REPO_URL"
fi

echo "📦 Asegurando rama principal 'main'..."
git branch -M main

echo "📤 Subiendo commits y código a GitHub..."
git push -u origin main

echo "✅ ¡Proyecto subido con éxito a GitHub!"
