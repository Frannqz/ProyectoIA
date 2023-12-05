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

<?php include "cabecera.php"; ?>
<div class="formulario">
    <h3>Procesar archivos</h3>
    <p>1 - Religion</p>
    <p>2 - Deportes</p>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="archivo" class="form-label mt-4">Seleccionar archivo:</label>
            <input class="form-control" type="file" accept=".txt" name="archivo" id="archivo" required>
        </div>

        <div class="form-group">
            <label for="tema" class="form-label mt-4">Tema</label>
            <select class="form-select" id="tema" name="tema" value="1">
                <option>1</option>
                <option>2</option>
            </select>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Procesar</button>
    </form>
</div>


<?php include "footer.php"; ?>