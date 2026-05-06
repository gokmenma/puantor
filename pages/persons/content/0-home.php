<?php
require_once "App/Helper/jobs.php";

$jobGroups = new Jobs();
use App\Helper\Helper;
use App\Helper\Date;
use App\Helper\Security;

if (isset($person->wage_type) && $person->wage_type == 1) {
    $wage_type_label = 'Aylık Maaş';
    $white_checked = 'checked';
} else {
    $wage_type_label = 'Günlük Ücret';
    $blue_checked = 'checked';
}
?>
<div class="row mb-3">

    <div class="col-auto d-flex ms-auto">
        <!-- Page title actions -->
        <div class="col-auto d-print-none me-2">
            <a href="#" class="btn btn-teal route-link" data-page="persons/manage">
                <i class="ti ti-plus icon me-2"></i> Yeni
            </a>
        </div>
        <div class="col-auto d-print-none">
            <button type="button" class="btn btn-primary" id="savePerson">
                <i class="ti ti-device-floppy icon me-2"></i>
                Kaydet
            </button>
        </div>
    </div>
</div>



<form action="" id="personForm">

    <div class="row d-none">
        <div class="col-4">
            <input type="text" class="form-control" name="id" id="person_id" value="<?php echo $new_id; ?>"
                required>
        </div>
        <div class="col-4">
            <input type="text" class="form-control" name="action" value="savePerson" required>
        </div>
    </div>

        <div class="col-md-12">


            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="">Adı Soyadı (*)</label>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="full_name"
                        value="<?php echo $person->full_name ?? ''; ?>" required>
                </div>
                <div class="col-md-2 mt-2">
                    <label for="">Tc Kimlik No (*) </label>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="kimlik_no"
                        value="<?php echo Security::safeDecrypt($person->kimlik_no ?? ''); ?>" required>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="">İşe Başlama/Ayrılma Tarihi (*)</label>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control flatpickr" name="job_start_date"
                        value="<?php echo $person->job_start_date ?? ''; ?>" required>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control flatpickr" name="job_end_date"
                        placeholder="İşten Ayrılma Tarihi" value="<?php echo $person->job_end_date ?? ''; ?>">
                </div>
                <div class="col-md-2 mt-2">
                    <label id="wage_type_label" for=""><?php echo $wage_type_label; ?></label>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control fw-bold money" name="daily_wages"
                        value="<?php echo Helper::moneyToNumber($person->daily_wages ?? 0) ?? ''; ?>">
                </div>
                <div class="col-md-2 mt-2">

                    <div class="d-flex">

                        <label class="form-check form-check-inline">
                            <input class="form-check-input wage_type" type="radio" value="2" name="wage_type"
                                id="blue_collar" <?php echo $blue_checked ?? ''; ?>>
                            <span class="form-check-label">Mavi Yaka</span>
                        </label>
                        <label class="form-check form-check-inline">
                            <input class="form-check-input wage_type" type="radio" value="1" id="white_collar"
                                name="wage_type" <?php echo $white_checked ?? ''; ?>>
                            <span class="form-check-label">Beyaz Yaka</span>
                        </label>
                    </div>
                </div>

            </div>
            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="">Telefon/Email Adresi</label>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" placeholder="505 555 55 55" maxlength="15" name="phone"
                        value="<?php echo $person->phone ?? ''; ?>">
                </div>

                <div class="col-md-2">
                    <input type="text" class="form-control" name="email" value="<?php echo $person->email ?? ''; ?>">
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="">PWA Giriş Şifresi</label>
                </div>
                <div class="col-md-4">
                    <input type="password" class="form-control" name="password" placeholder="Yeni şifre girin (Boş bırakılırsa değişmez)">
                </div>
                <div class="col-md-2 mt-2">
                    <label for="">İban Numarası</label>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="iban_number" maxlength="32"
                        value="<?php echo Security::safeDecrypt($person->iban_number ?? ''); ?>">
                </div>
            </div>
            <div class="row mt-2">

            <div class="col-md-2">
                    <label for="">Çalıştığı Proje</label>
                </div>
                <div class="col-md-4">
                    <?php echo $projectHelper->getProjectSelectMultiple("person_project",$personProjectsIds); ?>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="">Grubu</label>
                </div>
                <div class="col-md-4">
                    <div class="row g-2">
                        <div class="col">
                            <?php echo $jobGroups->jobGroupsSelect("job_groups", $person->job_group ?? ''); ?>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary btn-icon" type="button" data-bs-toggle="modal"
                                data-bs-target="#modal-job-group" title="Yeni İş Grubu Ekle">
                                <i class="ti ti-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mt-2">
                    <label for="">Görevi</label>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="job" value="<?php echo $person->job ?? ''; ?>">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="">Adresi</label>
                </div>
                <div class="col-md-4">
                    <textarea class="form-control" style="min-height:100px" name="address"
                        id="address"><?php echo $person->address ?? ''; ?></textarea>
                </div>
                <div class="col-md-2 mt-2">
                    <label for="">Açıklama</label>
                </div>
                <div class="col-md-4">
                    <textarea class="form-control" style="min-height:100px"
                        placeholder="Personel hakkında not ekleyebilirsiniz" name="aciklama"
                        id="aciklama"><?php echo $person->aciklama ?? ''; ?></textarea>
                </div>

            </div>
        </div>
</form>

<!-- İş Grubu Ekleme Modalı -->
<div class="modal modal-blur fade" id="modal-job-group" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni İş Grubu Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Grup Adı</label>
                    <input type="text" class="form-control" id="new_job_group_name" placeholder="Grup adını giriniz">
                </div>
                <div class="mb-3">
                    <label class="form-label">Açıklama</label>
                    <textarea class="form-control" id="new_job_group_description" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="btnSaveJobGroup">Kaydet</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        console.log("Job group modal script loaded");
        
        $(document).on("click", "#btnSaveJobGroup", function () {
            console.log("Save job group clicked");
            const groupName = $("#new_job_group_name").val();
            const description = $("#new_job_group_description").val();

            if (!groupName) {
                Swal.fire("Hata!", "Grup adı boş olamaz.", "error");
                return;
            }

            const btn = $(this);
            const originalText = btn.text();
            btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-2"></span>Kaydediliyor...');

            const formData = new FormData();
            formData.append("action", "saveJobGroups");
            formData.append("id", 0);
            formData.append("job_group_name", groupName);
            formData.append("description", description);

            console.log("Sending request to api/defines/job-groups.php");
            fetch("api/defines/job-groups.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => {
                    console.log("Response received", response);
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error("Sunucu hatası: " + response.status + " " + text);
                        });
                    }
                    return response.json();
                })
                .then((data) => {
                    console.log("Data parsed", data);
                    btn.prop("disabled", false).text(originalText);
                    if (data.status === "success") {
                        Swal.fire("Başarılı!", data.message, "success");
                        $("#modal-job-group").modal("hide");

                        // Update the select box
                        const newOption = new Option(groupName, data.id, true, true);
                        $("#job_groups").append(newOption).trigger("change");

                        // Clear inputs
                        $("#new_job_group_name").val("");
                        $("#new_job_group_description").val("");
                    } else {
                        Swal.fire("Hata!", data.message, "error");
                    }
                })
                .catch((error) => {
                    btn.prop("disabled", false).text(originalText);
                    console.error("Fetch Error:", error);
                    Swal.fire("Hata!", error.message || "Bir hata oluştu veya sunucuya erişilemedi.", "error");
                });
        });
    });
</script>