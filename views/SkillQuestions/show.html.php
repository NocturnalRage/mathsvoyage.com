<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
    <div class="container">
      <div class="row">
        <div class="col-12 worksheetQuestionWrapper">
          <div class="worksheetQuestion">
            <p><?= $this->esc($question['question']) ?></p>
            <?php if ($question['question_image']) { ?>
              <img src="/uploads/skill-questions/<?= $this->esc($question['question_image']); ?>" alt="<?= $this->esc($question['question_image']); ?>" class="questionImage"/>
            <?php } ?>
            <?php if ($question['skill_question_type_id'] == 1) { ?>
              <?php foreach ($options as $option) { ?>
                <p><?= $this->esc($option['option_letter'].' : '.$option['option_text']); ?></p>
              <?php } ?>
            <?php } ?>
          </div><!-- worksheetQuestion -->
        </div><!-- col-4 -->
      </div><!-- row -->
    </div><!-- container -->
</body>
</html>

