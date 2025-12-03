<?php
session_start(); 

require_once 'gestionDatos.php';

if (!isset($_GET['token']) || !isset($_GET['email'])) {
    $mensaje = "Enlace de validación incompleto. Falta el token o el email.";
    $correcto = false;
} else {
    $token = $_GET['token'];
    $email = $_GET['email'];
    $mensaje = "";
    $correcto = false;

    $usuarioValidar = cargarUsuarioPorEmailYTokenDB($email, $token); 

    if ($usuarioValidar) {
        $idCliente = $usuarioValidar['IDusuario']; 
        

        if (actualizarEstadoValidacionDB($idCliente)) { 
            $mensaje = "¡Felicidades! Tu cuenta ha sido validada. Ya puedes iniciar sesión.";
            $correcto = true;

            if (isset($_SESSION['email']) && $_SESSION['email'] === $email) {
                $_SESSION['validacion_email'] = true; 
            }
            
        } else {
            $mensaje = "Error al guardar la validación en la base de datos. Por favor, inténtalo de nuevo.";
            $correcto = false;
        }
    } else {
        $mensaje = "Error: El enlace de validación no es válido, ha expirado o la cuenta ya está activa.";
        $correcto = false;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validación de Cuenta</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body class="login-section">
    <div class="form-container">
        
        <h2 style="color: <?php echo $correcto ? 'var(--color-secundario-verde)' : 'var(--color-peligro)'; ?>;">
            <?php echo $correcto ? 'Validación Exitosa' : 'Error de Validación'; ?>
        </h2>
        
        <p style="text-align: center; margin-bottom: 20px; color: #555;">
            <?php echo $mensaje; ?>
        </p>

        <?php if ($correcto): ?>
            <a href="perfilUsuario.php" class="cta-button" style="display: block; text-align: center;">Ir al Perfil</a>
        <?php else: ?>
            <a href="perfilUsuario.php" class="cta-button" style="display: block; text-align: center; background-color: var(--color-secundario-verde);">Volver al Perfil</a>
        <?php endif; ?>

    </div>
</body>
</html>