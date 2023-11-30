<?php
// Configuración de conexión a MySQL
$servername = "localhost";
$username = "usuarioia";
$password = "123";
$dbname = "ProyectoIA";
// Función para procesar el archivo y actualizar la base de datos
function procesarArchivo($archivo, $tema, $conn)
{
    $contenido = file_get_contents($archivo);
    $contenido = strtolower($contenido);
    $palabras = preg_split('/\s+/', preg_replace('/[[:punct:]]/', '', $contenido));

    $contadorPalabras = array_count_values($palabras);

    foreach ($contadorPalabras as $palabra => $frecuencia) {
        $stmt = $conn->prepare("SELECT frecuencia FROM palabras WHERE tema = ? AND palabra = ?");
        $stmt->bind_param("is", $tema, $palabra);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // La palabra ya existe, obtener la frecuencia existente
            $frecuenciaExistente = 0;
            $stmt->bind_result($frecuenciaExistente);
            $stmt->fetch();

            // Calcular la frecuencia combinada
            $frecuenciaCombinada = $frecuencia + $frecuenciaExistente;

            // Actualizar la frecuencia en la base de datos
            $stmt = $conn->prepare("UPDATE palabras SET frecuencia = ? WHERE tema = ? AND palabra = ?");
            $stmt->bind_param("iss", $frecuenciaCombinada, $tema, $palabra);
            $stmt->execute();
        } else {
            // La palabra no existe, insertar nueva fila
            $stmt = $conn->prepare("INSERT INTO palabras (tema, palabra, frecuencia) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $tema, $palabra, $frecuencia);
            $stmt->execute();
        }
    }
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tema = isset($_POST["tema"]) ? $_POST["tema"] : 1;

    if (!empty($_FILES["archivo"]["tmp_name"])) {
        $archivoTmp = $_FILES["archivo"]["tmp_name"];
        $archivoNombre = $_FILES["archivo"]["name"];

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Error de conexión a MySQL: " . $conn->connect_error);
        }

        procesarArchivo($archivoTmp, $tema, $conn);

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Archivo</title>
</head>

<body>
    <h2>Procesar Archivo</h2>

    <form method="post" enctype="multipart/form-data">
        <label for="archivo">Seleccionar Archivo:</label>
        <input type="file" name="archivo" id="archivo" required>
        <br>
        <label for="tema">Tema:</label>
        <input type="number" name="tema" id="tema" value="1">
        <br>
        <input type="submit" value="Procesar">
    </form>
</body>

</html>