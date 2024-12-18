<?php
session_start();
include('conexion.php');

// Verificar si el usuario está autenticado o es invitado
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['invitado_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

// Determinar el ID del usuario/invitado y el tipo de usuario
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $invitado_id = null; // No aplica para usuarios registrados
    $tipo_usuario = 'usuario';
} elseif (isset($_SESSION['invitado_id'])) {
    $invitado_id = $_SESSION['invitado_id'];
    $usuario_id = null; // No aplica para invitados
    $tipo_usuario = 'invitado';
}

// Obtener los datos enviados por JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$producto_id = $data['producto_id'];
$cantidad = $data['cantidad'];
$precio_unitario = $data['precio_unitario'];
$nota = isset($data['nota']) ? $data['nota'] : null;

// Verificar si el usuario ya tiene un carrito activo
$sqlCarrito = "
    SELECT id 
    FROM carrito 
    WHERE 
        (usuario_id = ? OR invitado_id = ?) 
        AND tipo_usuario = ? 
        AND entregado = 0 
    LIMIT 1";
$stmtCarrito = $conn->prepare($sqlCarrito);
$stmtCarrito->bind_param("iis", $usuario_id, $invitado_id, $tipo_usuario);
$stmtCarrito->execute();
$resultCarrito = $stmtCarrito->get_result();
$carrito = $resultCarrito->fetch_assoc();

// Si no hay carrito activo, crea uno
if (!$carrito) {
    $sqlCrearCarrito = "INSERT INTO carrito (usuario_id, invitado_id, tipo_usuario) VALUES (?, ?, ?)";
    $stmtCrearCarrito = $conn->prepare($sqlCrearCarrito);
    $stmtCrearCarrito->bind_param("iis", $usuario_id, $invitado_id, $tipo_usuario);
    if ($stmtCrearCarrito->execute()) {
        $carrito_id = $stmtCrearCarrito->insert_id;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear el carrito']);
        exit();
    }
} else {
    $carrito_id = $carrito['id'];
}

// Insertar o actualizar el detalle del carrito
$sqlDetalle = "
    INSERT INTO carrito_detalle (carrito_id, producto_id, cantidad, precio_unitario, nota)
    VALUES (?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE cantidad = cantidad + VALUES(cantidad), nota = VALUES(nota)";
$stmtDetalle = $conn->prepare($sqlDetalle);
$stmtDetalle->bind_param("iiids", $carrito_id, $producto_id, $cantidad, $precio_unitario, $nota);

if (!$stmtDetalle->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al agregar el producto al carrito: ' . $stmtDetalle->error]);
} else {
    echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito']);
}
?>
