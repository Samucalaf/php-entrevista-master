<?php

require 'database.php';

function processUserAction($action)
{

    switch ($action) {
        case 'create':
            return createUserService();
        case 'update':
            return updateUserService();
        case 'delete':
            return deleteUserService();
        case 'assing_color':
            return assignColorService();
        case 'remove_color':
            return removeColorService();
        default:
            return ['message' => 'Ação inválida', 'type' => 'danger'];
    }
}

function validateRequiredFields(array $fields)
{
    foreach ($fields as $name => $value) {
        if (empty(trim($value))) {
            return "O campo $name é obrigatório";
        }
    }
}
function createUserService()
{
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $error = validateRequiredFields(['Nome' => $name, 'Email' => $email]);

    if ($error) {
        return ['message' => $error, 'type' => 'warning'];
    }

    try {
        $result = createUser($name, $email);
        return $result
            ? ['message' => 'Usuário criado com sucesso!', 'type' => 'sucess']
            : ['message' => 'Erro ao criar usuário', 'type' => 'danger'];
    } catch (Exception $e) {
        return ['message' => 'Erro ao criar usuário: ' . $e->getMessage(), 'type' => 'danger'];
    }
}

function updateUserService()
{
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $error = validateRequiredFields(['ID' => $id, 'Nome' => $name, 'Email' => $email]);

    if ($error) {
        return ['message' => $error, 'type' => 'warning'];
    }


    try {
        $result = updateUser($id, $name, $email);
        return $result
            ? ['message' => 'Usuário atualizado com sucesso!', 'type' => 'success']
            : ['message' => 'Erro ao atualizar usuário', 'type' => 'danger'];
    } catch (Exception $e) {
        return ['message' => 'Erro ao atualizar usuário: ' . $e->getMessage(), 'type' => 'danger'];
    }
}

function deleteUserService()
{
    $id = $_POST['id'] ?? '';

    if (!$id || $id < 0) {
        return ['message' => 'ID do usuário é obrigatório', 'type' => 'warning'];
    }

    try {
        $result = deleteUser($id);
        return $result
            ? ['message' => 'Usuário excluído com sucesso!', 'type' => 'success']
            : ['message' => 'Erro ao excluir usuário', 'type' => 'danger'];
    } catch (Exception $e) {
        return ['message' => 'Erro ao excluir usuário: ' . $e->getMessage(), 'type' => 'danger'];
    }
}

function assignColorService()
{

    $userId = (int)($_POST['user_id'] ?? '');
    $colorId = (int)($_POST['color_id'] ?? '');

    if (!$userId || !$colorId) {
        return ['message' => 'Usuário e cor são obrigatórios', 'type' => 'warning'];
    }

    try {
        $result = assignColor($userId, $colorId);
        return $result
            ? ['message' => 'Cor vinculada com sucesso!', 'type' => 'success']
            : ['message' => 'Esta cor já está vinculada ao usuário', 'type' => 'warning'];
    } catch (Exception $e) {
        return ['message' => 'Erro ao vincular cor: ' . $e->getMessage(), 'type' => 'danger'];
    }
}

function removeColorService()
{
    $userId = (int)($_POST['user_id'] ?? 0);
    $colorId = (int)($_POST['color_id'] ?? 0);

    if ($userId <= 0 || $colorId <= 0) {
        return ['message' => 'Usuário e cor são obrigatórios', 'type' => 'warning'];
    }

    try {
        $result = removeColor($userId, $colorId);
        return $result
            ? ['message' => 'Cor desvinculada com sucesso!', 'type' => 'success']
            : ['message' => 'Erro ao desvincular cor', 'type' => 'danger'];
    } catch (Exception $e) {
        return ['message' => 'Erro ao desvincular cor: ' . $e->getMessage(), 'type' => 'danger'];
    }
}


function getPageData()
{
    return [
        'users' => getAllUsers(),
        'colors' => getAllColors(),
        'userColors' => getUserColors()
    ];
}
