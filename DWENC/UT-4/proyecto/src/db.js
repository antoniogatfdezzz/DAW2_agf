import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { Low } from 'lowdb';
import { JSONFile } from 'lowdb/node';
import { customAlphabet } from 'nanoid';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const dataDir = path.join(__dirname, '..', 'data');
const dbPath = path.join(dataDir, 'db.json');

if (!fs.existsSync(dataDir)) fs.mkdirSync(dataDir, { recursive: true });

const nanoid = customAlphabet('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz', 10);

export async function getDb() {
  const adapter = new JSONFile(dbPath);
  const db = new Low(adapter, { libro: [], editorial: [], autor: [] });
  await db.read();
  if (!db.data || Object.keys(db.data).length === 0) {
    db.data = { libro: [], editorial: [], autor: [] };
    // semillas
    db.data.editorial.push(
      { id: 1, nombre: 'Alfaguara' },
      { id: 2, nombre: 'Planeta' },
      { id: 3, nombre: 'Anagrama' },
      { id: 4, nombre: 'Tusquets' }
    );
    db.data.autor.push(
      { id: 1, nombre: 'Gabriel García Márquez' },
      { id: 2, nombre: 'Isabel Allende' },
      { id: 3, nombre: 'Jorge Luis Borges' },
      { id: 4, nombre: 'Miguel de Cervantes' }
    );
    db.data.libro.push(
      { id: 1, titulo: 'Cien Años de Soledad', editorial_id: 1, anio: 1967, edicion: 1, autor_id: 1, web: 'https://ejemplo.com/cien-anos' },
      { id: 2, titulo: 'La Casa de los Espíritus', editorial_id: 2, anio: 1982, edicion: 1, autor_id: 2, web: 'https://ejemplo.com/casa-espiritus' },
      { id: 3, titulo: 'El Aleph', editorial_id: 3, anio: 1945, edicion: 3, autor_id: 3, web: 'https://ejemplo.com/aleph' },
      { id: 4, titulo: 'Don Quijote de la Mancha', editorial_id: 4, anio: 1605, edicion: 10, autor_id: 4, web: 'https://ejemplo.com/quijote' }
    );
    await db.write();
  }
  // utilidades
  db.util = {
    nextId(collection) {
      // id numérico incremental
      const items = db.data[collection] || [];
      return items.length ? Math.max(...items.map(i => i.id)) + 1 : 1;
    },
    nano() { return nanoid(); }
  };
  return db;
}
