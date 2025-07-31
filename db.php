<?php

// Datos necesarios para conectarse a la base de datos PostgreSQL
$host = 'localhost';            // Dirección del servidor de base de datos (localhost si es local)
$db   = 'Admin_bodegas';        // Nombre de la base de datos
$user = 'postgres';             // Usuario de PostgreSQL
$pass = '12345678';             // Contraseña del usuario
$port = "5432";                 // Puerto predeterminado de PostgreSQL

// Se usa PDO (PHP Data Objects) para establecer la conexión de forma segura
try {
    // Crea un nuevo objeto PDO con los parámetros de conexión a PostgreSQL
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);

    // Configura PDO para lanzar excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si ocurre un error durante la conexión, se muestra el mensaje y se detiene la ejecución
    die("Error en la conexión: " . $e->getMessage());
}
?>
