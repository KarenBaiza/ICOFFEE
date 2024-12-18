<?php
session_start();
include('conexion.php');

// Verificar si el usuario es un trabajador
if (!isset($_SESSION['numero_control']) || $_SESSION['numero_control'] !== '00000000') {
    header("Location: index.php");
    exit();
}

// Actualizar disponibilidad de productos
if (isset($_POST['disponible'])) {
    foreach ($_POST['disponible'] as $producto_id => $disponible) {
        $sql = "UPDATE productos SET disponible = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $disponible, $producto_id);
        $stmt->execute();
    }
}

header("Location: admin.php");
exit();
