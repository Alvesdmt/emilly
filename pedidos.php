<?php
session_start();
require_once 'config.php';

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$usuario = $_SESSION['usuario'];

// Atualizar status do pedido
if (isset($_POST['atualizar_status'])) {
    $pedido_id = $_POST['pedido_id'];
    $novo_status = $_POST['novo_status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        $stmt->execute([$novo_status, $pedido_id]);
        
        header('Location: pedidos.php?msg=status_atualizado');
        exit;
    } catch (PDOException $e) {
        $erro = "Erro ao atualizar status: " . $e->getMessage();
    }
}

// Buscar todos os pedidos com detalhes
try {
    $stmt = $pdo->query("
        SELECT 
            p.id,
            p.data_pedido,
            p.valor_total,
            p.status,
            u.nome as nome_cliente,
            u.telefone,
            u.endereco
        FROM pedidos p
        JOIN usuarios u ON p.usuario_id = u.id
        ORDER BY p.data_pedido DESC
    ");
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = "Erro ao buscar pedidos: " . $e->getMessage();
    $pedidos = [];
}

$titulo = "Gerenciar Pedidos - Pizzaria da Zeza";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>

    <?php require_once 'includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="header">
            <div class="user-info">
                <img src="assets/user-avatar.png" alt="Avatar">
                <div>
                    <h3>Bem-vindo, <?= htmlspecialchars($usuario['nome']) ?>!</h3>
                    <small>Administrador</small>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>

        <div class="content">
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'status_atualizado'): ?>
                <div class="alert alert-success">
                    Status do pedido atualizado com sucesso!
                </div>
            <?php endif; ?>

            <div class="section">
                <div class="section-header">
                    <h2>Gerenciar Pedidos</h2>
                    <div class="filter-controls">
                        <select id="statusFilter" onchange="filterPedidos()">
                            <option value="">Todos os Status</option>
                            <option value="pendente">Pendente</option>
                            <option value="preparando">Preparando</option>
                            <option value="saiu_entrega">Saiu para Entrega</option>
                            <option value="entregue">Entregue</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>

                <div class="pedidos-grid">
                    <?php if (empty($pedidos)): ?>
                        <p>Nenhum pedido encontrado.</p>
                    <?php else: ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <div class="pedido-card" data-status="<?= htmlspecialchars($pedido['status']) ?>">
                                <div class="pedido-header">
                                    <h3>Pedido #<?= htmlspecialchars($pedido['id']) ?></h3>
                                    <span class="status-badge <?= htmlspecialchars($pedido['status']) ?>">
                                        <?= ucfirst(str_replace('_', ' ', htmlspecialchars($pedido['status']))) ?>
                                    </span>
                                </div>
                                
                                <div class="pedido-info">
                                    <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['nome_cliente']) ?></p>
                                    <p><strong>Telefone:</strong> <?= htmlspecialchars($pedido['telefone']) ?></p>
                                    <p><strong>Endereço:</strong> <?= htmlspecialchars($pedido['endereco']) ?></p>
                                    <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></p>
                                    <p><strong>Valor Total:</strong> R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></p>
                                </div>

                                <div class="pedido-actions">
                                    <button onclick="verDetalhes(<?= $pedido['id'] ?>)" class="btn btn-info">
                                        <i class="fas fa-eye"></i> Ver Detalhes
                                    </button>
                                    
                                    <form method="POST" class="status-form">
                                        <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                                        <select name="novo_status" onchange="this.form.submit()">
                                            <option value="pendente" <?= $pedido['status'] == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                            <option value="preparando" <?= $pedido['status'] == 'preparando' ? 'selected' : '' ?>>Preparando</option>
                                            <option value="saiu_entrega" <?= $pedido['status'] == 'saiu_entrega' ? 'selected' : '' ?>>Saiu para Entrega</option>
                                            <option value="entregue" <?= $pedido['status'] == 'entregue' ? 'selected' : '' ?>>Entregue</option>
                                            <option value="cancelado" <?= $pedido['status'] == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                        </select>
                                        <input type="hidden" name="atualizar_status" value="1">
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Detalhes do Pedido -->
    <div id="detalhesPedidoModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Detalhes do Pedido</h2>
            <div id="detalhesPedidoContent"></div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }

        function filterPedidos() {
            const status = document.getElementById('statusFilter').value;
            const pedidos = document.querySelectorAll('.pedido-card');
            
            pedidos.forEach(pedido => {
                if (!status || pedido.dataset.status === status) {
                    pedido.style.display = 'block';
                } else {
                    pedido.style.display = 'none';
                }
            });
        }

        const modal = document.getElementById('detalhesPedidoModal');
        const span = document.getElementsByClassName('close')[0];

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function verDetalhes(pedidoId) {
            const modal = document.getElementById('detalhesPedidoModal');
            modal.style.display = "block";
            
            fetch(`get_pedido_detalhes.php?id=${pedidoId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('detalhesPedidoContent').innerHTML = `
                        <h3>Pedido #${data.id}</h3>
                        <p><strong>Cliente:</strong> ${data.cliente}</p>
                        <p><strong>Itens do Pedido:</strong></p>
                        <ul>
                            ${data.itens.map(item => `
                                <li>${item.quantidade}x ${item.nome} - R$ ${item.preco}</li>
                            `).join('')}
                        </ul>
                        <p><strong>Total:</strong> R$ ${data.valor_total}</p>
                    `;
                })
                .catch(error => {
                    document.getElementById('detalhesPedidoContent').innerHTML = 
                        '<p class="error">Erro ao carregar detalhes do pedido.</p>';
                });
        }
    </script>
</body>
</html> 