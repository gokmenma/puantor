<?php
define('ROOT', $_SERVER["DOCUMENT_ROOT"]);

require_once ROOT . '/Model/Persons.php';
require_once ROOT . '/Database/require.php';
require_once ROOT . '/App/Helper/date.php';
require_once ROOT . '/App/Helper/security.php';
require ROOT . '/vendor/autoload.php';


$firm_id = $_SESSION['firm_id'];

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Helper\Date;
use App\Helper\Security;
$Persons = new Persons();

if ($_POST["action"] == "persons-load-from-xls") {

    $file = $_FILES["persons-load-file"];
    $file_name = $file["name"];
    $file_tmp = $file["tmp_name"];
    $file_size = $file["size"];
    $file_error = $file["error"];
    $file_ext = explode(".", $file_name);
    $file_ext = strtolower(end($file_ext));
    $allowed = ["xls", "xlsx"];

    $lastInsertedId = 0;
    if (in_array($file_ext, $allowed)) {
        try {
            //excel dosyasını okuma
            $spreadsheet = IOFactory::load($file_tmp);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $data = [];
            //hata mesajlarını bir değişkene ata ve sonuç olarak döndür
            $all_errors = [];
            foreach ($sheetData as $key => $row) {
                if ($key == 1) {
                    continue;
                }
                
                $row_errors = [];

                //Kuralları geçen kayıtların eklenmesi sağlanır
                //full_name en az 3 karakter olmalı
                $fullName = trim($row["A"]);
                if (strlen($fullName) < 3 || strlen($fullName) > 50) {
                    $row_errors[] = "Ad Soyad 3-50 karakter arasında olmalıdır.";
                }

                //kimlik_no 11 karakter olmalı
                $tcNo = trim((string)$row["B"]);
                if (strlen($tcNo) != 11 || !is_numeric($tcNo)) {
                    $row_errors[] = "Kimlik No 11 haneli ve sayısal olmalıdır.";
                }

                //job_start_date tarih formatında olmalı
                if (!Date::isDate($row["C"])) {
                    $row_errors[] = "İşe Başlama Tarihi tarih formatında olmalıdır.";
                }

                //iban_number 26 karakter olmalı
                $iban = trim((string)$row["D"]);
                if (strlen($iban) != 26) {
                    $row_errors[] = "Iban Numarası 26 karakter olmalıdır.";
                }

                //daily_wages sayısal olmalı
                if (!is_numeric($row["E"])) {
                    $row_errors[] = "Günlük/Aylık Ücret sayısal olmalıdır.";
                }

                // Eğer bu satırda hata varsa, hataları ana listeye ekle ve atla
                if (!empty($row_errors)) {
                    $all_errors[] = "Satır $key: " . implode(" ", $row_errors);
                    continue;
                }

                $data = [
                    "id" => 0,
                    "full_name" => Security::escape($row["A"]),
                    "kimlik_no" => Security::encrypt($tcNo),
                    "job_start_date" => Date::dmY($row["C"], "d.m.Y"),
                    "iban_number" => Security::encrypt($iban),
                    "daily_wages" => Security::escape($row["E"]),
                    "phone" => Security::escape($row["F"]),
                    "email" => Security::escape($row["G"]),
                    "wage_type" => Security::escape($row["H"]),
                    "address" => Security::escape($row["I"]),
                    "description" => Security::escape($row["J"]),
                    "firm_id" => $firm_id,
                ];
                $lastInsertedId = $Persons->saveWithAttr($data) ?? 0;
            }

            //en az bir satır eklendiyse başarılı mesajı ver
            if ($lastInsertedId > 0) {
                $status = "success";
                $message = "Personeller başarıyla yüklendi";
            } else {
                $status = "error";
                $message = "Personeller yüklenemedi. Hiçbir geçerli kayıt bulunamadı.";
            }
        } catch (Exception $ex) {
            $status = "error";
            $message = $ex->getMessage();
        }

    } else {
        $status = "error";
        $message = "Dosya uzantısı uygun değil";
    }

    if (!empty($all_errors)) {
        if ($status == "success") {
            $message .= "<br><br><b>Bazı satırlar hatalı olduğu için atlandı:</b>";
        } else {
            $message = "<b>Hatalı kayıtlar var:</b>";
        }
        foreach ($all_errors as $error) {
            $message .= "<br>" . $error;
        }
    }

    $res = [
        "status" => $status,
        "message" => $message,
        "data" => $data,
        "error_message" => $all_errors
    ];

    echo json_encode($res);
}