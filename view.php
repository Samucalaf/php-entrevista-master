<?php
function showPage($users, $colors, $userColors, $message = '', $messageType = '')
{
?>
    <!DOCTYPE html>
    <html lang="pt-BR">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Teste PHP</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    </head>

    <body class="bg-light">
        <div class="container mt-4">
            <h1 class="text-center mb-4"><i class="bi bi-people-fill"></i>Teste PHP</h1>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-plus"></i> <span id="form-title">Adicionar Usuário</span></h5>
                </div>
                <div class="card-body">
                    <form id="user-form" method="POST">
                        <input type="hidden" name="action" id="form-action" value="create">
                        <input type="hidden" name="id" id="form-id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Digite seu Nome" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Digite seu Email" required>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Salvar</button>
                            <button type="button" class="btn btn-secondary" onclick="resetForm()"><i class="bi bi-arrow-clockwise"></i> Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i>Lista de Usuários</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Cores</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user):
                                    $userColorList = array_filter($userColors, fn($uc) => $uc->user_id == $user->id);
                                ?>
                                    <tr>
                                        <td><?= $user->id ?></td>
                                        <td><?= htmlspecialchars($user->name) ?></td>
                                        <td><?= htmlspecialchars($user->email) ?></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                <?php foreach ($userColorList as $userColor): ?>
                                                    <span class="badge bg-primary">
                                                        <?= htmlspecialchars($userColor->color_name) ?>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Remover cor?')">
                                                            <input type="hidden" name="action" value="remove_color">
                                                            <input type="hidden" name="user_id" value="<?= $user->id ?>">
                                                            <input type="hidden" name="color_id" value="<?= $userColor->color_id ?>">
                                                            <button type="submit" class="btn-close btn-close-white" style="font-size: 0.7em;"></button>
                                                        </form>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-plus"></i> Adicionar Cor
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php foreach ($colors as $color):
                                                        $assigned = array_filter($userColorList, fn($uc) => $uc->color_id == $color->id);
                                                    ?>
                                                        <?php if (empty($assigned)): ?>
                                                            <li>
                                                                <form method="POST" style="display: inline;">
                                                                    <input type="hidden" name="action" value="assign_color">
                                                                    <input type="hidden" name="user_id" value="<?= $user->id ?>">
                                                                    <input type="hidden" name="color_id" value="<?= $color->id ?>">
                                                                    <button type="submit" class="dropdown-item"><?= htmlspecialchars($color->name) ?></button>
                                                                </form>
                                                            </li>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-warning" onclick="editUser(<?= $user->id ?>, '<?= htmlspecialchars($user->name) ?>', '<?= htmlspecialchars($user->email) ?>')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Excluir usuário?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $user->id ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            function editUser(id, name, email) {
                document.getElementById('form-title').textContent = 'Editar Usuário';
                document.getElementById('form-action').value = 'update';
                document.getElementById('form-id').value = id;
                document.getElementById('name').value = name;
                document.getElementById('email').value = email;
                document.getElementById('user-form').scrollIntoView({
                    behavior: 'smooth'
                });
            }

            function resetForm() {
                document.getElementById('form-title').textContent = 'Adicionar Usuário';
                document.getElementById('form-action').value = 'create';
                document.getElementById('form-id').value = '';
                document.getElementById('name').value = '';
                document.getElementById('email').value = '';
            }
        </script>
    </body>

    </html>
<?php
}
