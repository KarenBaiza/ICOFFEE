<?php
session_start();
include('conexion.php');

// Verificar si el usuario es un trabajador
if (!isset($_SESSION['numero_control']) || $_SESSION['numero_control'] !== '00000000') {
    header("Location: index.php");
    exit();
}

// Procesar el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    // Manejo de la imagen
    $imagen = '';
    if (!empty($_FILES['imagen']['name'])) {
        $targetDir = "imagenes/";
        $targetFile = $targetDir . basename($_FILES['imagen']['name']);
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFile)) {
            $imagen = $targetFile;
        } else {
            echo "Error al subir la imagen.";
        }
    }

    // Insertar el producto en la base de datos
    $sql = "INSERT INTO productos (nombre, precio, descripcion, imagen, disponible) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdssi", $nombre, $precio, $descripcion, $imagen, $disponible);

    if ($stmt->execute()) {
        header("Location: admin.php?mensaje=Producto agregado exitosamente");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Agregar Producto</h1>
    <form action="agregar_producto.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Producto</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio</label>
            <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
        </div>
        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen</label>
            <input type="file" class="form-control" id="imagen" name="imagen" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="disponible" name="disponible">
            <label class="form-check-label" for="disponible">Disponible</label>
        </div>
        <button type="submit" class="btn btn-primary">Agregar Producto</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>