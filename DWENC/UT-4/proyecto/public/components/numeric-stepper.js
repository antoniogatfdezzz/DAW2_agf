class NumericStepper extends HTMLElement {
  constructor() {
    super();
    this.input = document.createElement('input');
    this.input.type = 'number';
    this.input.className = 'form-control';
    this.input.style.width = '8rem';

    this.group = document.createElement('div');
    this.group.className = 'input-group input-group-sm';

    this.btnMinus = document.createElement('button');
    this.btnMinus.type = 'button';
    this.btnMinus.className = 'btn btn-outline-secondary';
    this.btnMinus.textContent = '−';

    this.btnPlus = document.createElement('button');
    this.btnPlus.type = 'button';
    this.btnPlus.className = 'btn btn-outline-secondary';
    this.btnPlus.textContent = '+';
  }
  connectedCallback() {
    const min = this.getAttribute('min');
    const max = this.getAttribute('max');
    const value = this.getAttribute('value');
    if (min !== null) this.input.min = min;
    if (max !== null) this.input.max = max;
    if (value !== null) this.input.value = value;

    const prepend = document.createElement('span');
    prepend.className = 'input-group-text';
    prepend.textContent = '';

    this.group.appendChild(this.btnMinus);
    this.group.appendChild(this.input);
    this.group.appendChild(this.btnPlus);
    this.appendChild(this.group);

    this.btnMinus.addEventListener('click', () => this.step(-1));
    this.btnPlus.addEventListener('click', () => this.step(1));
  }
  step(dir) {
    const step = parseInt(this.getAttribute('step') || '1', 10);
    const min = this.input.min ? parseInt(this.input.min, 10) : -Infinity;
    const max = this.input.max ? parseInt(this.input.max, 10) : Infinity;
    let val = this.input.value ? parseInt(this.input.value, 10) : 0;
    val = val + dir * step;
    if (val < min) val = min;
    if (val > max) val = max;
    this.input.value = String(val);
    this.dispatchEvent(new Event('change'));
  }
  get value() { return this.input.value; }
  set value(v) { this.input.value = v; }
}
customElements.define('numeric-stepper', NumericStepper);
