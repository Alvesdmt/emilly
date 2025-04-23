<?php
session_start();
require_once 'config.php';

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$usuario = $_SESSION['usuario'];

// Busca todos os usuários cadastrados
$stmt = $pdo->query("SELECT * FROM usuarios");
$usuarios = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Pizzaria da Zeza</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff6b6b;
            --secondary-color: #4ecdc4;
            --dark-color: #2d3436;
            --light-color: #f9f9f9;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            box-shadow: var(--shadow);
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 10px;
        }

        h2 {
            color: var(--dark-color);
            margin-bottom: 20px;
        }

        .logout-btn {
            background: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #ff5252;
            transform: translateY(-2px);
        }

        .users-table {
            background: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th {
            background: var(--secondary-color);
            color: white;
            padding: 15px;
            text-align: left;
        }

        .users-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .users-table tr:hover {
            background: #f5f5f5;
            transition: background 0.3s ease;
        }

        .users-table tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .users-table {
                display: block;
                overflow-x: auto;
            }
        }

        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header, .users-table {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Bem-vindo, <?= htmlspecialchars($usuario['nome']) ?>!</h1>
                <h2>Painel Administrativo</h2>
            </div>
            <a href="logout.php" class="logout-btn">Sair</a>
            <a href="cadastrar_pizza.php" class="logout-btn">Pizzas</a>

        </div>

        <h3>Usuários Cadastrados</h3>
        <table class="users-table">
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Tipo de Usuário</th>
            </tr>
            <?php foreach ($usuarios as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['nome']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['tipo']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
<?php
$conteudo = ob_get_clean();
require_once 'layout.php';
?> 