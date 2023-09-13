<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>All Topics</h1>
        <?php
           include __DIR__.'/../layout/flash.html.php';
?>
        <hr />
        <?php foreach ($topics as $topic) { ?>
          <h2><a href="/topics/<?= $this->esc($topic['curriculum_slug'].'/'.$topic['slug']) ?>"><?= $this->esc($topic['curriculum_name'].' - '.$topic['title']) ?></a></h2>
        <?php } ?>
      </div>
    </div>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
