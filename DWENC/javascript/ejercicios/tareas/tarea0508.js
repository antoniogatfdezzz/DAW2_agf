const ObservableString = {
    _value: '',
    
    _listener: null,
    
    getValue() {
        return this._value;
    },
    
    setValue(newValue) {
        this._value = newValue;
        if (this._listener && typeof this._listener === 'function') {
            this._listener(newValue);
        }
    },
    
    setListener(listenerFunction) {
        this._listener = listenerFunction;
    }
};

const valueChangeListener = (newValue) => {
    console.log(`📢 Valor cambiado a: "${newValue}"`);
};

ObservableString.setListener(valueChangeListener);

console.log('1. Asignando listener y probando varios valores:\n');

console.log('Asignando "Hola Mundo"...');
ObservableString.setValue('Hola Mundo');

console.log('\nAsignando "JavaScript es genial"...');
ObservableString.setValue('JavaScript es genial');

console.log('\nAsignando número como string "12345"...');
ObservableString.setValue('12345');

console.log('\nAsignando string vacío...');
ObservableString.setValue('');

console.log('\nAsignando "¡Funciona perfectamente!"...');
ObservableString.setValue('¡Funciona perfectamente!');

console.log('\n2. Verificando que el getter funciona correctamente:');
console.log(`Valor actual obtenido con getValue(): "${ObservableString.getValue()}"`);

console.log('\n3. Probando sin listener:');
ObservableString.setListener(null);
console.log('Listener eliminado. Asignando "Sin notificación"...');
ObservableString.setValue('Sin notificación');
console.log(`Valor actual: "${ObservableString.getValue()}" (sin notificación)`);

console.log('\n4. Volviendo a asignar listener:');
ObservableString.setListener(valueChangeListener);
console.log('Listener restaurado. Asignando "¡De vuelta!"...');
ObservableString.setValue('¡De vuelta!');

console.log('\n=== Fin del programa de prueba ===');
