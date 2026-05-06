<?php

//Sayfaya erişim yetkisi kontrolü
$Auths->checkAuthorize('upload_payment_permission');

?>


<div class="container-xl mt-3">
    <div class="page-header">
        <h2>Personel Yükleme</h2>
    </div>

    <form action="" method="post" id="personsLoadForm">


        <div class="row mt-3">
            <div class="col-md-9">
                <label for="file" class="form-label">Dosya:</label>
                <input type="file" name="persons-load-file" id="persons-load-file" class="form-control">
            </div>

            <div class="col-md-3 me-auto mt-auto d-flex">


                <label for="" class="form-label"></label>
                <a href="#" class="btn btn-primary me-2" id="personsLoadButton" data-tooltip="Personlleri yükleyin">
                    <i class="ti ti-file-excel icon"></i> Yükle
                </a>
                <label for="İndir"></label>
                <a href="pages/persons/xls/person-load-from.xls" class="btn me-2" data-tooltip="Yüklenecek Şablonu indirin">
                    <i class="ti ti-file-excel icon"></i> Örnek Dosya İndir
                </a>
                <a href="#" class="btn btn-ghost-danger me-2 clear" data-tooltip="Formu Temizleyin">
                    <i class="ti ti-trash icon"></i> Temizle
                </a>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header">
                <h5>Personel Bilgileri</h5>
            </div>
            <div class="card-body">

                <div class="row">
                    <div id="result">
                        <table class="table" id="persons-load-table">
                            <thead>
                                <tr>
                                    
                                    <th>Ad Soyad</th>
                                    <th>Tc Kimlik</th>
                                    <th>İşe Başlama Tarihi</th>
                                    <th>İban Numarası</th>
                                    <th>Günlük/Aylık Ücret</th>
                                    <th>Telefon</th>
                                    <th>Email Adresi</th>
                                    <th>Beyaz/Mavi Yaka</th>
                                    <th>Adresi</th>
                                    <th>Açıklama</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr></tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>