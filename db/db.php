<?php

class db{
private $host = 'localhost'; 
private $user = 'root';
private $pass ='root';
private $dbname='atc_db';

public function connect(){
    $conn_strr="mysql:host=$this->host;dbname=$this->dbname";
    $conn= new PDO($conn_strr,$this->user,$this->pass); 
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    return $conn;
}
}