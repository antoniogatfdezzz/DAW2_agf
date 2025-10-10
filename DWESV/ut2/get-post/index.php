<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <div>
        <div class="login-form">
            <h2>Iniciar Sesión</h2>
            <form action="index.php" method="GET">
                <div class="form-group">
                    <label for="usuario">Usuario:</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>
        </div>
        
        <?php
        if (!empty($_GET)) {
            echo '<div class="datos-enviados">';
            echo '<h3>Datos enviados por GET:</h3>';
            
            if (isset($_GET['usuario']) && !empty($_GET['usuario'])) {
                echo '<div class="dato-item">';
                echo '<span class="dato-label">Usuario:</span>';
                echo '<span class="dato-valor">' . htmlspecialchars($_GET['usuario']) . '</span>';
                echo '</div>';
            }
            
            // Mostrar la contraseña si existe (NOTA: En aplicaciones reales NUNCA mostrar contraseñas)
            if (isset($_GET['password']) && !empty($_GET['password'])) {
                echo '<div class="dato-item">';
                echo '<span class="dato-label">Contraseña:</span>';
                echo '<span class="dato-valor">' . htmlspecialchars($_GET['password']) . '</span>';
                echo '</div>';
            }
            
            // Mostrar todos los parámetros GET
            echo '<div class="dato-item">';
            echo '<span class="dato-label">URL completa:</span>';
            echo '<span class="dato-valor">' . htmlspecialchars($_SERVER['REQUEST_URI']) . '</span>';
            echo '</div>';
            
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>