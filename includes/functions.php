<?php
session_start();
require_once 'db_connect.php';

function secureSessionStart() {
    $session_name = 'secure_session';
    $secure = true;
    $httponly = true;

    ini_set('session.use_only_cookies', 1);
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(
        $cookieParams['lifetime'],
        $cookieParams['path'],
        $cookieParams['domain'],
        $secure,
        $httponly
    );
    session_name($session_name);
    session_start();
    session_regenerate_id(true);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_profile']) && $_SESSION['user_profile'] == 'Admin';
}

function login($email, $password) {
    global $db;

    try {
        $email = strtolower(trim($email));
        $stmt = $db->prepare('SELECT * FROM Empregado WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user !== false && $password === $user['password']) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['ID_Empregado'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_profile'] = $user['Perfil'];
            return true;
        }
    } catch (Exception $e) {
        echo "Falha na query: " . $e->getMessage();
    }
    return false;
}

function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

function getFoldersByUser($userID) { 
    global $db;

    $stmt = $db->prepare('
        SELECT P.ID_Pasta, P.ID_Cliente, C.nome AS Nome_Cliente, P.Armario, H.DataHora_Evento AS Data_Hora_Retirada
        FROM Pasta P
        JOIN Cliente C ON P.ID_Cliente = C.ID_Cliente
        JOIN Historico H ON P.ID_Ultimo_Evento = H.ID_Evento
        WHERE H.ID_Empregado = ? AND H.Tipo_Evento = "RETIRADA" 
        AND NOT EXISTS (
            SELECT 1 FROM Historico H2
            JOIN Pasta P2 ON H2.ID_Evento = P2.ID_Ultimo_Evento
            WHERE H2.ID_Pasta = P.ID_Pasta AND H2.Tipo_Evento = "DEVOLVIDA"
        )
    ');
    $stmt->execute([$userID]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function updateUltimoEvento($folderID) {
    global $db;

    $updateStmt = $db->prepare('
        UPDATE Pasta SET ID_Ultimo_Evento = (
            SELECT ID_Evento FROM Historico WHERE ID_Pasta = ? ORDER BY DataHora_Evento DESC LIMIT 1
        )
        WHERE ID_Pasta = ?
    ');
    $updateStmt->execute([$folderID, $folderID]);
}

function checkOutFolder($folderID, $userID) {
    global $db;

    $stmt = $db->prepare('
        INSERT INTO Historico (ID_Pasta, ID_Cliente, ID_Empregado, Tipo_Evento, DataHora_Evento)
        SELECT ID_Pasta, ID_Cliente, ?, "RETIRADA", DATETIME("now") FROM Pasta WHERE ID_Pasta = ?
    ');
    $stmt->execute([$userID, $folderID]);

    updateUltimoEvento($folderID);
}

function returnFolder($folderID, $userID) {
    global $db;

    $stmt = $db->prepare('
        INSERT INTO Historico (ID_Pasta, ID_Cliente, ID_Empregado, Tipo_Evento, DataHora_Evento)
        SELECT ID_Pasta, ID_Cliente, ?, "DEVOLVIDA", DATETIME("now") FROM Pasta WHERE ID_Pasta = ?
    ');
    $stmt->execute([$userID, $folderID]);

    updateUltimoEvento($folderID);
}

function searchFolders($searchTerm) {
    global $db;

    $stmt = $db->prepare('
        SELECT P.ID_Pasta, C.nome AS Nome_Cliente, P.Armario,
               H.DataHora_Evento AS Data_Hora_Retirada,
               H.Tipo_Evento, H.ID_Empregado, E.nome AS Nome_Empregado
        FROM Pasta P
        JOIN Cliente C ON P.ID_Cliente = C.ID_Cliente
        LEFT JOIN Historico H ON P.ID_Ultimo_Evento = H.ID_Evento
        LEFT JOIN Empregado E ON H.ID_Empregado = E.ID_Empregado
        WHERE C.nome LIKE ? OR P.ID_Pasta LIKE ?
        ORDER BY P.ID_Pasta ASC
    ');
    $stmt->execute(["%$searchTerm%", "%$searchTerm%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}





function addNewCliente($nome, $cpf) {
    global $db;
    
    $stmt = $db->prepare('INSERT INTO Cliente (nome, CPF) VALUES (?, ?)');
    $stmt->execute([$nome, $cpf]);
}

function addNewPasta($idCliente, $armario) {
    global $db;

    $stmt = $db->prepare('INSERT INTO Pasta (ID_Cliente, Armario) VALUES (?, ?)');
    $stmt->execute([$idCliente, $armario]);
}
?>
