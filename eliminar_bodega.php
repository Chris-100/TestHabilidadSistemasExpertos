<?php
require 'db.php'; // Importa el archivo de conexión a la base de datos

// Obtiene el código de la bodega desde la URL (GET). Si no está presente, se detiene el script.
$codigo = $_GET['codigo'] ?? null;

if (!$codigo) {
    die("Código de bodega no especificado."); // Muestra un error si no se proporciona el código
}

// Primero se deben eliminar las relaciones con encargados
// Esto es importante porque la tabla `bodega_encargado` probablemente tiene una restricción de clave foránea (FK)
// que impide eliminar una bodega si todavía está relacionada con encargados
$sql = "DELETE FROM bodega_encargado WHERE codigo_bodega = :codigo";
$stmt = $pdo->prepare($sql);
$stmt->execute([':codigo' => $codigo]); // Ejecuta la eliminación de las relaciones de encargados

// Ahora que no hay relaciones que impidan la eliminación, se puede borrar la bodega
$sql = "DELETE FROM bodegas WHERE codigo = :codigo";
$stmt = $pdo->prepare($sql);
$stmt->execute([':codigo' => $codigo]); // Ejecuta la eliminación de la bodega

// Redirige al listado de bodegas una vez completada la eliminación
header("Location: listar_bodegas.php");
exit;
