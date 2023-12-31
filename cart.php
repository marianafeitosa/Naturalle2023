<?php
@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
    $delete_cart_item->execute([$delete_id]);
    header('location:cart.php');
}

if (isset($_GET['delete_all'])) {
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
    $delete_cart_item->execute([$user_id]);
    header('location:cart.php');
}

if (isset($_POST['update_qty'])) {
    $cart_id = $_POST['cart_id'];
    $p_qty = $_POST['p_qty'];
    $p_qty = filter_var($p_qty, FILTER_SANITIZE_STRING);
    $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
    $update_qty->execute([$p_qty, $cart_id]);
    $message[] = 'cart quantity updated';
}

$select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
$select_cart->execute([$user_id]);
$total_products_in_cart = $select_cart->rowCount();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Delivery</title>
    <!-- Inclua os links para estilos CSS e bibliotecas externas, como Bootstrap, se necessário -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .shopping-cart {
            padding: 50px;
        }

        .marmitas-info {
            float: left;
            width: 30%;
            margin-right: 20px;
        }

        .cart-info {
            float: left;
            width: 65%;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .footer {
            margin-top: 20px;
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
   
    <?php include 'delivery_header.php'; ?>
    

    <div style="height: 20px; padding: 150px;"></div>

    <section class="shopping-cart clearfix">
        <div class="container">
            <div class="marmitas-info">
                <h1 class="mb-4">Marmita para Doação</h1>
                <br>
                <div class="box-container">
                    <?php
                    if ($total_products_in_cart > 0) {
                        for ($i = 1; $i <= $total_products_in_cart; $i++) {
                            $item = $select_cart->fetch(PDO::FETCH_ASSOC);
                    ?>
                            <div class="box">
                                <!-- Informações da Marmita com base na quantidade de produtos -->
                                <h2>Marmita <?= $i ?></h2>
                                <p><?php echo $item['name']; ?></p>
                                <p>Ao comprar uma refeição em nosso restaurante, automaticamente você contribui para o nosso projeto social.</p>
                                <br>
                            </div>
                            <style>
                              .box-container{
                               background: ;
                              }


                              th {
        color: white; /* Adicione esta linha para definir a cor do texto para branco */
    }

    tbody tr td {
        color: white; /* Adicione esta linha para definir a cor do texto para branco */
    }

    tbody tr td input,
    tbody tr td select,
    tbody tr td button {
        color: white; /* Adicione esta linha para definir a cor do texto para branco */
    }

    
    
                            </style>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="cart-info">
                <h1 class="title">Seu Carrinho de Compras</h1>
                <?php if ($total_products_in_cart > 0): ?>
                    <table class="table" style="margin-top: 20px;">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Produto</th>
                                <th>Preço Unitário</th>
                                <th>Quantidade</th>
                                <th>Subtotal</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $select_cart->execute([$user_id]); // Reset the cursor position
                            while ($item = $select_cart->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <form method="POST" action="cart.php">
                                    <tr>
                                        <td><img src="uploaded_img/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" style="max-width: 100px;"></td>
                                        <td><?php echo $item['name']; ?></td>
                                        <td>R$ <?php echo $item['price']; ?></td>
                                        <td>
                                            <input type="number" name="p_qty" value="<?php echo $item['quantity']; ?>">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                            <input type="submit" name="update_qty" value="Atualizar">
                                        </td>
                                        <td>R$ <?php echo $item['price'] * $item['quantity']; ?></td>
                                        <td><a href="cart.php?delete=<?php echo $item['id']; ?>" class="btn">Remover</a></td>
                                    </tr>
                                </form>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <div class="total"></div>
                    <div class="total"></div>
<div class="actions mt-3 d-flex justify-content-between">
    <a href="shop.php" class="btn mr-2">Continuar Comprando</a>
    <a href="cart.php?delete_all" class="btn mr-2">Limpar Carrinho</a>
    <a href="checkout.php" class="btn btn-success">Finalizar Compra</a>
</div>

                <?php else: ?>
                    <p class="empty-cart">Seu carrinho está vazio.</p>
                    <a href="shop.php" class="btn">Continuar Comprando</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: black;">
            <h2 class="modal-title" id="infoModalLabel">Bem-vindo ao Carrinho, <?= $fetch_profile['name']; ?>!</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="background: black;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body text-center" style="background: black;">
            <script src="https://cdn.lordicon.com/lordicon-1.2.0.js"></script>
<lord-icon
    src="https://cdn.lordicon.com/gqjpawbc.json"
    trigger="hover"
    colors="primary:#9cf4a7,secondary:#ffffff"
    style="width:140px;height:140px">
</lord-icon>
                <p>Ao comprar uma refeição em nosso restaurante, automaticamente você contribui para o nosso projeto social.</p>
                <p>Confira as marmitas para doação disponíveis no carrinho.</p>
            </div>
            <div class="modal-footer" style="background: black;">
                <button type="button" class="btn" data-dismiss="modal">Entendi</button>
                <button type="button" class="btn" data-dismiss="modal" onclick="window.location.href='SaborSolidario.php'">Saiba mais</button>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="assets/js/script.js"></script>

<script>
    // Adicione este trecho de código para exibir o modal quando o carrinho é carregado
    $(document).ready(function() {
        $('#infoModal').modal('show');
    });
</script>

   
        <?php include 'footer.php'; ?>
    

    <script src="assets/js/script.js"></script>
</body>

</html>
