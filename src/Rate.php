<?php

class Rate {
    private $conn;
    private $table_name = "settings";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getRate() {
        $query = "SELECT value FROM " . $this->table_name . " WHERE key = 'exchange_rate'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            return $row['value'];
        }
        return 0;
    }

    public function setRate($rate) {
        $query = "UPDATE " . $this->table_name . " SET value = :value WHERE key = 'exchange_rate'";
        $stmt = $this->conn->prepare($query);
        
        $rate = htmlspecialchars(strip_tags($rate));
        $stmt->bindParam(":value", $rate);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
