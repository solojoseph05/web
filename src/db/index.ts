/**
 * FACTORÍA PRINCIPAL DEL SISTEMA DE BASE DE DATOS
 * ===============================================
 * Centraliza la inicialización y el acceso al adaptador de persistencia.
 */

import { IDatabaseAdapter } from './DatabaseAdapter.js';
import { MemoryDatabaseAdapter } from './adapters/MemoryAdapter.js';
import { PostgresDatabaseAdapter } from './adapters/PostgresAdapter.js';

export * from './schema.js';
export * from './DatabaseAdapter.js';

let databaseInstance: IDatabaseAdapter | null = null;

export const getDatabase = (): IDatabaseAdapter => {
  if (databaseInstance) {
    return databaseInstance;
  }

  const connectionType = (process.env.DB_CONNECTION || 'memory').toLowerCase();

  switch (connectionType) {
    case 'postgres':
    case 'postgresql':
    case 'supabase':
      databaseInstance = new PostgresDatabaseAdapter();
      break;

    case 'memory':
    default:
      databaseInstance = new MemoryDatabaseAdapter();
      break;
  }

  databaseInstance.connect().catch((err) => {
    console.error('❌ Error al inicializar base de datos:', err);
  });

  return databaseInstance;
};
