<?php

namespace App\Models;

use PDO;

class Contact extends \Core\Model
{   
    private $errors = [];
    private $name;
    private $lastname;
    private $phone;

    public function __construct($data = [])
    {
        if(!empty($data)){
            $this->name = $data['name'];
            $this->lastname = $data['lastname'];
            $this->phone = $data['phone'];
        }
    }

    public function store()
    {   $this->validate(__FUNCTION__);
        if(empty($this->errors)){
            $sql = "INSERT INTO contacts (name, lastname, phone)
            VALUES (:name, :lastname, :phone)";

            $db = static::getDb();

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':lastname', $this->lastname, PDO::PARAM_STR);
            $stmt->bindValue(':phone', $this->phone, PDO::PARAM_STR);

            return $stmt->execute();
        }
    }

    public function update($id)
    {
        $this->validate(__FUNCTION__);
        if(empty($this->errors)){
            $sql = "UPDATE contacts SET name = :name, lastname = :lastname, phone = :phone
            WHERE id = :id";

            $db = static::getDb();

            $stmt = $db->prepare($sql);
            $stmt->bindValue(":name", $this->name, PDO::PARAM_STR);
            $stmt->bindValue(":lastname", $this->lastname, PDO::PARAM_STR);
            $stmt->bindValue(":phone", $this->phone, PDO::PARAM_STR);
            $stmt->bindValue(":id", $id, PDO::PARAM_STR);

            return $stmt->execute();
        }
    }

    public function destroy($id)
    {
        $sql = "DELETE FROM contacts WHERE id = :id";
        $db = static::getDb();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_STR);
        $stmt->execute();
    }

    protected function validate($method)
    {
        if ($this->name == '') {
            $this->errors[] = "Name field is required";
        }

        if ($this->lastname == '') {
            $this->errors[] = "Lastname field is required";
        }

        if ($this->phone == '') {
            $this->errors[] = "Phone field is required";
        }

        if(!preg_match('/^[0-9]*$/', $this->phone)) {
            $this->errors[] = "Just numbers required";
        }
        
        if ($method != "update"){
            if ($this->phoneExists()) {
                $this->errors[] = "Phone already exists";
            }
        }
    }

    public function getContacts($start_point, $per_page)
    {

        $sql = "SELECT * FROM contacts ORDER BY name
        LIMIT $start_point, $per_page";
        $stmt = static::getDb()->query($sql);
        return $stmt;
    }

    public function getOneContact($id)
    {
        $sql = "SELECT * FROM contacts WHERE id = $id";
        $stmt = static::getDb()->query($sql);
        return $stmt;
    }

    public function phoneExists()
    {
        $sql = "SELECT * FROM contacts WHERE phone = :phone";
        $db = static::getDb();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":phone", $this->phone, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }


    public function numberOfContacts()
    {
        $sql = "SELECT id FROM contacts";
        $stmt = static::getDb()->query($sql)->fetchAll();
        $stmt = count($stmt);
        return $stmt;
    }

    public function searchData($start_point, $per_page, $keyword) {
        $sql = "SELECT * FROM contacts WHERE name LIKE '$keyword%'
        ORDER BY lastname
        LIMIT $start_point, $per_page";
        $stmt = static::getDb()->query($sql);
        return $stmt;
    }

    public function searchResults($keyword) {
        $sql = "SELECT * FROM contacts WHERE name LIKE '$keyword%'";
        $stmt = static::getDb()->query($sql)->fetchAll();
        $stmt = count($stmt);
        return $stmt;
    }


    public function getErrors()
    {
        return $this->errors;
    }
}