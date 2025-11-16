<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mi Álbum de Fotos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100 text-light" style="background-color: #2b2d30ff;">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary mb-4">
  <div class="container">
    <a class="navbar-brand" href="/">Mi Álbum</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="/fotos">Mis Fotos</a></li>
          <li class="nav-item"><a class="nav-link" href="/fotos/crear">Subir Foto</a></li>
          <li class="nav-item"><a class="nav-link" href="/logout">Cerrar sesión (<?=htmlspecialchars($_SESSION['user_name']);?>)</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/usuarios/crear">Registrarme</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
<?php if (isset($_SESSION['flash_success'])): ?>
  <div class="alert alert-success"><?=htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']);?></div>
<?php endif; ?>
<?php if (isset($_SESSION['flash_error'])): ?>
  <div class="alert alert-danger"><?=htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']);?></div>
<?php endif; ?>
