<?php
session_start();

// Inicializa o carrinho
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Adiciona produto
if (isset($_POST['adicionar'])) {
    $id = $_POST['id'];
    $quantidade = $_POST['quantidade'];

    if (!isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id] = [
            'nome' => $_POST['nome'],
            'preco' => $_POST['preco'],
            'quantidade' => $quantidade
        ];
    } else {
        $_SESSION['carrinho'][$id]['quantidade'] += $quantidade;
    }
}

// Remove produto
if (isset($_GET['remover'])) {
    unset($_SESSION['carrinho'][$_GET['remover']]);
}

// Atualiza quantidade
if (isset($_POST['atualizar'])) {
    foreach ($_POST['quantidades'] as $id => $qtd) {
        $_SESSION['carrinho'][$id]['quantidade'] = $qtd;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="carrinho-container">
        <h2>Carrinho de Compras</h2>

        <?php if (empty($_SESSION['carrinho'])): ?>
            <p class="carrinho-vazio">Seu carrinho está vazio.</p>
        <?php else: ?>
            <form method="POST">
                <table class="carrinho-table">
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Qtd</th>
                        <th>Total</th>
                        <th>Ação</th>
                    </tr>
                    <?php
                    $totalGeral = 0;
                    foreach ($_SESSION['carrinho'] as $id => $item):
                        $total = $item['preco'] * $item['quantidade'];
                        $totalGeral += $total;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome']) ?></td>
                        <td>R$<?= number_format($item['preco'], 2, ',', '.') ?></td>
                        <td>
                            <input type="number" name="quantidades[<?= $id ?>]" value="<?= $item['quantidade'] ?>" min="1">
                        </td>
                        <td>R$<?= number_format($total, 2, ',', '.') ?></td>
                        <td><a href="?remover=<?= $id ?>">Remover</a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <p class="total-geral">Total Geral: <strong>R$<?= number_format($totalGeral, 2, ',', '.') ?></strong></p>
                <div class="carrinho-botoes">
                    <button type="submit" name="atualizar" class="btn-atualizar">Atualizar Quantidades</button>
                    <a href="finalizar_compra.php" class="btn-finalizar">Finalizar Compra</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
