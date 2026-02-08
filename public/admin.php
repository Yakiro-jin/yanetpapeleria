<?php
require_once '../src/Database.php';
require_once '../src/Article.php';
require_once '../src/Rate.php';
require_once '../src/Auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if(!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$article = new Article($db);
$rateObj = new Rate($db);

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_rate'])) {
        $rateObj->setRate($_POST['rate']);
    } elseif (isset($_POST['create_article'])) {
        $article->name = $_POST['name'];
        $article->quantity = $_POST['quantity'];
        $article->description = $_POST['description'];
        $article->price_usd = $_POST['price_usd'];
        $article->create();
    } elseif (isset($_POST['update_article'])) {
        $article->id = $_POST['id'];
        $article->name = $_POST['name'];
        $article->quantity = $_POST['quantity'];
        $article->description = $_POST['description'];
        $article->price_usd = $_POST['price_usd'];
        $article->update();
    } elseif (isset($_POST['delete_article'])) {
        $article->id = $_POST['id'];
        $article->delete();
    }
    // Reflect changes immediately
    header("Location: admin.php");
    exit;
}

$exchange_rate = $rateObj->getRate();
$stmt = $article->read();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Yanet Papelería</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container navbar">
            <span class="logo">Panel de Control</span>
            <div>
                <span style="margin-right: 15px;">Hola, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="index.php" class="btn btn-primary" target="_blank" style="margin-right: 10px;">Ver Tienda</a>
                <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <div class="container">
        
        <!-- Rate Management -->
        <div class="card" style="margin-bottom: 20px;">
            <h3 class="card-title">Tasa de Cambio</h3>
            <form method="POST" style="display: flex; gap: 10px; align-items: center;">
                <input type="number" step="0.01" name="rate" value="<?php echo $exchange_rate; ?>" required style="max-width: 150px;">
                <button type="submit" name="update_rate" class="btn btn-primary">Actualizar Tasa</button>
            </form>
        </div>

        <div class="admin-header">
            <h2>Artículos</h2>
            <button onclick="openModal('createModal')" class="btn btn-primary">+ Nuevo Artículo</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Precio ($)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td>$<?php echo number_format($row['price_usd'], 2); ?></td>
                    <td>
                        <button onclick='editArticle(<?php echo json_encode($row); ?>)' class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem;">Editar</button>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro?');">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_article" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Create Modal -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <h3>Nuevo Artículo</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Cantidad</label>
                    <input type="number" name="quantity" required>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Precio ($)</label>
                    <input type="number" step="0.01" name="price_usd" required>
                </div>
                <div style="text-align: right;">
                    <button type="button" onclick="closeModal('createModal')" class="btn btn-danger">Cancelar</button>
                    <button type="submit" name="create_article" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Editar Artículo</h3>
            <form method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>Cantidad</label>
                    <input type="number" name="quantity" id="edit_quantity" required>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="description" id="edit_description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Precio ($)</label>
                    <input type="number" step="0.01" name="price_usd" id="edit_price_usd" required>
                </div>
                <div style="text-align: right;">
                    <button type="button" onclick="closeModal('editModal')" class="btn btn-danger">Cancelar</button>
                    <button type="submit" name="update_article" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function editArticle(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_quantity').value = data.quantity;
            document.getElementById('edit_description').value = data.description;
            document.getElementById('edit_price_usd').value = data.price_usd;
            openModal('editModal');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = "none";
            }
        }
    </script>
</body>
</html>
