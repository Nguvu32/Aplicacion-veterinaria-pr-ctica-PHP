<?php
session_start();
require_once "gestionDatos.php";

$encontrado = False;
$usuario = $_REQUEST['email'];
$password = $_REQUEST['password'];
$usuarios = cargarUsuariosDB();
for ($i = 0; $i < count($usuarios); $i++) { 
    if ( $usuario == $usuarios[$i]['email'] && $password == $usuarios[$i]['password']) {
        $_SESSION['email'] = $usuarios[$i]['email'];
        $_SESSION['rol'] = $usuarios[$i]['rol'];
        $_SESSION['nombre'] = $usuarios[$i]['nombre'];
        $_SESSION['telefono'] = $usuarios[$i]['telefono'];
        $_SESSION['imagen_perfil'] = $usuarios[$i]['imagen_perfil'];
        $_SESSION['validacion_email'] = $usuarios[$i]['validacion_email'];
        $_SESSION['intereses'] = $usuarios[$i]['intereses'];
        $_SESSION['id'] = $usuarios[$i]['id'];
        $encontrado = True;
        if ($_SESSION['rol'] == "admin") {
            header("Location: admin.php");
            exit();
        }else {
            header("Location: usuario.php");
            exit();
        }
    }
}
if ($encontrado == False) {
    header("Location: ../html/loginMal.html");
    exit();
}
