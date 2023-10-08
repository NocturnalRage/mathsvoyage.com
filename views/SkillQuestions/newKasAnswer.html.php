<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <h1 class="text-center">New Skill Kas Answer Question</h1>
    <?php
       include __DIR__.'/../layout/flash.html.php';
?>
    <form id="skillQuestionKasAnswerForm" enctype="multipart/form-data" method="post" action="/skill-questions/createKasAnswer">
    <?php include __DIR__.'/formKasAnswer.html.php'; ?>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="https://www.google.com/recaptcha/api.js"></script>
  <script>
    function onSubmit(token) {
      document.getElementById("skillQuestionKasAnswerForm").submit();
    }
  </script>
</body>
</html>
