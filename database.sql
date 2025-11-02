CREATE DATABASE venda_frutas;
USE venda_frutas;

CREATE TABLE produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categoria VARCHAR(50),
  nome VARCHAR(100),
  preco DECIMAL(10,2),
  unidade VARCHAR(20)
);

INSERT INTO produtos (categoria, nome, preco, unidade) VALUES
('Hortaliças', 'Alface Crespa', 4.00, 'un'),
('Hortaliças', 'Cebolinha', 3.50, 'maço'),
('Frutas', 'Banana Nanica', 7.50, 'kg'),
('Frutas', 'Maçã Pink (500g)', 12.90, '500g'),
('Legumes', 'Cenoura (500g)', 7.50, '500g'),
('Legumes', 'Tomate Italiano (500g)', 8.50, '500g');

CREATE TABLE pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
  forma_pagamento ENUM('Pix na hora', 'Dinheiro na hora') NOT NULL,
  data_retirada DATE NOT NULL
);

CREATE TABLE itens_pedido (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT,
  produto_id INT,
  quantidade INT DEFAULT 1,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
);
ALTER TABLE pedidos
ADD COLUMN nome_cliente VARCHAR(100),
ADD COLUMN telefone VARCHAR(20);
