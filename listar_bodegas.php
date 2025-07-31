<?php
require 'db.php'; // Incluye el archivo con la conexión PDO

// Obtiene el filtro de estado desde la URL (GET). Por defecto es 'todos'
$estado = $_GET['estado'] ?? 'todos';

// Construye la consulta base para obtener bodegas y sus encargados concatenados
$sql = "SELECT 
            b.codigo, 
            b.nombre, 
            b.direccion, 
            b.dotacion, 
            b.fecha_creacion, 
            b.estado,
            STRING_AGG(e.nombre || ' ' || e.apellido1 || ' ' || e.apellido2, ', ') AS encargados
        FROM bodegas b
        LEFT JOIN bodega_encargado be ON b.codigo = be.codigo_bodega
        LEFT JOIN encargados e ON be.run_encargado = e.run";

// Si se ha especificado un estado válido (Activada o Desactivada), agrega condición WHERE
if ($estado === 'Activada' || $estado === 'Desactivada') {
    $sql .= " WHERE b.estado = :estado";
}

// Completa la consulta agrupando por las columnas de la bodega
$sql .= " GROUP BY b.codigo, b.nombre, b.direccion, b.dotacion, b.fecha_creacion, b.estado";

// Prepara la consulta para ejecución segura
$stmt = $pdo->prepare($sql);

// Si hay filtro por estado, enlaza el parámetro
if ($estado === 'Activada' || $estado === 'Desactivada') {
    $stmt->bindParam(':estado', $estado);
}

// Ejecuta la consulta
$stmt->execute();

// Obtiene todas las bodegas (resultado de la consulta)
$bodegas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="estilos.css"> <!-- Archivo CSS externo -->
    <meta charset="UTF-8">
    <title>Listado de Bodegas</title>
</head>
<body>
    <h2>Listado de Bodegas</h2>

    <!-- Formulario para filtrar el listado por estado -->
    <form method="GET" action="listar_bodegas.php">
        <label for="estado">Filtrar por estado:</label>
        <select name="estado" id="estado" onchange="this.form.submit()">
            <option value="todos" <?= $estado === 'todos' ? 'selected' : '' ?>>Todos</option>
            <option value="Activada" <?= $estado === 'Activada' ? 'selected' : '' ?>>Activada</option>
            <option value="Desactivada" <?= $estado === 'Desactivada' ? 'selected' : '' ?>>Desactivada</option>
        </select>
    </form>

    <!-- Tabla para mostrar el listado de bodegas -->
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Dirección</th>
                <th>Dotación</th>
                <th>Encargado(s)</th>
                <th>Fecha de creación</th>
                <th>Estado</th>
                <th>Fecha de Creación</th> <!-- Repetido: tal vez borrar una de las dos columnas -->
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Recorre cada bodega y muestra sus datos en filas -->
            <?php foreach ($bodegas as $bodega): ?>
                <tr>
                    <td><?= htmlspecialchars($bodega['codigo']) ?></td>
                    <td><?= htmlspecialchars($bodega['nombre']) ?></td>
                    <td><?= htmlspecialchars($bodega['direccion']) ?></td>
                    <td><?= htmlspecialchars($bodega['dotacion']) ?></td>
                    <td><?= htmlspecialchars($bodega['encargados'] ?? 'Sin encargados') ?></td>
                    <td><?= htmlspecialchars($bodega['fecha_creacion']) ?></td>
                    <td><?= htmlspecialchars($bodega['estado']) ?></td>
                    <td>
                        <?php 
                            // Da formato legible a la fecha/hora de creación
                            $fecha = new DateTime($bodega['fecha_creacion']);
                            echo $fecha->format('d-m-Y H:i');
                        ?>
                    </td>
                    <td>
                        <!-- Enlaces para editar y eliminar, pasando el código de bodega -->
                        <a href="editar_bodega.php?codigo=<?= urlencode($bodega['codigo']) ?>"  class="btn btn-editar">Editar</a> |
                        <a href="eliminar_bodega.php?codigo=<?= urlencode($bodega['codigo']) ?>" class="btn btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta bodega? Esta acción no se puede deshacer.')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>

            <!-- Mensaje si no hay bodegas -->
            <?php if (empty($bodegas)): ?>
                <tr><td colspan="9">No hay bodegas que mostrar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
