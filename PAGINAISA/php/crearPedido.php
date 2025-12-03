<?php
session_start();
require 'gestionDatos.php';

$idUsuario = null;
$usuarioNombre = null;

if (!isset($_POST['clienteEmail'], $_POST['tipo_servicio'], $_POST['descripcion'], $_POST['fecha'], $_POST['total'], $_POST['estado'])) {
    die('Faltan datos del formulario.');
} else {
    $clienteOk = false;
    $usuario = buscarUsuarioPorEmailDB($_POST['clienteEmail']);

    if ($usuario) {
        $clienteOk = true;
        $idUsuario = $usuario['id'];
        $usuarioNombre = $usuario['nombre'];
    }
    $tipo_servicioOK = false;
    $tipos_servicio_validos = ['Asesoría', 'Curso', 'Dieta', 'Informe', 'Otro'];
    if (in_array($_POST['tipo_servicio'], $tipos_servicio_validos)) {
        $tipo_servicioOK = true;
    }
    
    $fechaOk = false;
    $fechaIngresada = DateTime::createFromFormat('Y-m-d', $_POST['fecha']);
    if ($fechaIngresada && $fechaIngresada->format('Y-m-d') === $_POST['fecha']) {
        $fechaOk = true;
    }
    
    $totalOk = false;
    if (is_numeric($_POST['total']) && $_POST['total'] >= 0) {
        $totalOk = true;
    }
    
    $estadoOk = false;
    $estados_validos = ['Pendiente', 'Completado', 'Cancelado'];
    if (in_array($_POST['estado'], $estados_validos)) {
        $estadoOk = true;
    }
    
    if (!$clienteOk || !$tipo_servicioOK || !$fechaOk || !$totalOk || !$estadoOk) {
        $mensajeError = "Datos inválidos proporcionados. Revise: ";
        if (!$clienteOk) $mensajeError .= " Cliente no encontrado; ";
        if (!$tipo_servicioOK) $mensajeError .= " Tipo de servicio; ";
        if (!$fechaOk) $mensajeError .= " Fecha; ";
        if (!$totalOk) $mensajeError .= " Total; ";
        if (!$estadoOk) $mensajeError .= " Estado.";

        echo($mensajeError);
        echo('<br><a href="admin.php">Volver al formulario</a>');
        exit();
    }
}

$tipo_servicio = $_POST['tipo_servicio'];
$descripcion = $_POST['descripcion'];
$fecha = $_POST['fecha'];
$total = (float)$_POST['total'];
$estado = $_POST['estado'];

$datosNuevoPedido = [
    'IDusuario' => $idUsuario,
    'nombre_usuario' => $usuarioNombre,
    'fecha' => $fecha,
    'tipo_servicio' => $tipo_servicio,
    'descripcion' => $descripcion,
    'total' => $total,
    'estado' => $estado
];

$exito = crearPedidoDB($datosNuevoPedido);

if ($exito) {
    header('Location: admin.php?mensaje=Pedido creado exitosamente');
} else {
    echo('Error al crear el pedido en la base de datos.');
    echo('<br><a href="admin.php">Volver al formulario</a>');
}
?>