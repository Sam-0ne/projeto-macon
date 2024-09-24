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
        $email = strtolower(trim($email)); // Standardize email
        $stmt = $db->prepare('SELECT * FROM Empregado WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['ID_Empregado'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_profile'] = $user['Perfil'];
                return true;
            }
        }
    } catch (Exception $e) {
        echo "Query failed: " . $e->getMessage();
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
    ');
    $stmt->execute([$userID]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function checkOutFolder($folderID, $userID) {
    global $db;

    $stmt = $db->prepare('
        INSERT INTO Historico (ID_Pasta, ID_Cliente, ID_Empregado, Tipo_Evento, DataHora_Evento)
        SELECT ID_Pasta, ID_Cliente, ?, "RETIRADA", NOW() FROM Pasta WHERE ID_Pasta = ?
    ');
    $stmt->execute([$userID, $folderID]);

    $updateStmt = $db->prepare('
        UPDATE Pasta SET ID_Ultimo_Evento = (SELECT ID_Evento FROM Historico WHERE ID_Pasta = ? ORDER BY DataHora_Evento DESC LIMIT 1)
        WHERE ID_Pasta = ?
    ');
    $updateStmt->execute([$folderID, $folderID]);
}

function returnFolder($folderID, $userID) {
    global $db;

    $stmt = $db->prepare('
        INSERT INTO Historico (ID_Pasta, ID_Cliente, ID_Empregado, Tipo_Evento, DataHora_Evento)
        SELECT ID_Pasta, ID_Cliente, ?, "DEVOLVIDA", NOW() FROM Pasta WHERE ID_Pasta = ?
    ');
    $stmt->execute([$userID, $folderID]);

    $updateStmt = $db->prepare('
        UPDATE Pasta SET ID_Ultimo_Evento = (SELECT ID_Evento FROM Historico WHERE ID_Pasta = ? ORDER BY DataHora_Evento DESC LIMIT 1)
        WHERE ID_Pasta = ?
    ');
    $updateStmt->execute([$folderID, $folderID]);
}

function searchFolders($searchTerm) {
    global $db;

    $stmt = $db->prepare('
        SELECT P.ID_Pasta, C.nome AS Nome_Cliente,
        CASE
            WHEN H.Tipo_Evento = "RETIRADA" THEN CONCAT("RETIRADA por ", E.nome, " em ", H.DataHora_Evento)
            ELSE "Disponível"
        END AS Status
        FROM Pasta P
        JOIN Cliente C ON P.ID_Cliente = C.ID_Cliente
        JOIN Historico H ON P.ID_Ultimo_Evento = H.ID_Evento
        JOIN Empregado E ON H.ID_Empregado = E.ID_Empregado
        WHERE P.ID_Pasta LIKE ? OR C.nome LIKE ? OR H.Tipo_Evento LIKE ?
    ');
    $term = "%$searchTerm%";
    $stmt->execute([$term, $term, $term]);
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
