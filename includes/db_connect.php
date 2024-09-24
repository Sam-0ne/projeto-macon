<?php
$databasePath = __DIR__ . '/../database/arquivoPastas.db';

try {
    $db = new PDO("sqlite:$databasePath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
?>
