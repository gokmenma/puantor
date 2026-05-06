<?php
// Puantor Mobil - Hızlı Puantaj Girişi (Masaüstü Pratikliğinde)
require_once ROOT . "/Model/Persons.php";
require_once ROOT . "/Model/Puantaj.php";
require_once ROOT . "/App/Helper/date.php";

use App\Helper\Date;

$personsModel = new Persons();
$puantajModel = new Puantaj();

$firm_id = $_SESSION['firm_id'] ?? 0;
$selected_date = $_GET['date'] ?? date('Y-m-d');
$persons = $personsModel->getPersonsByFirm($firm_id);

// Puantaj Türleri (Desktop ile uyumlu)
$types = [
    ['id' => 1, 'code' => 'G', 'label' => 'Geldi', 'color' => 'success'],
    ['id' => 2, 'code' => 'X', 'label' => 'Gelmedi', 'color' => 'danger'],
    ['id' => 3, 'code' => 'İ', 'label' => 'İzinli', 'color' => 'warning'],
    ['id' => 4, 'code' => 'R', 'label' => 'Raporlu', 'color' => 'info'],
];
?>

<div class="container px-0">
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h2 class="mb-0 text-semibold" style="letter-spacing: -0.5px;">Hızlı Puantaj</h2>
            <div class="d-flex gap-2">
                <a href="puantaj-detail" class="btn btn-icon btn-sm btn-outline-secondary border-0" title="Aylık Özet">
                    <i class="ti ti-list-details"></i>
                </a>
                <input type="date" class="form-control form-control-sm w-auto border-0 bg-secondary-lt" 
                       value="<?php echo $selected_date; ?>" 
                       onchange="location.href='puantaj?date='+this.value">
            </div>
        </div>
        <div class="d-flex gap-2 overflow-auto pb-2 no-scrollbar">
            <button class="btn btn-sm btn-pill <?php echo $selected_date == date('Y-m-d') ? 'btn-primary' : 'btn-outline-primary'; ?>" 
                    onclick="location.href='puantaj?date=<?php echo date('Y-m-d'); ?>'">Bugün</button>
            <button class="btn btn-sm btn-pill <?php echo $selected_date == date('Y-m-d', strtotime('-1 day')) ? 'btn-primary' : 'btn-outline-primary'; ?>"
                    onclick="location.href='puantaj?date=<?php echo date('Y-m-d', strtotime('-1 day')); ?>'">Dün</button>
            <button class="btn btn-sm btn-pill btn-outline-secondary" onclick="setAll('G')">Tümünü Geldi Yap</button>
        </div>
    </div>

    <div class="list-group list-group-mobile mb-5">
        <?php foreach ($persons as $person): 
            $current_status_id = 0; // Varsayılan
        ?>
            <div class="list-group-item py-3 person-row" data-person-id="<?php echo $person->id; ?>">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-sm rounded-circle bg-secondary-lt text-secondary font-weight-bold">
                            <?php echo mb_substr($person->full_name, 0, 1, 'UTF-8'); ?>
                        </div>
                        <div class="text-bold text-sm"><?php echo htmlspecialchars($person->full_name); ?></div>
                    </div>
                    <div id="status-badge-<?php echo $person->id; ?>" class="badge bg-secondary-lt text-secondary text-xs">Seçilmedi</div>
                </div>
                
                <div class="d-flex gap-2">
                    <?php foreach ($types as $type): ?>
                        <button type="button" 
                                class="btn btn-outline-<?php echo $type['color']; ?> flex-fill py-2 btn-puantaj" 
                                data-type-id="<?php echo $type['id']; ?>"
                                data-type-code="<?php echo $type['code']; ?>"
                                onclick="savePuantaj(<?php echo $person->id; ?>, <?php echo $type['id']; ?>, '<?php echo $type['code']; ?>', '<?php echo $type['color']; ?>')">
                            <?php echo $type['code']; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .btn-puantaj.active { 
        background-color: var(--tblr-primary); 
        color: white;
        border-color: var(--tblr-primary);
    }
    .person-row.saved { background-color: rgba(47, 179, 68, 0.03); transition: background 0.3s; }
</style>

<script>
function savePuantaj(personId, typeId, typeCode, color) {
    const date = '<?php echo $selected_date; ?>';
    const row = document.querySelector(`.person-row[data-person-id="${personId}"]`);
    const badge = document.getElementById(`status-badge-${personId}`);
    
    badge.className = `badge bg-${color}-lt text-${color} text-xs`;
    badge.innerText = typeCode === 'G' ? 'Geldi' : (typeCode === 'X' ? 'Gelmedi' : 'İzinli');
    
    row.classList.add('saved');
    setTimeout(() => row.classList.remove('saved'), 1000);

    console.log(`Kaydediliyor: Personel ${personId}, Tarih ${date}, Tür ${typeId}`);
    
    // AJAX Kayıt
    $.post('modules/puantaj/api/puantaj-save.php', {
        person_id: personId,
        date: date,
        type_id: typeId
    }, function(response) {
        console.log("Kayıt sonucu:", response);
    });
}

function setAll(typeCode) {
    const buttons = document.querySelectorAll(`.btn-puantaj[data-type-code="${typeCode}"]`);
    buttons.forEach(btn => btn.click());
}
</script>
