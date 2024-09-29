<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userID = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['return'])) {
        returnFolder($_POST['folderID'], $userID);
    } elseif (isset($_POST['checkout'])) {
        checkOutFolder($_POST['folderID'], $userID);
    }
    header('Location: search.php?search=' . urlencode($_GET['search']));
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
    <title>Macon Contabilidade - Resultados</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="top-bar">
        <h1>Macon Contabilidade</h1>
        <h2>Resultados da pesquisa</h2>
        <a href="folders.php">&lt; Voltar</a>
    </div>

    <?php if ($searchResults): ?>
        <div class="search-results">
            <table>
                <thead>
                    <tr>
                        <th>ID da Pasta</th>
                        <th>Nome do Cliente</th>
                        <th>Armário</th>
                        <th>Data Hora Retirada/Devolução</th>
                        <th>Local Atual</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($searchResults as $result): ?>
                        <tr>
                            <td><?= $result['ID_Pasta']; ?></td>
                            <td><?= $result['Nome_Cliente']; ?></td>
                            <td><?= $result['Armario']; ?></td>
                            <td><?= $result['Data_Hora_Retirada']; ?></td>
                            <td>
                                <?php if ($result['Tipo_Evento'] === 'RETIRADA'): ?>
                                    <?= htmlspecialchars($result['Nome_Empregado']); ?>
                                <?php else: ?>
                                    Armario n. <?= htmlspecialchars($result['Armario']); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($result['Tipo_Evento'] === 'RETIRADA' && $result['ID_Empregado'] == $userID): ?>
                                    <form action="search.php?search=<?= urlencode($searchTerm); ?>" method="post">
                                        <input type="hidden" name="folderID" value="<?= $result['ID_Pasta']; ?>">
                                        <button type="submit" name="return">Devolver</button>
                                    </form>
                                <?php elseif ($result['Tipo_Evento'] !== 'RETIRADA'): ?>
                                    <form action="search.php?search=<?= urlencode($searchTerm); ?>" method="post">
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
    <?php else: ?>
        <p>Nenhum cliente/pasta localizado.</p>
    <?php endif; ?>
</body>
</html>
