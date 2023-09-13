<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <h1 class="text-center">New Video</h1>
    <?php
       include __DIR__.'/../layout/flash.html.php';
?>
    <form id="videoForm" enctype="multipart/form-data" method="post" action="/videos">
    <?php include __DIR__.'/form.html.php'; ?>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="https://www.google.com/recaptcha/api.js"></script>
  <script>
    function onSubmit(token) {
      document.getElementById("videoForm").submit();
    }
  </script>
</body>
</html>
