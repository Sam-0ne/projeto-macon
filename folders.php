<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userID = $_SESSION['user_id'];
$folders = getFoldersByUser($userID);

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
    header('Location: folders.php');
    exit;
}

$searchResults = [];
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $searchResults = searchFolders($searchTerm);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pastas</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/main.js"></script>
</head>
<body>
    <div class="top-bar">
        <h2>Pastas Retiradas</h2>
        <form action="folders.php" method="get">
            <input type="text" name="search" placeholder="Procurar Pasta, Cliente, etc.">
            <button type="submit">Pesquisar</button>
        </form>
        <a href="logout.php">Logout</a>
    </div>
    
    <?php if ($searchResults): ?>
        <div class="search-results">
            <table>
                <thead>
                    <tr>
                        <th>ID da Pasta</th>
                        <th>Nome do Cliente</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($searchResults as $result): ?>
                        <tr>
                            <td><?= $result['ID_Pasta']; ?></td>
                            <td><?= $result['Nome_Cliente']; ?></td>
                            <td><?= $result['Status']; ?></td>
                            <td>
                                <?php if ($result['Status'] == 'Disponível'): ?>
                                    <form action="folders.php" method="post">
                                        <input type="hidden" name="folderID" value="<?= $result['ID_Pasta']; ?>">
                                        <button type="submit" name="checkout">Retirar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if (isAdmin()): ?>
        <div class="admin-actions">
            <h2>Admin Ações</h2>
            <div>
                <h3>Adicionar Cliente</h3>
                <form action="folders.php" method="post" name="addClienteForm">
                    <input type="text" name="nome" placeholder="Nome do Cliente" required>
                    <input type="text" name="cpf" placeholder="CPF" required>
                    <button type="submit" name="addCliente">Adicionar Cliente</button>
                </form>
            </div>
            <div>
                <h3>Adicionar Pasta</h3>
                <form action="folders.php" method="post" name="addPastaForm">
                    <input type="text" name="idCliente" placeholder="ID do Cliente" required>
                    <input type="text" name="armario" placeholder="Armário" required>
                    <button type="submit" name="addPasta">Adicionar Pasta</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

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
