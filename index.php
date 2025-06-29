<?php
session_start();
session_regenerate_id(true); 

$json = file_get_contents('https://teclab.uct.cl/~oscar.cariaga/portafolio-admin/api/proyectos.php');
$proyectos = json_decode($json, true);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Portafolio Dinámico - Oscar</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" href="./assets/imgs/icon.png">
  <link rel="stylesheet" href="./assets/css/main.css">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-4">
  <header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm rounded">
      <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2 efecto-blanco" href="#">
          <img src="./assets/imgs/icon.png" alt="Logo" width="24" height="24" class="d-inline-block align-text-top" />
          Administración Portafolio
        </a>
        <a href="./crud/add.php" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Proyecto</a>
        <?php
        if (!isset($_SESSION['user'])):
        ?>
          <a href="login.php" class="btn btn-outline-primary">Iniciar Sesión</a>
        <?php else: ?>
          <a href="logout.php" class="btn btn-outline-danger">Cerrar sesión</a>
        <?php endif; ?>

      </div>
    </nav>
  </header>
  <!-- Se usó ChatGPT para adaptar el código anterior que usaba while con un query directo a la base de datos a este foreach que usa la API. -->
  <h2 class="my-4">Tus proyectos</h2>
  <?php if (is_array($proyectos) && count($proyectos) > 0): ?>
    <?php foreach ($proyectos as $p): ?>
      <div class="card mb-3 shadow-sm">
        <div class="row g-0">
          <div class="col-md-3 d-flex align-items-center justify-content-center p-2">
            <img src="./uploads/<?= htmlspecialchars($p['imagen']) ?>" class="img-fluid rounded" style="max-width: 200px;" alt="<?= htmlspecialchars($p['titulo']) ?>">
          </div>
          <div class="col-md-9">
            <div class="card-body">
              <h3 class="card-title"><?= htmlspecialchars($p['titulo']) ?></h3>
              <p class="card-text"><?= htmlspecialchars($p['descripcion']) ?></p>
              <a href="<?= htmlspecialchars($p['url_github']) ?>" class="btn btn-dark btn-sm me-2" target="_blank"><i class="fab fa-github"></i> GitHub</a>
              <a href="<?= htmlspecialchars($p['url_produccion']) ?>" class="btn btn-primary btn-sm me-2" target="_blank"><i class="fa fa-link"></i> Enlace</a>
              <?php if (isset($_SESSION['user'])): ?>
                <a href="./crud/edit.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm me-2">
                  <i class="fa fa-edit"></i> Editar
                </a>
                <a href="./crud/delete.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro?')">
                  <i class="fa fa-trash-can"></i> Eliminar
                </a>
              <?php else: ?>
                <span data-bs-toggle="tooltip" title="Inicia sesión para editar">
                  <button type="button" class="btn btn-warning btn-sm me-2" disabled>
                    <i class="fa fa-edit"></i> Editar
                  </button>
                </span>
                <span data-bs-toggle="tooltip" title="Inicia sesión para eliminar">
                  <button type="button" class="btn btn-danger btn-sm" disabled>
                    <i class="fa fa-trash-can"></i> Eliminar
                  </button>
                </span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No se encontraron proyectos.</p>
  <?php endif; ?>

  <footer>
    <a href="https://teclab.uct.cl/~oscar.cariaga/portafolio" target="_blank">Ir a portafolio</a>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  <script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
  </script>

</body>

</html>