<?php
session_start();
if (empty($_SESSION['carrinho'])) {
    header("Location: cardapio.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Finalizar Compra</h2>

        <form action="pedido_confirmado.php" method="POST" id="checkoutForm">
            <div class="form-group">
                <label for="nome">Nome Completo:</label>
                <input type="text" name="nome" id="nome" required pattern="[A-Za-zÀ-ÿ\s]+" minlength="3">
                <span class="error-message">Por favor, insira seu nome completo</span>
            </div>

            <div class="form-group">
                <label for="cep">CEP:</label>
                <input type="text" name="cep" id="cep" required pattern="\d{5}-?\d{3}">
                <span class="error-message">Insira um CEP válido</span>
            </div>

            <div class="form-group">
                <label for="endereco">Endereço:</label>
                <input type="text" name="endereco" id="endereco" required>
                <span class="error-message">Endereço é obrigatório</span>
            </div>

            <div class="form-group">
                <label for="cidade">Cidade:</label>
                <input type="text" name="cidade" id="cidade" required>
                <span class="error-message">Cidade é obrigatória</span>
            </div>

            <div class="form-group">
                <label for="uf">UF:</label>
                <input type="text" name="uf" id="uf" required maxlength="2">
                <span class="error-message">UF é obrigatória</span>
            </div>

            <button type="submit" id="submitBtn">Confirmar Pedido</button>
        </form>
    </div>

    <script>
    document.getElementById('cep').addEventListener('blur', function() {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(res => res.json())
                .then(data => {
                    if (!data.erro) {
                        const endereco = document.getElementById('endereco');
                        const cidade = document.getElementById('cidade');
                        const uf = document.getElementById('uf');
                        
                        endereco.value = data.logradouro;
                        cidade.value = data.localidade;
                        uf.value = data.uf;
                        
                        [endereco, cidade, uf].forEach(input => {
                            input.classList.add('auto-filled');
                        });
                    }
                })
                .finally(() => {
                    submitBtn.classList.remove('loading');
                });
        }
    });

    // Formatação do CEP
    document.getElementById('cep').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 5) {
            this.value = value.slice(0,5) + '-' + value.slice(5,8);
        } else {
            this.value = value;
        }
    });
    </script>
</body>
</html>
