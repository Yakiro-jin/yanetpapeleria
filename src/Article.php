<?php

class Article {
    private $conn;
    private $table_name = "articles";

    public $id;
    public $name;
    public $quantity;
    public $description;
    public $price_usd;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all articles
    public function read($search = "") {
        $query = "SELECT * FROM " . $this->table_name;
        
        if (!empty($search)) {
            $query .= " WHERE name ILIKE :search OR description ILIKE :search";
        }
        
        $query .= " ORDER BY name ASC";

        $stmt = $this->conn->prepare($query);

        if (!empty($search)) {
            $search_term = "%{$search}%";
            $stmt->bindParam(":search", $search_term);
        }

        $stmt->execute();
        return $stmt;
    }

    // Create article
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, quantity, description, price_usd) 
                  VALUES (:name, :quantity, :description, :price_usd)";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price_usd = htmlspecialchars(strip_tags($this->price_usd));

        // Bind
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price_usd", $this->price_usd);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update article
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET name = :name,
                      quantity = :quantity,
                      description = :description,
                      price_usd = :price_usd
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price_usd = htmlspecialchars(strip_tags($this->price_usd));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price_usd", $this->price_usd);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete article
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Read single article
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->name = $row['name'];
            $this->quantity = $row['quantity'];
            $this->description = $row['description'];
            $this->price_usd = $row['price_usd'];
            return true;
        }
        return false;
    }
}
