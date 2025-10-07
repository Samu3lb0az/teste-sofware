<?php
require_once '../conexao.php';

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../homepage.php");
    exit;
}

$email = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST['email'])) || empty(trim($_POST['senha']))) {
        $error_message = "Por favor, preencha o email e a senha.";
    } else {
        $email = trim($_POST['email']);
        $senha = trim($_POST['senha']);
    }

    if (empty($error_message)) {
        $sql = "SELECT id, nome, senha FROM usuarios WHERE email = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = $email;

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $nome, $hashed_senha);
                    if ($stmt->fetch()) {
                        if (password_verify($senha, $hashed_senha)) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["nome"] = $nome;

                            $_SESSION['swal_success'] = "Login efetuado com sucesso!";

                            header("location: ../homepage.php");
                            exit;
                        } else {
                            $error_message = "A senha que você inseriu não é válida.";
                        }
                    }
                } else {
                    $error_message = "Nenhuma conta encontrada com esse email.";
                }
            } else {
                $error_message = "Oops! Algo deu errado. Por favor, tente novamente.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <style>
        .back-arrow { position: absolute; top: 20px; left: 20px; font-size: 24px; text-decoration: none; color: #333; }
    </style>
</head>
<body>
    <a href="../index.php" class="back-arrow" title="Voltar">&#8592;</a>

    <div class="login-container">
        <h2>Login</h2>
        <?php
        if (!empty($error_message)) {
            echo '<div class="error-message">' . htmlspecialchars($error_message) . '</div>';
        }
        if (!empty($_SESSION['success_message'])) {
            echo '<div class="success-message">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="loginForm">
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn">Entrar</button>
            <p class="link-cadastro">Não tem uma conta? <a href="cadastrar.php">Cadastre-se</a></p>
        </form>
    </div>
    <script src="../assets/js/login-validation.js"></script>
</body>
</html>