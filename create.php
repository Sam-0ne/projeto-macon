<?php
$databasePath = __DIR__ . '/database/arquivoPastas.db';

try {
    // Create the SQLite Database
    $db = new PDO("sqlite:$databasePath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL to create tables
    $createTablesSQL = "
        CREATE TABLE IF NOT EXISTS Cliente (
            ID_Cliente INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            CPF TEXT UNIQUE NOT NULL
        );
        
        CREATE TABLE IF NOT EXISTS Empregado (
            ID_Empregado INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            Perfil TEXT,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL
        );
        
        CREATE TABLE IF NOT EXISTS Pasta (
            ID_Pasta INTEGER PRIMARY KEY AUTOINCREMENT,
            ID_Cliente INTEGER,
            Armario INTEGER CHECK (Armario BETWEEN 1 AND 25),
            ID_Ultimo_Evento INTEGER,
            FOREIGN KEY (ID_Cliente) REFERENCES Cliente(ID_Cliente) ON DELETE CASCADE ON UPDATE CASCADE
        );
        
        CREATE TABLE IF NOT EXISTS Historico (
            ID_Evento INTEGER PRIMARY KEY AUTOINCREMENT,
            ID_Pasta INTEGER,
            ID_Cliente INTEGER,
            ID_Empregado INTEGER,
            Tipo_Evento TEXT CHECK (Tipo_Evento IN ('RETIRADA', 'DEVOLVIDA')),
            DataHora_Evento DATETIME,
            FOREIGN KEY (ID_Pasta) REFERENCES Pasta(ID_Pasta) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (ID_Cliente) REFERENCES Cliente(ID_Cliente) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (ID_Empregado) REFERENCES Empregado(ID_Empregado) ON DELETE SET NULL ON UPDATE CASCADE
        );
    ";

    $db->exec($createTablesSQL);

    // Insert sample data for Cliente
    $clientes = [
        ['Maria Silva', '12345678901'],
        ['João Souza', '10987654321'],
        ['Carlos Pereira', '22345678901'],
        ['Ana Oliveira', '32987654321'],
        ['Paulo Santos', '42345678901'],
        ['Mariana Fernandes', '52987654321'],
        ['José Lima', '62345678901'],
        ['Renata Costa', '72987654321'],
        ['Fernando Carvalho', '82345678901'],
        ['Juliana Ribeiro', '92987654321'],
        ['Rafael Castro', '12312312304'],
        ['Isabela Alves', '32132132103'],
        ['Luis Martins', '23123123107'],
        ['Bruna Moura', '21321321302'],
        ['Fábio Nunes', '45345678901'],
        ['Leonardo Teixeira', '56456789012'],
        ['Gabriela Barros', '67567890123'],
        ['Lucas Rocha', '78678901234'],
        ['Sofia Mendes', '89789012345'],
        ['Miguel Azevedo', '90890123456'],
        ['Clara Costa', '12398765432'],
        ['Pedro Brito', '32109876543'],
        ['Marisa Silva', '22310987654'],
        ['Igor Barbosa', '32901234567'],
        ['Amanda Oliveira', '42789012345'],
        ['Cleber Monteiro', '51234567890'],
        ['Cecília Duarte', '61234567891'],
        ['Henrique Reis', '71234567892'],
        ['Patrícia Souza', '81234567893'],
        ['Lucas Gonçalves', '91234567894'],
    ];

    $stmt = $db->prepare("INSERT INTO Cliente (nome, CPF) VALUES (?, ?)");
    foreach ($clientes as $cliente) {
        $stmt->execute($cliente);
    }

    // Insert sample data for Empregado
    $empregados = [
        ['Carlos Lima', 'Admin', 'carlos.lima@maconcontabilidade.com.br', 'password1'],
        ['Ana Oliveira', 'User', 'ana.oliveira@maconcontabilidade.com.br', 'password2'],
        ['Roberto Farias', 'User', 'roberto.farias@maconcontabilidade.com.br', 'password3'],
        ['Mariana Lopes', 'User', 'mariana.lopes@maconcontabilidade.com.br', 'password4'],
        ['Eduardo Almeida', 'Admin', 'eduardo.almeida@maconcontabilidade.com.br', 'password5'],
    ];

    $stmt = $db->prepare("INSERT INTO Empregado (nome, Perfil, email, password) VALUES (?, ?, ?, ?)");
    foreach ($empregados as $empregado) {
        $stmt->execute($empregado);
    }

    // Insert sample data for Pasta
    $pastas = [
        [1, 1], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6], [7, 7], [8, 8], [9, 9], [10, 10],
        [11, 11], [12, 12], [13, 13], [14, 14], [15, 15], [16, 16], [17, 17], [18, 18], [19, 19], [20, 20],
        [21, 21], [22, 22], [23, 23], [24, 24], [25, 25], [1, 1], [2, 2], [3, 3], [4, 4], [5, 5],
        [1, 6], [2, 7], [3, 8], [4, 9], [5, 10], [6, 11], [7, 12], [8, 13], [9, 14], [10, 15],
        [11, 16], [12, 17], [13, 18], [14, 19], [15, 20], [16, 21], [17, 22], [18, 23], [19, 24], [20, 25]
    ];

    $stmt = $db->prepare("INSERT INTO Pasta (ID_Cliente, Armario) VALUES (?, ?)");
    foreach ($pastas as $pasta) {
        $stmt->execute($pasta);
    }

    // Insert sample data for Historico
    $historicos = [
        [1, 1, 1, 'RETIRADA', '2024-04-03 10:15:00'],
        [2, 2, 2, 'RETIRADA', '2024-04-05 14:30:00'],
        [2, 2, 2, 'DEVOLVIDA', '2024-04-10 10:45:00'],
        [4, 4, 2, 'RETIRADA', '2024-05-02 09:00:00'],
        [5, 5, 3, 'RETIRADA', '2024-05-12 16:25:00'],
        [6, 6, 4, 'DEVOLVIDA', '2024-06-07 14:30:00'],
        [8, 8, 5, 'DEVOLVIDA', '2024-04-12 12:15:00'],
        [9, 9, 5, 'RETIRADA', '2024-04-20 10:00:00'],
        [11, 11, 1, 'RETIRADA', '2024-05-01 11:45:00'],
        [12, 12, 2, 'DEVOLVIDA', '2024-04-20 11:00:00'],
        [14, 14, 2, 'RETIRADA', '2024-05-25 13:30:00'],
        [15, 15, 3, 'RETIRADA', '2024-06-15 09:45:00'],
        [16, 16, 3, 'DEVOLVIDA', '2024-07-02 12:50:00'],
        [18, 18, 2, 'DEVOLVIDA', '2024-06-29 11:15:00'],
        [21, 21, 1, 'RETIRADA', '2024-07-06 10:30:00'],
        [22, 22, 4, 'DEVOLVIDA', '2024-08-05 14:00:00'],
        [24, 24, 2, 'RETIRADA', '2024-08-10 15:30:00'],
        [25, 25, 3, 'RETIRADA', '2024-06-18 16:15:00'],
        [26, 5, 4, 'DEVOLVIDA', '2024-06-25 13:00:00'],
        [27, 4, 4, 'RETIRADA', '2024-07-08 14:45:00'],
        [28, 3, 5, 'DEVOLVIDA', '2024-07-20 16:30:00'],
        [29, 2, 5, 'RETIRADA', '2024-04-15 11:00:00'],
        [30, 1, 4, 'DEVOLVIDA', '2024-04-25 15:15:00'],
        [31, 6, 1, 'RETIRADA', '2024-08-05 16:45:00'],
        [32, 7, 2, 'DEVOLVIDA', '2024-07-15 14:00:00'],
        [33, 8, 2, 'RETIRADA', '2024-06-10 09:30:00'],
        [34, 9, 3, 'RETIRADA', '2024-07-05 12:00:00'],
        [35, 10, 3, 'DEVOLVIDA', '2024-08-20 09:45:00'],
        [36, 11, 4, 'DEVOLVIDA', '2024-07-22 15:00:00'],
        [37, 12, 4, 'RETIRADA', '2024-06-08 13:15:00'],
        [38, 13, 4, 'RETIRADA', '2024-08-17 10:00:00'],
        [39, 14, 4, 'DEVOLVIDA', '2024-08-28 10:30:00'],
        [40, 15, 3, 'RETIRADA', '2024-06-06 15:15:00'],
        [41, 16, 4, 'RETIRADA', '2024-08-07 12:45:00'],
        [42, 17, 3, 'DEVOLVIDA', '2024-07-21 14:00:00'],
        [43, 18, 1, 'RETIRADA', '2024-07-30 09:00:00'],
        [44, 19, 5, 'DEVOLVIDA', '2024-08-28 15:30:00'],
        [45, 20, 2, 'DEVOLVIDA', '2024-08-25 11:45:00'],
        [46, 21, 1, 'RETIRADA', '2024-08-29 11:30:00']
    ];

    $stmt = $db->prepare("INSERT INTO Historico (ID_Pasta, ID_Cliente, ID_Empregado, Tipo_Evento, DataHora_Evento) VALUES (?, ?, ?, ?, ?)");
    foreach ($historicos as $historico) {
        $stmt->execute($historico);
    }

    // Update the Pasta table with the IDs of the last event
    $updatePastaSQL = "
        UPDATE Pasta
        SET ID_Ultimo_Evento = (
            SELECT ID_Evento
            FROM Historico
            WHERE Historico.ID_Pasta = Pasta.ID_Pasta
            ORDER BY DataHora_Evento DESC
            LIMIT 1
        );
    ";

    $db->exec($updatePastaSQL);

    echo "Database created and initialized successfully.";
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
?>
