class ServerSelect extends HTMLElement {
  constructor() {
    super();
    this.select = document.createElement('select');
    this.select.className = 'form-select';
  }
  connectedCallback() {
    this.appendChild(this.select);
    this.load();
  }
  async load() {
    const endpoint = this.dataset.endpoint;
    const valueField = this.dataset.valueField || 'id';
    const textField = this.dataset.textField || 'nombre';
    try {
      const res = await fetch(endpoint);
      const data = await res.json();
      this.select.innerHTML = '<option value="">-- Seleccionar --</option>' +
        data.map(item => `<option value="${item[valueField]}">${item[textField]}</option>`).join('');
    } catch (e) {
      console.error('Error cargando opciones', e);
    }
  }
  get value() { return this.select.value; }
  set value(v) { this.select.value = v; }
}
customElements.define('server-select', ServerSelect);
