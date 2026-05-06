<?php
require_once "App/Helper/helper.php";
require_once "App/Helper/date.php";
require_once "Model/Bordro.php";
require_once "Model/Puantaj.php";
require_once "Model/Projects.php";
require_once "App/Helper/security.php";


use App\Helper\Security;
use App\Helper\Helper;
use App\Helper\Date;

$bordro = new Bordro();
$puantajObj = new Puantaj();
$projects = new Projects();

$person_id = $person->id;
$puantaj_info = $puantajObj->getPuantajInfoByPerson($person_id);

if (!$Auths->Authorize("person_page_puantaj_info")) {
    Helper::authorizePage();
    return;
}

// Takvim etkinliklerini hazırla
$events = [];
foreach ($puantaj_info as $item) {
    $puantaj_turu = $puantajObj->getPuantajTuruById($item->puantaj_id);
    if (!$puantaj_turu) continue;

    // Ymd (20260504) formatını Y-m-d (2026-05-04) formatına çevir
    $gun = $item->gun;
    if (strlen($gun) == 8 && is_numeric($gun)) {
        $gun = substr($gun, 0, 4) . '-' . substr($gun, 4, 2) . '-' . substr($gun, 6, 2);
    } else {
        $gun = Date::dmY($gun, 'Y-m-d');
    }

    $events[] = [
        'id' => $item->id,
        'title' => ($puantaj_turu->PuantajKod ?? '') . " (" . ($item->saat ?? 0) . " sa)",
        'start' => $gun,
        'allDay' => true,
        'extendedProps' => [
            'project' => $projects->find($item->project_id)->project_name ?? '',
            'type' => $puantaj_turu->PuantajAdi ?? '',
            'amount' => Helper::formattedMoney($item->tutar ?? 0)
        ],
        'backgroundColor' => $puantaj_turu->ArkaPlanRengi ?? '#206bc4',
        'textColor' => $puantaj_turu->FontRengi ?? '#ffffff',
        'borderColor' => $puantaj_turu->ArkaPlanRengi ?? '#206bc4'
    ];
}

?>
<style>
    table.datatable th,
    table.datatable td {
        text-align: left !important;
    }
    #ec {
        min-height: 600px;
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }
    .ec-event-title {
        font-weight: 600;
    }
    .ec-event {
        cursor: pointer;
        border-radius: 4px !important;
    }
    
    /* Yıllık Görünüm - Özel Minimalist Takvim */
    .year-grid-container {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        padding: 10px;
        align-items: start;
    }
    @media (max-width: 1600px) {
        .year-grid-container {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    @media (max-width: 1200px) {
        .year-grid-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 992px) {
        .year-grid-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 576px) {
        .year-grid-container {
            grid-template-columns: repeat(1, 1fr);
        }
    }
    .mini-month {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px;
        font-family: inherit;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .mini-month:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .mini-month-header {
        text-align: center;
        font-size: 0.8rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
        text-transform: capitalize;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 5px;
    }
    .mini-month-days {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        text-align: center;
        gap: 0;
    }
    .mini-day-head {
        font-size: 0.6rem;
        font-weight: 700;
        color: #94a3b8;
        padding: 4px 0;
    }
    .mini-day {
        font-size: 0.7rem;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        border-radius: 4px;
        transition: all 0.15s;
        color: #334155;
    }
    .mini-day:hover:not(.mini-day-empty) {
        background: #f1f5f9;
        color: #0f172a;
    }
    .mini-day-empty {
        cursor: default;
        visibility: hidden;
    }
    .mini-day.has-event {
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .mini-day.sunday {
        color: #ef4444;
    }
    .mini-day.today {
        background: #eff6ff;
        font-weight: 800;
        color: #2563eb;
        box-shadow: inset 0 0 0 1px #dbeafe;
    }
</style>
<div class="container-xl mt-3">
    <div class="row row-deck row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title">Çalışma Bilgileri</h3>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm" id="calendar_year_select" style="width: 100px;">
                            <?php
                            $currentYear = date('Y');
                            for ($i = $currentYear - 5; $i <= $currentYear + 5; $i++) {
                                $selected = ($i == $currentYear) ? 'selected' : '';
                                echo "<option value='$i' $selected>$i</option>";
                            }
                            ?>
                        </select>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary active" id="view_calendar_btn" title="Takvim Görünümü">
                                <i class="ti ti-calendar icon"></i> Takvim
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="view_table_btn" title="Liste Görünümü">
                                <i class="ti ti-table icon"></i> Liste
                            </button>
                        </div>
                        <a href="#" class="btn btn-icon btn-sm excel" id="export_excel_puantaj_info" data-tooltip="Excele Aktar">
                            <i class="ti ti-file-excel icon"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Takvim Görünümü -->
                    <div id="puantaj_calendar_view">
                        <div id="ec"></div>
                    </div>

                    <!-- Yıllık Görünüm -->
                    <div id="puantaj_year_view" style="display: none;">
                        <div id="puantaj_year_view_grid" class="year-grid-container">
                            <!-- JS ile 12 ay takvimi buraya eklenecek -->
                        </div>
                    </div>

                    <!-- Liste Görünümü (Başlangıçta Gizli) -->
                    <div id="puantaj_table_view" style="display: none;">
                        <div class="table-responsive p-3">
                            <table class="table card-table table-hover text-nowrap datatable" id="puantaj_info_table">
                                <thead>
                                    <tr>
                                        <th style="width:7%">id</th>
                                        <th>Proje</th>
                                        <th>Puantaj Türü</th>
                                        <th>Tarih</th>
                                        <th>Saat</th>
                                        <th class="text-start">Tutar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($puantaj_info as $item): ?>
                                        <tr>
                                            <td><?php echo $item->id ?></td>
                                            <td><?php echo $projects->find($item->project_id)->project_name ?? '' ?></td>
                                            <td>
                                                <?php
                                                $puantaj_turu = $puantajObj->getPuantajTuruById($item->puantaj_id);
                                                echo $puantaj_turu->PuantajKod . " - " . $puantaj_turu->PuantajAdi;
                                                ?>
                                            </td>
                                            <td><?php echo Date::ymd($item->gun, "d.m.Y") ?></td>
                                            <td class="text-start"><?php echo $item->saat ?></td>
                                            <td class="text-start"><?php echo Helper::formattedMoney($item->tutar) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Takvim verilerini PHP'den al
    const events = <?php echo json_encode($events); ?>;
    const rawPuantaj = <?php echo json_encode($puantaj_info); ?>;
    
    // Takvimi ilklendir
    const ec = new EventCalendar(document.getElementById('ec'), {
        view: 'dayGridMonth',
        locale: 'tr',
        firstDay: 1,
        headerToolbar: {
            start: 'prev,next today',
            center: 'title',
            end: 'yearView,dayGridMonth,dayGridWeek,listMonth'
        },
        customButtons: {
            yearView: {
                text: 'Yıl',
                click: function() {
                    $('#puantaj_calendar_view').hide();
                    $('#puantaj_year_view').show();
                    const year = $('#calendar_year_select').val();
                    renderYearlyGrid(year);
                    updateYearlySummary(year);
                    // Update active state manually if needed
                    $('.ec-button-yearView').addClass('ec-active');
                    $('.ec-button-dayGridMonth, .ec-button-dayGridWeek, .ec-button-listMonth').removeClass('ec-active');
                }
            }
        },
        buttonText: {
            today: 'Bugün',
            dayGridMonth: 'Ay',
            dayGridWeek: 'Hafta',
            listMonth: 'Ajanda'
        },
        viewDidMount: function(info) {
            if (info.view && info.view.type !== 'yearView') {
                $('#puantaj_year_view').hide();
                $('#puantaj_calendar_view').show();
                $('.ec-button-yearView').removeClass('ec-active');
            }
        },
        events: events,
        eventClick: function(info) {
            const props = info.event.extendedProps;
            Swal.fire({
                title: info.event.title,
                html: `
                    <div class="text-start">
                        <p><strong>Proje:</strong> ${props.project}</p>
                        <p><strong>Tür:</strong> ${props.type}</p>
                        <p><strong>Tarih:</strong> ${info.event.start.toLocaleDateString('tr-TR')}</p>
                        <p><strong>Tutar:</strong> ${props.amount}</p>
                    </div>
                `,
                icon: 'info'
            });
        }
    });

    // Görünüm Değiştirme
    $('#view_calendar_btn').on('click', function() {
        $(this).addClass('active');
        $('#view_table_btn').removeClass('active');
        $('#puantaj_calendar_view').show();
        $('#puantaj_table_view, #puantaj_year_view').hide();
    });

    $('#view_table_btn').on('click', function() {
        $(this).addClass('active');
        $('#view_calendar_btn').removeClass('active');
        $('#puantaj_calendar_view, #puantaj_year_view').hide();
        $('#puantaj_table_view').show();
        
        // DataTable adjustment
        if ($.fn.DataTable.isDataTable('#puantaj_info_table')) {
            const table = $('#puantaj_info_table').DataTable();
            table.columns.adjust().draw();
        } else {
             $("#puantaj_info_table").DataTable({
                autoWidth: false,
                order: [[3, "desc"]],
                language: {
                    url: "src/tr.json"
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'd-none',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            });
        }
    });


    // Yıllık Izgara Oluşturma (Özel Minimalist Versiyon)
    function renderYearlyGrid(year) {
        const container = $('#puantaj_year_view_grid');
        container.empty();
        
        const monthNames = ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"];
        const dayNames = ["P", "S", "Ç", "P", "C", "C", "P"]; // Pzt başlangıçlı ama Pazartesi "P"

        for (let m = 0; m < 12; m++) {
            const monthDiv = $('<div class="mini-month"></div>');
            const header = $(`<div class="mini-month-header">${monthNames[m]} ${year}</div>`);
            monthDiv.append(header);
            
            const daysGrid = $('<div class="mini-month-days"></div>');
            
            // Başlıklar
            dayNames.forEach(d => daysGrid.append(`<div class="mini-day-head">${d}</div>`));
            
            // Ayın ilk günü ve gün sayısı
            const firstDay = new Date(year, m, 1).getDay(); // 0=Sun, 1=Mon...
            const daysInMonth = new Date(year, m + 1, 0).getDate();
            
            // Boşluklar (Pzt başlangıçlı yapmak için ayar)
            let startOffset = firstDay === 0 ? 6 : firstDay - 1;
            for (let i = 0; i < startOffset; i++) {
                daysGrid.append('<div class="mini-day mini-day-empty"></div>');
            }
            
            // Bugünü yerel saat dilimine göre bulalım
            const todayDate = new Date();
            const todayStr = `${todayDate.getFullYear()}-${String(todayDate.getMonth() + 1).padStart(2, '0')}-${String(todayDate.getDate()).padStart(2, '0')}`;

            // Günler
            for (let d = 1; d <= daysInMonth; d++) {
                const dateStr = `${year}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const isSunday = (startOffset + d - 1) % 7 === 6;
                const isToday = dateStr === todayStr;
                
                // Tarihte eşleşen tüm etkinlikleri bul (saat formatı içerebilir diye split)
                const dayEvents = events.filter(e => e.start.split(' ')[0] === dateStr);
                const hasEvent = dayEvents.length > 0;
                
                const dayDiv = $(`<div class="mini-day ${isSunday ? 'sunday' : ''} ${isToday ? 'today' : ''} ${hasEvent ? 'has-event' : ''}">${d}</div>`);
                
                if (hasEvent) {
                    const primaryEvent = dayEvents[0];
                    const tooltipText = dayEvents.map(ev => ev.title).join(', ');
                    dayDiv.attr('title', tooltipText);
                    
                    dayDiv.css({
                        'background-color': primaryEvent.backgroundColor || '#206bc4',
                        'color': primaryEvent.textColor || '#ffffff',
                        'font-weight': '600'
                    });

                    dayDiv.on('click', function() {
                        let htmlContent = '';
                        dayEvents.forEach(ev => {
                            htmlContent += `
                                <div class="text-start mb-3 border-bottom pb-2 last:border-0">
                                    <h4 class="mb-1" style="color: ${ev.backgroundColor || '#206bc4'}">${ev.title}</h4>
                                    <p class="mb-1"><strong>Proje:</strong> ${ev.extendedProps.project}</p>
                                    <p class="mb-1"><strong>Tür:</strong> ${ev.extendedProps.type}</p>
                                    <p class="mb-0"><strong>Tutar:</strong> ${ev.extendedProps.amount}</p>
                                </div>
                            `;
                        });

                        Swal.fire({
                            title: `${d} ${monthNames[m]} ${year} Puantaj Kayıtları`,
                            html: htmlContent,
                            icon: 'info'
                        });
                    });
                }
                
                daysGrid.append(dayDiv);
            }
            
            monthDiv.append(daysGrid);
            container.append(monthDiv);
        }
    }

    // Yıllık görünüm seçildiğinde tetikle
    $('#calendar_year_select').on('change', function() {
        const year = $(this).val();
        
        // Ana takvimi güncelle
        const currentDate = ec.getDate();
        ec.gotoDate(new Date(year, currentDate.getMonth(), 1));
        
        // Yıllık görünüm açıksa onu da güncelle
        if ($('#puantaj_year_view').is(':visible')) {
            renderYearlyGrid(year);
        }
    });

    // Liste / Takvim geçişleri
    $('#view_calendar_btn').on('click', function() {
        $('#puantaj_calendar_view').show();
        $('#puantaj_year_view').hide();
        $('#puantaj_table_view').hide();
        $('.btn-outline-primary').removeClass('active');
        $(this).addClass('active');
    });

    $('#view_table_btn').on('click', function() {
        $('#puantaj_calendar_view').hide();
        $('#puantaj_year_view').hide();
        $('#puantaj_table_view').show();
        $('.btn-outline-primary').removeClass('active');
        $(this).addClass('active');
    });

    // İlk yüklemede varsayılan olarak Yılı göster
    $('#puantaj_calendar_view').hide();
    $('#puantaj_year_view').show();
    const currentYear = $('#calendar_year_select').val();
    renderYearlyGrid(currentYear);
    
    // Yıllık butonu takvim içinde olduğu için, takvim yüklendikten sonra aktifliği set edelim
    setTimeout(() => {
        $('.ec-button-yearView').addClass('ec-active');
        $('.ec-button-dayGridMonth, .ec-button-dayGridWeek, .ec-button-listMonth').removeClass('ec-active');
    }, 100);

    // Sayfa yüklendiğinde takvim görünümünü yenile

});
</script>