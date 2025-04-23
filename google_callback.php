<?php
session_start();
require_once 'config.php'; // onde está o $client e a conexão $pdo

if (isset($_GET['code'])) {
    // Troca o "code" pelo token de acesso
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);

        // Obtem informações do usuário Google
        $google_oauth = new Google\Service\Oauth2($client);
        $google_account = $google_oauth->userinfo->get();

        $nome  = $google_account->name;
        $email = $google_account->email;

        // Verifica se o e-mail já está cadastrado
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            // Se não existir, insere novo usuário (tipo padrão = cliente)
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, '', 'cliente')");
            $stmt->execute([$nome, $email]);

            // Pega o novo usuário
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
        }

        $_SESSION['usuario'] = $usuario;

        // Redireciona conforme o tipo
        header("Location: " . ($usuario['tipo'] === 'admin' ? "admin_dashboard.php" : "cardapio.php"));
        exit;
    } else {
        echo "Erro ao autenticar com Google.";
    }
} else {
    echo "Código de autenticação não fornecido.";
}
