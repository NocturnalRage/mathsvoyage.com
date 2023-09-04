<?php if (! empty($flash)) { ?>
  <div class="alert alert-<?= $this->esc($flash['type']) ?> alert-dismissible fade show" role="alert">
    <?php if ($flash['type'] == 'danger') { ?>
      <i class="bi bi-exclamation-triangle"></i>
    <?php } elseif ($flash['type'] == 'success') { ?>
      <i class="bi bi-check-circle"></i>
    <?php } ?>
    <?= $this->esc($flash['message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php } ?>
