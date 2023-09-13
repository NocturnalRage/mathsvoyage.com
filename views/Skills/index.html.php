<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>All Skills</h1>
        <?php
           include __DIR__.'/../layout/flash.html.php';
?>
        <hr />
        <?php foreach ($skills as $skill) { ?>
          <h2><?= $this->esc($skill['curriculum_name'].' - '.$skill['topic_title'].' - '.$skill['title']) ?></a></h2>
          <a class="btn btn-primary" href="/skills/<?= $this->esc($skill['curriculum_slug'].'/'.$skill['topic_slug'].'/'.$skill['slug']); ?>">
             View Question Bank
          </a>
          <hr />
        <?php } ?>
      </div>
    </div>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
