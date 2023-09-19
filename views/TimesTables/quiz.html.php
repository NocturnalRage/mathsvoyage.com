<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div id="instructions" class="col-md-12 text-center">
        <h1>Ready for the <?= $this->esc($attempt['title']); ?> Quiz?</h1>
        <p>Show us what you can do!</p>
        <h2><?= $this->esc($attempt['total_questions']); ?> questions</h2>
        <button id="startBtn" class="btn btn-success">Let's go</button>
      </div>
    </div><!-- row -->

    <div class="row">
      <div id="quiz"
           data-attempt-id="<?= $this->esc($attempt['id']); ?>"
           data-times-tables-id="<?= $this->esc($attempt['times_tables_id']); ?>"
           data-min-number="<?= $this->esc($attempt['min_number']); ?>"
           data-max-number="<?= $this->esc($attempt['max_number']); ?>"
           data-total-questions="<?= $this->esc($attempt['total_questions']); ?>"
           data-repetitions="<?= $this->esc($attempt['repetitions']); ?>"
           data-attempt="<?= $this->esc($attempt['attempt']); ?>"
           data-crsf-token="<?= $this->esc($crsfToken); ?>"
           class="col-md-12"
      >
      </div>
    </div><!-- row -->

    <div class="row add-bottom">
      <div id="progress" class="col-md-12">
        <button id="action" class="btn btn-lg btn-primary">Check Answer</button>
        <?php for ($i = $attempt['total_questions'] - 1; $i >= 0; $i--) { ?>
          <span id="dot<?= $this->esc($i); ?>" class="dot"></span>
        <?php } ?>
      </div>
    </div><!-- row -->

    <div class="row">
      <div id="feedback" class="col-md-12"></div>
    </div><!-- row -->

    <?php include __DIR__.'/../layout/footer.html.php'; ?>
  </div><!-- container -->
  <script src="<?= mix('js/times-tables-quiz.js'); ?>"></script>
</body>
</html>
