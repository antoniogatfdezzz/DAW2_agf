<?php /** @var string $mensaje */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - FEDEXVB</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="google-site-verification" content="9LTK5YA7LDF9m8jRsqfYj4WM4qd-fuqNNEDKkmNt9Lg" />
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <img src="/assets/imagenes/colegioarb.png" alt="FEDEXVB Logo" class="login-logo">
        </div>
        <div class="login-body-content">
            <?php echo $mensaje ?? ''; ?>
            <form method="POST" action="/auth/iniciar" class="validate-form">
                <div class="form-group icon-left">
                    <i class="fas fa-user"></i>
                    <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" required>
                </div>
                <div class="form-group icon-left">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>
        </div>
        <div class="login-footer">
            <i class="fas fa-shield-alt"></i>
            Acceso seguro al sistema interno
        </div>
    </div>

    <script src="/js/app.js"></script>
</body>
</html>
