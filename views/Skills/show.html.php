<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1><?= $this->esc($curriculum['curriculum_name'].' - '.$topic['title'].' - '.$skill['title']) ?></h1>
        <?php
           include __DIR__.'/../layout/flash.html.php';
?>
        <hr />
        <?php $questionId = 0; ?>
        <?php foreach ($questions as $question) { ?>
          <?php if ($questionId != $question['skill_question_id']) { ?>
            <?php if ($questionId != 0) { ?> 
                </div>
              </div>
            <?php } ?>
            <?php $questionId = $question['skill_question_id']; ?>
            <div class="card">
              <div class="card-body">
                <h2 class="card-title">
                  <?= $this->esc($question['question']) ?>
                </h2>
                <?php if ($question['question_image']) { ?>
                     <img src="/images/skill-questions/<?= $this->esc($question['question_image']); ?>" alt="<?= $this->esc($question['question_image']); ?>" width="640"/>
                <?php } ?>
          <?php } ?>
                <?php if ($question['skill_question_type_id'] == 1) { ?>
                  <?php if ($question['correct'] == 1) { ?>
                    <p><b><?= $this->esc($question['option_order'].': '.$question['option_text']); ?></b></p>
                  <?php } else { ?>
                    <p><?= $this->esc($question['option_order'].': '.$question['option_text']); ?></p>
                  <?php } ?>
                <?php } else { ?>
                  <p><?= $this->esc($question['answer']); ?></p>
                <?php } ?>
        <?php } ?>
              </div>
            </div>
      </div>
    </div>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
