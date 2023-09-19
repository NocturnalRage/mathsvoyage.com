<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <h1 class="text-center">New Skill Number Question</h1>
    <?php
       include __DIR__.'/../layout/flash.html.php';
?>
    <form id="skillQuestionNumberForm" enctype="multipart/form-data" method="post" action="/skill-questions/createNumber">
    <?php include __DIR__.'/formNumber.html.php'; ?>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="https://www.google.com/recaptcha/api.js"></script>
  <script>
    function onSubmit(token) {
      document.getElementById("skillQuestionNumberForm").submit();
    }
  </script>
</body>
</html>
