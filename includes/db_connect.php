<?php
$databasePath = __DIR__ . '/../database/arquivoPastas.db';

try {
    $db = new PDO("sqlite:$databasePath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}


// $query = "DELETE FROM 'Empregado' WHERE email = 'ccarloss.lima@maconcontabilidade.com.br'";
//$query = "SELECT * FROM 'Empregado'";
//$stmt = $db->prepare($query);
//$stmt->execute();
//while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//    echo "ID: " . $row['ID_Empregado'] . "<br>";
//    echo "Name: " . $row['nome'] . "<br>";
  //  echo "Profile: " . $row['Perfil'] . "<br>";
    //echo "Email: " . $row['email'] . "<br>";
    //echo "Password: " . $row['password'] . "<br><br>";
//}
?>
