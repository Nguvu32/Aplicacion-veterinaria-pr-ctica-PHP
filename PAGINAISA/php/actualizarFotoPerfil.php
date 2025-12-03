<?php 
session_start();
require_once 'gestionDatos.php';

if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'user') {
    header('Location: ../html/login.html?mensaje=Acceso no autorizado.');
    exit();
}

$idUsuario = $_POST["id"] ?? $_SESSION["id"]; 

$tamanio_maximo = 50000000; 
$extensiones_permitidas = array("jpg", "jpeg", "png", "gif");

if (isset($_FILES['imagen_perfil']) && $_FILES['imagen_perfil']['error'] === UPLOAD_ERR_OK) {
    
    $archivo = $_FILES['imagen_perfil'];
    $nombre_archivo = basename($archivo['name']);
    $tipo_archivo = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

    if ($idUsuario != $_SESSION['id']) {
         $_SESSION['mensaje_perfil'] = "Error: ID de usuario no coincide con la sesión.";
    } elseif ($archivo['size'] > $tamanio_maximo) {
        $_SESSION['mensaje_perfil'] = "Error: El archivo es demasiado grande (máximo 50000000KB).";
    } elseif (!in_array($tipo_archivo, $extensiones_permitidas)) {
        $_SESSION['mensaje_perfil'] = "Error: Solo se permiten archivos JPG, JPEG, PNG y GIF.";
    } else {
     
        $contenido_binario = file_get_contents($archivo['tmp_name']);

        if ($contenido_binario === false) {
             $_SESSION['mensaje_perfil'] = "Error: No se pudo leer el contenido binario del archivo, contacte con el servicio técnico.";
        } else {
            if (actualizarImagenPerfil($idUsuario, $contenido_binario)) {
                
                $_SESSION['mensaje_perfil'] = "¡Imagen de perfil actualizada!";
                
            } else {
                $_SESSION['mensaje_perfil'] = "Error: Fallo al actualizar en la base de datos. Contacte con el servicio técnico";
            }
        }
    }
} else {
    $error_code = $_FILES['imagen_perfil']['error'] ?? 'N/A';
    $_SESSION['mensaje_perfil'] = "Error: No se seleccionó archivo o falló la subida (Código: $error_code).";
}

header('Location: perfilUsuario.php');
exit();
?>