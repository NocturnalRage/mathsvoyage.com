<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1><?= $this->esc($curriculum['curriculum_name'].' - '.$topic['title']) ?></h1>
           <a class="btn btn-primary" href="/topics/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug']); ?>/quiz/create"
                    onclick="event.preventDefault();
                    document.getElementById('create-topic-quiz-form').submit();">
                    Take <?= $this->esc($topic['title']) ?> Quiz
           </a>
           <form id="create-topic-quiz-form" action="/topics/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug']); ?>/quiz/create" method="POST" style="display: none;">
             <?php $this->crsfToken(); ?>
           </form>
        <?php
           include __DIR__.'/../layout/flash.html.php';
?>
        <hr />
        <?php foreach ($skills as $skill) { ?>
          <div class="card">
            <div class="card-body">
              <h2 id="skill-<?= $this->esc($skill['skill_id']); ?>" class="card-title">
                <?= $this->esc($skill['title']) ?>
              </h2>
              <h4>Mastery Level: <?= $this->esc($skill['mastery_level_desc']); ?></h4>
              <hr />
              <p class="card-text">
                <form id="create-skills-quiz-form-<?= $skill['skill_id']; ?>" action="/skills/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug'].'/'.$skill['slug']); ?>/quiz/create" method="POST" style="display: none;">
                <?php $this->crsfToken(); ?>
                </form>
                <a class="btn btn-success" href="/skills/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug'].'/'.$skill['slug']); ?>/quiz/create"
                    onclick="event.preventDefault();
                    document.getElementById('create-skills-quiz-form-<?= $skill['skill_id']; ?>').submit();">
                    Take Skill Quiz
                </a>
                <a class="btn btn-danger" href="/skills/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug'].'/'.$skill['slug']) ?>/videos">Watch Videos</a>
                <a class="btn btn-primary" href="/skills/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug'].'/'.$skill['slug']) ?>/worked-solutions">Worked Solutions</a>
                <?php if ($isAdmin) { ?>
                    <hr />
                    <a class="btn btn-primary" href="/skills/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug'].'/'.$skill['slug']) ?>/worksheet/1/20">Generate 20 Fluency Questions</a>
                    <a class="btn btn-primary" href="/skills/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug'].'/'.$skill['slug']) ?>/worksheet/2/10">Generate 10 Problem Solving Questions</a>
                    <a class="btn btn-primary" href="/skills/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug'].'/'.$skill['slug']) ?>/worksheet/3/3">Generate 3 Reasoning Questions</a>
                    <a class="btn btn-primary" href="/skills/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug'].'/'.$skill['slug']) ?>/worksheet/4/30">Generate 30 Questions of any Type</a>
                <?php } ?>
              </p>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
