<?php
require 'db.php'; // Importa el archivo de conexión a la base de datos

// Obtiene el código de la bodega desde la URL
$codigo = $_GET['codigo'] ?? null;
if (!$codigo) {
    die("No se especificó código de bodega."); // Si no se proporciona código, termina la ejecución
}

// Consulta para obtener los datos de la bodega correspondiente
$sql = "SELECT * FROM bodegas WHERE codigo = :codigo";
$stmt = $pdo->prepare($sql);
$stmt->execute(['codigo' => $codigo]);
$bodega = $stmt->fetch(); // Guarda la información de la bodega en una variable

if (!$bodega) {
    die("Bodega no encontrada."); // Si no existe la bodega, muestra un mensaje y detiene
}

// Obtener todos los encargados disponibles para mostrar en el <select>
$sql = "SELECT * FROM encargados";
$stmt = $pdo->query($sql);
$encargados = $stmt->fetchAll();

// Obtener los encargados que están actualmente asignados a esta bodega
$sql = "SELECT run_encargado FROM bodega_encargado WHERE codigo_bodega = :codigo";
$stmt = $pdo->prepare($sql);
$stmt->execute(['codigo' => $codigo]);
$encargados_asignados = $stmt->fetchAll(PDO::FETCH_COLUMN); // Obtiene solo la columna 'run_encargado'
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="estilos.css"> <!-- Enlace al archivo CSS para estilos -->
    <meta charset="UTF-8">
    <title>Editar Bodega <?= htmlspecialchars($codigo) ?></title>
</head>
<body>
    <h2>Editar Bodega <?= htmlspecialchars($codigo) ?></h2>

    <!-- Formulario que envía los cambios a actualizar_bodega.php -->
    <form action="actualizar_bodega.php" method="POST">
        <!-- Campo oculto para enviar el código de la bodega -->
        <input type="hidden" name="codigo" value="<?= htmlspecialchars($codigo) ?>">

        <!-- Campo para editar el nombre -->
        <label>Nombre:</label><br>
        <input type="text" name="nombre" maxlength="100" required value="<?= htmlspecialchars($bodega['nombre']) ?>"><br><br>

        <!-- Campo para editar la dirección -->
        <label>Dirección:</label><br>
        <textarea name="direccion" required><?= htmlspecialchars($bodega['direccion']) ?></textarea><br><br>

        <!-- Campo para editar la dotación (cantidad de personal) -->
        <label>Dotación:</label><br>
        <input type="number" name="dotacion" min="0" required value="<?= htmlspecialchars($bodega['dotacion']) ?>"><br><br>

        <!-- Selector para el estado actual de la bodega -->
        <label>Estado:</label><br>
        <select name="estado" required>
            <option value="Activada" <?= $bodega['estado'] === 'Activada' ? 'selected' : '' ?>>Activada</option>
            <option value="Desactivada" <?= $bodega['estado'] === 'Desactivada' ? 'selected' : '' ?>>Desactivada</option>
        </select><br><br>

        <!-- Selector múltiple para elegir encargados -->
        <label>Encargados (Ctrl+Click para seleccionar varios):</label><br>
        <select name="encargados[]" multiple size="5" required>
            <?php foreach ($encargados as $enc): ?>
                <option value="<?= htmlspecialchars($enc['run']) ?>" 
                    <?= in_array($enc['run'], $encargados_asignados) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($enc['nombre'] . ' ' . $enc['apellido1'] . ' ' . $enc['apellido2']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <!-- Botón para enviar los cambios -->
        <button type="submit">Actualizar Bodega</button>
    </form>

    <br>

    <!-- Enlace para volver al listado de bodegas -->
    <a href="listar_bodegas.php">Volver al listado</a>

    <br><br>

    <!-- Enlace estilizado para volver al inicio -->
    <a href="index.html" style="text-decoration: none; padding: 10px 20px; background-color: #777; color: white; border-radius: 5px;">
        Volver al Inicio
    </a>
</body>
</html>
