<?php 
session_start();
require_once 'gestionDatos.php';
$usuarios = cargarUsuariosDB();
if(!isset($_POST['nombre_registro'], $_POST['email_registro'], $_POST['telefono'], $_POST['password_registro'], $_POST['password_confirmar'], $_POST['tipo_usuario'], $_POST['acepto_terminos'])) {
    die('Faltan datos del formulario.');
}else{
    $nombreOk = false;
    if(!empty(trim($_POST['nombre_registro']))){
        $nombreOk = true;
    }
    $emailOk = false;
    $usuarioExistente = buscarUsuarioPorEmailDB($_POST['email_registro']);
    if ($usuarioExistente) {
        echo('Error: Ya existe un usuario registrado con ese correo electrónico.');
        echo('<br><a href="admin.php">Volver al formulario</a>');
        exit();
    }
    if(filter_var($_POST['email_registro'], FILTER_VALIDATE_EMAIL)){ 
        $emailOk = true;
    }   
    $telefonoOk = false;
    if(strlen($_POST["telefono"]) == 9){
        $telefonoOk = true;
    }
    $passwordOk = false;
    if(strlen($_POST['password_registro']) === strlen(trim($_POST['password_confirmar'])) && strlen($_POST['password_registro']) >= 8){
        $passwordOk = true;
    }
    if(!$nombreOk || !$emailOk || !$telefonoOk || !$passwordOk){
        echo('Datos inválidos proporcionados.');
        echo('<br><a href="../html/registro.html">Volver al formulario</a>');
        exit();
    }   
}
$nombre_registro = $_POST['nombre_registro'];
$email_registro = $_POST['email_registro'];
$telefono = $_POST['telefono'];
$password_registro = $_POST['password_registro'];
$tipo_usuario = $_POST['tipo_usuario'];

$nuevoUsuario = [
    'nombre' => $nombre_registro,
    'email' => $email_registro,
    'telefono' => $telefono,
    'total_gastado' => 0,
    'password' => $password_registro,
    'tipo_usuario' => $tipo_usuario,
    'acepto_terminos' => true,
    'rol' => 'user',
    'validacion_email' => false,
    'imagen_perfil' => ''
];
$_SESSION['nombre'] = $nombre_registro;
$_SESSION['email'] = $email_registro;
$_SESSION['id'] = $nuevoUsuario['id'];
$_SESSION['telefono'] = $telefono;
$_SESSION['tipo_usuario'] = $tipo_usuario;
if(crearUsuarioDB($nuevoUsuario)){
    header('Location: ../html/login.html?mensaje=Registro exitoso, por favor inicia sesión');
}



