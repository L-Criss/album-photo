<div class="card mb-3">
  <img src="<?=htmlspecialchars($foto['ruta']);?>" 
       class="card-img-top" 
       style="max-height:500px;object-fit:contain;">
  <div class="card-body">
    <p class="card-text"><?=nl2br(htmlspecialchars($foto['descripcion']));?></p>
    <p class="text-muted">Subida: <?=htmlspecialchars($foto['created_at']);?></p>
    <a href="/fotos/<?=$foto['id'];?>/editar" class="btn btn-warning">Editar</a>
    <a href="/fotos" class="btn btn-secondary">Regresar</a>
  </div>
</div>
