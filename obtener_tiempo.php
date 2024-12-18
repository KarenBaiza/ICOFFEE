<?php
session_start();
include('conexion.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['numero_control'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit();
}

$numero_control = $_SESSION['numero_control'];

// Obtener el tiempo restante del pedido más reciente
$sql = "SELECT tiempo_estimado FROM carrito WHERE usuario_id = ? AND entregado = 0 ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$result = $stmt->get_result();
$pedido = $result->fetch_assoc();

if ($pedido) {
    echo json_encode(['tiempo_restante' => $pedido['tiempo_estimado']]);
} else {
    echo json_encode(['tiempo_restante' => null]);
}
?>
