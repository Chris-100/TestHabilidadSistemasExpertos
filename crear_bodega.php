<?php
// Incluye el archivo que contiene la conexión a la base de datos usando PDO
include 'db.php';

// Verifica si la solicitud fue hecha mediante el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura los datos enviados desde el formulario
    $codigo     = $_POST['codigo'];                     // Código de la bodega
    $nombre     = $_POST['nombre'];                     // Nombre de la bodega
    $direccion  = $_POST['direccion'];                  // Dirección de la bodega
    $dotacion   = (int) $_POST['dotacion'];             // Dotación (cantidad de personas)
    $encargados = $_POST['encargados'];                 // Lista (array) de RUN de encargados asociados

    try {
        // Inicia una transacción para asegurar la integridad de los datos
        $pdo->beginTransaction();

        // Inserta la bodega en la tabla `bodegas`
        $stmt = $pdo->prepare("INSERT INTO bodegas (codigo, nombre, direccion, dotacion) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([$codigo, $nombre, $direccion, $dotacion]);

        // Prepara la sentencia para insertar los encargados asociados a la bodega
        $stmt2 = $pdo->prepare("INSERT INTO bodega_encargado (codigo_bodega, run_encargado) VALUES (?, ?)");

        // Itera sobre cada encargado y lo inserta en la tabla relacional
        foreach ($encargados as $run) {
            $stmt2->execute([$codigo, $run]);
        }

        // Confirma (commit) la transacción si todo fue exitoso
        $pdo->commit();

        // Muestra mensaje de éxito al usuario
        echo "Bodega creada correctamente.";
        echo "<br><a href='formulario_bodega.php'>Crear otra</a>"; // Enlace para volver a crear otra bodega

    } catch (Exception $e) {
        // Si algo falla, revierte los cambios realizados
        $pdo->rollBack();
        // Muestra el mensaje de error
        echo "Error al crear la bodega: " . $e->getMessage();
    }
}
?>
