<?php
require_once '../src/Database.php';
require_once '../src/Article.php';
require_once '../src/Rate.php';

$database = new Database();
$db = $database->getConnection();

$article = new Article($db);
$rateObj = new Rate($db);

$exchange_rate = $rateObj->getRate();
$search = isset($_GET['search']) ? $_GET['search'] : "";
$stmt = $article->read($search);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yanet Papelería - Inventario</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container navbar">
            <a href="index.php" class="logo">Yanet Papelería</a>
            <div class="rate-display">Tasa del día: <?php echo number_format($exchange_rate, 2); ?> Bs/$</div>
        </div>
    </header>

    <div class="container">
        <div style="margin-bottom: 30px; text-align: center;">
            <input type="text" id="searchInput" class="search-bar" placeholder="Buscar artículo..." value="<?php echo htmlspecialchars($search); ?>">
        </div>

        <div class="grid" id="articleGrid">
            <?php
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $price_bs = $price_usd * $exchange_rate;
                    $quantity_class = $quantity < 5 ? 'low' : 'normal';
                    ?>
                    <div class="card">
                        <div>
                            <span class="quantity-badge <?php echo $quantity_class; ?>">
                                Disponible: <?php echo $quantity; ?>
                            </span>
                            <h3 class="card-title"><?php echo htmlspecialchars($name); ?></h3>
                            <p class="card-description"><?php echo htmlspecialchars($description); ?></p>
                        </div>
                        <div>
                            <div class="price-tag">$<?php echo number_format($price_usd, 2); ?></div>
                            <div class="price-bs">Bs <?php echo number_format($price_bs, 2); ?></div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='text-align: center; grid-column: 1/-1;'>No se encontraron artículos.</p>";
            }
            ?>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        let timeout = null;

        searchInput.addEventListener('input', function (e) {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                window.location.href = '?search=' + encodeURIComponent(e.target.value);
            }, 500);
        });
        
        // Focus input if search param exists
        if(window.location.search) {
             searchInput.focus();
             // move cursor to end
             const val = searchInput.value;
             searchInput.value = '';
             searchInput.value = val;
        }
    </script>
</body>
</html>
