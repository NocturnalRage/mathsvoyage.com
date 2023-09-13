<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <h1 class="text-center">New Skill</h1>
    <?php
       include __DIR__.'/../layout/flash.html.php';
?>
    <form id="skillForm" method="post" action="/skills">
    <?php include __DIR__.'/form.html.php'; ?>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="https://www.google.com/recaptcha/api.js"></script>
  <script>
    function onSubmit(token) {
      document.getElementById("skillForm").submit();
    }
  </script>
</body>
</html>
