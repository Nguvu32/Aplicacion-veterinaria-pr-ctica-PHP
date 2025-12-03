<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../html/login.html');
    exit();
}
require_once 'gestionDatos.php';

$tiempoActual = time();
$limiteInactivo = 60*5;

if (isset($_COOKIE['admin_ultimoClick'])) {
    $ultimoClick = (int)$_COOKIE['admin_ultimoClick'];

    if (($tiempoActual - $ultimoClick) > $limiteInactivo) {
        $_SESSION = array();
        session_destroy();
        setcookie('admin_ultimoClick', '', $tiempoActual - 3600, '/'); 
        header('Location: ../html/login.html?mensaje=Sesión cerrada por inactividad de seguridad.');
        exit();
    }
}

setcookie('admin_ultimoClick', $tiempoActual, 0, '/');

$pedidos = cargarPedidosDB();
$usuarios = cargarUsuariosDB();

$totalIngresos = 0.0;
foreach ($pedidos as $pedido) {
    if($pedido['estado'] == "Completado"){
       $totalIngresos += (float)($pedido['total'] ?? 0.0); 
    }
}

function getEstadoTagClass($estado) {
    switch ($estado) {
        case 'Completado': return 'tag-completed';
        case 'Pendiente': return 'tag-pending';
        case 'Cancelado': return 'tag-cancelled';
        case true: return 'tag-completed'; 
        case false: return 'tag-pending'; 
        default: return 'tag-pending';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - CRUD PHP</title>
    <link rel="stylesheet" href="../css/stylesAdmin.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');</style>
</head>
<body>

    <div class="admin-container"> 
        
        <aside class="sidebar">
            <h1>Admin Panel</h1>
            <nav>
                <a href="#dashboard" class="active" onclick="document.getElementById('dashboard').style.display='block'; document.getElementById('pedidos').style.display='none'; document.getElementById('usuarios').style.display='none'; setActiveLink(this);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="3" y="12" rx="1"/><rect width="7" height="5" x="14" y="16" rx="1"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="#pedidos" onclick="document.getElementById('dashboard').style.display='none'; document.getElementById('pedidos').style.display='block'; document.getElementById('usuarios').style.display='none'; setActiveLink(this);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.58l.63-12.42H5.5"/></svg>
                    <span>Pedidos</span>
                </a>
                <a href="#usuarios" onclick="document.getElementById('dashboard').style.display='none'; document.getElementById('pedidos').style.display='none'; document.getElementById('usuarios').style.display='block'; setActiveLink(this);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-check"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 17 2 2 4-4"/></svg>
                    <span>Usuarios</span>
                </a>
            </nav>
            <div class="logout-button">
                <a href="cerrarSesion.php" class="cta-button">
                    <svg xmlns="http://www.w3.org/2d00/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                    Cerrar Sesión
                </a>
            </div>
        </aside>

        <main class="main-content">
            
            <section id="dashboard" class="content-section">
                <h2 class="title">Resumen del Panel de Control</h2>
                <div class="summary-grid">
                    <div class="summary-card" style="border-left-color: #4C51BF;">
                        <p class="label">Usuarios Registrados</p>
                        <p class="value"><?php echo count($usuarios); ?></p>
                    </div>
                    <div class="summary-card" style="border-left-color: var(--color-exito);">
                        <p class="label">Ingresos Totales (€)</p>
                        <p class="value">€<?php echo number_format($totalIngresos, 2, ',', '.'); ?></p>
                    </div>
                    <div class="summary-card" style="border-left-color: var(--color-alerta);">
                        <p class="label">Pedidos Pendientes</p>
                        <p class="value"><?php echo count(array_filter($pedidos, fn($p) => $p['estado'] === 'Pendiente')); ?></p>
                    </div>
                    <div class="summary-card" style="border-left-color: var(--color-peligro);">
                        <p class="label">Usuarios sin Validar</p>
                        <p class="value"><?php echo count(array_filter($usuarios, fn($u) => isset($u['validacion_email']) && $u['validacion_email'] == 0)); ?></p>
                    </div>
                </div>
            </section>

            <section id="pedidos" class="content-section" style="display: none;">
                <div class="section-header">
                    <h2 class="title">Gestión de Pedidos</h2>
                    <button class="cta-button" onclick="document.getElementById('modal-crear-pedido').style.display='flex';">
                        <svg xmlns="http://www.w3.org/2d00/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        Añadir Pedido
                    </button>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Total (€)</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(isset($pedidos)){
                                    foreach ($pedidos as $pedido){
                                    $pedido_json = htmlspecialchars(json_encode($pedido), ENT_QUOTES, 'UTF-8');
                                    echo '<tr>';
                                    echo '<td>' . $pedido['id'] . '</td>';
                                    echo '<td>' . htmlspecialchars($pedido['cliente_nombre']) . '</td>';
                                    echo '<td>' . htmlspecialchars($pedido['tipo_servicio']) . '</td>';
                                    echo '<td>' . htmlspecialchars($pedido['fecha']) . '</td>';
                                    echo '<td>' . htmlspecialchars($pedido['descripcion']) . '</td>';
                                    echo '<td>' . number_format($pedido['total'], 2, ',', '.'). '€' . '</td>';
                                    echo '<td><span class="tag ' . getEstadoTagClass($pedido['estado']) . '">' . $pedido['estado'] . '</span></td>';
                                    echo '<td>
                                            <button class="action-btn edit-btn" onclick="openEditPedido(\'' . $pedido_json . '\')">Editar</button>
                                            <a href="eliminar_pedido.php?id=' . $pedido['id'] . '" 
                                            class="action-btn delete-btn"
                                            onclick="return confirm(\'¿Estás seguro de que deseas eliminar el pedido #' . $pedido['id'] . ' (Concepto: ' . htmlspecialchars($pedido['descripcion']) . ')? Esta acción es irreversible.\');">Eliminar</a>
                                        </td>';
                                    echo '</tr>';
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="usuarios" class="content-section" style="display: none;">
                <div class="section-header">
                    <h2 class="title">Gestión de Usuarios</h2>
                    
                    <a href="../html/registro.html" class="cta-button">
                        <svg xmlns="http://www.w3.org/2d00/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        Crear Usuario
                    </a>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Tipo</th>
                                <th>Teléfono</th>
                                <th>Gasto Total (€)</th>
                                <th>Validación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(isset($usuarios)){
                                    foreach ($usuarios as $usuario) {
                                        $usuario_json = htmlspecialchars(json_encode($usuario), ENT_QUOTES, 'UTF-8');
                                        $isValidate = false;
                                        $validationStatus = "";
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($usuario["id"]) . '</td>';
                                        echo '<td>' . htmlspecialchars($usuario["nombre"]) . '</td>';
                                        echo '<td>' . htmlspecialchars($usuario["email"]) . '</td>';
                                        echo '<td>' . htmlspecialchars($usuario["rol"]) . '</td>';
                                        echo '<td>' . htmlspecialchars($usuario["tipo_usuario"]) . '</td>';
                                        echo '<td>' . htmlspecialchars($usuario["telefono"]) . '</td>';
                                        echo '<td>' . number_format($usuario["total_gastado"], 2, ',', '.') . '€</td>';
                                        if($usuario["validacion_email"] == 1){
                                            $isValidate = true;
                                            $validationStatus = "Validado";
                                        }else{
                                            $validationStatus = "No validado";
                                        }                                    
                                        echo '<td><span class="tag ' . getEstadoTagClass($usuario["validacion_email"]) . '">' . $validationStatus . '</span></td>';
                                        echo '<td>
                                                <button class="action-btn edit-btn" onclick="openEditUsuario(\'' . $usuario_json . '\')">Editar</button>
                                                <a href="eliminar_usuario.php?id=' . $usuario["id"] . '" 
                                                class="action-btn delete-btn"
                                                onclick="return confirm(\'¿Estás seguro de que deseas eliminar al usuario ' . htmlspecialchars($usuario["nombre"]) . '? Esta acción es irreversible.\');">Eliminar</a>
                                            </td>';
                                        echo '</tr>'; 
                                    }  
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <div id="modal-crear-pedido" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="document.getElementById('modal-crear-pedido').style.display='none'">&times;</span>
            <h3>➕ Registrar Nuevo Pedido</h3>
            <form action="crearPedido.php" method="POST" class="crud-form">
                <label for="p_cliente_id">Cliente (Email):</label>
                <input type="email" id="p_cliente_id" name="clienteEmail" required>
                
                <label for="p_tipo_servicio">Tipo de Servicio:</label>
                <select id="p_tipo_servicio" name="tipo_servicio" required>
                    <option value="Asesoría">Asesoría</option>
                    <option value="Curso">Curso</option>
                    <option value="Dieta">Dieta</option>
                    <option value="Informe">Informe</option>
                    <option value="Otro">Otro/Producto</option>
                </select>

                <label for="p_descripcion">Descripción / Concepto:</label>
                <input type="text" id="p_descripcion" name="descripcion" required>
                
                <label for="p_total">Total (€):</label>
                <input type="number" step="0.01" id="p_total" name="total" required>

                <label for="p_estado">Estado:</label>
                <select id="p_estado" name="estado" required>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Completado">Completado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
                
                <input type="hidden" name="fecha" value="<?php echo date('Y-m-d'); ?>">

                <button type="submit" class="submit-btn" style="background-color: var(--color-exito);">Guardar Pedido</button>
            </form>
        </div>
    </div>

    <div id="modal-editar-pedido" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="document.getElementById('modal-editar-pedido').style.display='none'">&times;</span>
            <h3>✏️ Editar Pedido</h3>
            <form action="editar_pedido.php" method="POST" class="crud-form">
                <input type="hidden" name="id" id="id_pedido_edit"> 
                
                <label for="cliente_nombre_pedido_edit">Nombre del Cliente:</label>
                <input type="text" id="cliente_nombre_pedido_edit" readonly>
                
                <label for="fecha_pedido_edit">Fecha:</label>
                <input type="date" id="fecha_pedido_edit" name="fecha" required> 

                <label for="tipo_servicio_pedido_edit">Tipo de Servicio:</label>
                <select id="tipo_servicio_pedido_edit" name="tipo_servicio" required>
                    <option value="Asesoría">Asesoría</option>
                    <option value="Curso">Curso</option>
                    <option value="Dieta">Dieta</option>
                    <option value="Informe">Informe</option>
                    <option value="Otro">Otro/Producto</option>
                </select>
                
                <label for="descripcion_pedido_edit">Descripción / Concepto:</label>
                <input type="text" id="descripcion_pedido_edit" name="descripcion" required>
                
                <label for="total_pedido_edit">Total (€):</label>
                <input type="number" step="0.01" id="total_pedido_edit" name="total" required>

                <label for="estado_pedido_edit">Estado:</label>
                <select id="estado_pedido_edit" name="estado" required>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Completado">Completado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>

                <button type="submit" class="submit-btn">Actualizar Pedido</button>
            </form>
        </div>
    </div>
    
    <div id="modal-editar-usuario" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="document.getElementById('modal-editar-usuario').style.display='none'">&times;</span>
            <h3>✏️ Editar Usuario</h3>
            <form action="editar_usuario.php" method="POST" class="crud-form">
                <input type="hidden" name="id" id="id_usuario_edit"> 
                
                <label for="nombre_usuario_edit">Nombre:</label>
                <input type="text" id="nombre_usuario_edit" name="nombre" required>
                
                <label for="email_usuario_edit">Email:</label>
                <input type="email" id="email_usuario_edit" name="email" required>
                
                <label for="telefono_usuario_edit">Teléfono:</label>
                <input type="tel" id="telefono_usuario_edit" name="telefono">
                
                <label for="rol_usuario_edit">Rol:</label>
                <select id="rol_usuario_edit" name="rol" required>
                    <option value="user">Usuario</option>
                    <option value="admin">Administrador</option>
                </select>
                
                <label for="tipo_usuario_edit">Tipo de Usuario:</label>
                <select id="tipo_usuario_edit" name="tipo_usuario" required>
                    <option value="asesoria">Asesoría</option>
                    <option value="empresa">Empresa</option>
                    <option value="formacion">Formación</option>
                    <option value="otro">Otro</option>
                </select>
                
                <label for="validacion_usuario_edit">Correo Validado:</label>
                <select id="validacion_usuario_edit" name="validacion_email" required>
                    <option value="1">Sí</option> <option value="0">No</option>  </select>

                <button type="submit" class="submit-btn">Actualizar Usuario</button>
            </form>
        </div>
    </div>


    <script>
        function openEditPedido(jsonString) {
            const pedido = JSON.parse(jsonString); 
            
            document.getElementById('id_pedido_edit').value = pedido.id || '';
            document.getElementById('cliente_nombre_pedido_edit').value = pedido.cliente_nombre || '';
            document.getElementById('fecha_pedido_edit').value = pedido.fecha || '';
            document.getElementById('tipo_servicio_pedido_edit').value = pedido.tipo_servicio || '';
            document.getElementById('descripcion_pedido_edit').value = pedido.descripcion || '';
            document.getElementById('total_pedido_edit').value = parseFloat(pedido.total).toFixed(2) || '';
            document.getElementById('estado_pedido_edit').value = pedido.estado || '';
            
            document.getElementById('modal-editar-pedido').style.display = 'flex';
        }

        function openEditUsuario(jsonString) {
            const usuario = JSON.parse(jsonString); 
            
            document.getElementById('id_usuario_edit').value = usuario.id || '';
            document.getElementById('nombre_usuario_edit').value = usuario.nombre || '';
            document.getElementById('email_usuario_edit').value = usuario.email || '';
            document.getElementById('telefono_usuario_edit').value = usuario.telefono || '';
            document.getElementById('rol_usuario_edit').value = usuario.rol || 'user';
            document.getElementById('tipo_usuario_edit').value = usuario.tipo_usuario || 'otro';
            
            const isValidated = (usuario.validacion_email == 1);
            document.getElementById('validacion_usuario_edit').value = isValidated ? '1' : '0';
            
            document.getElementById('modal-editar-usuario').style.display = 'flex';
        }

        window.onload = function() {
            lucide.createIcons();
            document.getElementById('dashboard').style.display='block';
        };

        function setActiveLink(element) {
            document.querySelectorAll('.sidebar a').forEach(link => {
                link.classList.remove('active');
            });
            element.classList.add('active');
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>