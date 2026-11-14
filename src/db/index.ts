/**
 * FACTORÍA PRINCIPAL DEL SISTEMA DE BASE DE DATOS
 * ===============================================
 * Centraliza la inicialización y el acceso al adaptador de persistencia real.
 * Por defecto opera con SQLite persistente en disco (`data/pollofeliz.sqlite`)
 * o PostgreSQL cuando se configura `DB_CONNECTION=postgres`.
 */

import { IDatabaseAdapter } from './DatabaseAdapter.js';
import { SQLiteDatabaseAdapter } from './adapters/SQLiteAdapter.js';
import { PostgresDatabaseAdapter } from './adapters/PostgresAdapter.js';

export * from './schema.js';
export * from './DatabaseAdapter.js';
export { SQLiteDatabaseAdapter } from './adapters/SQLiteAdapter.js';

let databaseInstance: IDatabaseAdapter | null = null;

export const getDatabase = (): IDatabaseAdapter => {
  if (databaseInstance) {
    return databaseInstance;
  }

  const connectionType = (process.env.DB_CONNECTION || 'sqlite').toLowerCase();

  switch (connectionType) {
    case 'postgres':
    case 'postgresql':
    case 'supabase':
      databaseInstance = new PostgresDatabaseAdapter();
      break;

    case 'sqlite':
    default:
      databaseInstance = new SQLiteDatabaseAdapter();
      break;
  }

  databaseInstance.connect().catch((err) => {
    console.error('❌ Error al inicializar base de datos:', err);
  });

  return databaseInstance;
};
