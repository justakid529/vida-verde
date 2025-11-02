<?php
include 'conexao.php';

$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);

$produtos = [];

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $produtos[] = [
      "id" => $row["id"],
      "category" => $row["categoria"],
      "name" => $row["nome"],
      "price" => $row["preco"],
      "unit" => $row["unidade"]
    ];
  }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($produtos);



if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'salvar_pedido') {
    include("conexao.php");

    $forma_pagamento = $_POST['forma_pagamento'];
    $data_retirada = $_POST['data_retirada'];
    $itens = json_decode($_POST['itens'], true); // array de produtos e quantidades

    // Inserir o pedido
    $stmt = $conn->prepare("INSERT INTO pedidos (forma_pagamento, data_retirada) VALUES (?, ?)");
    $stmt->bind_param("ss", $forma_pagamento, $data_retirada);
    $stmt->execute();
    $pedido_id = $stmt->insert_id;
    $stmt->close();

    // Inserir itens do pedido
    $stmtItem = $conn->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade) VALUES (?, ?, ?)");
    foreach ($itens as $item) {
        $stmtItem->bind_param("iii", $pedido_id, $item['produto_id'], $item['quantidade']);
        $stmtItem->execute();
    }
    $stmtItem->close();

    $conn->close();
    echo json_encode(["status" => "sucesso", "pedido_id" => $pedido_id]);
    exit;
}

?>
