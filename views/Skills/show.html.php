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
        <?php $questionNo = 0; ?>
        <?php $questionId = 0; ?>
        <?php foreach ($questions as $question) { ?>
          <?php if ($questionId != $question['skill_question_id']) { ?>
            <?php if ($questionId != 0) { ?> 
                    </div>
                  </div>
                </div>
            <?php } ?>
            <?php $questionId = $question['skill_question_id']; ?>
            <?php $questionNo++; ?>
            <div class="card">
              <div class="card-body">
                <div class="card-title">
                  <h2>
                    <a href="/skill-questions/<?= $this->esc($questionId); ?>">
                      Question <?= $this->esc($questionNo); ?>
                    </a>
                  </h2>
                </div>
                <div class="card-text">
                  <?php
                    $htmlQuestion = str_replace('{MATHFIELD_SMALL}', '<math-field class="small"></math-field>', $question['question']);
              $htmlQuestion = str_replace('{MATHFIELD_LARGE}', '<math-field class="large"></math-field>', $htmlQuestion);
              $htmlQuestion = str_replace('{MATHFIELD}', '<math-field class="standard"></math-field>', $htmlQuestion);
              $htmlQuestion = str_replace('{NUMERIC_INPUT_SMALL}', '<input class="small" type="text">', $htmlQuestion);
              $htmlQuestion = str_replace('{NUMERIC_INPUT_LARGE}', '<input class="large" type="text">', $htmlQuestion);
              $htmlQuestion = str_replace('{NUMERIC_INPUT}', '<input class="standard" type="text">', $htmlQuestion);
              $htmlQuestion = str_replace('{IMAGE}', '<img class="questionImage" src="/uploads/skill-questions/'.$question['question_image'].'" alt="Question Image" />', $htmlQuestion);
              ?>
                  <?= $htmlQuestion ?>
          <?php } ?>
                <?php if ($question['skill_question_type_id'] == 1) { ?>
                  <?php if ($question['correct'] == 1) { ?>
                    <p><b><?= $this->esc($question['option_order'].': '.$question['option_text']); ?></b></p>
                  <?php } else { ?>
                    <p><?= $this->esc($question['option_order'].': '.$question['option_text']); ?></p>
                  <?php } ?>
                <?php } elseif ($question['skill_question_type_id'] == 2) { ?>
                  <p><?= $this->esc($question['kas_answer']); ?></p>
                  <p><?= $this->esc('Same form: '.$question['kas_form']); ?></p>
                  <p><?= $this->esc('Simplify: '.$question['kas_simplify']); ?></p>
                <?php } elseif ($question['skill_question_type_id'] == 3) { ?>
                  <p><?= $this->esc($question['numeric_answer']); ?></p>
                  <p><?= $this->esc('Simplify: '.$question['numeric_simplify']); ?></p>
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
  <script type="module">
    window.addEventListener("DOMContentLoaded", () =>
      import("https://unpkg.com/mathlive?module").then((mathlive) =>
        mathlive.renderMathInDocument()
      )
    );
  </script>
</body>
</html>
