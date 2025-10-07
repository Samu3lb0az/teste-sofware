<?php
require_once '../conexao.php';

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../homepage.php");
    exit;
}

$nome = $email = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $confirma_senha = trim($_POST['confirma_senha']);

    if (empty($nome) || empty($email) || empty($senha) || empty($confirma_senha)) {
        $error_message = "Por favor, preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "O formato do email é inválido.";
    } elseif (strlen($senha) < 6) {
        $error_message = "A senha deve ter pelo menos 6 caracteres.";
    } elseif ($senha !== $confirma_senha) {
        $error_message = "As senhas não coincidem.";
    } else {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = $email;

            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $error_message = "Este email já está em uso.";
                } else {
                    $sql_insert = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
                    if ($stmt_insert = $conn->prepare($sql_insert)) {
                        $senha_hashed = password_hash($senha, PASSWORD_DEFAULT);
                        
                        $stmt_insert->bind_param("sss", $nome, $email, $senha_hashed);

                        if ($stmt_insert->execute()) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $stmt_insert->insert_id;
                            $_SESSION["nome"] = $nome;

                            $_SESSION['swal_success'] = "Sua conta foi criada com sucesso!";

                            header("location: ../homepage.php");
                            exit();
                        } else {
                            $error_message = "Algo deu errado. Tente novamente.";
                        }
                        $stmt_insert->close();
                    }
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
    <title>Cadastro</title>
    <link rel="stylesheet" href="../assets/css/cadastro.css">
    <style>
        .back-arrow { position: absolute; top: 20px; left: 20px; font-size: 24px; text-decoration: none; color: #333; }
    </style>
</head>
<body>
    <a href="../index.php" class="back-arrow" title="Voltar">&#8592;</a>

    <div class="cadastro-container">
        <h2>Crie sua Conta</h2>
        <?php
        if (!empty($error_message)) {
            echo '<div class="error-message">' . htmlspecialchars($error_message) . '</div>';
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="cadastroForm">
            <div class="input-group">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($nome); ?>">
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required minlength="6">
            </div>
            <div class="input-group">
                <label for="confirma_senha">Confirmar Senha</label>
                <input type="password" id="confirma_senha" name="confirma_senha" required>
            </div>
            <button type="submit" class="btn">Cadastrar</button>
            <p class="link-login">Já tem uma conta? <a href="login.php">Faça login</a></p>
        </form>
    </div>
    <script src="../assets/js/cadastro-validation.js"></script>
</body>
</html>