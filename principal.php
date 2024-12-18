<?php
session_start();
include('conexion.php');

// Verificar si el usuario está como invitado o registrado
if (isset($_SESSION['invitado']) && $_SESSION['invitado']) {
    $identificador = htmlspecialchars($_SESSION['celular']);
} else {
    $identificador = isset($_SESSION['numero_control']) ? htmlspecialchars($_SESSION['numero_control']) : 'Desconocido';
}


// Verificar si hay sesión activa
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['invitado'])) {
    header("Location: login.php");
    exit();
}

// Obtener los productos del menú
$sql = "SELECT id, nombre, precio, imagen, descripcion, disponible FROM productos";
$result = $conn->query($sql);
$productos = $result->fetch_all(MYSQLI_ASSOC);

// Filtrar productos si se ingresó una búsqueda
$searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
if (!empty($searchQuery)) {
    $sql .= " WHERE LOWER(nombre) LIKE '%" . $conn->real_escape_string($searchQuery) . "%'";
    $result = $conn->query($sql);
    $productos = $result->fetch_all(MYSQLI_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafetería Escolar</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <header class="bg-primary text-white py-3">
        <div class="container d-flex align-items-center">
            <h1 class="fs-3 m-0">ICoffee</h1>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Cafetería Escolar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                         <a class="nav-link" id="tiempo-restante">Tiempo restante: Cargando...</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="principal.php">Menú</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">Carrito</a>
                    </li>

                    <!-- Perfil -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Perfil
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="perfilDropdown">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <strong>Identificador:</strong>
                                    <span id="identificador"><?php echo $identificador; ?></span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Cerrar Sesión</a></li>
                        </ul>
                    </li>

                </ul>

                <!-- Formulario de búsqueda -->
                <form class="d-flex ms-3" action="principal.php" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Buscar productos..." aria-label="Buscar">
                    <button class="btn btn-outline-primary" type="submit">Buscar</button>
                </form>
            </div>
        </div>
    </nav>




    
    <!-- Main Content -->
    <main class="container my-4">
        <section id="menu">
            <h2 class="text-center mb-4">Menú</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($productos as $producto): ?>
                    <div class="col">
                        <div class="card h-100">
                            <!-- Mostrar la imagen del producto -->
                            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                class="card-img-top rounded" 
                                alt="<?php echo htmlspecialchars($producto['nombre']); ?>">

                            <div class="card-body text-center">
                                <h3 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                <p class="card-text">Precio: $<?php echo number_format($producto['precio'], 2); ?> MXN</p>
                                <?php if ($producto['disponible']): ?>
                                    <!-- Campo para la cantidad -->
                                    <input type="number" class="form-control mb-2" value="1" min="1" id="cantidad-<?php echo $producto['id']; ?>">
                                    <!-- Campo para la nota -->
                                    <textarea class="form-control mb-3" id="nota-<?php echo $producto['id']; ?>" placeholder="Agregar nota (opcional)"></textarea>
                                    <!-- Botón para agregar al carrito -->
                                    <button class="btn btn-primary" onclick="agregarAlCarrito(
                                        <?php echo $producto['id']; ?>, 
                                        <?php echo $producto['precio']; ?>, 
                                        document.getElementById('cantidad-<?php echo $producto['id']; ?>').value,
                                        document.getElementById('nota-<?php echo $producto['id']; ?>').value
                                    )">Agregar al carrito</button>
                                <?php else: ?>
                                    <p class="text-danger">No disponible</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>


    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <p class="mb-0">&copy; 2024 Cafetería Escolar</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="menu.js"></script>
    <script>
        function actualizarTiempoRestante() {
            fetch('obtener_tiempo.php')
                .then(response => response.json())
                .then(data => {
                    const tiempoLink = document.getElementById('tiempo-restante');
                    if (data.tiempo_restante !== null && data.tiempo_restante > 0) {
                        tiempoLink.textContent = `Tu pedido tardará: ${data.tiempo_restante} mins`;
                    } else {
                        tiempoLink.textContent = 'No hay pedidos pendientes';
                    }
                })
                .catch(error => console.error('Error al obtener el tiempo restante:', error));
        }

        // Actualizar cada 30 segundos
        setInterval(actualizarTiempoRestante, 30000);
        actualizarTiempoRestante();

    </script>



</body>
</html>