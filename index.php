<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafetería Escolar - Inicio de Sesión</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">ICoffee</h1>
        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <ul class="nav nav-tabs" id="authTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Iniciar Sesión</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Registrar Usuario</button>
                    </li>
                </ul>
                <div class="tab-content mt-4" id="authTabsContent">
                    <!-- Inicio de Sesión -->
                    <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                            <!-- Formulario de Inicio de Sesión -->
                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="identificador" class="form-label">Número de Control o Celular</label>
                                <input type="text" class="form-control" id="identificador" name="identificador" required>
                            </div>
                            <div class="mb-3">
                                <label for="contrasena" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary">Iniciar Sesión</button>
                        </form>
                    
    

                    </div>

                    <!-- Registro -->
                    <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                        <form action="registro.php" method="POST">
                            <div class="mb-3">
                                <label for="control" class="form-label">Número de Control</label>
                                <input type="text" name="numero_control" class="form-control" id="numero_control" maxlength="8" pattern="\d{8}" placeholder="Utiliza 11111111 si no cuentas con un numero de control" required>
                            </div>

                            <div class="mb-3">
                                <label for="celular" class="form-label">Número de Celular</label>
                                <input type="text" name="celular" class="form-control" id="celular" maxlength="10" pattern="\d{10}" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" name="contrasena" class="form-control" id="contrasena" maxlength="10" required>
                            </div>
    
                            <button type="submit" class="btn btn-success w-100">Registrar Usuario</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
