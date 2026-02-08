<?php
require_once '../src/Database.php';
require_once '../src/Auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if($auth->isLoggedIn()) {
    header("Location: admin.php");
    exit;
}

$error = '';

if($_POST) {
    if($auth->login($_POST['username'], $_POST['password'])) {
        header("Location: admin.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Yanet Papelería</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2 style="text-align: center; margin-bottom: 20px;">Administración</h2>
        
        <?php if($error): ?>
            <div style="background: #fadbd8; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Ingresar</button>
        </form>
        <div style="margin-top: 15px; text-align: center;">
            <a href="index.php">Volver al inicio</a>
        </div>
    </div>

</body>
</html>
