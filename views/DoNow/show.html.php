<?php
include __DIR__.'/../layout/header.html.php';
?>
<?php $questionNo = 1; ?>
<?php foreach ($questions as $question) { ?>
  <div style="display:none;">
    <img id="question-<?= $questionNo; ?>" src="/uploads/worked-solutions/question-<?= $question['worked_solution_id']; ?>.png" />
  </div>
  <div style="display:none;">
    <img id="answer-<?= $questionNo; ?>" src="/uploads/worked-solutions/answer-<?= $question['worked_solution_id']; ?>.png" />
  </div>
  <?php $questionNo++; ?>
<?php } ?>

<button id="downloadBtn" class="btn btn-primary m-3">Download Do Now Questions and Answers</button>
<canvas id="questionCanvas" width="1920" height="1080">
</canvas>
<canvas id="answerCanvas" width="1920" height="1080">
</canvas>
<script src="<?= mix('js/doNow.js'); ?>"></script>
</body>
</html>
