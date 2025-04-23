<?php
session_start();

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if (isset($_POST['produto_id'], $_POST['nome'], $_POST['preco'], $_POST['quantidade'])) {
    $produto = [
        'id' => $_POST['produto_id'],
        'nome' => $_POST['nome'],
        'preco' => floatval($_POST['preco']),
        'quantidade' => intval($_POST['quantidade'])
    ];

    // Verifica se o produto já está no carrinho e atualiza
    $encontrado = false;
    foreach ($_SESSION['carrinho'] as &$item) {
        if ($item['id'] == $produto['id']) {
            $item['quantidade'] += $produto['quantidade'];
            $encontrado = true;
            break;
        }
    }
    unset($item);

    if (!$encontrado) {
        $_SESSION['carrinho'][] = $produto;
    }

    header("Location: carrinho.php");
    exit;
} else {
    echo "Erro: dados incompletos.";
}
