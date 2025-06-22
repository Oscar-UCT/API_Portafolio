<?php
session_start();
include './api/config.php';

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST['username']);
  $password = md5($_POST['password']);

  // Prevención de injecciones SQL
  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows === 1) {
    $_SESSION['user'] = $username;
    $redirect = isset($_GET['redirect']) ? "./crud/" . $_GET['redirect'] : 'index.php';
    header("Location: " . $redirect);
    exit;
  } else {
    $error = "Credenciales incorrectas.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" href="./assets/imgs/icon.png">
  <link rel="stylesheet" href="./assets/css/main.css">
  <title>Ingreso</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-4">
  <header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm rounded">
      <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2 efecto-blanco" href="index.php">
          <img src="./assets/imgs/icon.png" alt="Logo" width="24" height="24" class="d-inline-block align-text-top" />
          Administración Portafolio
        </a>
        <span>Inicio de sesión</span>
      </div>
    </nav>
  </header>
  <form method="post" class="my-4 card p-5">
    <h2 class="mb-4">Ingresa tus datos</h2>
    <label for="username" class="form-label">Nombre de usuario</label>
    <div class="input-group mb-3">
      <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-user"></i></span>
      <input type="text" name="username" class="form-control" placeholder="Usuario" aria-label="Usuario" aria-describedby="basic-addon1" required>
    </div>
    <label for="password" class="form-label">Contraseña</label>
    <div class="input-group mb-3">
      <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-lock"></i></span>
      <input type="password" name="password" class="form-control" placeholder="*********" aria-label="Contraseña" required><br>
    </div>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
  </form>
</body>

</html>