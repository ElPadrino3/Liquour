<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../../../Assets/CSS/pos.css">

<div class="register">
  <div class="left">
    <div class="order-window">
      <table>
        <tbody id="cart-body">
          <tr>
            <td>Cant.</td>
            <td>Producto</td>
            <td>P. Unit</td>
            <td>Subtotal</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="order-total">
      <span id="display-total">$0.00</span>
    </div>
    <div class="buttons">
      <button class="btn-special op-plus"><i class="fas fa-plus"></i></button>
      <button class="btn-special op-minus"><i class="fas fa-minus"></i></button>
      <button class="btn-special op-reset"><i class="fas fa-times"></i> Reiniciar</button>
      <button class="btn-special op-void"><i class="fas fa-ban"></i> Anular</button>

      <button class="btn-num n1">1</button>
      <button class="btn-num n2">2</button>
      <button class="btn-num n3">3</button>
      <button class="btn-num n4">4</button>
      <button class="btn-num n5">5</button>
      <button class="btn-num n6">6</button>
      <button class="btn-num n7">7</button>
      <button class="btn-num n8">8</button>
      <button class="btn-num n9">9</button>
      <button class="btn-num n0">0</button>
      <button class="btn-num ndot">.00</button>
      
      <button class="btn-special op-equal"><i class="fas fa-equals"></i></button>
    </div>
  </div>
  <div class="right">
    <div class="categories">
      <ul>
        <li><a href="#" data-filter="Todos" style="color: #C5A059;">Todos</a></li>
        <li><a href="#" data-filter="Whisky">Whisky</a></li>
        <li><a href="#" data-filter="Vino">Vinos</a></li>
        <li><a href="#" data-filter="Tequila">Tequila</a></li>
        <li><a href="#" data-filter="Cerveza">Cervezas</a></li>
        <li><a href="#" data-filter="Ron">Ron</a></li>
      </ul>
    </div>
    <div class="menu-items">
      <ul>
        <?php
        $items = [
            ["id" => 1, "barcode" => "750100001", "name" => "WHISKY ESCOCÉS 12 AÑOS", "sale_price" => 139.00, "purchase_price" => 95.00, "stock" => 24, "cat" => "Whisky", "img" => "https://images.pexels.com/photos/11271794/pexels-photo-11271794.jpeg"],
            ["id" => 2, "barcode" => "750100002", "name" => "VINO TINTO RESERVA", "sale_price" => 159.00, "purchase_price" => 110.50, "stock" => 15, "cat" => "Vino", "img" => "https://images.pexels.com/photos/2912108/pexels-photo-2912108.jpeg"],
            ["id" => 3, "barcode" => "750100003", "name" => "BUDWEISER BLACK LAGER", "sale_price" => 18.00, "purchase_price" => 12.00, "stock" => 120, "cat" => "Cerveza", "img" => "https://i.pinimg.com/1200x/d2/72/e1/d272e13fea9d56f79c44d63a085bdf2d.jpg"],
            ["id" => 4, "barcode" => "750100004", "name" => "BLUE LABEL - JOHNNY WALKER", "sale_price" => 220.99, "purchase_price" => 165.00, "stock" => 8, "cat" => "Whisky", "img" => "https://i.pinimg.com/736x/f0/67/97/f0679774f573ddd6dc3c82fd10624a6f.jpg"],
            ["id" => 5, "barcode" => "750100005", "name" => "TEQUILA REPOSADO PREMIUM", "sale_price" => 85.00, "purchase_price" => 55.00, "stock" => 32, "cat" => "Tequila", "img" => "https://i.pinimg.com/736x/4e/df/d7/4edfd76539603505ef771b0ec4a1f343.jpg"],
            ["id" => 6, "barcode" => "750100006", "name" => "VODKA GREY GOOSE", "sale_price" => 45.50, "purchase_price" => 30.00, "stock" => 45, "cat" => "Vodka", "img" => "https://i.pinimg.com/736x/4f/c9/c4/4fc9c44c6d835350c9aa4e8009f61a83.jpg"],
            ["id" => 7, "barcode" => "750100007", "name" => "RON AÑEJO 7 AÑOS", "sale_price" => 32.00, "purchase_price" => 20.50, "stock" => 60, "cat" => "Ron", "img" => "https://i.pinimg.com/736x/ea/ff/a2/eaffa23a48caa3aab3487219f25de1fe.jpg"],
            ["id" => 8, "barcode" => "750100008", "name" => "GINEBRA TANQUERAY", "sale_price" => 29.99, "purchase_price" => 18.00, "stock" => 28, "cat" => "Ginebra", "img" => "https://i.pinimg.com/1200x/ad/90/61/ad9061891a96361c0aac6fab61ab63f0.jpg"],
            ["id" => 9, "barcode" => "750100009", "name" => "CHAMPAGNE MOËT & CHANDON", "sale_price" => 110.00, "purchase_price" => 80.00, "stock" => 12, "cat" => "Champagne", "img" => "https://i.pinimg.com/736x/3c/e4/88/3ce488c312dc2d9b152d8f23b9d243ea.jpg"],
            ["id" => 10, "barcode" => "750100010", "name" => "MEZCAL ARTESANAL", "sale_price" => 65.00, "purchase_price" => 40.00, "stock" => 18, "cat" => "Mezcal", "img" => "https://i.pinimg.com/736x/2e/91/4f/2e914fd9c44a0fd9f425a8b7639730a0.jpg"],
            ["id" => 11, "barcode" => "750100011", "name" => "CERVEZA ARTESANAL IPA", "sale_price" => 4.50, "purchase_price" => 2.50, "stock" => 150, "cat" => "Cerveza", "img" => "https://i.pinimg.com/736x/39/d7/57/39d757e939c56ff67c1270603d7dadb1.jpg"],
            ["id" => 12, "barcode" => "750100012", "name" => "LICOR DE CAFÉ", "sale_price" => 22.00, "purchase_price" => 14.00, "stock" => 40, "cat" => "Licor", "img" => "https://i.pinimg.com/736x/29/28/2f/29282fcd6cd3b4e15433484719854e14.jpg"]
        ];

        foreach ($items as $p) {
        ?>
          <li class="product-card" data-categoria="<?php echo $p['cat']; ?>">
              <div class="product-stock">
                  <span class="stock-label">STOCK</span>
                  <span class="stock-value"><?php echo $p['stock']; ?></span>
              </div>

              <div class="product-image-container">
                  <img src="<?php echo $p['img']; ?>" alt="<?php echo $p['name']; ?>" class="product-img">
              </div>
              
              <div class="product-info">
                  <span class="item"><?php echo $p['name']; ?></span>
                  <span class="category"><?php echo $p['cat']; ?></span>
              </div>

              <div class="product-price">
                  $<?php echo number_format($p['sale_price'], 2); ?>
              </div>

              <div class="product-controls">
                  <button class="btn-qty btn-minus"><i class="fas fa-minus"></i></button>
                  <span class="qty-counter"></span>
                  <button class="btn-qty btn-plus"><i class="fas fa-plus"></i></button>
              </div>
          </li>
        <?php } ?>
      </ul>
    </div>
    
    <div class="payment-keys">
      <ul>
          <li id="toggle-keyboard" style="cursor: pointer;">
            <i class="fas fa-keyboard fa-2x fa-fw" data-fa-transform="up-2"></i> Teclado
          </li>
          <li>
              <a href="ventas_efectivo.php" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center;">
                  <i class="fas fa-money-bill-alt fa-2x fa-fw" data-fa-transform="up-2"></i> Efectivo
              </a>
          </li>
          <li>
              <a href="ventas_tarjeta.php" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center;">
                  <i class="fas fa-credit-card fa-2x fa-fw" data-fa-transform="up-2"></i> Tarjeta
              </a>
          </li>
          <li>
              <a href="promociones.php" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center;">
                  <i class="fas fa-tags fa-2x fa-fw" data-fa-transform="up-2"></i> Descuento
              </a>
          </li>
          <li>
              <a href="perfil_empleado.php" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center;">
                  <i class="fas fa-user fa-2x fa-fw" data-fa-transform="up-2"></i> Empleado
              </a>
          </li>
          <li>
              <a href="../../Include/Admin/dashboard.php" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center;">
                  <i class="fas fa-sign-out-alt fa-2x fa-fw" data-fa-transform="up-2"></i> Salir
              </a>
          </li>
      </ul>
    </div>
  </div>
</div>

<script src="../../../Assets/JS/pos.js"></script>