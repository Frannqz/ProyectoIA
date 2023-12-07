<?php include "cabecera.php"; ?>

<div class="container text-center my-2">
    <h2>Mostrar palabras por tema</h2>
    <form method="get" action="" class="mb-3">
        <label for="tema" class="form-label">Seleccionar tema:</label>
        <select name="tema" id="tema" class="form-select">
            <option value="1">Religión</option>
            <option value="2">Deportes</option>
        </select>
        <input type="submit" value="Mostrar palabras" class="btn btn-primary mt-2">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['tema'])) {
        $selected_tema = $_GET['tema'];

        // Configuración de conexión a MySQL
        $servername = "localhost";
        $username = "usuarioia";
        $password = "123";
        $dbname = "ProyectoIA";

        // Crear conexión
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar la conexión
        if ($conn->connect_error) {
            die("Error de conexión a MySQL: " . $conn->connect_error);
        }

        // Consultar palabras por tema en la base de datos
        $sql = "SELECT tema, palabra, frecuencia,frecuencia_relativa FROM palabras WHERE tema = $selected_tema";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Mostrar los resultados en una tabla con clases de Bootstrap
            echo "<div class='table-responsive'><table class='table table-bordered table-hover'><thead class='table-dark'><tr><th>Tema</th><th>Palabra</th><th>Frecuencia</th><th>Frecuencia Relativa</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["tema"] . "</td><td>" . $row["palabra"] . "</td><td>" . $row["frecuencia"] . "</td><td>" . $row["frecuencia_relativa"] . "</td></tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "<p class='mt-3'>No hay palabras para el tema seleccionado.</p>";
        }

        // Cerrar conexión
        $conn->close();
    }
    ?>
</div>

<?php include "footer.php"; ?>