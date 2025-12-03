<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.html');
    exit();
}
require_once 'gestionDatos.php';

$id_a_eliminar = $_REQUEST["id"];
eliminarUsuarioDB($id_a_eliminar);

header('Location: admin.php#usuarios');
exit();
