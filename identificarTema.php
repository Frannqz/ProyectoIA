<?php
// Configuración de conexión a MySQL
$servername = "localhost";
$username = "usuarioia";
$password = "123";
$dbname = "ProyectoIA";

function determinarTema($archivo, $conn)
{
    $contenido = file_get_contents($archivo);
    $contenido = strtolower($contenido);
    $palabras = preg_split('/\s+/', preg_replace('/[[:punct:]]/', '', $contenido));

    $contadorPalabras = array_count_values($palabras);

    $tema1 = 0;
    $tema2 = 0;

    foreach ($contadorPalabras as $palabra => $frecuencia) {
        $stmt = $conn->prepare("SELECT tema FROM palabras WHERE palabra = ? ORDER BY frecuencia DESC LIMIT 1");
        $stmt->bind_param("s", $palabra);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($tema);
            $stmt->fetch();

            if ($tema == 1) {
                $tema1 += $frecuencia;
            } elseif ($tema == 2) {
                $tema2 += $frecuencia;
            }
        }
    }

    if ($tema1 > $tema2) {
        return 1;
    } elseif ($tema2 > $tema1) {
        return 2;
    } else {
        // En caso de empate, puedes manejarlo de acuerdo a tus necesidades
        return "Empate";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_FILES["archivo"]["tmp_name"])) {
        $archivoTmp = $_FILES["archivo"]["tmp_name"];

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Error de conexión a MySQL: " . $conn->connect_error);
        }

        $tema = determinarTema($archivoTmp, $conn);

        $conn->close();
    }
}
?>
<?php include "cabecera.php" ?>
<div class="formulario">
    <h3>Identificar Tema</h3>
    <p>1 - Religion</p>
    <p>2 - Deportes</p>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="archivo">Seleccionar Archivo:</label>
            <input class="form-control" type="file" name="archivo" id="archivo" accept=".txt" required>
        </div>
        <br>
        <input type="submit" class="btn btn-primary" value="Determinar Tema">
    </form>

</div>

<?php
if (isset($tema)) {
    echo "<div class='text-center'><h3>Resultado:</h3>";
    echo "<p>El tema del archivo es el tipo: " . $tema . "</p> </div>";
}
?>
<?php include "footer.php" ?>