<div class="row justify-content-center">
  <div class="col-md-5">
    <h2 class="mb-3">Iniciar sesión</h2>
    <form action="/login" method="post">
            <!-- CSRF TOKEN -->
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Entrar</button>
    </form>
  </div>
</div>
