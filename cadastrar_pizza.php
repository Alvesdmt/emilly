<?php
$titulo = "Cadastrar Pizza - Pizzaria da Zeza";
ob_start();
?>

<!-- Conteúdo específico da página -->
<div class="section">
    <h2>Cadastrar Nova Pizza</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="nome">Nome da Pizza:</label>
            <input type="text" id="nome" name="nome" required>
        </div>
        
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="preco">Preço:</label>
            <input type="number" id="preco" name="preco" step="0.01" required>
        </div>
        
        <div class="form-group">
            <label for="categoria">Categoria:</label>
            <select id="categoria" name="categoria" required>
                <option value="Tradicional">Tradicional</option>
                <option value="Especial">Especial</option>
                <option value="Doce">Doce</option>
            </select>
        </div>
        
        <button type="submit" class="btn">Cadastrar Pizza</button>
        <a href="admin_dashboard.php" class="btn" style="background: var(--dark-color);">Voltar</a>
    </form>
</div>

<style>
.section {
    max-width: 600px;
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

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #2d3436;
    font-weight: 600;
    font-size: 1rem;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid #dfe6e9;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: #d63031;
    outline: none;
    box-shadow: 0 0 5px rgba(214, 48, 49, 0.2);
}

.form-group textarea {
    height: 120px;
    resize: vertical;
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
    margin: 0.5rem;
}

button.btn {
    background: #d63031;
    color: white;
}

button.btn:hover {
    background: #b71c1c;
    transform: translateY(-2px);
}

a.btn {
    background: #2d3436;
    color: white;
}

a.btn:hover {
    background: #1e272e;
    transform: translateY(-2px);
}

/* Responsividade */
@media (max-width: 768px) {
    .section {
        margin: 1rem;
        padding: 1rem;
    }
    
    .section h2 {
        font-size: 1.5rem;
    }
    
    .btn {
        width: 100%;
        margin: 0.5rem 0;
    }
}
</style>

<?php
$conteudo = ob_get_clean();
require_once 'layout.php';
?> 