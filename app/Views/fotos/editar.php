<h2>Editar Foto</h2>
<form action="/fotos/<?=$foto['id'];?>" method="post">
  <input type="hidden" name="_method" value="PUT">
  <div class="mb-3">
    <label class="form-label">Descripción</label>
    <textarea name="descripcion" class="form-control"><?=htmlspecialchars($foto['descripcion']);?></textarea>
  </div>
  <button type="submit" class="btn btn-success">Guardar</button>
</form>
