<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <meta name="description" content="<?= $this->esc($metaDescription) ?>">
  <meta name="author" content="MathsVoyage.com">
  <title><?= $this->esc($pageTitle); ?></title>
  <link rel="stylesheet" href="<?= mix('css/app.css'); ?>">
  <style>
     @page { size: A4;  margin: 0mm; }
     @media print {
       html, body {
         width: 210mm;
         height: 297mm;
       }
     }
  </style>
</head>
<body>
    <div class="container">
      <div class="row">
        <div class="col-12 worksheetHeading">
          <h4><?= $this->esc($curriculum['curriculum_name'].' - '.$topic['title'].' - '.$skill['title'].' - '.$worksheetType) ?></h4>
        </div>
      </div><!-- row -->
      <div class="row mb-3">
        <?php $questionNumber = 0; ?>
        <?php foreach ($questions as $question) { ?>
          <?php $questionNumber++; ?>
          <div class="col-4 worksheetQuestionWrapper">
            <div class="worksheetQuestion">
              <h5>Question <?= $this->esc($questionNumber); ?></h5>
              <p><?= $this->esc($question['question']) ?></p>
              <?php if ($question['question_image']) { ?>
                <img src="/uploads/skill-questions/<?= $this->esc($question['question_image']); ?>" alt="<?= $this->esc($question['question_image']); ?>" class="questionImage"/>
              <?php } ?>
              <?php if ($question['skill_question_type_id'] == 1) { ?>
                <?php foreach ($question['options'] as $option) { ?>
                  <p><?= $this->esc($option['option_letter'].' : '.$option['option_text']); ?></p>
                <?php } ?>
              <?php } ?>
            </div><!-- worksheetQuestion -->
          </div><!-- col-4 -->
          <?php if ($questionNumber % 3 == 0) { ?>
            </div><!-- row -->
            <div class="row mb-3">
          <?php } ?>
        <?php } ?>
      </div><!-- row -->
      <div class="row">
        <div class="col-12 worksheetHeading">
            <h4>Answers</h4>
        </div>
        <div class="col-12 worksheetQuestion">
            <?php $questionNumber = 0; ?>
            <?php foreach ($questions as $question) { ?>
              <?php $questionNumber++; ?>
              <?php if ($question['skill_question_type_id'] == 1) { ?>
                <?php foreach ($question['options'] as $option) { ?>
                  <?php if ($option['correct'] == 1) { ?>
                    <p>Question <?= $this->esc($questionNumber.' : '.$option['option_letter'].' - '.$option['option_text']); ?></p>
                  <?php } ?>
                <?php } ?>
              <?php } else { ?>
                <p>Question <?= $this->esc($questionNumber.' : '.$question['answer']); ?></p>
              <?php } ?>
            <?php } ?>
        </div>
      </div><!-- row -->
    </div><!-- container -->
</body>
</html>
