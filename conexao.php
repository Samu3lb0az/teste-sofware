<?php
$servidor = "localhost";
$usuario = "root"; 
$senha = "";     
$banco = "teste_software";     

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servidor, $usuario, $senha, $banco);

    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {

    die("ERRO: Não foi possível conectar ao banco de dados.");
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>