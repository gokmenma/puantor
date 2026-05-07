<style>
:root {
    --todo-card-bg: #ffffff;
    --todo-card-border: var(--mobile-card-border-light);
    --todo-text-main: #1d273b;
    --todo-text-muted: #64748b;
}

body[data-bs-theme="dark"] {
    --todo-card-bg: #1e293b;
    --todo-card-border: var(--mobile-card-border-dark);
    --todo-text-main: #f4f6fa;
    --todo-text-muted: #94a3b8;
}

.section-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--todo-text-muted);
    margin-bottom: 0.75rem;
    opacity: 0.8;
}

.todo-group-card {
    background: var(--todo-card-bg);
    border-radius: 16px;
    border: 1px solid var(--todo-card-border);
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
}

body[data-bs-theme="dark"] .todo-group-card {
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.swipe-container {
    position: relative;
    overflow: hidden;
    background: transparent;
}

.swipe-actions {
    position: absolute;
    right: 0;
    top: 0;
    height: 100%;
    width: 75px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #d63f3f;
    z-index: 1;
}

.swipe-content {
    position: relative;
    z-index: 2;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    width: 100%;
    background: var(--todo-card-bg) !important;
    border-bottom: 1px solid var(--todo-card-border);
}

.swipe-container:last-child .swipe-content {
    border-bottom: none;
}

.todo-title {
    color: var(--todo-text-main);
    font-weight: 600;
    font-size: 0.9rem;
}

.todo-meta {
    font-size: 0.75rem;
    color: var(--todo-text-muted);
    display: flex;
    align-items: center;
    gap: 4px;
}

.btn-swipe-action {
    width: 100%;
    height: 100%;
    background: transparent;
    border: none;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    font-size: 0.65rem;
    font-weight: 700;
}

/* Custom Checkbox aligned with Global Style */
.custom-checkbox {
    display: block;
    position: relative;
    cursor: pointer;
    width: 22px;
    height: 22px;
}

.custom-checkbox input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 22px;
    width: 22px;
    background-color: #f1f3f7;
    border-radius: 6px;
    border: 1.5px solid rgba(0,0,0,0.08);
    transition: all 0.2s ease;
}

body[data-bs-theme="dark"] .checkmark {
    background-color: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.1);
}

.custom-checkbox input:checked ~ .checkmark {
    background-color: var(--mobile-primary);
    border-color: var(--mobile-primary);
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
    left: 7.5px;
    top: 3.5px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2.5px 2.5px 0;
    transform: rotate(45deg);
}

.custom-checkbox input:checked ~ .checkmark:after {
    display: block;
}
</style>
<?php
require_once ROOT . "/Model/TodoModel.php";
require_once ROOT . "/Model/Projects.php";
require_once ROOT . "/App/Helper/security.php";

use App\Helper\Security;

$todoModel = new Todo();
$projectModel = new Projects();
$todos = $todoModel->getTodosByFirm();
$projects = $projectModel->getProjectsByFirm($_SESSION['firm_id']);

// Group todos
$pending_todos = [];
$completed_todos = [];
foreach ($todos as $todo) {
    if (($todo->status ?? '0') == '1') {
        $completed_todos[] = $todo;
    } else {
        $pending_todos[] = $todo;
    }
}
?>

<div class="container px-3 pb-5">
    <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
        <div>
            <h2 class="mb-0 text-bold" style="letter-spacing: -0.5px; font-size: 1.6rem;">Yapılacaklar</h2>
            <p class="text-muted text-xs mb-0 mt-1">Görevlerinizi buradan takip edebilirsiniz.</p>
        </div>
        <button class="btn btn-primary shadow-sm" onclick="openTodoModal()" style="border-radius: 12px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border: none; background: var(--mobile-primary);">
            <i class="ti ti-plus" style="font-size: 1.5rem;"></i>
        </button>
    </div>

    <!-- Devam Eden Görevler -->
    <div class="mb-5 px-1">
        <div class="section-label">Devam Edenler (<?php echo count($pending_todos); ?>)</div>
        
        <div class="todo-group-card">
            <?php if (empty($pending_todos)): ?>
                <div class="text-center py-5">
                    <div class="avatar avatar-lg rounded-circle bg-success-lt mb-3 mx-auto">
                        <i class="ti ti-confetti fs-1"></i>
                    </div>
                    <p class="text-muted text-sm mb-0">Harika! Bekleyen görev yok.</p>
                </div>
            <?php else: ?>
                <?php foreach ($pending_todos as $todo): 
                    $todo_id_encrypted = Security::encrypt($todo->id);
                ?>
                    <div class="swipe-container" data-id="<?php echo $todo_id_encrypted; ?>">
                        <div class="swipe-actions">
                            <button class="btn-swipe-action" onclick="deleteTodo('<?php echo $todo_id_encrypted; ?>')">
                                <i class="ti ti-trash"></i>
                                <span>SİL</span>
                            </button>
                        </div>
                        <div class="swipe-content p-3 todo-row" 
                             data-id="<?php echo $todo_id_encrypted; ?>"
                             data-title="<?php echo htmlspecialchars($todo->title); ?>"
                             data-description="<?php echo htmlspecialchars($todo->description ?? ''); ?>"
                             data-project-id="<?php echo $todo->project_id; ?>"
                             data-due-date="<?php echo $todo->due_date; ?>"
                             data-status="0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="todo-check-wrapper">
                                        <label class="custom-checkbox m-0">
                                            <input type="checkbox" onchange="toggleTodoStatus('<?php echo $todo_id_encrypted; ?>', this)">
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="todo-content" onclick="editTodo('<?php echo $todo_id_encrypted; ?>')">
                                        <div class="todo-title"><?php echo htmlspecialchars($todo->title); ?></div>
                                        <div class="todo-meta mt-0.5">
                                            <?php if (!empty($todo->project_name)): ?>
                                                <span><?php echo htmlspecialchars($todo->project_name); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($todo->due_date) && $todo->due_date !== '0000-00-00 00:00:00'): ?>
                                                <?php if (!empty($todo->project_name)): ?><span class="text-muted-50 mx-1">•</span><?php endif; ?>
                                                <span class="<?php echo strtotime($todo->due_date) < time() ? 'text-danger' : ''; ?>">
                                                    <?php echo date('d M H:i', strtotime($todo->due_date)); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-muted" onclick="editTodo('<?php echo $todo_id_encrypted; ?>')">
                                    <i class="ti ti-chevron-right opacity-30"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tamamlanan Görevler -->
    <?php if (!empty($completed_todos)): ?>
    <div class="mb-4 px-1">
        <div class="section-label d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#completedList" role="button" aria-expanded="true" style="cursor: pointer;">
            <span>Tamamlananlar (<?php echo count($completed_todos); ?>)</span>
            <i class="ti ti-chevron-down opacity-50"></i>
        </div>
        
        <div class="collapse show" id="completedList">
            <div class="todo-group-card">
                <?php foreach ($completed_todos as $todo): 
                    $todo_id_encrypted = Security::encrypt($todo->id);
                ?>
                    <div class="swipe-container" data-id="<?php echo $todo_id_encrypted; ?>">
                        <div class="swipe-actions">
                            <button class="btn-swipe-action" onclick="deleteTodo('<?php echo $todo_id_encrypted; ?>')">
                                <i class="ti ti-trash"></i>
                                <span>SİL</span>
                            </button>
                        </div>
                        <div class="swipe-content p-3 todo-row completed" 
                             data-id="<?php echo $todo_id_encrypted; ?>"
                             data-title="<?php echo htmlspecialchars($todo->title); ?>"
                             data-description="<?php echo htmlspecialchars($todo->description ?? ''); ?>"
                             data-project-id="<?php echo $todo->project_id; ?>"
                             data-due-date="<?php echo $todo->due_date; ?>"
                             data-status="1">
                            <div class="d-flex align-items-center gap-3">
                                <div class="todo-check-wrapper">
                                    <label class="custom-checkbox m-0">
                                        <input type="checkbox" checked onchange="toggleTodoStatus('<?php echo $todo_id_encrypted; ?>', this)">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="todo-content">
                                    <div class="todo-title text-decoration-line-through opacity-50"><?php echo htmlspecialchars($todo->title); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Structure -->
<div class="modal modal-blur fade" id="todoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 overflow-hidden" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold" id="todoModalTitle">Yeni Görev Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <form id="todoForm" onsubmit="saveTodo(event)">
                <div class="modal-body py-4">
                    <input type="hidden" id="todoId" name="id">
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control border-0 bg-light-lt" id="todoTitle" name="title" required placeholder="Neler yapılacak?" style="border-radius: 12px;">
                        <label for="todoTitle">Görev Başlığı</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select border-0 bg-light-lt" id="todoProjectId" name="project_id" style="border-radius: 12px;">
                            <option value="0">Proje Yok</option>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?php echo $project->id; ?>"><?php echo htmlspecialchars($project->project_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="todoProjectId">İlgili Proje</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control border-0 bg-light-lt" id="todoDueDate" name="due_date" placeholder="Tarih ve Saat Seçin" style="border-radius: 12px;">
                        <label for="todoDueDate">Bitiş Tarihi & Saati</label>
                    </div>

                    <div class="form-floating mb-0">
                        <textarea class="form-control border-0 bg-light-lt" id="todoDescription" name="description" placeholder="Detay ekleyebilirsiniz..." style="border-radius: 12px; height: 100px;"></textarea>
                        <label for="todoDescription">Açıklama (Opsiyonel)</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 12px; font-weight: 600;">Görevi Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-floating > .form-control,
    .form-floating > .form-select,
    .form-floating > textarea {
        color: var(--todo-text-main) !important;
        font-family: 'DM Sans', sans-serif !important;
    }
    
    .form-floating > label {
        color: var(--todo-text-muted) !important;
        font-size: 0.85rem;
    }
    .form-floating > .form-control,
    .form-floating > .form-select,
    .form-floating > textarea {
        color: #1d273b !important; /* Explicit dark color for visibility */
        font-weight: 500;
    }
    
    body[data-bs-theme="dark"] .form-floating > .form-control,
    body[data-bs-theme="dark"] .form-floating > .form-select,
    body[data-bs-theme="dark"] .form-floating > textarea {
        color: #f4f6fa !important;
    }

    .form-floating > label {
        color: #64748b !important;
    }

    .form-floating > .form-control:focus {
        background-color: #fff !important;
        color: #000 !important;
    }
    
    body[data-bs-theme="dark"] .form-floating > .form-control:focus {
        background-color: #1e293b !important;
        color: #fff !important;
    }
</style>

<script>
$(document).ready(function() {
    flatpickr("#todoDueDate", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        locale: "tr",
        disableMobile: "true"
    });
});

function openTodoModal() {
    $('#todoForm')[0].reset();
    $('#todoId').val('');
    $('#todoModalTitle').text('Yeni Görev Ekle');
    new bootstrap.Modal($('#todoModal')).show();
}

function saveTodo(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    $.ajax({
        url: 'modules/todos/api/todo-save.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                bootstrap.Modal.getInstance($('#todoModal')[0]).hide();
                location.reload();
            } else {
                Swal.fire('Hata', response.message, 'error');
            }
        }
    });
}

function editTodo(id) {
    const row = $(`.todo-row[data-id="${id}"]`);
    if (!row.length) return;

    $('#todoId').val(id);
    $('#todoTitle').val(row.attr('data-title'));
    $('#todoDescription').val(row.attr('data-description'));
    $('#todoProjectId').val(row.attr('data-project-id') || '0');
    
    const dueDate = row.attr('data-due-date');
    if (dueDate && dueDate !== '0000-00-00 00:00:00') {
        $('#todoDueDate')[0]._flatpickr.setDate(dueDate);
    } else {
        $('#todoDueDate').val('');
    }

    $('#todoModalTitle').text('Görevi Düzenle');
    new bootstrap.Modal($('#todoModal')).show();
}

function toggleTodoStatus(id, checkbox) {
    const status = checkbox.checked ? '1' : '0';
    $.ajax({
        url: 'modules/todos/api/todo-toggle.php',
        method: 'POST',
        data: { id: id, status: status },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                location.reload();
            }
        }
    });
}

function deleteTodo(id) {
    Swal.fire({
        title: 'Silmek istediğinize emin misiniz?',
        text: "Bu işlem geri alınamaz!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet, Sil',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'modules/todos/api/todo-delete.php',
                method: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        location.reload();
                    }
                }
            });
        }
    });
}
    // Improved Swipe Logic (Finance Module Style)
    let touchStartX = 0;
    let touchMoveX = 0;
    let currentSwipeItem = null;
    const swipeThreshold = 75;

    $(document).on('touchstart', '.swipe-content', function(e) {
        touchStartX = e.originalEvent.touches[0].clientX;
        currentSwipeItem = $(this);
        
        // Reset other open items
        $('.swipe-content').not(currentSwipeItem).css('transform', 'translateX(0)');
    });

    $(document).on('touchmove', '.swipe-content', function(e) {
        touchMoveX = e.originalEvent.touches[0].clientX;
        let diff = touchStartX - touchMoveX;
        
        // Only swipe left
        if (diff > 0) {
            if (diff > swipeThreshold + 20) diff = swipeThreshold + 20; // Limit over-swipe
            $(this).css('transition', 'none');
            $(this).css('transform', 'translateX(-' + diff + 'px)');
        } else {
            $(this).css('transform', 'translateX(0)');
        }
    });

    $(document).on('touchend', '.swipe-content', function(e) {
        let diff = touchStartX - touchMoveX;
        $(this).css('transition', 'transform 0.2s ease-out');
        
        if (diff > swipeThreshold / 2) {
            $(this).css('transform', 'translateX(-' + swipeThreshold + 'px)');
        } else {
            $(this).css('transform', 'translateX(0)');
        }
    });

    // Close swipe on click elsewhere
    $(document).on('touchstart', function(e) {
        if (!$(e.target).closest('.swipe-container').length) {
            $('.swipe-content').css('transform', 'translateX(0)');
        }
    });
</script>
