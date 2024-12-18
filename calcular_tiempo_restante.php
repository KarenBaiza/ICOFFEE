<?php
session_start();
include('conexion.php');

// Obtener el carrito del usuario
$carrito_id = $_SESSION['carrito_id']; // Cambiar según cómo manejes la sesión

$sql = "SELECT tiempo_estimado, hora_asignacion FROM carrito WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $carrito_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $tiempo_asignado = $row['tiempo_estimado'];
    $hora_asignacion = strtotime($row['hora_asignacion']);
    $tiempo_transcurrido = (time() - $hora_asignacion) / 60;
    $tiempo_restante = max(0, $tiempo_asignado - $tiempo_transcurrido);

    echo json_encode(['tiempo_restante' => round($tiempo_restante)]);
} else {
    echo json_encode(['tiempo_restante' => null]);
}
$stmt->close();
$conn->close();
?>
