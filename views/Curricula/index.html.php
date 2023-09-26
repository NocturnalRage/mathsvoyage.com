<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>Curriculum</h1>
        <?php
           include __DIR__.'/../layout/flash.html.php';
?>
        <?php if ($isAdmin) { ?>
          <a href="/curriculum/new" class="btn btn-primary">
            <i class="bi bi-plus"></i> New Curriculum</a>
          <a href="/topics/new" class="btn btn-primary">
            <i class="bi bi-plus"></i> New Topic</a>
          <a href="/skills/new" class="btn btn-primary">
            <i class="bi bi-plus"></i> New Skill</a>
          <a href="/skill-questions/new" class="btn btn-primary">
            <i class="bi bi-plus"></i> New Skill Multiple Choice Question</a>
          <a href="/skill-questions/newNumber" class="btn btn-primary">
            <i class="bi bi-plus"></i> New Skill Number Question</a>
          <a href="/videos/new" class="btn btn-primary">
            <i class="bi bi-plus"></i> New Video</a>
          <a href="/worked-solutions/new" class="btn btn-primary">
            <i class="bi bi-plus"></i> New Worked Solution</a>
        <?php } ?>
        <hr />
        <?php foreach ($curricula as $curriculum) { ?>
          <div class="card">
            <div class="card-body">
              <h2 class="card-title">
                <a href="/curriculum/<?= $this->esc($curriculum['curriculum_slug']) ?>">
                  <?= $this->esc($curriculum['curriculum_name']) ?> Curriculum
                </a>
              </h2>
              <p>
                <a class="btn btn-primary"
                   href="/curriculum/<?= $this->esc($curriculum['curriculum_slug']); ?>/quiz/create"
                   onclick="event.preventDefault();
                            document.getElementById('create-curriculum-quiz-form-<?= $curriculum['curriculum_id']; ?>').submit();">
                  Take <?= $this->esc($curriculum['curriculum_name']) ?> Quiz
                </a>
              </p>
            </div>
          </div>

          <form id="create-curriculum-quiz-form-<?= $curriculum['curriculum_id']; ?>" action="/curriculum/<?= $this->esc($curriculum['curriculum_slug']); ?>/quiz/create" method="POST" style="display: none;">
            <?php $this->crsfToken(); ?>
          </form>
        <?php } ?>
      </div>
    </div>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
