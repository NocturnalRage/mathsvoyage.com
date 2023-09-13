<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center">
        <h1><?= $this->esc($skill['title']); ?> - Worked Solutions</h1>
        <hr />
      </div>
    </div><!-- row -->

    <div class="row">
      <div id="workedSolution" class="col-md-12 text-center" data-skill-id="<?= $this->esc($skill['skill_id']); ?>">
      </div><!-- workedSolution -->
    </div><!-- row -->
    <div class="row">
      <div class="col-md-12 text-center">
        <button id="toggleAnswerBtn" class="btn btn-success">Toggle Answer</button>
        <button id="nextQuestionBtn" class="btn btn-primary">Next Question</button>
      </div>
    </div><!-- row -->
    <hr />
    <div class="row">
      <div class="col-md-12 text-center">
        <a class="btn btn-success" href="/skills/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug'].'/'.$skill['slug']); ?>/quiz/create"
            onclick="event.preventDefault();
            document.getElementById('create-skills-quiz-form-<?= $skill['skill_id']; ?>').submit();">
            Take Skill Quiz
        </a>
        <form id="create-skills-quiz-form-<?= $skill['skill_id']; ?>" action="/skills/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug'].'/'.$skill['slug']); ?>/quiz/create" method="POST" style="display: none;">
        <?php $this->crsfToken(); ?>
        </form>

        <a class="btn btn-danger" href="/skills/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug'].'/'.$skill['slug']) ?>/videos">Watch Videos</a>
        <a href="/topics/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug'].'#skill-'.$skill['skill_id']); ?>" class="btn btn-secondary">Back to Topic</a>
      </div>
    </div><!-- row -->

    <?php include __DIR__.'/../layout/footer.html.php'; ?>
  </div><!-- container -->
  <script src="<?= mix('js/workedSolutions.js'); ?>"></script>
</body>
</html>
