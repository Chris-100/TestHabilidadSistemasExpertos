<?php include 'db.php'; ?> 
<!-- Incluye el archivo de conexión a la base de datos, necesario para usar $pdo -->
<!DOCTYPE html>
<html>
<head>
    <title>Crear Bodega</title>
    <link rel="stylesheet" href="estilos.css"> <!-- Enlaza el archivo de estilos CSS -->
</head>
<body>
    <h1>Crear Nueva Bodega</h1>

    <!-- Formulario para crear una nueva bodega -->
    <form action="crear_bodega.php" method="POST"> <!-- Envia los datos al script crear_bodega.php por POST -->

        <!-- Campo para el código de la bodega -->
        <label>Código:</label>
        <input type="text" name="codigo" maxlength="5" required><br>

        <!-- Campo para el nombre -->
        <label>Nombre:</label>
        <input type="text" name="nombre" maxlength="100" required><br>

        <!-- Campo para la dirección -->
        <label>Dirección:</label>
        <input type="text" name="direccion" required><br>

        <!-- Campo para dotación (cantidad de personal) -->
        <label>Dotación:</label>
        <input type="number" name="dotacion" min="1" required><br>

        <!-- Lista desplegable múltiple para seleccionar encargados asociados a la bodega -->
        <label>Encargados:</label><br>
        <select name="encargados[]" multiple required>
            <?php
            // Consulta para obtener todos los encargados disponibles
            $stmt = $pdo->query("SELECT run, nombre, apellido1, apellido2 FROM encargados");

            // Se recorre el resultado y se crea una opción por cada encargado
            while ($row = $stmt->fetch()) {
                $nombreCompleto = $row['nombre'] . ' ' . $row['apellido1'] . ' ' . $row['apellido2'];
                // Cada opción mostrará el nombre completo y el RUT, y tendrá como valor el run
                echo "<option value='{$row['run']}'>{$nombreCompleto} ({$row['run']})</option>";
            }
            ?>
        </select><br>

        <!-- Botón para enviar el formulario -->
        <button type="submit" class="btn btn-crear">Guardar Bodega</button>

        <br><br>
        <!-- Botón para volver al inicio -->
        <a href="index.html" class="btn btn-volver" style="text-decoration: none; padding: 10px 20px; background-color: #777; color: white; border-radius: 5px;">Volver al Inicio</a>

    </form>
</body>
</html>
