<?php
include('conexion.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener el producto por ID
    $sql = "SELECT * FROM productos WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $producto = $result->fetch_assoc();
    } else {
        die('Producto no encontrado');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar producto
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    // Manejo de imagen (opcional)
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $rutaImagen = 'imagenes/' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen);

        $sql = "UPDATE productos SET nombre=?, precio=?, descripcion=?, imagen=?, disponible=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sdssii', $nombre, $precio, $descripcion, $rutaImagen, $disponible, $id);
    } else {
        $sql = "UPDATE productos SET nombre=?, precio=?, descripcion=?, disponible=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sdsii', $nombre, $precio, $descripcion, $disponible, $id);
    }

    $stmt->execute();
    header('Location: admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Editar Producto</h1>
        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <form action="editar_producto.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($producto['id']); ?>">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio:</label>
                        <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen:</label>
                        <input type="file" class="form-control" id="imagen" name="imagen">
                        <p class="mt-2">Imagen actual:</p>
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Producto" class="img-thumbnail" width="150">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="disponible" name="disponible" <?php echo $producto['disponible'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="disponible">Disponible</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
