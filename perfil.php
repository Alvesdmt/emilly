<?php
session_start();
require_once 'config.php'; // Conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

// Atualizar dados do usuário
if (isset($_POST['atualizar_dados'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    // Atualiza os dados no banco
    $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $usuario_id]);

    // Atualiza os dados na sessão
    $_SESSION['usuario']['nome'] = $nome;
    $_SESSION['usuario']['email'] = $email;

    $mensagem = "Dados atualizados com sucesso!";
}

// Alterar senha do usuário
if (isset($_POST['alterar_senha'])) {
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if (password_verify($senha_atual, $usuario['senha'])) {
        if ($nova_senha === $confirmar_senha) {
            // Atualiza a senha no banco
            $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            $stmt->execute([$nova_senha_hash, $usuario_id]);

            $mensagem_senha = "Senha alterada com sucesso!";
        } else {
            $mensagem_senha = "As senhas não coincidem!";
        }
    } else {
        $mensagem_senha = "A senha atual está incorreta!";
    }
}

// Excluir a conta do usuário
if (isset($_POST['excluir_conta'])) {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);

    // Destrói a sessão e redireciona para login
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Pizzaria da Zeza</title>
</head>
<body>
    <h1>Perfil do Usuário</h1>

    <!-- Exibir mensagens de sucesso ou erro -->
    <?php if (isset($mensagem)): ?>
        <p style="color: green;"><?= $mensagem ?></p>
    <?php endif; ?>
    <?php if (isset($mensagem_senha)): ?>
        <p style="color: red;"><?= $mensagem_senha ?></p>
    <?php endif; ?>

    <!-- Formulário para atualizar dados -->
    <h2>Atualizar Dados</h2>
    <form method="POST">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required><br><br>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required><br><br>

        <button type="submit" name="atualizar_dados">Atualizar Dados</button>
    </form>

    <hr>

    <!-- Formulário para alterar senha -->
    <h2>Alterar Senha</h2>
    <form method="POST">
        <label for="senha_atual">Senha Atual:</label>
        <input type="password" id="senha_atual" name="senha_atual" required><br><br>

        <label for="nova_senha">Nova Senha:</label>
        <input type="password" id="nova_senha" name="nova_senha" required><br><br>

        <label for="confirmar_senha">Confirmar Nova Senha:</label>
        <input type="password" id="confirmar_senha" name="confirmar_senha" required><br><br>

        <button type="submit" name="alterar_senha">Alterar Senha</button>
    </form>

    <hr>

    <!-- Formulário para excluir conta -->
    <h2>Excluir Conta</h2>
    <form method="POST">
        <p>Deseja excluir sua conta permanentemente?</p>
        <button type="submit" name="excluir_conta" style="color: red;">Excluir Conta</button>
    </form>

    <p><a href="index.php">Voltar à Página Inicial</a></p>
</body>
</html>
