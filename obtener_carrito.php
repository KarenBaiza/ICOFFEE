<?php
session_start();
include('conexion.php');

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]); // Retorna un arreglo vacío si no está autenticado
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener los productos del carrito, incluyendo notas
$sql = "SELECT cd.id, p.nombre, cd.cantidad, cd.precio_unitario, cd.nota
        FROM carrito_detalle cd
        JOIN carrito c ON cd.carrito_id = c.id
        JOIN productos p ON cd.producto_id = p.id
        WHERE c.usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$carrito = [];
while ($row = $result->fetch_assoc()) {
    $carrito[] = $row;
}

echo json_encode($carrito);
?>
