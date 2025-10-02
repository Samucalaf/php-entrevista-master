<?php

require 'connection.php';

function getAllUsers()
{
    return (new Connection())->query("SELECT * FROM users ORDER BY name");
}

function getAllColors()
{
    return (new Connection())->query("SELECT * FROM colors ORDER BY name");
}

function getUserColors()
{
    return (new Connection())->query("
        SELECT uc.user_id, uc.color_id, c.name as color_name 
        FROM user_colors uc 
        JOIN colors c ON uc.color_id = c.id
    ");
}

function createUser($name, $email)
{
    try {
        if (empty(trim($name))) {
            throw new Exception("Nome não pode ser vazio");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email inválido");
        }

        if (emailExists($email)){
            throw new Exception("Este email já está cadastrado");
        }

        if (strlen($name) < 3 || strlen($name) > 100) {
            throw new Exception("Nome deve ter entre 3 e 100 caracteres");
        }

        return (new Connection())->execute("INSERT INTO users (name, email) VALUES (?, ?)", [$name, $email]);
    } catch (PDOException $e) {
        error_log("Erro ao criar usuário: " . $e->getMessage());
        throw $e;
    }
}

function updateUser($id, $name, $email)
{
    try {
        $id = (int) $id;
        if ($id <= 0) {
            throw new Exception("ID inválido");
        }

        if (empty(trim($name))) {
            throw new Exception("Nome não pode ser vazio");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email inválido");
        }

        return (new Connection())->execute("UPDATE users SET name = ?, email = ? WHERE id = ?", [$name, $email, $id]);
    } catch (PDOException $e) {
        error_log("Erro ao editar usuário: " . $e->getMessage());
        return $e;
    }
}

function deleteUser($id)
{
    $id = (int) $id;
    if ($id <= 0) {
        throw new Exception("ID inválido");
    }
    $db = new Connection();
    $db->execute("DELETE FROM user_colors WHERE user_id = ?", [$id]);
    return $db->execute("DELETE FROM users WHERE id = ?", [$id]);
}

function assignColor($userId, $colorId)
{
    $db = new Connection();
    $existing = $db->query("SELECT * FROM user_colors WHERE user_id = ? AND color_id = ?", [$userId, $colorId]);
    return empty($existing) ? $db->execute("INSERT INTO user_colors (user_id, color_id) VALUES (?, ?)", [$userId, $colorId]) : false;
}

function removeColor($userId, $colorId)
{
    return (new Connection())->execute("DELETE FROM user_colors WHERE user_id = ? AND color_id = ?", [$userId, $colorId]);
}


function emailExists($email, $excludeId = null) {
    $db = new Connection();
    $sql = "SELECT id FROM users WHERE email = ?";
    $params = [$email];

    if ($excludeId) {
        $sql .= " AND id != ?";
        $params[] = $excludeId;
    }

    $result = $db->query($sql, $params);
    return !empty($result);
}