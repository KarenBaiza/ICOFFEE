<?php
session_start();
include('conexion.php');

// Verificar si el usuario está autenticado
debug_backtrace();
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

// Validar que se haya proporcionado el ID del detalle
if (!isset($_POST['detalle_id']) || empty($_POST['detalle_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de detalle no proporcionado']);
    exit();
}

$detalle_id = (int)$_POST['detalle_id']; // Convertir a entero por seguridad

// Preparar la consulta para eliminar el producto del carrito
$sql = "DELETE FROM carrito_detalle WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta: ' . $conn->error]);
    exit();
}

$stmt->bind_param("i", $detalle_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Producto eliminado del carrito']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
