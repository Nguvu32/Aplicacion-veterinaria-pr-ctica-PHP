<?php 
session_start();
require_once 'gestionDatos.php'; 
$usuario = cargarUsuarioPorIdDB($_SESSION['id']);

if(!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'user') {
    header('Location: ../html/login.html?mensaje=Por favor inicia sesión para acceder a tu perfil');
    exit();
}
$idUsuario = $_SESSION["id"]; 
$contenidoBinario = cargarImagenBlobPorIdDB($idUsuario); 
$imagenPerfil = '../imagenes/avatar_defecto.png'; 

if ($contenidoBinario) {
    
    $mimeType = false;

    if (extension_loaded('fileinfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($contenidoBinario); 
    } 

    if ($mimeType) {
        $base64Image = base64_encode($contenidoBinario);
        $imagenPerfil = "data:{$mimeType};base64,{$base64Image}";
    }
} 


$nombre = $usuario["nombre"];
$email = $usuario["email"];
$acepto_terminos = $usuario["acepto_terminos"];
$telefono = $usuario["telefono"] ;
$tipoUsuarioActual = $usuario["tipo_usuario"] ?? '';
$validacionActual = $usuario["validacion_email"]; 

$mostrarBotonValidacion = !$validacionActual;
$mensajeEstado = $mostrarBotonValidacion ? "Tu correo no está validado. Pulsa aquí para recibir el enlace." : "Bienvenido a tu perfil.";

$mensajeAccion = '';
if(isset($_SESSION['mensaje_perfil'])) {
    $mensajeAccion = $_SESSION['mensaje_perfil'];
    unset($_SESSION['mensaje_perfil']); 
}

$modoEdicion = isset($_GET['mode']) && $_GET['mode'] === 'editar';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Wildvet</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body class="perfil-page">

    <header id="main-header">
        <div id="logo-container">
            <h2>Wildvet</h2> 
        </div>
        <nav>
            <a href="usuario.php#asesorias">Asesorías</a>
            <a href="usuario.php#planes-salud">Planes de Salud</a>
            <a href="usuario.php#formaciones">Formaciones</a>
            <a href="usuario.php#contacto">Contacto</a>
            <a href="perfilUsuario.php">
                Perfil
            </a>
            <a href="cerrarSesion.php" class="cta-button" style="padding: 8px 15px; margin-left: 20px; background-color: var(--color-secundario-amarillo); color: var(--color-principal-morado); font-size: 1em;">
                Cerrar Sesión
            </a>
        </nav>
    </header>

    <main class="login-section" style="padding-top: 40px; padding-bottom: 40px; flex-grow: 1;">
        <div class="form-container" style="max-width: 700px; width: 100%;">
            
            <h2 style="margin-top: 0;">Gestión de Perfil</h2>

            <?php if (!empty($mensajeAccion)): ?>
                <div style="padding: 15px; margin-bottom: 20px; text-align: center; border-radius: 5px; 
                        <?php 
                        if (strpos($mensajeAccion, 'Error') !== false || strpos($mensajeAccion, '⚠️') !== false) {
                            echo 'background-color: #ffe0e0; border: 1px solid var(--color-peligro); color: var(--color-peligro);';
                        } else {
                            echo 'background-color: #e6ffe6; border: 1px solid var(--color-secundario-verde); color: #008000;';
                        }
                        ?>">
                    <?php echo $mensajeAccion; ?>
                </div>
            <?php endif; ?>

            <div style="display: flex; flex-wrap: wrap; gap: 30px;">
                
                <div class="datos-section" style="flex: 1; min-width: 300px;">
                    
                    <?php if (!$modoEdicion): ?>
                        
                        <h3>Mis Datos</h3>
                        
                        <div class="dato-item form-group"><label>Nombre:</label><p class="dato-valor"><?php echo htmlspecialchars($nombre) ?></p></div>
                        <div class="dato-item form-group"><label>Correo:</label><p class="dato-valor"><?php echo htmlspecialchars($email) ?></p></div>
                        <div class="dato-item form-group"><label>Teléfono:</label><p class="dato-valor"><?php echo htmlspecialchars($telefono ?: 'No especificado') ?></p></div>
                        <div class="dato-item form-group"><label>Tipo de Usuario:</label><p class="dato-valor"><?php echo htmlspecialchars($tipoUsuarioActual) ?></p></div>
                        
                        <a href="perfilUsuario.php?mode=editar" class="cta-button" 
                           style="background-color: var(--color-principal-morado); color: var(--color-blanco) !important; margin-top: 20px;">
                            Editar Datos
                        </a>

                    <?php else: ?>

                        <h3>Editar Datos</h3>
                        
                        <form action="guardarEdicionUsuario.php" method="POST">
                            
                            <input type="hidden" name="validacion_email" value="<?php echo $validacionActual ? 'true' : 'false'; ?>">
                            <input type="hidden" name="acepto_terminos" value="<?php echo $acepto_terminos ?>">

                            <div class="form-group">
                                <label for="nombre">Nombre Completo (campo 'nombre'):</label>
                                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Correo Electrónico (campo 'email' - No modificable):</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly style="background-color: #f1f1f1; cursor: not-allowed;">
                            </div>
                            
                            <div class="form-group">
                                <label>Estado del Correo:</label>
                                <p style="margin: 0; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-weight: 600; 
                                          background-color: <?php echo $validacionActual ? '#e6ffe6' : '#ffe0e0'; ?>; 
                                          color: <?php echo $validacionActual ? '#008000' : 'var(--color-peligro)'; ?>">
                                    <?php echo $validacionActual ? 'Verificado' : 'Pendiente de Verificación'; ?>
                                </p>
                            </div>

                            <div class="form-group">
                                <label for="telefono">Teléfono (campo 'telefono'):</label>
                                <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" placeholder="Ej: 600123456">
                            </div>

                            <div class="form-group">
                                <label for="tipo_usuario">Tipo de Usuario:</label>
                                <select id="tipo_usuario" name="tipo_usuario" required>
                                    <option value="asesoria" <?php if ($tipoUsuarioActual == 'asesoria') echo 'selected'; ?>>Asesoría y planes de salud</option>
                                    <option value="formacion" <?php if ($tipoUsuarioActual == 'formacion') echo 'selected'; ?>>Formaciones</option>
                                    <option value="empresa" <?php if ($tipoUsuarioActual == 'empresa') echo 'selected'; ?>>Consultoría para Empresas (Formulación)</option>
                                    <option value="otro" <?php if ($tipoUsuarioActual == 'otro') echo 'selected'; ?>>Otro Motivo</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="cta-button morado" style="width: 100%; margin-top: 15px;">
                                Guardar Cambios
                            </button>
                            
                            <a href="perfilUsuario.php" class="cta-button" 
                               style="width: 100%; display: block; text-align: center; margin-top: 10px; background-color: #ccc; color: var(--color-texto-oscuro) !important;">
                                Cancelar
                            </a>
                        </form>

                    <?php endif; ?>
                    </div>
                
                <div class="avatar-section" style="flex: 1; min-width: 250px;">
                    <h3>Avatar y Validación</h3>

                    <div class="avatar-display" style="text-align: center; margin-bottom: 20px;">
                        <img id="perfil-avatar-grande" src="<?php echo htmlspecialchars($imagenPerfil) ?>" alt="Avatar de Usuario" class="avatar-grande">
                    </div>

                    <form action="actualizarFotoPerfil.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="form-group">
                            <label for="avatar_file">Cambiar Imagen:</label>
                            <input type="file" name="imagen_perfil" id="avatar_file" accept="image/*" required>
                            <input type="hidden" value="<?php echo htmlspecialchars($idUsuario); ?>" name="id">
                        </div>
                        
                        <button type="submit" class="cta-button morado" style="width: 100%;">
                            Subir y Guardar Imagen
                        </button>
                        
                        <div style="margin-top: 25px; border-top: 1px solid #eee; padding-top: 15px;">
                            <p style="text-align: center; font-style: italic; color: var(--color-texto-gris);"><?php echo $mensajeEstado; ?></p>
                            <?php if ($mostrarBotonValidacion): ?>
                                <a href="enviarEmailConfirmacion.php" class="cta-button" 
                                   style="background-color: var(--color-secundario-verde); color: white !important; margin-top: 10px; display: block; text-align: center; text-decoration: none; padding: 10px 20px;">
                                    Recibir Enlace de Validación
                                </a>
                            <?php else: ?>
                                <p style="color: var(--color-secundario-verde); text-align: center; margin-top: 10px; font-weight: 600;">
                                    ¡Cuenta de correo verificada!
                                </p>
                            <?php endif; ?>
                        </div>
                        
                    </form>
                </div>
            </div>

        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2025 Wildvet. Todos los derechos reservados.</p>
    </footer>

</body>
</html>