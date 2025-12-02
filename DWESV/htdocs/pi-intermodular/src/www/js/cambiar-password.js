document.addEventListener('DOMContentLoaded', function() {
    const nuevaPassword = document.getElementById('password_nueva');
    const confirmarPassword = document.getElementById('password_confirmar');

    function validarPasswords() {
        if (nuevaPassword.value && confirmarPassword.value) {
            if (nuevaPassword.value !== confirmarPassword.value) {
                confirmarPassword.setCustomValidity('Las contraseñas no coinciden');
            } else {
                confirmarPassword.setCustomValidity('');
            }
        }
    }

    if (nuevaPassword && confirmarPassword) {
        nuevaPassword.addEventListener('input', validarPasswords);
        confirmarPassword.addEventListener('input', validarPasswords);
    }

    const form = document.querySelector('.change-password-form');
    if (form && nuevaPassword && confirmarPassword) {
        form.addEventListener('submit', function(e) {
            if (nuevaPassword.value !== confirmarPassword.value) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
            }
        });
    }
});
