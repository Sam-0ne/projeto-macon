<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userID = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];
$folders = getFoldersByUser($userID);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['return'])) {
        returnFolder($_POST['folderID'], $userID);
    } elseif (isset($_POST['checkout'])) {
        checkOutFolder($_POST['folderID'], $userID);
    } elseif (isAdmin()) {
        if (isset($_POST['addCliente'])) {
            addNewCliente($_POST['nome'], $_POST['cpf']);
        } elseif (isset($_POST['addPasta'])) {
            addNewPasta($_POST['idCliente'], $_POST['armario']);
        }
    }
    $folders = getFoldersByUser($userID);
    header('Location: folders.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Macon Contabilidade - Pastas</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/main.js"></script>
</head>
<body>
    <div class="top-bar">
        <h1>Macon Contabilidade</h1>
        <h2>Controle de Pastas</h2>
        <form action="search.php" method="get">
            <input type="text" name="search" placeholder="Procurar Pasta, Cliente, etc.">
            <button type="submit">Pesquisar</button>
        </form>
        <a href="logout.php">Logout</a>
    </div>
    
    <h3>Pastas retiradas por <?= $userName; ?></h3>

    <div class="folder-list">
        <table>
            <thead>
                <tr>
                    <th>ID da Pasta</th>
                    <th>Nome do Cliente</th>
                    <th>Armário</th>
                    <th>Data Hora Retirada</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($folders as $folder): ?>
                    <tr>
                        <td><?= $folder['ID_Pasta']; ?></td>
                        <td><?= $folder['Nome_Cliente']; ?></td>
                        <td><?= $folder['Armario']; ?></td>
                        <td><?= $folder['Data_Hora_Retirada']; ?></td>
                        <td>
                            <form action="folders.php" method="post">
                                <input type="hidden" name="folderID" value="<?= $folder['ID_Pasta']; ?>">
                                <button type="submit" name="return">Devolver</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
