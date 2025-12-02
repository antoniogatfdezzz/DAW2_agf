<?php /** @var string $mensaje */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - FEDEXVB</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="change-password-body">
    <div class="change-password-container">
        <div class="change-password-card">
            <div class="change-password-header">
                <div class="logo"><i class="fas fa-volleyball-ball"></i></div>
                <h1>Cambiar Contraseña</h1>
                <p>Por seguridad, debe cambiar su contraseña temporal</p>
            </div>

            <?php if (!empty($_SESSION['password_temporal'])): ?>
            <div class="warning-banner">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Su contraseña es temporal. Debe cambiarla para continuar.</span>
            </div>
            <?php endif; ?>

            <?php echo $mensaje ?? ''; ?>

            <form method="POST" action="/auth/cambiar-password" class="change-password-form">
                <div class="form-group">
                    <label for="password_actual" class="form-label">
                        <i class="fas fa-lock"></i> Contraseña Actual
                    </label>
                    <input type="password" id="password_actual" name="password_actual" class="form-control" placeholder="Ingrese su contraseña actual" required>
                </div>

                <div class="password-requirements">
                    <h4><i class="fas fa-info-circle"></i> Requisitos de la nueva contraseña:</h4>
                    <ul>
                        <li>Mínimo 6 caracteres</li>
                        <li>Se recomienda usar mayúsculas, minúsculas y números</li>
                        <li>Evite usar información personal</li>
                    </ul>
                </div>

                <div class="form-group">
                    <label for="password_nueva" class="form-label">
                        <i class="fas fa-key"></i> Nueva Contraseña
                    </label>
                    <input type="password" id="password_nueva" name="password_nueva" class="form-control" placeholder="Ingrese su nueva contraseña" minlength="6" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmar" class="form-label">
                        <i class="fas fa-check-circle"></i> Confirmar Nueva Contraseña
                    </label>
                    <input type="password" id="password_confirmar" name="password_confirmar" class="form-control" placeholder="Confirme su nueva contraseña" minlength="6" required>
                </div>

                <button type="submit" class="btn-change">
                    <i class="fas fa-save"></i> Cambiar Contraseña
                </button>
            </form>

            <div class="texto-proteccion">
                <small>
                    <i class="fas fa-shield-alt"></i>
                    Sus datos están protegidos con encriptación
                </small>
            </div>
        </div>
    </div>

    <script src="/js/cambiar-password.js"></script>
</body>
</html>
