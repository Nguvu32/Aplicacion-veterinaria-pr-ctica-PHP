<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.html');
    exit();
}

require_once 'gestionDatos.php'; 
$archivoPedidos = '../archivos/pedidos.json';

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit();
}

$id_a_eliminar = $_GET["id"];

eliminarPedidoDB($id_a_eliminar);

header('Location: admin.php#pedidos');
exit();
?>