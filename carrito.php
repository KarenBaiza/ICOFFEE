<?php
session_start();
include('conexion.php');

// Verificar si el usuario está autenticado (registrado o invitado)
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['invitado_id'])) {
    header("Location: index.php");
    exit();
}

// Determinar el identificador y tipo de usuario
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $numero_control = $_SESSION['numero_control'];
    $tipo_usuario = 'usuario';
} elseif (isset($_SESSION['invitado_id'])) {
    $usuario_id = $_SESSION['invitado_id']; // ID del invitado desde la tabla `invitados`
    $numero_control = $_SESSION['celular']; // Mostrar el número de celular como identificador
    $tipo_usuario = 'invitado';
}

// Obtener el ID del carrito activo
$sql_carrito = "SELECT id FROM carrito WHERE (usuario_id = ? OR invitado_id = ?) AND tipo_usuario = ? AND entregado = 0 LIMIT 1";
$stmt_carrito = $conn->prepare($sql_carrito);
$stmt_carrito->bind_param("iis", $usuario_id, $usuario_id, $tipo_usuario);
$stmt_carrito->execute();
$result_carrito = $stmt_carrito->get_result();
$carrito = $result_carrito->fetch_assoc();

if (!$carrito) {
    $productos = [];
    $total = 0;
} else {
    $carrito_id = $carrito['id'];

    // Obtener los productos del carrito
    $sql_productos = "
        SELECT d.id AS detalle_id, p.nombre, d.cantidad, d.precio_unitario, d.nota,
            (d.cantidad * d.precio_unitario) AS total
        FROM carrito_detalle d
        INNER JOIN productos p ON d.producto_id = p.id
        WHERE d.carrito_id = ?";
    $stmt_productos = $conn->prepare($sql_productos);
    $stmt_productos->bind_param("i", $carrito_id);
    $stmt_productos->execute();
    $result_productos = $stmt_productos->get_result();
    $productos = $result_productos->fetch_all(MYSQLI_ASSOC);

    // Calcular el total del carrito
    $total = array_sum(array_column($productos, 'total'));
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.paypal.com/sdk/js?client-id=AfLbZhCUlu91J10qnsTAbHWBxcddpry-uTuMTZ1PPVLVga2rhryz3joYrOornd84NCSSEQaWfL135XHU&currency=MXN"></script>
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
                    <li class="nav-item"><a class="nav-link" href="principal.php">Menú</a></li>
                    <li class="nav-item"><a class="nav-link" href="carrito.php">Carrito</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Carrito de Compras</h1>
        <p><strong>Número de Control:</strong> <?php echo htmlspecialchars($numero_control); ?></p>

        <?php if (!empty($productos)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Nota</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($producto['nota'] ?? 'Sin nota'); ?></td>
                            <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                            <td>$<?php echo htmlspecialchars($producto['precio_unitario']); ?> MXN</td>
                            <td>$<?php echo htmlspecialchars($producto['total']); ?> MXN</td>
                            <td>
                                <button 
                                    class="btn btn-danger btn-sm eliminar-producto" 
                                    data-id="<?php echo $producto['detalle_id']; ?>">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="mt-3">
                <h3>Total a pagar: $<?php echo number_format($total, 2, '.', ''); ?> MXN</h3>
            </div>

            <!-- Formulario de entrega -->
            <!-- Formulario de entrega -->
            <div class="mt-4">
                <h4>Selecciona tu método de entrega:</h4>
                <form id="entrega-form">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_entrega" id="recoger" value="Recoger" checked onchange="document.getElementById('seleccion-edificio').classList.add('d-none')">
                        <label class="form-check-label" for="recoger">Recoger en la cafetería</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_entrega" id="entrega" value="Entrega" onchange="document.getElementById('seleccion-edificio').classList.remove('d-none')">
                        <label class="form-check-label" for="entrega">Entregar en edificio</label>
                    </div>
                    <div class="mt-3 d-none" id="seleccion-edificio">
                        <label for="edificio" class="form-label">Selecciona el edificio:</label>
                        <select class="form-select" id="edificio" name="edificio">
                            <option value="Edificio A">Edificio A</option>
                            <option value="Edificio B">Edificio B</option>
                            <option value="Edificio C">Edificio C</option>
                        </select>
                    </div>
                </form>
            </div>


            <!-- Opciones de pago -->
            <div class="mt-4">
                <h4>Selecciona tu método de pago:</h4>
                <form id="pago-form">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="metodo_pago" id="pago_efectivo" value="Efectivo" checked>
                        <label class="form-check-label" for="pago_efectivo">Pago en Efectivo</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="metodo_pago" id="pago_paypal" value="PayPal">
                        <label class="form-check-label" for="pago_paypal">Pago con tarjeta</label>
                    </div>
                </form>
            </div>

            <!-- Contenedor de botones de PayPal -->
            <div id="paypal-button-container" class="mt-4" style="display: none;"></div>

        <?php else: ?>
            <div class="alert alert-warning" role="alert">No hay productos en el carrito.</div>
        <?php endif; ?>
    </div>

    <script>
        document.querySelectorAll('.eliminar-producto').forEach(button => {
            button.addEventListener('click', function () {
                const detalleId = this.dataset.id;
                fetch('eliminar_carrito.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `detalle_id=${detalleId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

        const paypalContainer = document.getElementById('paypal-button-container');
        const pagoPaypal = document.getElementById('pago_paypal');

        // Mostrar u ocultar PayPal dependiendo del método de pago seleccionado
        function actualizarMetodoPago() {
            if (pagoPaypal.checked) {
                paypalContainer.style.display = 'block';
                renderizarBotonesPaypal();
            } else {
                paypalContainer.style.display = 'none';
            }
        }

        document.querySelectorAll('input[name="metodo_pago"]').forEach(input => {
            input.addEventListener('change', actualizarMetodoPago);
        });

        actualizarMetodoPago();

        // Renderizar botones de PayPal
        function renderizarBotonesPaypal() {
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '<?php echo number_format($total, 2, '.', ''); ?>'
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        alert('Pago completado por ' + details.payer.name.given_name);
                        location.reload();
                    });
                },
                onError: function(err) {
                    console.error(err);
                    alert('Hubo un error al procesar el pago.');
                }
            }).render('#paypal-button-container');
        }
    </script>

    <div class="mt-4">
        <button id="hacer-pedido" class="btn btn-success btn-lg">Hacer Pedido</button>
    </div>

    <script>
        document.getElementById('hacer-pedido').addEventListener('click', function () {
            const tipoEntrega = document.querySelector('input[name="tipo_entrega"]:checked').value;
            const edificio = tipoEntrega === 'Entrega' ? document.getElementById('edificio').value : null;
            const metodoPago = document.querySelector('input[name="metodo_pago"]:checked').value;

            fetch('hacer_pedido.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    tipo_entrega: tipoEntrega,
                    edificio: edificio,
                    metodo_pago: metodoPago
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = "principal.php"; // Redirige después del éxito
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>

</body>
</html>
