<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <h1 class="text-center">Edit Curriculum</h1>
    <?php
       include __DIR__.'/../layout/flash.html.php';
?>
    <form id="curriculumForm" method="post" action="/curriculum/<?= $this->esc($curriculum['curriculum_slug']); ?>">
    <?php $this->formMethod('patch'); ?>
    <?php include __DIR__.'/form.html.php'; ?>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="https://www.google.com/recaptcha/api.js"></script>
  <script>
    function onSubmit(token) {
      document.getElementById("curriculumForm").submit();
    }
  </script>
</body>
</html>
