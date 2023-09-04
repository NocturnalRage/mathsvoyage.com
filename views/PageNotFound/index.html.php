<?php
$this->header('HTTP/1.1 404 Not Found');
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <h1>Sorry, the page you are looking for isn't here</h1>
    <p>We're not sure what happened but maybe you should visit the
    <a href="/">homepage.</a></p>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
