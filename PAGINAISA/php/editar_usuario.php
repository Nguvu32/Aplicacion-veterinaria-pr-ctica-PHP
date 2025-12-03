<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ..//html/login.html');
    exit();
}
require_once 'gestionDatos.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    
    $errores = [];
    $id_a_editar = $_POST['id'];
    $nombre = $_POST["nombre"];
    $telefono = $_POST["telefono"];
    $rol = $_POST["rol"];
    $tipo_usuario = $_POST["tipo_usuario"];
    $validacion_mail = $_POST["validacion_email"];

    if (empty($nombre)) {
        $errores[] = 'El nombre no puede estar vacío.';
    }
    if (strlen($telefono) != 9) {
        $errores[] = 'El formato del teléfono es inválido (9 dígitos numéricos).';
    }
    if (!in_array($rol, ['user', 'admin'])) {
        $errores[] = 'El rol seleccionado es inválido.';
    }
    if (!in_array($tipo_usuario, ['asesoria', 'empresa', 'formacion', 'otro'])) {
        $errores[] = 'El tipo de usuario es inválido.';
    }
    if ($validacion_mail != "1" && $validacion_mail != "0") {
        $errores[] = 'La validacion no se ajusta a los parametros';
    }
    if (!empty($errores)) {
        $_SESSION['mensaje_admin'] = "Error de validación al actualizar el usuario con ID {$id_a_editar}: " . implode(' ', $errores);
        header('Location: admin.php#usuarios');
        exit();
    }

    $usuario = cargarUsuarioPorIdDB($id_a_editar);

    if ($usuario) {
        $usuario['nombre'] = $nombre;
        $usuario['telefono'] = $telefono;
        $usuario['rol'] = $rol;
        $usuario['tipo_usuario'] = $tipo_usuario;
        $usuario['validacion_email'] = $validacion_mail; 

        $exito = actualizarUsuarioDB($id_a_editar, $usuario);

        if ($exito) {
            $_SESSION['mensaje_admin'] = "Usuario {$id_a_editar} ({$nombre}) actualizado con éxito.";
        } else {
            $_SESSION['mensaje_admin'] = "Error: No se pudo actualizar el usuario en la base de datos.";
        }
    } else {
        $_SESSION['mensaje_admin'] = "Error: Usuario con ID {$id_a_editar} no encontrado en la base de datos.";
    }
} else {
    $_SESSION['mensaje_admin'] = "Error: Datos insuficientes para editar el usuario.";
}

header('Location: admin.php#usuarios');
exit();
?>