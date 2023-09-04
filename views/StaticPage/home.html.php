<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <?php include __DIR__.'/../layout/flash.html.php'; ?>
    <div class="row">
      <div class="col-md-12">
        <h1>Cube Shack</h2>
        <p>A framework built over time from working with legacy applications. A lot of this is based off the excellent book <a href="https://leanpub.com/mlaphp">Modernizing Legacy Applications in PHP</a>. This framework does not use an ORM which I find easier to use as I come from a database background.</p>
      </div>
    </div>
    <div class="row my-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <figure>
              <blockquote class="blockquote">
                <p>
                  <i class="bi bi-quote"></i>
                  We act as though comfort and luxury were the chief requirements of life. All that we need to make us happy is something to be enthusiastic about.
                </p>
              </blockquote>
              <figcaption class="blockquote-footer">
                Albert Einstein
              </figcaption>
            </figure>
          </div>
        </div>
      </div>
    </div>
    <?php include __DIR__.'/../layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
