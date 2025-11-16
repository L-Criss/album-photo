<div class="row justify-content-center">
  <div class="col-md-5">
    <h2 class="mb-3">Registro de Usuario</h2>

    <form action="/usuarios" method="post">
            <!-- CSRF TOKEN -->
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Apellido</label>
        <input type="text" name="apellido" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Usuario</label>
        <input type="text" name="user" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-success">Crear cuenta</button>
    </form>
  </div>
</div>

