<?php
// Configuración de conexión a MySQL
$servername = "localhost";
$username = "usuarioia";
$password = "123";
$dbname = "ProyectoIA";

// Lista de palabras a excluir (preposiciones, palabras no relevantes, etc.)
$palabrasExcluidas = array(
    "en", "con", "y", "o", "el", "la", "los", "las", "un", "una", "de", "para",
    "a", "ante", "bajo", "cabe", "como", "contra", "cual", "cuando", "de", "desde",
    "donde", "durante", "e", "el", "en", "entre", "excepto", "hacia", "hasta", "mediante",
    "mucho", "ni", "no", "o", "para", "pero", "que", "segun", "sin", "sobre",
    "tras", "también", "uno", "unos", "una", "unas", "otro", "otros", "otra", "otras", "porque",
    "este", "esta", "estos", "estas", "ese", "esa", "esos", "esas", "aquel", "aquella", "aquellos", "aquellas",
    "mi", "tu", "su", "nuestro", "nuestra", "vuestro", "vuestra", "mío", "mía", "tuyo", "tuya", "suyo", "suya",
    "nuestro", "nuestra", "vuestro", "vuestra", "míos", "mías", "tuyos", "tuyas", "suyos", "suyas", "nuestros",
    "nuestras", "vuestros", "vuestras", "cuyo", "cuya", "cuyos", "cuyas",
    "este", "esta", "estos", "estas", "ese", "esa", "esos", "esas", "aquel", "aquella", "aquellos", "aquellas",
    "mientras", "quien", "cual", "cuya", "aquello", "aquel", "aquellos", "aquellas", "cualquiera", "ninguno",
    "alguno", "ambos", "varios", "demás", "muchos", "pocos", "poco", "mucho", "bastante", "demasiado", "tan", "tanto",
    "tanta", "tantos", "tantas", "más", "menos", "además", "inclusive", "exclusivo", "alrededor", "fuera", "dentro", "arriba",
    "abajo", "encima", "debajo", "delante", "detrás", "cerca", "lejos", "al", "del", "alrededor", "debajo", "encima", "aunque", "ha", "por", "es", "se", "qué","lo"
);

// Función para procesar el archivo y actualizar la base de datos
function procesarArchivo($archivo, $tema, $conn, $palabrasExcluidas)
{
    $contenido = file_get_contents($archivo);
    $contenido = strtolower($contenido);
    $palabras = preg_split('/\s+/', preg_replace('/[[:punct:]]/', '', $contenido));

    $palabrasFiltradas = array_diff($palabras, $palabrasExcluidas);
    $contadorPalabras = array_count_values($palabrasFiltradas);

    foreach ($contadorPalabras as $palabra => $frecuencia) {
        $stmt = $conn->prepare("SELECT frecuencia FROM palabras WHERE tema = ? AND palabra = ?");
        $stmt->bind_param("is", $tema, $palabra);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $frecuenciaExistente = 0;
            $stmt->bind_result($frecuenciaExistente);
            $stmt->fetch();

            $frecuenciaCombinada = $frecuencia + $frecuenciaExistente;

            $stmt = $conn->prepare("UPDATE palabras SET frecuencia = ? WHERE tema = ? AND palabra = ?");
            $stmt->bind_param("iss", $frecuenciaCombinada, $tema, $palabra);
            $stmt->execute();
        } else {
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

        procesarArchivo($archivoTmp, $tema, $conn, $palabrasExcluidas);

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
            <input class="form-control" type="file" accept=".txt" name="archivo" id="archivo" multiple required>
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