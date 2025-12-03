<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', 'Palomer@s'); 
define('DB_NAME', 'frances_balaguera');

function conectarDB(): PDO {
    $dsn = 'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    try {
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $opciones);
        return $pdo;
    } catch (\PDOException $e) {
        throw new \PDOException("Conexión fallida: " . $e->getMessage(), (int)$e->getCode());
    }
}

function crearUsuarioDB(array $datos): bool {
    $pdo = conectarDB();
    $sql = "INSERT INTO usuarios (Nombre, email, telefono, total_gastado, password, tipo_usuario, acepto_terminos, rol, validacion_email, imagen_perfil) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        
        $parametros = [
            $datos['nombre'],
            $datos['email'],
            $datos['telefono'],
            $datos['total_gastado'],
            $datos['password'],
            $datos['tipo_usuario'],
            (int)($datos['acepto_terminos']),
            $datos['rol'] ?? 'user',
            (int)($datos['validacion_email']),
            null
        ];
        
        return $stmt->execute($parametros);
    } catch (\PDOException $e) {
        error_log("Error al crear usuario: " . $e->getMessage());
        echo('<br><a href="../html/registro.html">Volver al formulario</a>');
        return false;
    }
}

function cargarUsuariosDB(): array {
    $pdo = conectarDB();
    $sql = "SELECT IDusuario as id, Nombre as nombre, email, telefono, total_gastado, password, tipo_usuario, acepto_terminos, rol, validacion_email, imagen_perfil FROM usuarios";
    
    try {
        $stmt = $pdo->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($usuarios as &$usuario) {
            $usuario['validacion_email'] = (bool)$usuario['validacion_email'];
        }
        
        return $usuarios;
    } catch (\PDOException $e) {
        error_log("Error al cargar usuarios: " . $e->getMessage());
        return [];
    }
}

function cargarUsuarioPorIdDB(int $id): ?array {
    $pdo = conectarDB();
    $sql = "SELECT IDusuario as id, Nombre as nombre, email, telefono, total_gastado, rol, tipo_usuario, acepto_terminos, validacion_email, imagen_perfil FROM usuarios WHERE IDusuario = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
        
        return $usuario ?: null;
    } catch (\PDOException $e) {
        error_log("Error al cargar usuario por ID: " . $e->getMessage());
        return null;
    }
}


function buscarUsuarioPorEmailDB(string $email): ?array {
    $pdo = conectarDB();
    $sql = "SELECT IDusuario as id, Nombre as nombre, email, telefono, total_gastado, rol, tipo_usuario, imagen_perfil FROM usuarios WHERE email = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        return $usuario ?: null;
    } catch (\PDOException $e) {
        error_log("Error al buscar usuario por email: " . $e->getMessage());

        return null;
    }
}

function actualizarUsuarioDB(int $id, array $datos): bool {
    $pdo = conectarDB();
    $sql = "UPDATE usuarios SET Nombre = ?, email = ?, telefono = ?, total_gastado = ?, tipo_usuario = ?, acepto_terminos = ?, rol = ?, validacion_email = ?, imagen_perfil = ? WHERE IDusuario = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        
        $parametros = [
            $datos['nombre'], 
            $datos['email'], 
            $datos['telefono'], 
            $datos['total_gastado'], 
            $datos['tipo_usuario'], 
            (int)($datos['acepto_terminos']), 
            $datos['rol'], 
            (int)($datos['validacion_email']), 
            $datos['imagen_perfil'], 
            $id
        ];
        
        return $stmt->execute($parametros);
    } catch (\PDOException $e) {
        error_log("Error al actualizar usuario: " . $e->getMessage());
        return false;
    }
}

function eliminarUsuarioDB(int $id): bool {
    $pdo = conectarDB();
    $sql = "DELETE FROM usuarios WHERE IDusuario = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (\PDOException $e) {
        error_log("Error al eliminar usuario: " . $e->getMessage());
        return false;
    }
}

function crearPedidoDB(array $datos): bool {
    $pdo = conectarDB();
    $sql = "INSERT INTO pedidos (IDusuario, nombre_usuario, fecha, tipo_servicio, descripcion, total, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        
        $parametros = [
            $datos['IDusuario'],
            $datos['nombre_usuario'],
            $datos['fecha'],
            $datos['tipo_servicio'],
            $datos['descripcion'],
            $datos['total'],
            $datos['estado']
        ];
        
        return $stmt->execute($parametros);
    } catch (\PDOException $e) {
        error_log("Error al crear pedido: " . $e->getMessage());
        return false;
    }
}

function cargarPedidosDB(): array {
    $pdo = conectarDB();
    $sql = "SELECT p.IDpedido as id, p.IDusuario as cliente_id, u.Nombre as cliente_nombre, p.fecha, p.tipo_servicio, p.descripcion, p.total, p.estado 
            FROM pedidos p
            JOIN usuarios u ON p.IDusuario = u.IDusuario";
    
    try {
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (\PDOException $e) {
        error_log("Error al cargar pedidos: " . $e->getMessage());
        return [];
    }
}


function cargarPedidoPorIdDB(int $id): ?array {
    $pdo = conectarDB();
    $sql = "SELECT p.IDpedido as id, p.IDusuario as cliente_id, u.Nombre as cliente_nombre, p.fecha, p.tipo_servicio, p.descripcion, p.total, p.estado 
            FROM pedidos p
            JOIN usuarios u ON p.IDusuario = u.IDusuario
            WHERE p.IDpedido = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $pedido = $stmt->fetch();
        
        return $pedido ?: null;
    } catch (\PDOException $e) {
        error_log("Error al cargar pedido por ID: " . $e->getMessage());
        return null;
    }
}

function actualizarPedidoDB(int $id, array $datos): bool {
    $pdo = conectarDB();
    $sql = "UPDATE pedidos SET fecha = ?, tipo_servicio = ?, descripcion = ?, total = ?, estado = ? WHERE IDpedido = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        
        $parametros = [
            $datos['fecha'], 
            $datos['tipo_servicio'], 
            $datos['descripcion'], 
            $datos['total'], 
            $datos['estado'], 
            $id
        ];
        
        return $stmt->execute($parametros);
    } catch (\PDOException $e) {
        error_log("Error al actualizar pedido: " . $e->getMessage());
        return false;
    }
}

function eliminarPedidoDB(int $id): bool {
    $pdo = conectarDB();
    $sql = "DELETE FROM pedidos WHERE IDpedido = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (\PDOException $e) {
        error_log("Error al eliminar pedido: " . $e->getMessage());
        return false;
    }
}

function actualizarImagenPerfil(int $idUsuario, string $contenidoBinario): bool {
    $pdo = conectarDB();
    $sql = "UPDATE usuarios SET imagen_perfil = ? WHERE IDusuario = ?";
    
    try {
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(1, $contenidoBinario, PDO::PARAM_LOB); 
        $stmt->bindParam(2, $idUsuario, PDO::PARAM_INT);
        
        return $stmt->execute();
    } catch (\PDOException $e) {
        error_log("Error al actualizar BLOB: " . $e->getMessage());
        return false;
    }
}

function cargarImagenBlobPorIdDB(int $idUsuario): ?string {
    $pdo = conectarDB();
    $sql = "SELECT imagen_perfil FROM usuarios WHERE IDusuario = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idUsuario]);
        
        $fila = $stmt->fetch();

        return $fila['imagen_perfil'];
    } catch (\PDOException $e) {
        error_log("Error al cargar BLOB: " . $e->getMessage());
        return null;
    }
}

function guardarTokenDB(int $idUsuario, string $token): bool {
    $pdo = conectarDB();
    $sql = "UPDATE usuarios SET validation_token = ?, validacion_email = 0 WHERE IDusuario = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$token, $idUsuario]);
    } catch (\PDOException $e) {
        error_log("Error al guardar token: " . $e->getMessage());
        return false;
    }
}

function cargarUsuarioPorEmailYTokenDB(string $email, string $token): ?array {
    $pdo = conectarDB();
    $sql = "SELECT IDusuario, Nombre FROM usuarios 
            WHERE email = ? AND validation_token = ? AND validacion_email = 0";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $token]);
        $usuario = $stmt->fetch();
        
        return $usuario ?: null;
    } catch (\PDOException $e) {
        error_log("Error al cargar usuario por email/token: " . $e->getMessage());
        return null;
    }
}

function actualizarEstadoValidacionDB(int $idUsuario): bool {
    $pdo = conectarDB();
    $sql = "UPDATE usuarios SET validacion_email = 1, validation_token = NULL WHERE IDusuario = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$idUsuario]);
    } catch (\PDOException $e) {
        error_log("Error al actualizar validación: " . $e->getMessage());
        return false;
    }
}

function cargarPasswordDB(int $idUsuario): mixed{
    $pdo = conectarDB();
    $sql = "SELECT password FROM usuarios WHERE IDusuario = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idUsuario]);
        $password = $stmt->fetchColumn();
        
        return $password;
    } catch (\PDOException $e) {
        error_log("Error al cargar password por ID: " . $e->getMessage());
        return null;
    }
}
?>