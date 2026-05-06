<?php
// Puantor Mobil - Yapılacaklar Listesi
require_once ROOT . "/Model/TodoModel.php";
$todoModel = new Todo();
$todos = $todoModel->getTodosByFirm();
?>

<div class="container px-0">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h2 class="mb-0 text-semibold" style="letter-spacing: -0.5px;">Yapılacaklar</h2>
    <button class="btn btn-icon btn-sm btn-primary border-0" style="border-radius: 12px; width: 36px; height: 36px;">
      <i class="ti ti-plus" style="font-size: 1.2rem;"></i>
    </button>
  </div>

  <div class="list-group list-group-mobile">
    <?php if (empty($todos)): ?>
      <div class="text-center py-5">
        <i class="ti ti-confetti text-muted mb-2" style="font-size: 3rem; opacity: 0.3;"></i>
        <p class="text-muted">Harika! Tüm işler tamamlanmış.</p>
      </div>
    <?php else: ?>
      <?php foreach ($todos as $todo): 
        $is_done = ($todo->state ?? 0) == 1;
      ?>
        <div class="list-group-item d-flex align-items-center justify-content-between py-3">
          <div class="d-flex align-items-center gap-3">
            <input class="form-check-input m-0" type="checkbox" <?php echo $is_done ? 'checked' : ''; ?> style="width: 20px; height: 20px; border-radius: 6px;">
            <div class="<?php echo $is_done ? 'text-decoration-line-through text-muted' : 'text-bold text-sm'; ?>">
              <?php echo htmlspecialchars($todo->title ?? $todo->content ?? 'Görev'); ?>
              <?php if (isset($todo->created_at)): ?>
                <div class="text-muted text-xs font-weight-normal"><?php echo date('d M Y', strtotime($todo->created_at)); ?></div>
              <?php endif; ?>
            </div>
          </div>
          <button class="btn btn-icon btn-sm btn-outline-secondary border-0">
            <i class="ti ti-dots-vertical"></i>
          </button>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
