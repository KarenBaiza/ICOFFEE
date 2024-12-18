<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_control = isset($_POST['numero_control']) ? $_POST['numero_control'] : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
    $celular = isset($_POST['celular']) ? $_POST['celular'] : '';

    // Validar que el número de control tenga 8 dígitos
    if (!preg_match('/^\d{8}$/', $numero_control)) {
        die("El número de control debe tener exactamente 8 dígitos.");
    }

    // Validar que la contraseña no exceda 10 caracteres
    if (strlen($contrasena) > 10) {
        die("La contraseña no puede exceder los 10 caracteres.");
    }

    // Validar que el número de celular tenga 10 dígitos
    if (!preg_match('/^\d{10}$/', $celular)) {
        die("El número de celular debe tener exactamente 10 dígitos.");
    }

    // Verificar que el número de celular no esté registrado
    $sql_verificar_celular = "SELECT id FROM usuarios WHERE celular = ?";
    $stmt_verificar_celular = $conn->prepare($sql_verificar_celular);
    $stmt_verificar_celular->bind_param("s", $celular);
    $stmt_verificar_celular->execute();
    $result_verificar_celular = $stmt_verificar_celular->get_result();
    if ($result_verificar_celular->num_rows > 0) {
        die("El número de celular ya está registrado.");
    }

    // Verificar que el número de control sea único, excepto si es "11111111"
    if ($numero_control !== "11111111") {
        $sql_verificar_control = "SELECT id FROM usuarios WHERE numero_control = ?";
        $stmt_verificar_control = $conn->prepare($sql_verificar_control);
        $stmt_verificar_control->bind_param("s", $numero_control);
        $stmt_verificar_control->execute();
        $result_verificar_control = $stmt_verificar_control->get_result();
        if ($result_verificar_control->num_rows > 0) {
            die("El número de control ya está registrado.");
        }
        $stmt_verificar_control->close();
    }

    // Insertar en la base de datos
    $sql = "INSERT INTO usuarios (numero_control, contrasena, celular) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $numero_control, $contrasena, $celular);

    if ($stmt->execute()) {
        echo "Usuario registrado con éxito.";
        header("Location: index.php"); // Redirige al inicio de sesión después del registro
        exit();
    } else {
        echo "Error al registrar el usuario: " . $stmt->error;
    }

    $stmt->close();
    $stmt_verificar_celular->close();
    $conn->close();
}
?>
