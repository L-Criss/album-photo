<div class="row justify-content-center">
  <div class="col-md-5">
    <h2 class="mb-3">Editar Usuario</h2>
    <form action="/usuarios/<?=$usuario['id'];?>" method="post">
      <input type="hidden" name="_method" value="PUT">
      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?=htmlspecialchars($usuario['nombre']);?>" required>
      </div>
      <button type="submit" class="btn btn-success">Actualizar</button>
    </form>
  </div>
</div>
