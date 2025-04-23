<?php
$titulo = "Relatórios - Pizzaria da Zeza";
ob_start();
?>

<div class="section">
    <h2>Relatórios do Sistema</h2>

    <div class="dashboard-grid">
        <!-- Relatório de Vendas -->
        <div class="report-card">
            <h3>Vendas Totais</h3>
            <?php
            require_once 'conexao.php';
            $sql = "SELECT COUNT(*) as total_vendas, SUM(valor_total) as receita_total FROM pedidos";
            $result = $conn->query($sql);
            $vendas = $result->fetch_assoc();
            ?>
            <div class="report-numbers">
                <p>Total de Pedidos: <strong><?php echo $vendas['total_vendas']; ?></strong></p>
                <p>Receita Total: <strong>R$ <?php echo number_format($vendas['receita_total'], 2, ',', '.'); ?></strong></p>
            </div>
        </div>

        <!-- Pizzas Mais Vendidas -->
        <div class="report-card">
            <h3>Top 5 Pizzas Mais Vendidas</h3>
            <div class="report-list">
                <?php
                $sql = "SELECT p.nome, COUNT(*) as quantidade 
                        FROM itens_pedido ip 
                        JOIN pizzas p ON ip.pizza_id = p.id 
                        GROUP BY p.id 
                        ORDER BY quantidade DESC 
                        LIMIT 5";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()) {
                    echo "<div class='list-item'>
                            <span>{$row['nome']}</span>
                            <span class='quantity'>{$row['quantidade']}</span>
                          </div>";
                }
                ?>
            </div>
        </div>

        <!-- Relatório por Categoria -->
        <div class="report-card">
            <h3>Vendas por Categoria</h3>
            <div class="report-list">
                <?php
                $sql = "SELECT categoria, COUNT(*) as quantidade 
                        FROM pizzas 
                        GROUP BY categoria";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()) {
                    echo "<div class='list-item'>
                            <span>{$row['categoria']}</span>
                            <span class='quantity'>{$row['quantidade']}</span>
                          </div>";
                }
                ?>
            </div>
        </div>

        <!-- Últimos Pedidos -->
        <div class="report-card">
            <h3>Últimos Pedidos</h3>
            <div class="report-table">
                <?php
                $sql = "SELECT p.id, p.data_pedido, p.valor_total, p.status 
                        FROM pedidos p 
                        ORDER BY p.data_pedido DESC 
                        LIMIT 5";
                $result = $conn->query($sql);
                ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>#{$row['id']}</td>
                                    <td>" . date('d/m/Y H:i', strtotime($row['data_pedido'])) . "</td>
                                    <td>R$ " . number_format($row['valor_total'], 2, ',', '.') . "</td>
                                    <td><span class='status-{$row['status']}'>{$row['status']}</span></td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="actions">
        <a href="admin_dashboard.php" class="btn">Voltar ao Dashboard</a>
        <button onclick="window.print()" class="btn btn-print">Imprimir Relatórios</button>
    </div>
</div>

<style>
.section {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

.section h2 {
    color: #d63031;
    font-size: 2rem;
    margin-bottom: 2rem;
    text-align: center;
    font-family: 'Roboto', sans-serif;
    border-bottom: 3px solid #d63031;
    padding-bottom: 1rem;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.report-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.report-card h3 {
    color: #2d3436;
    font-size: 1.2rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #dfe6e9;
}

.report-numbers {
    font-size: 1.1rem;
}

.report-numbers strong {
    color: #d63031;
    font-size: 1.2rem;
}

.report-list .list-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #dfe6e9;
}

.report-list .quantity {
    font-weight: bold;
    color: #d63031;
}

.report-table table {
    width: 100%;
    border-collapse: collapse;
}

.report-table th,
.report-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #dfe6e9;
}

.report-table th {
    background: #f1f2f6;
    font-weight: 600;
}

.status-pendente { color: #e17055; }
.status-preparando { color: #00b894; }
.status-entregue { color: #0984e3; }
.status-cancelado { color: #d63031; }

.actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    display: inline-block;
    padding: 0.8rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #d63031;
    color: white;
}

.btn:hover {
    background: #b71c1c;
    transform: translateY(-2px);
}

.btn-print {
    background: #0984e3;
}

.btn-print:hover {
    background: #0769b6;
}

@media (max-width: 768px) {
    .section {
        margin: 1rem;
        padding: 1rem;
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        margin: 0.5rem 0;
        text-align: center;
    }
}

@media print {
    .actions {
        display: none;
    }
    
    .section {
        box-shadow: none;
        margin: 0;
        padding: 0;
    }
}
</style>

<?php
$conteudo = ob_get_clean();
require_once 'layout.php';
?> 