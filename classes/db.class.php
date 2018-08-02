<?php
class DB{
    private $registry;
    private $initQuery;
    public $db;

    function __construct($registry){
        $this->registry = $registry;
        $this->initQuery = 'CREATE TABLE IF NOT EXISTS users (
                              uuid VARCHAR(255) NOT NULL,
                              first_name VARCHAR(255) NOT NULL,
                              last_name VARCHAR(255) NOT NULL,
                              phone VARCHAR(255) DEFAULT NULL,
                              email VARCHAR(255) DEFAULT NULL,
                              address TEXT NOT NULL,
                              registered_at DATETIME NOT NULL,
                              PRIMARY KEY (uuid)
                              )';
        switch(DB_TYPE){
            case 'sqlite':
                $this->sqlite();
                break;
            case 'mysqli':
                $this->mysqli();
                break;
        }
    return $this->db;
    }
    public function mysqli(){
        $this->db = new mysqli();
        $this->db->__construct(DB_HOST, DB_USER, DB_PASS, DB_BASE);

        if (mysqli_connect_error()) {
            die('Connect Error (' . mysqli_connect_errno() . ') '
                . mysqli_connect_error());
        }
    }
    public function sqlite()
    {
        $this->db = new PDO('sqlite:'.DB_BASE);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->exec($this->initQuery);
    }
}