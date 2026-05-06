<?php

use App\Helper\Security;

require_once "../../Database/require.php";
require_once "../../Model/Projects.php";

$Projects = new Projects();

if ($_POST['action'] == "addPersonToProject") {
    //$record_id = $Projects->findById($_POST['project_id']);

    try {
        $persons = explode(",", $_POST['person_id']);
        $project_id = $_POST['project_id'];

        // Projeye kayıtlı olan personelleri al
        $existing_persons = $Projects->getPersonFromProject($project_id);
        $existing_person_ids = array_map(function ($person) {
            return $person->person_id;
        }, $existing_persons);

        // Silinecek personelleri belirle
        $persons_to_delete = array_diff($existing_person_ids, $persons);

        // Eklenecek personelleri belirle
        $persons_to_add = array_diff($persons, $existing_person_ids);

        // Silinecek personelleri sil
        foreach ($persons_to_delete as $person_id) {
            $Projects->deletePersonFromProjects($person_id, $project_id);

        }

        // Eklenecek personelleri ekle
        foreach ($persons_to_add as $person_id) {
            if ($person_id == 0 || $person_id == "") {
                continue;
            }
            
            $data = [
                    "id" => 0,
                    'project_id' => $project_id,
                    'person_id' => $person_id,
                    "state" => 1,
                    "user_id" => $_SESSION['user']->id,
                ];
                $Projects->addPersontoProject($data);
        }

        $status = "success";
        $message = "Personeller başarı ile güncellendi";
     
    } catch (PDOException $ex) {
        $status = "error";
        $message = $ex->getMessage();
    }

    echo json_encode([
        "status" => $status,
        "message" => $message
    ]);

}