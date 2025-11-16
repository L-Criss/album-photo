<h2>Subir Foto</h2>
<form action="/fotos" method="post" enctype="multipart/form-data">
  <div class="mb-3">
    <label class="form-label">Imagen</label>
    <input type="file" name="foto" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Descripción</label>
    <textarea name="descripcion" class="form-control"></textarea>
  </div>
  <button type="submit" class="btn btn-success">Subir</button>
</form>
