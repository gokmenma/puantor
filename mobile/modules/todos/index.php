<?php
// Puantor Mobil - Yapılacaklar Listesi (Premium Design)
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

<div class="container px-0 pb-5">
    <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
        <div>
            <h2 class="mb-0 text-bold" style="letter-spacing: -0.8px; font-size: 1.6rem;">Yapılacaklar</h2>
            <p class="text-muted text-xs mb-0 mt-1">Görevlerinizi buradan takip edebilirsiniz.</p>
        </div>
        <button class="btn btn-primary shadow-sm" onclick="openTodoModal()" style="border-radius: 16px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border: none; background: linear-gradient(135deg, #206bc4, #0054a6);">
            <i class="ti ti-plus" style="font-size: 1.4rem;"></i>
        </button>
    </div>

    <!-- Devam Eden Görevler -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0 text-semibold opacity-75" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Devam Edenler (<?php echo count($pending_todos); ?>)</h5>
        </div>
        
        <div class="todo-list-wrapper">
            <?php if (empty($pending_todos)): ?>
                <div class="mobile-card text-center py-5 glass-card mb-3">
                    <div class="avatar avatar-lg rounded-circle bg-success-lt mb-3 mx-auto">
                        <i class="ti ti-confetti fs-1"></i>
                    </div>
                    <p class="text-muted text-sm mb-0">Şu an bekleyen bir göreviniz yok.</p>
                </div>
            <?php else: ?>
                <?php foreach ($pending_todos as $todo): 
                    $todo_id_encrypted = Security::encrypt($todo->id);
                ?>
                    <div class="todo-card glass-card mb-3 p-3 todo-row" 
                         data-id="<?php echo $todo_id_encrypted; ?>"
                         data-title="<?php echo htmlspecialchars($todo->title); ?>"
                         data-description="<?php echo htmlspecialchars($todo->description ?? ''); ?>"
                         data-project-id="<?php echo $todo->project_id; ?>"
                         data-due-date="<?php echo $todo->due_date; ?>"
                         data-status="0">
                        <div class="d-flex gap-3">
                            <div class="todo-check-wrapper">
                                <label class="custom-checkbox">
                                    <input type="checkbox" onchange="toggleTodoStatus('<?php echo $todo_id_encrypted; ?>', this)">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="todo-content flex-grow-1" onclick="editTodo('<?php echo $todo_id_encrypted; ?>')">
                                <h4 class="todo-title mb-1"><?php echo htmlspecialchars($todo->title); ?></h4>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <?php if (!empty($todo->project_name)): ?>
                                        <span class="badge bg-primary-lt">
                                            <i class="ti ti-layout-grid me-1"></i><?php echo htmlspecialchars($todo->project_name); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($todo->due_date) && $todo->due_date !== '0000-00-00 00:00:00'): ?>
                                        <?php 
                                            $due_timestamp = strtotime($todo->due_date);
                                            $is_overdue = $due_timestamp < time();
                                        ?>
                                        <span class="badge <?php echo $is_overdue ? 'bg-danger-lt' : 'bg-orange-lt'; ?>">
                                            <i class="ti ti-calendar-time me-1"></i><?php echo date('d M H:i', $due_timestamp); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($todo->description)): ?>
                                    <p class="todo-description mb-0"><?php echo htmlspecialchars($todo->description); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="todo-actions">
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm btn-ghost-secondary border-0" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius: 12px;">
                                        <a class="dropdown-item py-2" href="javascript:void(0)" onclick="editTodo('<?php echo $todo_id_encrypted; ?>')">
                                            <i class="ti ti-edit me-2"></i> Düzenle
                                        </a>
                                        <a class="dropdown-item py-2 text-danger" href="javascript:void(0)" onclick="deleteTodo('<?php echo $todo_id_encrypted; ?>')">
                                            <i class="ti ti-trash me-2"></i> Sil
                                        </a>
                                    </div>
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
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3" data-bs-toggle="collapse" href="#completedList" role="button" aria-expanded="false" style="cursor: pointer;">
            <h5 class="mb-0 text-semibold opacity-50" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Tamamlananlar (<?php echo count($completed_todos); ?>)</h5>
            <i class="ti ti-chevron-down text-muted"></i>
        </div>
        
        <div class="collapse show" id="completedList">
            <div class="todo-list-wrapper">
                <?php foreach ($completed_todos as $todo): 
                    $todo_id_encrypted = Security::encrypt($todo->id);
                ?>
                    <div class="todo-card glass-card mb-2 p-3 todo-row completed" 
                         data-id="<?php echo $todo_id_encrypted; ?>"
                         data-title="<?php echo htmlspecialchars($todo->title); ?>"
                         data-description="<?php echo htmlspecialchars($todo->description ?? ''); ?>"
                         data-project-id="<?php echo $todo->project_id; ?>"
                         data-due-date="<?php echo $todo->due_date; ?>"
                         data-status="1">
                        <div class="d-flex gap-3 align-items-center">
                            <div class="todo-check-wrapper">
                                <label class="custom-checkbox">
                                    <input type="checkbox" checked onchange="toggleTodoStatus('<?php echo $todo_id_encrypted; ?>', this)">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="todo-content flex-grow-1">
                                <h4 class="todo-title mb-0 text-decoration-line-through text-muted"><?php echo htmlspecialchars($todo->title); ?></h4>
                            </div>
                            <div class="todo-actions">
                                <button class="btn btn-icon btn-sm btn-ghost-danger border-0" onclick="deleteTodo('<?php echo $todo_id_encrypted; ?>')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Structure (Keeping original but slightly styling) -->
<div class="modal modal-blur fade" id="todoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-modal border-0 overflow-hidden">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold" id="todoModalTitle">Yeni Görev Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <form id="todoForm" onsubmit="saveTodo(event)">
                <div class="modal-body py-4">
                    <input type="hidden" id="todoId" name="id">
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control border-0 bg-light-lt" id="todoTitle" name="title" required placeholder="Neler yapılacak?" style="border-radius: 14px;">
                        <label for="todoTitle">Görev Başlığı</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select border-0 bg-light-lt" id="todoProjectId" name="project_id" style="border-radius: 14px;">
                            <option value="0">Proje Yok</option>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?php echo $project->id; ?>"><?php echo htmlspecialchars($project->project_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="todoProjectId">İlgili Proje</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control border-0 bg-light-lt" id="todoDueDate" name="due_date" placeholder="Tarih ve Saat Seçin" style="border-radius: 14px;">
                        <label for="todoDueDate">Bitiş Tarihi & Saati</label>
                    </div>

                    <div class="form-floating mb-0">
                        <textarea class="form-control border-0 bg-light-lt" id="todoDescription" name="description" placeholder="Detay ekleyebilirsiniz..." style="border-radius: 14px; height: 100px;"></textarea>
                        <label for="todoDescription">Açıklama (Opsiyonel)</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 14px; font-weight: 600;">Görevi Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Premium Glassmorphism Theme */
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 20px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    body[data-bs-theme="dark"] .glass-card {
        background: rgba(30, 41, 59, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
    }
    
    .todo-card:active {
        transform: scale(0.98);
    }
    
    .todo-title {
        font-size: 0.95rem;
        font-weight: 650;
        letter-spacing: -0.3px;
        color: var(--tblr-body-color);
    }
    
    .todo-description {
        font-size: 0.75rem;
        color: #64748b;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
    }
    
    body[data-bs-theme="dark"] .todo-description {
        color: #94a3b8;
    }
    
    .badge {
        font-weight: 600;
        font-size: 0.68rem;
        border-radius: 8px;
        padding: 4px 8px;
        letter-spacing: 0.2px;
    }
    
    /* Custom Checkbox */
    .custom-checkbox {
        position: relative;
        padding-left: 24px;
        margin-bottom: 24px;
        cursor: pointer;
        font-size: 22px;
        user-select: none;
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
        height: 24px;
        width: 24px;
        background-color: #f1f5f9;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        transition: all 0.2s ease;
    }
    
    body[data-bs-theme="dark"] .checkmark {
        background-color: #1e293b;
        border-color: #334155;
    }
    
    .custom-checkbox:hover input ~ .checkmark {
        border-color: #206bc4;
    }
    
    .custom-checkbox input:checked ~ .checkmark {
        background-color: #206bc4;
        border-color: #206bc4;
    }
    
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }
    
    .custom-checkbox input:checked ~ .checkmark:after {
        display: block;
    }
    
    .custom-checkbox .checkmark:after {
        left: 8px;
        top: 4px;
        width: 6px;
        height: 11px;
        border: solid white;
        border-width: 0 2.5px 2.5px 0;
        transform: rotate(45deg);
    }
    
    /* Modal Tweaks */
    .glass-modal {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    
    body[data-bs-theme="dark"] .glass-modal {
        background: rgba(15, 23, 42, 0.9) !important;
    }
    
    .bg-light-lt {
        background-color: #f8fafc !important;
    }
    
    body[data-bs-theme="dark"] .bg-light-lt {
        background-color: #1e293b !important;
    }
    
    .form-floating > .form-control:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(32, 107, 196, 0.1);
        border: 1px solid #206bc4 !important;
    }
    
    body[data-bs-theme="dark"] .form-floating > .form-control:focus {
        background-color: #0f172a !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#todoDueDate", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        locale: "tr",
        disableMobile: "true"
    });
});

function openTodoModal() {
    document.getElementById('todoForm').reset();
    document.getElementById('todoId').value = '';
    document.getElementById('todoModalTitle').innerText = 'Yeni Görev Ekle';
    new bootstrap.Modal(document.getElementById('todoModal')).show();
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
                bootstrap.Modal.getInstance(document.getElementById('todoModal')).hide();
                location.reload();
            } else {
                Swal.fire('Hata', response.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Hata', 'İşlem sırasında bir hata oluştu.', 'error');
        }
    });
}

function editTodo(id) {
    const row = document.querySelector(`.todo-row[data-id="${id}"]`);
    if (!row) return;

    document.getElementById('todoId').value = id;
    document.getElementById('todoTitle').value = row.getAttribute('data-title');
    document.getElementById('todoDescription').value = row.getAttribute('data-description');
    document.getElementById('todoProjectId').value = row.getAttribute('data-project-id') || '0';
    
    const dueDate = row.getAttribute('data-due-date');
    if (dueDate && dueDate !== '0000-00-00 00:00:00') {
        document.getElementById('todoDueDate')._flatpickr.setDate(dueDate);
    } else {
        document.getElementById('todoDueDate').value = '';
    }

    document.getElementById('todoModalTitle').innerText = 'Görevi Düzenle';
    new bootstrap.Modal(document.getElementById('todoModal')).show();
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
                // Refresh to move between sections
                location.reload();
            } else {
                Swal.fire('Hata', response.message, 'error');
                checkbox.checked = !checkbox.checked;
            }
        }
    });
}

function deleteTodo(id) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu görev kalıcı olarak silinecektir!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet, Sil!',
        cancelButtonText: 'Vazgeç',
        customClass: {
            confirmButton: 'btn btn-danger px-4',
            cancelButton: 'btn btn-link text-muted'
        },
        buttonsStyling: false
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
                    } else {
                        Swal.fire('Hata', response.message, 'error');
                    }
                }
            });
        }
    });
}
</script>
