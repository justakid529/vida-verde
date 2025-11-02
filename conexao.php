<?php
$host = "localhost";
$usuario = "root"; // No InfinityFree será tipo epiz_12345678
$senha = "";
$banco = "venda_frutas";

$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
  die("Erro na conexão: " . $conn->connect_error);
}
?>
