/**
 * Script de post-instalación para backend (Laravel Cloud / Docker)
 * Garantiza que .laravel/cache y storage cuenten con permisos de lectura y escritura (0777).
 */
const fs = require('fs');
const path = require('path');

const targetDirs = [
  '.laravel/cache',
  'bootstrap/cache',
  'storage/framework/cache/data',
  'storage/framework/sessions',
  'storage/framework/views',
  'storage/logs',
  '/var/www/workspace/backend/.laravel/cache',
  '/var/www/workspace/backend/bootstrap/cache',
  '/var/www/workspace/backend/storage/framework/cache/data',
  '/var/www/workspace/backend/storage/framework/sessions',
  '/var/www/workspace/backend/storage/framework/views',
  '/var/www/workspace/backend/storage/logs',
];

targetDirs.forEach((dirPath) => {
  try {
    const resolved = path.isAbsolute(dirPath) ? dirPath : path.resolve(process.cwd(), dirPath);
    if (!fs.existsSync(resolved)) {
      fs.mkdirSync(resolved, { recursive: true, mode: 0o777 });
    }
    fs.chmodSync(resolved, 0o777);
  } catch (err) {
    // Continuar
  }
});
