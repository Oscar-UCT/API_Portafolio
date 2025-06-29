<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: ../login.php");
  exit;
}

$id = intval($_GET['id']);

// Carga de los datos ya presentes del proyecto para luego poner en el formulario
$json = file_get_contents("https://teclab.uct.cl/~oscar.cariaga/portafolio-admin/api/proyectos.php?id=$id");
$proyecto = json_decode($json, true);

if (!$proyecto || !isset($proyecto['titulo'])) {
  die("Error: Proyecto no encontrado");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = [
    'titulo' => $_POST['titulo'],
    'descripcion' => $_POST['descripcion'],
    'url_github' => $_POST['url_github'],
    'url_produccion' => $_POST['url_produccion'],
  ];

  // Validación simple
  if (empty($data['titulo']) || empty($data['descripcion']) || empty($data['imagen'])) {
    die("Error: Título, descripción e imagen son obligatorios.");
  }

  // Detecta si se desea subir una imágen nueva
  if (!empty($_FILES['imagen']['name'])) {
    $img = $_FILES['imagen']['name'];
    move_uploaded_file($_FILES['imagen']['tmp_name'], "../uploads/$img");
    $data['imagen'] = $img;
  }

  // Llamada final a la API con el atributo personalizado PATCH
  session_write_close();

  $data['_method'] = 'PATCH';
  $ch = curl_init("https://teclab.uct.cl/~oscar.cariaga/portafolio-admin/api/proyectos.php?id=$id");
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Cookie: PHPSESSID=' . $_COOKIE["PHPSESSID"]],
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_POST => true
  ]);

  $response = curl_exec($ch);
  if (curl_errno($ch)) {
    die("Curl error: " . curl_error($ch));
  }
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
  <title>Editar proyecto</title>
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
    <h2 class="mb-4">Editar proyecto</h2>
    <div class="mb-3">
      <label for="titulo" class="form-label">Título</label>
      <input type="text" id="titulo" name="titulo" value="<?= $proyecto['titulo'] ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="descripcion" class="form-label">Descripción (máximo 200 carácteres)</label>
      <textarea id="descripcion" name="descripcion" maxlength="200" class="form-control" style="resize: none;" required><?= $proyecto['descripcion'] ?></textarea>
    </div>
    <div class="mb-3">
      <label for="url_github" class="form-label">URL GitHub</label>
      <input type="url" id="url_github" name="url_github" value="<?= $proyecto['url_github'] ?>" class="form-control">
    </div>
    <div class="mb-3">
      <label for="url_producción" class="form-label">URL Producción</label>
      <input type="url" id="url_produccion" name="url_produccion" value="<?= $proyecto['url_produccion'] ?>" class="form-control">
    </div>
    <div class="mb-3">
      <label for="imagen" class="form-label btn btn-outline-success">
        Subir Imágen
      </label>
      <input type="file" id="imagen" name="imagen" accept="image/png, image/jpeg" required><br>
    </div>
    <button type="submit" class="btn btn-success">Editar</button>
  </form>

</body>

</html>