<h2>Mis Fotos</h2>
<a href="/fotos/crear" class="btn btn-primary mb-3">Subir nueva foto</a>
<div class="row">
<?php foreach($fotos as $f): ?>
  <div class="col-md-3 mb-3">
    <div class="card">
      <a href="/fotos/<?=$f['id'];?>">
        <img src="<?=htmlspecialchars($f['ruta']);?>" class="card-img-top" style="height:200px;object-fit:cover;">
      </a>
      <div class="card-body">
        <p class="card-text"><?=htmlspecialchars(substr($f['descripcion'],0,40));?></p>
        <form action="/fotos/<?=$f['id'];?>" method="post" onsubmit="return confirm('¿Eliminar foto?');">
          <input type="hidden" name="_method" value="DELETE">
          <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
