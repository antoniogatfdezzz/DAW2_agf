// API simple para Libros (fetch al backend)
class _LibroApi {
  async listar(page=1, order='asc') {
    const res = await fetch(`/api/libros?page=${page}&order=${order}`);
    return res.json();
  }
  async crear(payload) {
    const res = await fetch('/api/libros', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
    if (!res.ok) throw new Error('Error creando');
    return res.json();
  }
  async actualizar(id, payload) {
    const res = await fetch(`/api/libros/${id}`, { method:'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
    if (!res.ok) throw new Error('Error actualizando');
    return res.json();
  }
  async borrar(id) {
    const res = await fetch(`/api/libros/${id}`, { method:'DELETE' });
    if (!res.ok) throw new Error('Error borrando');
    return res.json();
  }
  async alfabetico(letter) {
    const res = await fetch(`/api/libros/by-letter/${letter}`);
    return res.json();
  }
  async buscar(field, q, order='asc') {
    const url = new URL('/api/libros/search', window.location.origin);
    url.searchParams.set('field', field);
    url.searchParams.set('q', q);
    url.searchParams.set('order', order);
    const res = await fetch(url);
    return res.json();
  }
}
const api = new _LibroApi();

// Manejo vistas
const links = document.querySelectorAll('a[data-view]');
links.forEach(a => a.addEventListener('click', e => {
  e.preventDefault();
  const view = a.dataset.view;
  document.querySelectorAll('.view').forEach(v => v.classList.add('d-none'));
  document.getElementById(`view-${view}`).classList.remove('d-none');
}));

// Consulta principal
const tbody = document.querySelector('#tablaLibros tbody');
const paginacion = document.getElementById('paginacion');
const orderSelect = document.getElementById('orderSelect');
let currentPage = 1;

async function cargarTabla() {
  const order = orderSelect.value;
  const data = await api.listar(currentPage, order);
  tbody.innerHTML = data.data.map(libro => `<tr>
    <td>${libro.titulo}</td>
    <td>${libro.editorial.nombre}</td>
    <td>${libro.anio}</td>
    <td>${libro.edicion}</td>
    <td>${libro.autor.nombre}</td>
    <td>${libro.web ? `<a href='${libro.web}' target='_blank'>link</a>` : ''}</td>
    <td>
      <button class='btn btn-sm btn-outline-secondary me-1' data-accion='edit' data-id='${libro.id}'>Editar</button>
      <button class='btn btn-sm btn-outline-danger' data-accion='del' data-id='${libro.id}'>Borrar</button>
    </td>
  </tr>`).join('');
  // Paginación
  const totalPages = Math.ceil(data.total / data.limit);
  paginacion.innerHTML = '';
  for (let p=1; p<= totalPages; p++) {
    const li = document.createElement('li');
    li.className = 'page-item' + (p===currentPage ? ' active':'');
    li.innerHTML = `<a class='page-link' href='#'>${p}</a>`;
    li.addEventListener('click', e => { e.preventDefault(); currentPage=p; cargarTabla(); });
    paginacion.appendChild(li);
  }
}
orderSelect.addEventListener('change', () => { currentPage=1; cargarTabla(); });

// Modal crear / editar
const btnCrear = document.getElementById('btnCrear');
const libroModalEl = document.getElementById('libroModal');
const libroModal = new bootstrap.Modal(libroModalEl);
const formLibro = document.getElementById('formLibro');
const tituloInput = document.getElementById('tituloInput');
const libroModalLabel = document.getElementById('libroModalLabel');
const editorialSelect = document.getElementById('editorialSelect');
const autorSelect = document.getElementById('autorSelect');
const anioInput = document.getElementById('anioInput');
const edicionInput = document.getElementById('edicionInput');
const webInput = document.getElementById('webInput');
const libroIdInput = document.getElementById('libroId');

btnCrear.addEventListener('click', () => {
  libroModalLabel.textContent = 'Crear Libro';
  formLibro.reset();
  libroIdInput.value = '';
  libroModal.show();
});

tbody.addEventListener('click', async e => {
  const btn = e.target.closest('button[data-accion]');
  if (!btn) return;
  const id = btn.dataset.id;
  if (btn.dataset.accion === 'del') {
    if (confirm('¿Seguro que deseas borrar el libro?')) {
      await api.borrar(id);
      cargarTabla();
    }
  } else if (btn.dataset.accion === 'edit') {
    const res = await fetch(`/api/libros/${id}`);
    const libro = await res.json();
    libroIdInput.value = libro.id;
    tituloInput.value = libro.titulo;
    editorialSelect.value = libro.editorial.id;
    autorSelect.value = libro.autor.id;
    anioInput.value = libro.anio;
    edicionInput.value = libro.edicion;
    webInput.value = libro.web || '';
    libroModalLabel.textContent = 'Editar Libro';
    libroModal.show();
  }
});

function validarFormulario() {
  let ok = true;
  // Título
  if (!tituloInput.value || tituloInput.value.trim().length < 2) {
    tituloInput.classList.add('is-invalid'); ok=false; } else { tituloInput.classList.remove('is-invalid'); }
  // Editorial
  if (!editorialSelect.value) { editorialSelect.querySelector('select').classList.add('is-invalid'); ok=false; } else { editorialSelect.querySelector('select').classList.remove('is-invalid'); }
  // Autor
  if (!autorSelect.value) { autorSelect.querySelector('select').classList.add('is-invalid'); ok=false; } else { autorSelect.querySelector('select').classList.remove('is-invalid'); }
  // Año
  const anio = parseInt(anioInput.value,10);
  if (isNaN(anio) || anio < 1500 || anio > 2100) { anioInput.querySelector('input')?.classList?.add('is-invalid'); ok=false; } else { anioInput.querySelector('input')?.classList?.remove('is-invalid'); }
  // Edicion
  const ed = parseInt(edicionInput.value,10);
  if (isNaN(ed) || ed < 1) { edicionInput.querySelector('input')?.classList?.add('is-invalid'); ok=false; } else { edicionInput.querySelector('input')?.classList?.remove('is-invalid'); }
  // Web
  if (webInput.value && !/^https?:\/\//i.test(webInput.value)) { webInput.classList.add('is-invalid'); ok=false; } else { webInput.classList.remove('is-invalid'); }
  return ok;
}

formLibro.addEventListener('submit', async e => {
  e.preventDefault();
  if (!validarFormulario()) return;
  const payload = {
    titulo: tituloInput.value.trim(),
    editorial_id: parseInt(editorialSelect.value,10),
    anio: parseInt(anioInput.value,10),
    edicion: parseInt(edicionInput.value,10),
    autor_id: parseInt(autorSelect.value,10),
    web: webInput.value.trim() || null
  };
  try {
    if (libroIdInput.value) {
      await api.actualizar(parseInt(libroIdInput.value,10), payload);
    } else {
      await api.crear(payload);
    }
    libroModal.hide();
    cargarTabla();
  } catch (err) {
    alert('Error guardando');
  }
});

// Alfabético
const letrasDiv = document.getElementById('letras');
const tbodyAlfa = document.getElementById('tbodyAlfa');
const letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
letras.forEach(l => {
  const btn = document.createElement('button');
  btn.type = 'button';
  btn.className = 'btn btn-sm btn-outline-primary me-1 mb-1';
  btn.textContent = l;
  btn.addEventListener('click', async () => {
    const data = await api.alfabetico(l);
    tbodyAlfa.innerHTML = data.map(libro => `<tr><td>${libro.titulo}</td><td>${libro.editorial.nombre}</td><td>${libro.autor.nombre}</td></tr>`).join('');
  });
  letrasDiv.appendChild(btn);
});

// Búsqueda
const formBusqueda = document.getElementById('formBusqueda');
const fieldSelect = document.getElementById('fieldSelect');
const qInput = document.getElementById('qInput');
const orderBusqueda = document.getElementById('orderBusqueda');
const tbodyBusqueda = document.getElementById('tbodyBusqueda');
formBusqueda.addEventListener('submit', async e => {
  e.preventDefault();
  const data = await api.buscar(fieldSelect.value, qInput.value, orderBusqueda.value);
  tbodyBusqueda.innerHTML = data.map(libro => `<tr><td>${libro.titulo}</td><td>${libro.editorial.nombre}</td><td>${libro.autor.nombre}</td><td>${libro.anio}</td></tr>`).join('');
});

// Logout
const btnLogout = document.getElementById('btnLogout');
btnLogout.addEventListener('click', async () => {
  await fetch('/api/logout', { method:'POST' });
  window.location.href = '/';
});

// Inicial
cargarTabla();
