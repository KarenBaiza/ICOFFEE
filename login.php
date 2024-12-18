<?php
session_start();
include('conexion.php');

// Verificar si se ha enviado el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $identificador = $_POST['identificador']; // Puede ser número de control o celular
    $contrasena = $_POST['contrasena'];

    // Verificar si se intenta iniciar sesión con el número de control predeterminado
    if ($identificador === '11111111') {
        $error = "Debes iniciar sesión con tu número de celular asociado a este número de control.";
    } else {
        // Consultar el usuario en la base de datos
        $sql = "SELECT * FROM usuarios WHERE (numero_control = ? OR celular = ?) AND contrasena = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $conn->error);
        }
        $stmt->bind_param("sss", $identificador, $identificador, $contrasena);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = "Número de control/celular o contraseña incorrectos.";
        } else {
            $usuario = $result->fetch_assoc();

            // Guardar datos del usuario en la sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['numero_control'] = $usuario['numero_control'];

            // Redirigir según el número de control
            if ($usuario['numero_control'] === '00000000') {
                header("Location: admin.php");
                exit();
            } else {
                header("Location: principal.php");
                exit();
            }
        }
    }
}

// Mostrar el error si existe
if (isset($error)) {
    echo "<div class='alert alert-danger'>$error</div>";
}
?>
