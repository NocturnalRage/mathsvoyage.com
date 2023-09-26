<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1><?= $this->esc($curriculum['curriculum_name']) ?> Curriculum Topics</h1>
        <?php
           include __DIR__.'/../layout/flash.html.php';
?>
        <?php if ($isAdmin) { ?>
          <a href="/curriculum/<?= $this->esc($curriculum['curriculum_slug']) ?>/edit" class="btn btn-success">
            <i class="bi bi-pen"></i> Edit Curriculum</a>
        <?php } ?>
        <hr />
        <?php foreach ($topics as $topic) { ?>
          <div class="card">
            <div class="card-body">
              <h2 class="card-title">
                <a href="/topics/<?= $this->esc($curriculum['curriculum_slug']).'/'.$this->esc($topic['slug']) ?>">
                  <?= $this->esc($topic['title']) ?>
                </a> 
              </h2>
              <p><?= $this->esc($topic['percent_complete']); ?>% mastered</p>

           <a class="btn btn-primary" href="/topics/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug']); ?>/quiz/create"
                    onclick="event.preventDefault();
                    document.getElementById('create-topic-quiz-form').submit();">
                    Take <?= $this->esc($topic['title']) ?> Quiz
           </a>
           <form id="create-topic-quiz-form" action="/topics/<?= $this->esc($curriculum['curriculum_slug'].'/'.$topic['slug']); ?>/quiz/create" method="POST" style="display: none;">
             <?php $this->crsfToken(); ?>
           </form>
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
