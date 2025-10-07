<?php
require_once 'conexao.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: src/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }

        .homepage-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            width: 90%;
            max-width: 600px; 
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h1 {
            color: #2c3e50;
            font-size: 2.2em;
            margin-bottom: 10px;
        }
        h1 b {
            color: #6a11cb;
            font-weight: 700;
        }

        p {
            color: #555;
            font-size: 1.2em;
            margin-bottom: 40px;
        }

        .btn-sair {
            display: inline-block;
            text-decoration: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            background-image: linear-gradient(45deg, #d53369, #daae51); 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-sair:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>

    <div class="homepage-container">
        <h1>Bem-vindo, <b><?php echo htmlspecialchars($_SESSION["nome"]); ?></b>!</h1>
        <p>Você está na sua página inicial.</p>
        <a id="logout-btn" class="btn-sair">Sair</a>
    </div>

    <script>
        <?php
        if (isset($_SESSION['swal_success'])) {
            echo "Swal.fire({
                    title: 'Sucesso!',
                    text: '" . htmlspecialchars($_SESSION['swal_success'], ENT_QUOTES) . "',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok'
                });";
            unset($_SESSION['swal_success']);
        }
        ?>

        document.getElementById('logout-btn').addEventListener('click', function(e) {
            e.preventDefault(); 

            Swal.fire({
                title: 'Tem certeza?',
                text: "Você será desconectado da sua conta.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, quero sair!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        });
    </script>
</body>
</html>