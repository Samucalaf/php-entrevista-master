<?php

class Connection
{


    private $pdo;

    public function __construct()
    {
        $databaseFile = realpath(__DIR__ . "/database/db.sqlite");
        $this->pdo = new PDO("sqlite:{$databaseFile}");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function execute($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}
