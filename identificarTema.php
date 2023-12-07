<?php
// Configuración de conexión a MySQL
$servername = "localhost";
$username = "usuarioia";
$password = "123";
$dbname = "ProyectoIA";

function determinarTemaPorFrecuenciaRelativa($archivo, $conn)
{
    $contenido = file_get_contents($archivo);
    $contenido = strtolower($contenido);
    $palabras = preg_split('/\s+/', preg_replace('/[[:punct:]]/', '', $contenido));

    $contadorPalabras = array_count_values($palabras);

    $tema1 = 0;
    $tema2 = 0;

    foreach ($contadorPalabras as $palabra => $frecuencia) {
        $stmt = $conn->prepare("SELECT frecuencia, tema FROM palabras WHERE palabra = ? ORDER BY frecuencia DESC LIMIT 1");
        $stmt->bind_param("s", $palabra);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($frecuenciaPalabra, $tema);
            $stmt->fetch();

            // Calcular la frecuencia relativa
            $frecuenciaRelativa = $frecuencia / $frecuenciaPalabra;

            if ($tema == 1) {
                $tema1 += $frecuenciaRelativa;
            } elseif ($tema == 2) {
                $tema2 += $frecuenciaRelativa;
            }
        }
    }

    if ($tema1 > $tema2) {
        return 1;
    } elseif ($tema2 > $tema1) {
        return 2;
    } else {
        // En caso de empate, puedes manejarlo de acuerdo a tus necesidades
        return "Tema no identificado";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_FILES["archivo"]["tmp_name"])) {
        $archivoTmp = $_FILES["archivo"]["tmp_name"];

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Error de conexión a MySQL: " . $conn->connect_error);
        }

        $tema = determinarTemaPorFrecuenciaRelativa($archivoTmp, $conn);

        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Determinar Tema</title>
</head>
<body>
    <h2>Determinar Tema por Frecuencia Relativa</h2>

    <form method="post" enctype="multipart/form-data">
        <label for="archivo">Seleccionar Archivo:</label>
        <input type="file" name="archivo" id="archivo" accept=".txt" required>
        <br>
        <input type="submit" value="Determinar Tema">
    </form>

    <?php
    if (isset($tema)) {
        echo "<h3>Resultado:</h3>";
        echo "<p>El tema del archivo es: " . $tema . "</p>";
    }
    ?>
</body>
</html>