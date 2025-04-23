<?php
session_start();
require_once 'config.php';

// Busca pizzas do banco
$pizzas = $pdo->query("SELECT * FROM pizzas")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardápio de Pizzas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Cardápio 🍕</h2>

    <div class="pizza-container">
        <?php foreach ($pizzas as $pizza): ?>
            <div class="pizza-card">
                <h3><?= htmlspecialchars($pizza['nome']) ?> - R$<?= number_format($pizza['preco'], 2, ',', '.') ?></h3>
                <p><?= htmlspecialchars($pizza['descricao']) ?></p>
                <form class="pizza-form" action="carrinho.php" method="POST">
                    <input type="hidden" name="id" value="<?= $pizza['id'] ?>">
                    <input type="hidden" name="nome" value="<?= $pizza['nome'] ?>">
                    <input type="hidden" name="preco" value="<?= $pizza['preco'] ?>">
                    <input type="number" name="quantidade" value="1" min="1" required>
                    <button type="submit" name="adicionar">Adicionar ao Carrinho</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="carrinho.php" class="carrinho-link">🛒 Ir para o Carrinho</a>
</body>
</html>
