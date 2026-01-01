//Este archivo lo usamos para debuggear el schema de la base de datos

<?php
include_once __DIR__ . "/src/lib/funciones.php";

$db = conectarDb();
$result = $db->query("DESCRIBE atenciones");
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
