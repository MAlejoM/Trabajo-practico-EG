<?php

include_once("funciones.php");

if (isset($_GET['error'])) {
    $error_code = $_GET['error'];

    // Ahora puedes trabajar con el código de error
    if ($error_code == 1) {
        echo "Ha ocurrido un error durante el inicio de sesión.";
        // Aquí puedes agregar más código para manejar este error específico
    }
    // Puedes agregar más condiciones para manejar otros códigos de error
}

if (isset($_POST['nombre']) && isset($_POST['contrasenia'])) {
    $nombre = $_POST['nombre'];
    $contrasenia = $_POST['contrasenia'];
    
    $query = "SELECT * FROM datosUsuario WHERE nombre = '$nombre' AND contrasenia = '$contrasenia'";
    $resultados = consultaSql($query);
    $cantidad = mysqli_num_rows($resultados);
    
    if ($cantidad == 1) {
        $usuario = mysqli_fetch_array($resultados);
        session_start();
        $_SESSION['usuario'] = $usuario;
        echo "<script>alert('SE LOGUEO EXISTOSAMENTE');</script>";
        header("Location: index.php");
    } else {
        echo "<script>alert('ERROR AL LOGUEARS');</script>";
        header("Location: login.php?error=1");
    }
}

?>

<h2>Iniciar sesión</h2>
    <form method="post" action="login.php">
        <label for="username">usuario:</label>
        <input type="text" name="nombre" required><br><br>
        <label for="password">contrasenia:</label>
        <input type="password" name="contrasenia" required><br><br>
        <input type="submit" value="Iniciar sesión">
        <input type="submit" value="Crear usuario" formaction="signup.php">

    </form>