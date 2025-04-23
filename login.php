<?php
session_start();
require_once 'config.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verifica se o usuário existe
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && $usuario['senha'] === $senha) {
        // Inicia a sessão
        $_SESSION['usuario'] = $usuario;

        // Redireciona conforme o tipo de usuário
        header("Location: " . ($usuario['tipo'] === 'admin' ? "admin_dashboard.php" : "cardapio.php"));
        exit;
    } else {
        $erro = 'E-mail ou senha inválidos!';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pizzaria da Zeza</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
            transform: translateY(0);
            transition: transform 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2.5em;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .error-message {
            background: #ff6b6b;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        input:focus {
            outline: none;
            border-color: #4ecdc4;
            box-shadow: 0 0 10px rgba(78, 205, 196, 0.3);
        }

        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1.1em;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .links {
            margin-top: 20px;
            text-align: center;
        }

        .links a {
            color: #4ecdc4;
            text-decoration: none;
            transition: color 0.3s ease;
            display: inline-block;
            margin: 10px 0;
        }

        .links a:hover {
            color: #ff6b6b;
            transform: translateY(-2px);
        }

        .google-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #fff;
            border: 2px solid #e0e0e0;
            padding: 12px;
            border-radius: 10px;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .google-login:hover {
            background: #f5f5f5;
            transform: translateY(-2px);
        }

        .google-login img {
            width: 20px;
            height: 20px;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
            }

            h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>

        <?php if (!empty($erro)): ?>
            <div class="error-message"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <input type="email" name="email" placeholder="E-mail" required>
            </div>
            <div class="form-group">
                <input type="password" name="senha" placeholder="Senha" required>
            </div>
            <button type="submit">Entrar</button>
        </form>

        <div class="links">
            <a href="cadastro.php">Não tem uma conta? Cadastre-se</a>
            <div class="google-login">
                <img src="https://www.google.com/favicon.ico" alt="Google">
                <a href="<?= $client->createAuthUrl(); ?>">Login com Google</a>
            </div>
        </div>
    </div>
</body>
</html>
