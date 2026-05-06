<?php

namespace Database;

use PDO;

class Db
{
    protected $db;



    protected $dbname = "mbeyazil_puantoryeni";
    protected $host = "localhost";

    // protected $username = "mbeyazil_puantor";
    // protected $password = "AGc7A}J&9{xGnun^";

    protected $username = "root";
    protected $password = "";


    public function __construct()
    {
        //$this->db = new PDO("mysql:host=localhost;dbname=mbeyazil_puantoryeni", "mbeyazil_root", "UB+KFJdBE%+*zV?F");
        $this->db = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8", $this->username, $this->password);
        //$this->db = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8", $this->username);
        //İstanbul saati
        $this->db->exec("SET time_zone = '+03:00'");
        //Hata modunu aktif et
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //utf 8 türkçe karakter sorunu
        $this->db->exec("SET NAMES 'utf8';");




    }

    // $db özelliğine dışarıdan erişim sağlayan metod
    public function connect()
    {
        return $this->db;
    }

    public function disconnect()
    {
        $this->db = null;
    }


    // Transaction başlatma
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    // Transaction commit etme
    public function commit()
    {
        return $this->db->commit();
    }

    // Transaction rollback etme
    public function rollBack()
    {
        return $this->db->rollBack();
    }
}