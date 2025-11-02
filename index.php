<?php include("conexao.php"); ?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Sítio Oliveira — Produtos</title>
  <link rel="stylesheet" href="stylesheet.css">
  <style>
  /* (mantém exatamente seu CSS inline como está) */
  <?php readfile("stylesheet.css"); ?>
  </style>
</head>
<body>
  <!-- (mantém seu HTML exatamente igual até o script) -->

  <!-- seu HTML aqui é igual ao que enviou acima -->
  <header>
    <div class="container brand">
      <div>
        <h1>Sítio Oliveira — Produtos</h1>
        <p class="lead">Produtos frescos direto do produtor</p>
      </div>

      <div style="margin-left:auto;display:flex;gap:12px;align-items:center">
        <div style="text-align:right">
          <div style="font-size:12px;color:var(--muted)">Carrinho</div>
          <div id="cart-count" style="font-weight:700">0 itens</div>
        </div>
      </div>
    </div>
  </header>

  <main class="container">
    <div class="layout">
      <!-- Sidebar -->
      <aside class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
          <strong>Categorias</strong>
          <small id="show-count" style="color:var(--muted)">Mostrando todos</small>
        </div>

        <div class="categories" style="margin-bottom:12px">
          <button class="active" data-cat="Todos">Todos</button>
          <button data-cat="Hortaliças">Hortaliças</button>
          <button data-cat="Frutas">Frutas</button>
          <button data-cat="Legumes">Legumes</button>
        </div>

        <div>
          <label style="font-size:13px;color:var(--muted)">Buscar</label>
          <div class="search">
            <input id="search" placeholder="Ex.: alface, banana, tomate" />
            <button id="clear-search" class="btn ghost">Limpar</button>
          </div>
        </div>

        <div style="margin-top:14px">
            <input type="text" id="nome_cliente" placeholder="Seu nome" required>
<input type="tel" id="telefone" placeholder="Seu telefone" required>

            <select id="forma_pagamento" required>
  <option value="">Selecione</option>
  <option value="Pix na hora">Pix na hora</option>
  <option value="Dinheiro na hora">Dinheiro na hora</option>
</select>

<h3>Data de Retirada:</h3>
<select id="data_retirada" required></select>

          <h4 style="margin:0 0 8px 0">Resumo do carrinho</h4>
          <div id="cart-contents">
            <p style="color:var(--muted);font-size:13px">Seu carrinho está vazio.</p>
          </div>
          

<script>
// Função para gerar as próximas 4 sextas-feiras
function gerarProximasSextas() {
  const select = document.getElementById("data_retirada");
  select.innerHTML = "";
  const hoje = new Date();
  let sexta = new Date();

  // Encontrar a próxima sexta
  sexta.setDate(hoje.getDate() + ((5 - hoje.getDay() + 7) % 7));

  for (let i = 0; i < 4; i++) {
    const data = new Date(sexta);
    data.setDate(sexta.getDate() + i * 7);
    const dataISO = data.toISOString().split("T")[0];
    const dataFormatada = data.toLocaleDateString("pt-BR", { weekday: "long", day: "2-digit", month: "2-digit" });
    const option = document.createElement("option");
    option.value = dataISO;
    option.textContent = dataFormatada;
    select.appendChild(option);
  }
}
gerarProximasSextas();
</script>

        </div>
      </aside>

      <!-- Products + grid -->
      <section>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <div>
            <h3 style="margin:0">Produtos</h3>
            <div id="result-info" style="color:var(--muted);font-size:13px">Mostrando 0 produtos</div>
          </div>
          <div style="display:flex;gap:8px;align-items:center">
            <!-- small search for mobile -->
            <input id="search-mobile" placeholder="Buscar" style="padding:8px;border-radius:8px;border:1px solid #e5e7eb;display:none" />
          </div>
        </div>

        <div class="products-grid" id="products"></div>
      </section>
    </div>
  </main>

  <footer>
    © <span id="year"></span> Sítio Oliveira — Catálogo
  </footer>



<script>
document.addEventListener("DOMContentLoaded", () => {
  const PRODUCTS = [];
  let cart = [];

  let currentCategory = 'Todos';
  let query = '';

  const money = v => Number(v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

  const productsEl = document.getElementById('products');
  const resultInfo = document.getElementById('result-info');
  const showCount = document.getElementById('show-count');
  const cartCountEl = document.getElementById('cart-count');
  const cartContents = document.getElementById('cart-contents');
  const yearEl = document.getElementById('year');
  yearEl.textContent = new Date().getFullYear();

  // ---------- Funções do carrinho ----------
  function addToCart(id) {
    const p = PRODUCTS.find(x => x.id == id);
    if (!p) return alert("Produto não encontrado!");
    const item = cart.find(c => c.id == id);
    if (item) item.qtd++;
    else cart.push({ id: id, qtd: 1 });
    renderCart();
  }

  function removeFromCart(id) {
    cart = cart.filter(c => c.id != id);
    renderCart();
  }

  function changeQty(id, delta) {
    const item = cart.find(c => c.id == id);
    if (!item) return;
    item.qtd += delta;
    if (item.qtd < 1) removeFromCart(id);
    renderCart();
  }

  function cartTotal() {
    return cart.reduce((sum, c) => {
      const p = PRODUCTS.find(x => x.id == c.id);
      return sum + (Number(p.price) * c.qtd);
    }, 0);
  }

  function renderCart() {
    const totalItens = cart.reduce((s, i) => s + i.qtd, 0);
    cartCountEl.textContent = `${totalItens} item(s)`;

    if (cart.length === 0) {
      cartContents.innerHTML = `<p style="color:var(--muted);font-size:13px">Seu carrinho está vazio.</p>`;
      return;
    }

    cartContents.innerHTML = '';
    cart.forEach(entry => {
      const p = PRODUCTS.find(x => x.id == entry.id);
      const div = document.createElement('div');
      div.className = 'cart-item';
      div.innerHTML = `
        <div style="flex:1">
          <div style="font-size:14px;font-weight:600">${p.name}</div>
          <div style="font-size:12px;color:var(--muted)">${entry.qtd} x ${money(p.price)}</div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px">
          <div class="qty-controls" style="display:flex;gap:6px">
            <button onclick="changeQty(${p.id}, -1)">-</button>
            <button onclick="changeQty(${p.id}, 1)">+</button>
          </div>
          <div style="font-weight:700">${money(p.price * entry.qtd)}</div>
          <button style="background:transparent;border:0;color:#ef4444;cursor:pointer;font-size:12px;padding:0" onclick="removeFromCart(${p.id})">Remover</button>
        </div>
      `;
      cartContents.appendChild(div);
    });

    const footer = document.createElement('div');
    footer.style.marginTop = '10px';
    footer.innerHTML = `
      <div style="display:flex;justify-content:space-between;font-weight:700;padding-top:8px;border-top:1px solid #eef2f7">
        <div>Total</div>
        <div>${money(cartTotal())}</div>
      </div>
      <div style="margin-top:8px">
        <button onclick="checkout()" class="btn add" style="width:100%">Finalizar compra</button>
      </div>
    `;
    cartContents.appendChild(footer);
  }

  function checkout() {
    if (cart.length === 0) return alert('Seu carrinho está vazio.');

    const resumo = cart.map(c => {
      const p = PRODUCTS.find(x => x.id == c.id);
      return `${p.name} — ${c.qtd} x ${money(p.price)} = ${money(p.price * c.qtd)}`;
    }).join('\n');
    const total = money(cartTotal());
    alert('Resumo do pedido:\n\n' + resumo + '\n\nTotal: ' + total);
  }

  function viewDetails(id) {
    const p = PRODUCTS.find(x => x.id == id);
    alert(`${p.name}\nCategoria: ${p.category}\nUnidade: ${p.unit}\nPreço: ${money(p.price)}`);
  }

  // ---------- Render de produtos ----------
  function filteredProducts() {
    return PRODUCTS.filter(p => {
      const byCat = currentCategory === 'Todos' || p.category === currentCategory;
      const byQuery = p.name.toLowerCase().includes(query.toLowerCase());
      return byCat && byQuery;
    });
  }

  function renderProducts() {
    const items = filteredProducts();
    productsEl.innerHTML = '';
    resultInfo.textContent = `Mostrando ${items.length} produto(s)`;
    showCount.textContent = currentCategory === 'Todos'
      ? 'Mostrando todos'
      : `Categoria: ${currentCategory}`;

    items.forEach(p => {
      const card = document.createElement('article');
      card.className = 'product';
      card.innerHTML = `
        <div class="thumb">Imagem</div>
        <div style="flex:1">
          <h4 style="margin:0 0 6px 0">${p.name}</h4>
          <div class="meta">${p.category} • ${p.unit}</div>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px">
          <div class="price">${money(p.price)}</div>
          <div class="actions">
            <button class="btn ghost" onclick="viewDetails(${p.id})">Detalhes</button>
            <button class="btn add" onclick="addToCart(${p.id})">Adicionar</button>
          </div>
        </div>
      `;
      productsEl.appendChild(card);
    });
  }

  // ---------- Eventos de busca e categoria ----------
  document.querySelectorAll('.categories button').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.categories button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentCategory = btn.dataset.cat;
      renderProducts();
    });
  });

  const searchInput = document.getElementById('search');
  searchInput.addEventListener('input', e => {
    query = e.target.value.trim();
    renderProducts();
  });
  document.getElementById('clear-search').addEventListener('click', () => {
    query = '';
    searchInput.value = '';
    renderProducts();
  });

  // ---------- Busca os produtos da API ----------
  fetch('api.php')
    .then(r => r.json())
    .then(data => {
      PRODUCTS.push(...data);
      renderProducts();
      renderCart();
    })
    .catch(err => {
      alert("Erro ao carregar produtos: " + err);
    });

  // ---------- Expor funções globalmente ----------
  window.addToCart = addToCart;
  window.changeQty = changeQty;
  window.removeFromCart = removeFromCart;
  window.checkout = checkout;
  window.viewDetails = viewDetails;
});

</script>

</body>
</html>
