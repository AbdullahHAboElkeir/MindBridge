<?php
// config/database.php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = 'localhost';
        $dbname = 'mindbridge';
        $username = 'root'; // Default XAMPP
        $password = ''; // Default XAMPP

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Try to create database if it doesn't exist
            try {
                $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
                $pdo->exec("USE `$dbname`");

                // Import schema
                $sql = file_get_contents(__DIR__ . '/../database/mindbridge.sql');
                $pdo->exec($sql);

                // Reconnect to the database
                $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e2) {
                die("Database setup failed. Please ensure MySQL is running and XAMPP is started. Error: " . $e2->getMessage());
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}
?>