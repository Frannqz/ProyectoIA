<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Archivo</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01"
                aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarColor01">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="navbar-brand nav-link <?php echo (strpos($_SERVER["REQUEST_URI"], "index.php") != false) ? "active" : ""; ?>"
                            href="index.php">Proyecto de IA</a>
                    </li>
                    <li class=" nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER["REQUEST_URI"], "procesar.php") != false) ? "active" : ""; ?>"" href="
                            procesar.php">Procesar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER["REQUEST_URI"], "mostrar.php") != false) ? "active" : ""; ?>"" href="
                            mostrar.php">Mostrar palabras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER["REQUEST_URI"], "identificarTema.php") != false) ? "active" : ""; ?>"" href="
                            identificarTema.php">Identificar Tema</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>