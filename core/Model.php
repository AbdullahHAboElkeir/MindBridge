<?php
// core/Model.php

abstract class Model {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = str_repeat('?, ', count($data) - 1) . '?';
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $stmt = $this->db->prepare("UPDATE {$this->table} SET $set WHERE id = ?");
        $stmt->execute(array_merge(array_values($data), [$id]));
        return $stmt->rowCount();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    public function where($column, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE $column = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }
}
?>