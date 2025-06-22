<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: ../login.php?redirect=add.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $img = $_FILES['imagen']['name'];

  // Recomendación de ChatGPT. Si la imágen es muy grande no la sube.
  if ($_FILES['imagen']['size'] > 2 * 1024 * 1024) { // 2MB
    die("File too large.");
  }

  move_uploaded_file($_FILES['imagen']['tmp_name'], "../uploads/$img");

  $data = [
    'titulo' => $_POST['titulo'],
    'descripcion' => $_POST['descripcion'],
    'url_github' => $_POST['url_github'],
    'url_produccion' => $_POST['url_produccion'],
    'imagen' => $img
  ];

  session_write_close();

  $ch = curl_init('https://teclab.uct.cl/~oscar.cariaga/portafolio-admin/api/proyectos.php');
  curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Cookie: PHPSESSID=' . $_COOKIE["PHPSESSID"]],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => json_encode($data)
  ]);
  curl_exec($ch);
  curl_close($ch);
  header("Location: ../index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" href="../assets/imgs/icon.png">
  <link rel="stylesheet" href="../assets/css/main.css">
  <title>Publicar Proyecto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-4">
  <header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm rounded">
      <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2 efecto-blanco" href="../index.php">
          <img src="../assets/imgs/icon.png" alt="Logo" width="24" height="24" class="d-inline-block align-text-top" />
          Administración Portafolio
        </a>
        <a href="add.php" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Proyecto</a>
        <a href="../logout.php" class="btn btn-outline-danger">Cerrar sesión</a>
      </div>
    </nav>
  </header>

  <form method="post" enctype="multipart/form-data" class="my-4 card p-5">
    <h2 class="mb-4">Agregar proyecto</h2>
    <div class="mb-3">
      <label for="titulo" class="form-label">Título</label>
      <input type="text" name="titulo" id="titulo" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="descripcion" class="form-label">Descripción (máximo 200 carácteres)</label>
      <textarea name="descripcion" id="descripcion" maxlength="200" style="resize: none;" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
      <label for="url_github" class="form-label">URL GitHub</label>
      <input type="url" name="url_github" id="url_github" class="form-control">
    </div>
    <div class="mb-3">
      <label for="url_produccion" class="form-label">URL Producción</label>
      <input type="url" name="url_produccion" id="url_produccion" class="form-control">
    </div>
    <div class="mb-3">
      <label for="imagen" class="form-label btn btn-outline-primary">
        Subir Imágen
      </label>
      <input type="file" id="imagen" name="imagen" accept="image/png, image/jpeg" required>
    </div>
    <button type="submit" class="btn btn-primary">Publicar</button>
  </form>

</body>

</html>