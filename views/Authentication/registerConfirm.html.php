<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12 text-center">
        <h1>Confirm Your Email Address</h1>
        <p>
          I've just sent you an email to <?= $this->esc($registeredEmail); ?> that contains
          a confirmation link. In order to activate your free membership,
          check your email and click on the link in that email. If you don't
          receive this email then check in your junk folder. If you still
          can't find the confirmation email then try
          <a href="/register">registering again</a> and double check your
          email address.
        </p>
        <p>
          Once you confirm your email address you'll get access to all the
          free resources on this website.
        </p>
      </div>
    </div>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
