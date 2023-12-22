<?php
include_once("funciones.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['dni'])) {
        $dni = $_POST['dni'];

        $query = "SELECT * FROM datosUsuario WHERE dni = '$dni'";
        $resultados = consultaSql($query);
        $cantidad = mysqli_num_rows($resultados);

        echo '$resultados';
        if ($cantidad == 0) {
            if ($_POST['password'] == $_POST['passwordDuplicada']) {
                $nombre = $_POST['nombre'];
                $apellido = $_POST['apellido'];
                $email = $_POST['email'];
                $dni = $_POST['dni'];
                $password = $_POST['password'];

                $query = "INSERT INTO datosUsuario (nombre, apellido, email, dni, contrasenia) VALUES ('$nombre', '$apellido', '$email', '$dni', '$password')";
                $resultados = consultaSql($query);

                if ($resultados == true) {
                    header("Location: login.php");
                    exit; // Termina la ejecución del script después de redirigir
                } else {
                    echo "<h1>error, no se pudo realizar la conexión con la base de datos</h1>";
                }
            } else {
                echo "<h1>error, las 2 contraseñas son distintas</h1>";
            }
        } else {
            echo "<h1>error, el dni ingresado ya está registrado</h1>";
        }
    }
}
?>


<h2>Registro</h2>
    <form method="POST" action="signup.php">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" required><br><br>

        <label for="apellido">apellido:</label>
        <input type="text" name="apellido" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>

        <label for="dni">dni:</label>
        <input type="number" name="dni" required><br><br>

        <label for="password">Contraseña:</label>
        <input type="text" name="password" required><br><br>

        <label for="password">Contraseña nuevamente:</label>
        <input type="text" name="passwordDuplicada" required><br><br>

        <input type="submit" value="Registrarse">
    </form>

