<?php
session_start();
include('conexion.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener datos enviados desde el formulario
$data = json_decode(file_get_contents('php://input'), true);
$tipo_entrega = $data['tipo_entrega'] ?? 'Recoger';
$edificio = ($tipo_entrega === 'Entrega') ? $data['edificio'] : null;
$metodo_pago = $data['metodo_pago'] ?? 'Efectivo';

// Actualizar el carrito actual como confirmado
$sql_confirmar_pedido = "UPDATE carrito 
                         SET tipo_entrega = ?, edificio = ?, metodo_pago = ?, confirmado = 1 
                         WHERE usuario_id = ? AND entregado = 0";
$stmt_confirmar = $conn->prepare($sql_confirmar_pedido);
if (!$stmt_confirmar) {
    error_log("Error al preparar consulta UPDATE carrito: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Error interno en el servidor al confirmar el pedido.']);
    exit();
}

$stmt_confirmar->bind_param("sssi", $tipo_entrega, $edificio, $metodo_pago, $usuario_id);

if ($stmt_confirmar->execute()) {
    echo json_encode(['success' => true, 'message' => 'Pedido registrado correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo registrar el pedido.']);
}

// Cerrar conexiones
$stmt_confirmar->close();
$conn->close();
?>
