<?php
session_start();
include('conexion.php');

// Verificar si el usuario es un trabajador autorizado
if (!isset($_SESSION['numero_control']) || $_SESSION['numero_control'] !== '00000000') {
    header("Location: index.php");
    exit();
}

// Validar datos recibidos
if (!isset($_POST['carrito_id']) || empty($_POST['carrito_id'])) {
    header("Location: admin.php?error=carrito_id_faltante");
    exit();
}

$carrito_id = $_POST['carrito_id'];

// Marcar el pedido como entregado
$sql = "UPDATE carrito SET entregado = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $carrito_id);

if ($stmt->execute()) {
    // Redirigir a admin.php con un mensaje de éxito
    header("Location: admin.php?success=entregado");
} else {
    // Redirigir a admin.php con un mensaje de error
    header("Location: admin.php?error=fallo_entregado");
}

$stmt->close();
$conn->close();
?>
