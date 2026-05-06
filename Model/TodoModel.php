<?php
require_once "BaseModel.php";
class Todo extends Model
{
    protected $table = 'todos';
    protected $firm_id;

    public function __construct()
    {
        parent::__construct($this->table);
        $this->firm_id = $_SESSION['firm_id'];
    }

    //Firma id sine göre tüm todoları getirir
    public function getTodosByFirm()
    {
        $sql = "SELECT t.*, p.project_name 
                FROM $this->table t 
                LEFT JOIN projects p ON t.project_id = p.id 
                WHERE t.firm_id = :firm_id 
                ORDER BY t.status ASC, t.created_at DESC";
        $query = $this->db->prepare($sql);
        $query->execute(['firm_id' => $this->firm_id]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    

}
