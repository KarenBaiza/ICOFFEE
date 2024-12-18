<?php
session_start();
include('conexion.php');

// Verificar si el usuario es un trabajador autorizado
if (!isset($_SESSION['numero_control']) || $_SESSION['numero_control'] !== '00000000') {
    header("Location: index.php");
    exit();
}

$numero_control = $_SESSION['numero_control'] ?? '';

// Obtener los pedidos pendientes
$sql_pedidos = "
    SELECT c.id AS carrito_id, u.numero_control, u.celular, c.fecha, c.tipo_entrega, c.edificio, c.metodo_pago, c.tiempo_estimado
    FROM carrito c
    INNER JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.entregado = 0 AND c.tipo_entrega IS NOT NULL AND c.confirmado = 1";
$result_pedidos = $conn->query($sql_pedidos);

// Obtener los productos para editar disponibilidad
$sql_productos = "SELECT id, nombre, disponible FROM productos";
$result_productos = $conn->query($sql_productos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Cafetería Escolar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="agregar_producto.php">Agregar Producto</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Perfil</a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="perfilDropdown">
                            <li>
                                <a class="dropdown-item">
                                    <strong>Número de Control:</strong>
                                    <span id="numero-control"><?php echo htmlspecialchars($numero_control); ?></span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>ICoffee</h1>

        <!-- Sección de Pedidos Pendientes -->
        <h2 class="mt-4">Pedidos Pendientes</h2>
        <?php if ($result_pedidos->num_rows > 0): ?>
            <?php while ($pedido = $result_pedidos->fetch_assoc()): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Pedido ID:</strong> <?php echo htmlspecialchars($pedido['carrito_id']); ?> -
                        <strong>Número de Control:</strong> <?php echo htmlspecialchars($pedido['numero_control']); ?> -
                        <strong>Celular:</strong> <?php echo htmlspecialchars($pedido['celular']); ?>
                    </div>
                    <div class="card-body">
                        <p><strong>Tipo de Entrega:</strong> <?php echo htmlspecialchars($pedido['tipo_entrega']); ?></p>
                        <p><strong>Edificio:</strong> <?php echo htmlspecialchars($pedido['edificio'] ?? 'N/A'); ?></p>
                        <p><strong>Método de Pago:</strong> <?php echo htmlspecialchars($pedido['metodo_pago']); ?></p>
                        <p><strong>Fecha de Pedido:</strong> <?php echo htmlspecialchars($pedido['fecha']); ?></p>

                        <!-- Productos del Pedido -->
                        <h5>Productos:</h5>
                        <ul>
                            <?php
                            $sql_productos_pedido = "
                                SELECT p.nombre, d.cantidad, d.precio_unitario, d.nota,
                                       (d.cantidad * d.precio_unitario) AS total
                                FROM carrito_detalle d
                                INNER JOIN productos p ON d.producto_id = p.id
                                WHERE d.carrito_id = ?";
                            $stmt_productos_pedido = $conn->prepare($sql_productos_pedido);
                            $stmt_productos_pedido->bind_param("i", $pedido['carrito_id']);
                            $stmt_productos_pedido->execute();
                            $result_productos_pedido = $stmt_productos_pedido->get_result();

                            $total_pedido = 0;
                            while ($producto = $result_productos_pedido->fetch_assoc()):
                                $total_pedido += $producto['total'];
                            ?>
                                <li>
                                    <?php echo htmlspecialchars($producto['nombre']); ?> -
                                    Cantidad: <?php echo htmlspecialchars($producto['cantidad']); ?> -
                                    Precio Unitario: $<?php echo htmlspecialchars(number_format($producto['precio_unitario'], 2)); ?> MXN -
                                    Nota: <?php echo htmlspecialchars($producto['nota'] ?? 'Sin nota'); ?> -
                                    Total: $<?php echo htmlspecialchars(number_format($producto['total'], 2)); ?> MXN
                                </li>
                            <?php endwhile; ?>
                        </ul>
                        <p><strong>Total del Pedido:</strong> $<?php echo htmlspecialchars(number_format($total_pedido, 2)); ?> MXN</p>

                        <!-- Asignar Tiempo y Marcar como Entregado -->
                        <form action="asignar_tiempo.php" method="POST" class="d-flex mb-2">
                            <input type="hidden" name="carrito_id" value="<?php echo $pedido['carrito_id']; ?>">
                            <input type="number" name="tiempo_estimado" class="form-control me-2" value="<?php echo htmlspecialchars($pedido['tiempo_estimado'] ?? ''); ?>" placeholder="Minutos" required>
                            <button type="submit" class="btn btn-primary btn-sm">Asignar Tiempo</button>
                        </form>
                        <form action="marcar_entregado.php" method="POST">
                            <input type="hidden" name="carrito_id" value="<?php echo $pedido['carrito_id']; ?>">
                            <button type="submit" class="btn btn-success btn-sm">Marcar como Entregado</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay pedidos pendientes.</p>
        <?php endif; ?>

        <!-- Sección de Disponibilidad de Productos -->
        <h2 class="mt-4">Disponibilidad de Productos</h2>
        <form action="actualizar_disponibilidad.php" method="POST">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Disponible</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($producto = $result_productos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td>
                                <select name="disponible[<?php echo $producto['id']; ?>]" class="form-select">
                                    <option value="1" <?php echo $producto['disponible'] ? 'selected' : ''; ?>>Sí</option>
                                    <option value="0" <?php echo !$producto['disponible'] ? 'selected' : ''; ?>>No</option>
                                </select>
                            </td>
                            <td>
                                <form action="editar_producto.php" method="GET" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                    <button type="submit" class="btn btn-warning btn-sm">Editar</button>
                                </form>
                                <form action="eliminar_producto.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-success">Actualizar Disponibilidad</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>