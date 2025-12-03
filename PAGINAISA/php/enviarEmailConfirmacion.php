<?php
session_start();
require_once 'gestionDatos.php';

if (!isset($_SESSION['email'])) {
    $_SESSION['mensaje_perfil'] = "Error: La sesión de usuario no está activa para enviar el email.";
    header('Location: perfilUsuario.php');
    exit();
}

$email = $_SESSION['email'];
$nombre = $_SESSION['nombre'];

$usuarioDB = buscarUsuarioPorEmailDB($email); 

if ($usuarioDB) {
    $idUsuario = $usuarioDB['id']; 
    $tokenValidacion = bin2hex(random_bytes(32)); 
    $urlBase = 'http://localhost/EJERCICIOSPHP/workspace/PAGINAISA/php/'; 
    $linkValidacion = $urlBase . 'validacionCuenta.php?token=' . $tokenValidacion . '&email=' . urlencode($email);

    if (guardarTokenDB($idUsuario, $tokenValidacion)) { 
        
        $asunto = 'Verifica tu cuenta en Wildvet';
        
        $cabeceras = 'From: noreply@wildvet.com' . "\r\n" .
                     'Reply-To: noreply@wildvet.com' . "\r\n" .
                     'Content-type: text/html; charset=utf-8' . "\r\n" .
                     'X-Mailer: PHP/' . phpversion();

        $contenidoEmail = "
            <html>
            <head>
              <title>Validación de Cuenta</title>
            </head>
            <body>
              <h2>Hola $nombre,</h2>
              <p>Gracias por registrarte. Por favor, haz clic en el siguiente enlace para validar tu cuenta:</p>
              <p><a href=\"$linkValidacion\">Validar mi cuenta</a></p>
              <p>Si no puedes hacer clic en el enlace, copia y pega la siguiente URL en tu navegador:</p>
              <p>$linkValidacion</p>
              <p>Saludos,</p>
              <p>El equipo de Wildvet</p>
            </body>
            </html>
        ";

        if (mail($email, $asunto, $contenidoEmail, $cabeceras)) {
            $_SESSION['mensaje_perfil'] = "Se ha enviado un correo de validación a $email. Revisa tu bandeja de entrada.";
        } else {
            $_SESSION['mensaje_perfil'] = "**ERROR** al intentar enviar el email a $email. (Problema de configuración del servidor).";
            $_SESSION['mensaje_perfil'] .= "<br>ENLACE DE PRUEBA (haz clic): <a href=\"$linkValidacion\">$linkValidacion</a>";
        }
        
        header('Location: perfilUsuario.php');
        exit();
        
    } else {
        $_SESSION['mensaje_perfil'] = "Error al guardar el token de validación en la base de datos. Inténtalo de nuevo.";
        header('Location: perfilUsuario.php');
        exit();
    }
} else {
    $_SESSION['mensaje_perfil'] = "Error: Usuario no encontrado en la base de datos.";
    header('Location: perfilUsuario.php');
    exit();
}
?>