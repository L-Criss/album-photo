<div class="card p-3">
  <h2>Perfil de Usuario</h2>
  <p><strong>Nombre:</strong> <?=htmlspecialchars($usuario['nombre']);?></p>
  <p><strong>Email:</strong> <?=htmlspecialchars($usuario['email']);?></p>
  <a href="/usuarios/<?=$usuario['id'];?>/editar" class="btn btn-warning">Editar</a>
</div>
