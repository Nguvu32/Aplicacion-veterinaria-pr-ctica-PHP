<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../html/login.html');
    exit();
}
require_once 'gestionDatos.php';

$errores = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    
    $id_a_editar = (int)$_POST['id'];

    if (!filter_var($id_a_editar, FILTER_VALIDATE_INT) || $id_a_editar <= 0) {
        $errores[] = "ID de pedido no válido.";
    }

    $pedido = cargarPedidoPorIdDB($id_a_editar);
    if (!$pedido) {
        $errores[] = "No se ha encontrado el pedido con ID #{$id_a_editar}.";
    }

    if (empty($errores) && $pedido) {

        $fecha = trim($_POST['fecha'] ?? '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $errores[] = "Formato de fecha no válido (debe ser YYYY-MM-DD).";
        }

        $tipos_validos = ['Asesoría', 'Curso', 'Dieta', 'Informe', 'Otro'];
        $tipo_servicio = trim($_POST['tipo_servicio'] ?? '');
        if (!in_array($tipo_servicio, $tipos_validos)) {
            $errores[] = "Tipo de servicio no válido.";
        }

        $descripcion = trim($_POST['descripcion'] ?? '');

        $descripcion = htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8'); 
        if (empty($descripcion) || strlen($descripcion) > 255) {
            $errores[] = "Descripción no puede estar vacía o superar los 255 caracteres.";
        }

        $total = filter_var($_POST['total'] ?? '', FILTER_VALIDATE_FLOAT);
        if ($total === false || $total < 0) {
            $errores[] = "El valor total no es un número válido o es negativo.";
            $total = 0.00;
        }

        $estados_validos = ['Pendiente', 'Completado', 'Cancelado'];
        $estado = trim($_POST['estado'] ?? '');
        if (!in_array($estado, $estados_validos)) {
            $errores[] = "Estado de pedido no válido.";
        }

        if (empty($errores)) {
            $pedido['fecha'] = $fecha;
            $pedido['tipo_servicio'] = $tipo_servicio;
            $pedido['descripcion'] = $descripcion;
            $pedido['total'] = $total;
            $pedido['estado'] = $estado;
            
            $exito = actualizarPedidoDB($id_a_editar, $pedido);
            
            if ($exito) {
                $_SESSION['mensaje_admin'] = "Pedido #{$id_a_editar} actualizado con éxito en la base de datos.";
            } else {
                $_SESSION['mensaje_admin'] = "Error: Fallo al actualizar el pedido #{$id_a_editar} en la base de datos (Error DB).";
            }
        } else {
            $_SESSION['mensaje_admin'] = "Error al actualizar pedido #{$id_a_editar}: " . implode(" ", $errores);
        }
    } else {
         $_SESSION['mensaje_admin'] = "Error: " . implode(" ", $errores);
    }
} else {
    $_SESSION['mensaje_admin'] = "Error: Datos insuficientes para editar el pedido. (Falta el ID).";
}

header('Location: admin.php#pedidos');
exit();
?>