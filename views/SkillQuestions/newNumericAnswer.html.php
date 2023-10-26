<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <h1 class="text-center">New Skill Question - Numeric Answer</h1>
    <?php
       include __DIR__.'/../layout/flash.html.php';
?>
    <form id="skillQuestionNumericAnswerForm" enctype="multipart/form-data" method="post" action="/skill-questions/createNumericAnswer">
    <?php include __DIR__.'/formNumericAnswer.html.php'; ?>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="https://www.google.com/recaptcha/api.js"></script>
  <script>
    function onSubmit(token) {
      document.getElementById("skillQuestionNumericAnswerForm").submit();
    }
  </script>
</body>
</html>
