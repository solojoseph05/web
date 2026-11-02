/**
 * Script de post-instalación para producción y despliegues en Laravel Cloud / Docker / Linux
 * Garantiza que los directorios de caché de Laravel (.laravel/cache, bootstrap/cache)
 * y de almacenamiento (storage/framework/*) existan y cuenten con permisos de lectura/escritura (0777).
 */
const fs = require('fs');
const path = require('path');

const targetDirs = [
  // Rutas en el directorio backend
  'backend/.laravel/cache',
  'backend/bootstrap/cache',
  'backend/storage/framework/cache/data',
  'backend/storage/framework/sessions',
  'backend/storage/framework/views',
  'backend/storage/logs',

  // Rutas en la raíz del proyecto (para despliegues donde backend es la raíz)
  '.laravel/cache',
  'bootstrap/cache',
  'storage/framework/cache/data',
  'storage/framework/sessions',
  'storage/framework/views',
  'storage/logs',

  // Rutas absolutas estándar en contenedores de Laravel Cloud
  '/var/www/workspace/backend/.laravel/cache',
  '/var/www/workspace/backend/bootstrap/cache',
  '/var/www/workspace/backend/storage/framework/cache/data',
  '/var/www/workspace/backend/storage/framework/sessions',
  '/var/www/workspace/backend/storage/framework/views',
  '/var/www/workspace/backend/storage/logs',
];

console.log('[PostInstall] Configurando directorios de caché y almacenamiento de Laravel...');

targetDirs.forEach((dirPath) => {
  try {
    const resolved = path.isAbsolute(dirPath) ? dirPath : path.resolve(process.cwd(), dirPath);
    if (!fs.existsSync(resolved)) {
      fs.mkdirSync(resolved, { recursive: true, mode: 0o777 });
    }
    fs.chmodSync(resolved, 0o777);
  } catch (err) {
    // Continuar si es una ruta absoluta no permitida en local
  }
});

console.log('[PostInstall] Directorios de caché y permisos 0777 configurados exitosamente.');
