<?php
session_start();
require_once 'gestionDatos.php';
$idCliente = $_SESSION['id'];

if (!isset($_SESSION['email']) || $_SESSION['rol'] !== 'user') {
    $_SESSION['mensaje_perfil'] = "Error de autenticación. Por favor, inicia sesión de nuevo.";
    header('Location: perfilUsuario.php');
    exit();
}

if(!isset($_REQUEST['nombre']) || !isset($_REQUEST['telefono']) || !isset($_REQUEST['tipo_usuario'])) {
    $_SESSION['mensaje_perfil'] = "Error: Faltan datos del formulario.";
    header('Location: perfilUsuario.php');
    exit();
}else{
    $nombreOk = false;
    if(!empty(trim($_REQUEST['nombre']))){
        $nombreOk = true;
    }
    $telefonoOk = false;
    if(preg_match('/^\+?[0-9]{7,15}$/', $_REQUEST['telefono'])){
        $telefonoOk = true;
    }
    $tipo_usuario = false;
    if(!empty(trim($_REQUEST['tipo_usuario']))){
        $tipo_usuario = true;
    }
    if(!$nombreOk || !$telefonoOk || !$tipo_usuario){
        $_SESSION['mensaje_perfil'] = "Error: Datos inválidos proporcionados.";
        header('Location: perfilUsuario.php');
        exit();
    }   
}
$nombre = $_REQUEST['nombre'];
$telefono = (int)($_REQUEST['telefono']);
$tipo_usuario = $_REQUEST['tipo_usuario'];
$usuario = cargarUsuarioPorIdDB($idCliente);
$usuario['nombre'] = $nombre;
$usuario['telefono'] = $telefono;
$usuario['tipo_usuario'] = $tipo_usuario;
actualizarUsuarioDB($idCliente,$usuario);
$_SESSION['mensaje_perfil'] = "Tus datos han sido actualizados con éxito.";
header('Location: perfilUsuario.php');
exit();
