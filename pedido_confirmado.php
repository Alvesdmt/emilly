<?php
session_start();
require_once 'config.php'; // Certifique-se de que a conexão com o banco está sendo carregada

$pedidoSalvo = false;
$dadosPedido = [];

// Adicionar log para debug
error_log("Iniciando processamento do pedido");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['carrinho'])) {
    try {
        // Log dos dados recebidos
        error_log("Dados do POST: " . print_r($_POST, true));
        error_log("Dados do carrinho: " . print_r($_SESSION['carrinho'], true));

        // Verificando se os dados do formulário foram passados corretamente
        if (isset($_POST['nome'], $_POST['cep'], $_POST['endereco'], $_POST['cidade'], $_POST['uf'])) {
            $nome = htmlspecialchars($_POST['nome']);
            $cep = htmlspecialchars(trim($_POST['cep']));  // Removendo espaços extras
            $endereco = htmlspecialchars($_POST['endereco']);
            $cidade = htmlspecialchars($_POST['cidade']);
            $uf = htmlspecialchars($_POST['uf']);
            $carrinho = $_SESSION['carrinho']; // Salva o carrinho antes de limpar!

            // Salvando os dados do pedido antes de limpar a sessão
            $dadosPedido = [
                'nome' => $nome,
                'endereco' => $endereco,
                'cidade' => $cidade,
                'uf' => $uf,
                'cep' => $cep,
                'itens' => $carrinho
            ];

            // Calculando o total do pedido
            $totalGeral = 0;
            foreach ($carrinho as $item) {
                $totalGeral += $item['preco'] * $item['quantidade'];
            }

            $pdo->beginTransaction();

            // Insere o pedido no banco
            $stmt = $pdo->prepare("INSERT INTO pedidos (nome_cliente, endereco, cidade, uf, cep, total) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $endereco, $cidade, $uf, $cep, $totalGeral]);

            // Pega o id do pedido recém-criado
            $pedido_id = $pdo->lastInsertId();

            // Insere os itens do pedido
            foreach ($carrinho as $item) {
                $pizza_id = isset($item['id']) ? $item['id'] : null;

                if ($pizza_id !== null) {
                    $stmt = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, pizza_id, nome, preco, quantidade) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$pedido_id, $pizza_id, $item['nome'], $item['preco'], $item['quantidade']]);
                } else {
                    // Pizza sem ID válido — você pode registrar isso ou apenas pular
                    error_log("Produto sem ID foi ignorado na gravação do pedido.");
                }
            }

            $pdo->commit();
            $pedidoSalvo = true;
            $dadosPedido['total'] = $totalGeral;
            unset($_SESSION['carrinho']);

        } else {
            // Se os dados não foram passados corretamente, redireciona para o cardápio
            header("Location: cardapio.php?erro=2");
            exit;
        }
    } catch (PDOException $e) {
        error_log("Erro detalhado ao salvar pedido: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        // Redirecionar com mensagem mais específica
        $erro = urlencode($e->getMessage());
        header("Location: cardapio.php?erro=1&mensagem=" . $erro);
        exit;
    }
} else {
    error_log("Método não é POST ou carrinho está vazio");
    header("Location: cardapio.php?erro=3");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .pedido-confirmado {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .success-icon {
            color: #28a745;
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php if ($pedidoSalvo && !empty($dadosPedido)): ?>
    <div class="container pedido-confirmado">
        <div class="text-center">
            <div class="success-icon">✅</div>
            <h2 class="mb-4">Pedido Confirmado com Sucesso!</h2>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3 class="mb-0">Dados da Entrega</h3>
            </div>
            <div class="card-body">
                <p><strong>Nome:</strong> <?= $dadosPedido['nome'] ?></p>
                <p><strong>Endereço:</strong> <?= $dadosPedido['endereco'] ?></p>
                <p><strong>Cidade:</strong> <?= $dadosPedido['cidade'] ?>/<?= $dadosPedido['uf'] ?></p>
                <p><strong>CEP:</strong> <?= $dadosPedido['cep'] ?></p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3 class="mb-0">Itens do Pedido</h3>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($dadosPedido['itens'] as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <?= $item['quantidade'] ?>x <?= htmlspecialchars($item['nome']) ?>
                            </span>
                            <span class="badge bg-primary rounded-pill">
                                R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h3 class="text-end">Total: R$ <?= number_format($dadosPedido['total'], 2, ',', '.') ?></h3>
            </div>
        </div>

        <div class="text-center">
            <a href="cardapio.php" class="btn btn-primary">Voltar ao Cardápio</a>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
