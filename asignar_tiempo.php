<?php
session_start();
include('conexion.php');

// Verificar si el usuario es un trabajador
if (!isset($_SESSION['numero_control']) || $_SESSION['numero_control'] !== '00000000') {
    header("Location: index.php");
    exit();
}

// Obtener datos del formulario
$carrito_id = $_POST['carrito_id'];
$tiempo_estimado = $_POST['tiempo_estimado'];

// Actualizar el tiempo estimado en la base de datos
$sql = "UPDATE carrito SET tiempo_estimado = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->bind_param("ii", $tiempo_estimado, $carrito_id);
if ($stmt->execute()) {
    header("Location: admin.php?mensaje=Tiempo actualizado correctamente");
    exit();
} else {
    echo "Error al actualizar el tiempo estimado: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
