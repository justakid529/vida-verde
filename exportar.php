<?php
include("conexao.php");

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="produtos.csv"');

$out = fopen("php://output", "w");
fputcsv($out, ["ID", "Categoria", "Nome", "Preço", "Unidade"]);

$res = $conn->query("SELECT * FROM produtos");
while ($r = $res->fetch_assoc()) {
  fputcsv($out, $r);
}
fclose($out);
$conn->close();
?>
