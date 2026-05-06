<?php

require_once 'BaseModel.php';
use App\Helper\Security;

class Persons extends Model
{
    protected $table = 'persons';

    public function __construct()
    {
        parent::__construct($this->table);
    }

    public function getPersonsByFirm($firm_id)
    {
        $query = $this->db->prepare('SELECT * FROM persons WHERE firm_id = ? and deleted_at IS NULL');
        $query->execute([$firm_id]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
//Personelin ad soyadını getir
    public function getPersonName($person_id)
    {
        $query = $this->db->prepare("SELECT full_name FROM $this->table WHERE id = ?");
        $query->execute([$person_id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    //Aktif personelleri getir
    public function getPersonsByActive()
    {
        $query = $this->db->prepare('SELECT * FROM persons WHERE firm_id = ? and job_end_date IS NOT NULL');
        $query->execute([$_SESSION['firm_id']]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getPersonIdByFirm($firm_id)
    {
        $query = $this->db->prepare('SELECT id FROM persons WHERE firm_id = ?');
        $query->execute([$firm_id]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    
    public function getPersonIdByFirmCurrentMonth($firm_id,$last_day)
    {
        $query = $this->db->prepare('SELECT id FROM persons WHERE firm_id = ? and STR_TO_DATE(job_start_date, "%d.%m.%Y") <= ?');
        $query->execute([$firm_id,$last_day]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    public function getPersonIdByFirmBlueCollar($firm_id)
    {
        $query = $this->db->prepare('SELECT id FROM persons WHERE firm_id = ? AND wage_type = ?');
        $query->execute([$firm_id,2]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    public function getPersonIdByFirmBlueCollarCurrentMonth($firm_id,$last_day)
    {
        $query = $this->db->prepare('SELECT id FROM persons WHERE firm_id = ? AND wage_type = ? AND STR_TO_DATE(job_start_date, "%d.%m.%Y") <= ? and deleted_at IS NULL');
        $query->execute([$firm_id,2,$last_day]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }



    public function getPersonByField($person_id,$field)
    {
        $query = $this->db->prepare("SELECT * FROM persons WHERE id = ?");
        $query->execute([$person_id]);
        $person = $query->fetch(PDO::FETCH_OBJ);
        if (!$person) {
            return "Personel Silinmiş";
        }
        return $person->$field;
    }
    public function getDailyWages($person_id)
    {
        $query = $this->db->prepare('SELECT daily_wages FROM persons WHERE id = ?');
        $query->execute([$person_id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function getPersonSalary($person_id, $start_date, $end_date)
    {
        $query = $this->db->prepare('SELECT SUM(TUTAR) as tutar FROM puantaj WHERE person=? AND GUN >= ? AND GUN <= ?');
        $query->execute([$person_id, $start_date, $end_date]);
        return $query->fetch(PDO::FETCH_OBJ)->tutar;
    }

    public function getPersonByKimlikNo($kimlik_no)
    {
        // Since kimlik_no is stored encrypted with a random IV, we cannot search it directly in SQL
        // We fetch all records and compare the decrypted value
        // Note: For large datasets, a hash column should be added to the database for indexing
        $sql = "SELECT * FROM $this->table WHERE deleted_at IS NULL";
        $query = $this->db->prepare($sql);
        $query->execute();
        $persons = $query->fetchAll(PDO::FETCH_OBJ);

        foreach ($persons as $person) {
            $decrypted = Security::safeDecrypt($person->kimlik_no);
            if ($decrypted == $kimlik_no) {
                return $person;
            }
        }
        return null;
    }
}