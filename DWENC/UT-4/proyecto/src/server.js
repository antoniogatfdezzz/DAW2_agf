import express from 'express';
import session from 'express-session';
import cors from 'cors';
import path from 'path';
import { fileURLToPath } from 'url';
import { getDb } from './db.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const dbPromise = getDb();

app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(session({
  secret: 'dwenc-ut4-secret',
  resave: false,
  saveUninitialized: true,
  cookie: { maxAge: 1000 * 60 * 60 }
}));

// Static files
app.use(express.static(path.join(__dirname, '..', 'public')));

// Simple login to create a session (not enforced for simplicity)
app.post('/api/login', (req, res) => {
  req.session.user = { name: 'alumno' };
  res.json({ ok: true });
});

app.post('/api/logout', (req, res) => {
  req.session.destroy(() => {
    res.json({ ok: true });
  });
});

// Catalog endpoints
app.get('/api/editoriales', async (req, res) => {
  const db = await dbPromise;
  const rows = [...db.data.editorial].sort((a,b)=> a.nombre.localeCompare(b.nombre));
  res.json(rows);
});

app.get('/api/autores', async (req, res) => {
  const db = await dbPromise;
  const rows = [...db.data.autor].sort((a,b)=> a.nombre.localeCompare(b.nombre));
  res.json(rows);
});

// Helpers
function mapLibro(row) {
  return {
    id: row.id,
    titulo: row.titulo,
    editorial: { id: row.editorial_id, nombre: row.editorial_nombre },
    anio: row.anio,
    edicion: row.edicion,
    autor: { id: row.autor_id, nombre: row.autor_nombre },
    web: row.web
  };
}

// List with pagination and sort
app.get('/api/libros', async (req, res) => {
  const db = await dbPromise;
  const page = parseInt(req.query.page || '1', 10);
  const limit = Math.min(parseInt(req.query.limit || '10', 10), 50);
  const offset = (page - 1) * limit;
  const order = (req.query.order === 'desc') ? 'DESC' : 'ASC';
  const sortField = ['titulo','anio','edicion'].includes(req.query.sortField) ? req.query.sortField : 'titulo';

  const all = db.data.libro.map(l => ({
    ...l,
    editorial_nombre: db.data.editorial.find(e=>e.id===l.editorial_id)?.nombre || '',
    autor_nombre: db.data.autor.find(a=>a.id===l.autor_id)?.nombre || ''
  }));
  all.sort((a,b) => {
    const dir = order === 'DESC' ? -1 : 1;
    if (a[sortField] < b[sortField]) return -1*dir;
    if (a[sortField] > b[sortField]) return 1*dir;
    return 0;
  });
  const total = all.length;
  const rows = all.slice(offset, offset+limit);
  res.json({ data: rows.map(mapLibro), page, limit, total });
});

// Get by id
app.get('/api/libros/:id', async (req, res) => {
  const db = await dbPromise;
  const id = parseInt(req.params.id, 10);
  const l = db.data.libro.find(x=>x.id===id);
  if (!l) return res.status(404).json({ error: 'No encontrado' });
  const row = {
    ...l,
    editorial_nombre: db.data.editorial.find(e=>e.id===l.editorial_id)?.nombre || '',
    autor_nombre: db.data.autor.find(a=>a.id===l.autor_id)?.nombre || ''
  };
  res.json(mapLibro(row));
});

// Create
app.post('/api/libros', async (req, res) => {
  const db = await dbPromise;
  const { titulo, editorial_id, anio, edicion, autor_id, web } = req.body;
  if (!titulo || !editorial_id || !anio || !edicion || !autor_id) {
    return res.status(400).json({ error: 'Campos obligatorios faltantes' });
  }
  const id = db.util.nextId('libro');
  db.data.libro.push({ id, titulo, editorial_id: +editorial_id, anio:+anio, edicion:+edicion, autor_id:+autor_id, web: web || null });
  await db.write();
  const created = db.data.libro.find(l=>l.id===id);
  const row = {
    ...created,
    editorial_nombre: db.data.editorial.find(e=>e.id===created.editorial_id)?.nombre || '',
    autor_nombre: db.data.autor.find(a=>a.id===created.autor_id)?.nombre || ''
  };
  res.status(201).json(mapLibro(row));
});

// Update
app.put('/api/libros/:id', async (req, res) => {
  const db = await dbPromise;
  const id = parseInt(req.params.id, 10);
  const { titulo, editorial_id, anio, edicion, autor_id, web } = req.body;
  const idx = db.data.libro.findIndex(l=>l.id===id);
  if (idx === -1) return res.status(404).json({ error: 'No encontrado' });
  db.data.libro[idx] = { id, titulo, editorial_id:+editorial_id, anio:+anio, edicion:+edicion, autor_id:+autor_id, web: web || null };
  await db.write();
  const updated = db.data.libro[idx];
  const row = {
    ...updated,
    editorial_nombre: db.data.editorial.find(e=>e.id===updated.editorial_id)?.nombre || '',
    autor_nombre: db.data.autor.find(a=>a.id===updated.autor_id)?.nombre || ''
  };
  res.json(mapLibro(row));
});

// Delete
app.delete('/api/libros/:id', async (req, res) => {
  const db = await dbPromise;
  const id = parseInt(req.params.id, 10);
  const before = db.data.libro.length;
  db.data.libro = db.data.libro.filter(l=>l.id!==id);
  if (db.data.libro.length === before) return res.status(404).json({ error: 'No encontrado' });
  await db.write();
  res.json({ ok: true });
});

// Extra listings
// By first letter of title
app.get('/api/libros/by-letter/:letter', async (req, res) => {
  const db = await dbPromise;
  const letter = (req.params.letter || '').trim().charAt(0).toUpperCase();
  const rows = db.data.libro
    .filter(l => (l.titulo||'').toUpperCase().startsWith(letter))
    .map(l => ({
      ...l,
      editorial_nombre: db.data.editorial.find(e=>e.id===l.editorial_id)?.nombre || '',
      autor_nombre: db.data.autor.find(a=>a.id===l.autor_id)?.nombre || ''
    }))
    .sort((a,b)=> a.titulo.localeCompare(b.titulo));
  res.json(rows.map(mapLibro));
});

// Search by field (titulo/autor/editorial) and optional order
app.get('/api/libros/search', async (req, res) => {
  const db = await dbPromise;
  const field = ['titulo','autor','editorial'].includes(req.query.field) ? req.query.field : 'titulo';
  const q = (req.query.q || '').trim().toLowerCase();
  const order = (req.query.order === 'desc') ? 'DESC' : 'ASC';

  const rows = db.data.libro.map(l => ({
    ...l,
    editorial_nombre: db.data.editorial.find(e=>e.id===l.editorial_id)?.nombre || '',
    autor_nombre: db.data.autor.find(a=>a.id===l.autor_id)?.nombre || ''
  })).filter(row => {
    if (field === 'autor') return row.autor_nombre.toLowerCase().includes(q);
    if (field === 'editorial') return row.editorial_nombre.toLowerCase().includes(q);
    return (row.titulo||'').toLowerCase().includes(q);
  }).sort((a,b)=> order==='DESC' ? b.titulo.localeCompare(a.titulo) : a.titulo.localeCompare(b.titulo));
  res.json(rows.map(mapLibro));
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Servidor escuchando en http://localhost:${PORT}`);
});
