<?php
// Se incluye el archivo de conexión a la base de datos
require 'db.php';

// Se verifica que el método de la petición sea POST (es decir, que venga desde un formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Se obtienen los valores enviados por el formulario (usando el operador ?? para manejar valores faltantes)
    $codigo = $_POST['codigo'] ?? null;                 // Código de la bodega (clave primaria)
    $nombre = $_POST['nombre'] ?? '';                   // Nombre de la bodega
    $direccion = $_POST['direccion'] ?? '';             // Dirección de la bodega
    $dotacion = $_POST['dotacion'] ?? 0;                // Cantidad de personas o capacidad
    $estado = $_POST['estado'] ?? 'Activada';           // Estado de la bodega (Activada o Desactivada)
    $encargados = $_POST['encargados'] ?? [];           // Lista de RUNs de encargados asignados

    // Si no se recibió un código, se detiene la ejecución
    if (!$codigo) {
        die("Código de bodega no especificado.");
    }

    // ----------------------------
    // ACTUALIZAR DATOS DE LA BODEGA
    // ----------------------------
    $sql = "UPDATE bodegas 
            SET nombre = :nombre, direccion = :direccion, dotacion = :dotacion, estado = :estado 
            WHERE codigo = :codigo";

    $stmt = $pdo->prepare($sql);  // Se prepara la consulta con PDO
    $stmt->execute([
        ':nombre' => $nombre,
        ':direccion' => $direccion,
        ':dotacion' => $dotacion,
        ':estado' => $estado,
        ':codigo' => $codigo,
    ]);

    // ----------------------------
    // ACTUALIZAR ENCARGADOS ASIGNADOS
    // ----------------------------

    // 1. Eliminar todos los encargados previamente asignados a esta bodega
    $sql = "DELETE FROM bodega_encargado WHERE codigo_bodega = :codigo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':codigo' => $codigo]);

    // 2. Insertar los nuevos encargados seleccionados desde el formulario
    $sql = "INSERT INTO bodega_encargado (codigo_bodega, run_encargado) 
            VALUES (:codigo_bodega, :run_encargado)";
    $stmt = $pdo->prepare($sql);

    // Se recorre la lista de encargados seleccionados
    foreach ($encargados as $run_encargado) {
        $stmt->execute([
            ':codigo_bodega' => $codigo,
            ':run_encargado' => $run_encargado
        ]);
    }

    // Redireccionar al usuario al listado de bodegas después de guardar los cambios
    header("Location: listar_bodegas.php");
    exit;

} else {
    // Si no es una solicitud POST, se bloquea el acceso
    die("Acceso no permitido.");
}
