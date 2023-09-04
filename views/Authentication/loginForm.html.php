<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <h1 class="text-center">Welcome Back!</h1>
    <form id="loginForm" method="post" action="/login">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <?php
            include __DIR__.'/../layout/flash.html.php';
?>
          <?php $this->crsfToken(); ?>
          <div class="form-floating mb-3">
            <input type="email" class="form-control" name="email" id="email" value="<?php if (isset($formVars['email'])) {
                echo $this->esc($formVars['email']);
            } ?>" maxlength="100" required autofocus>
            <label for="email">Email address</label>
          </div>
          <?php if (isset($errors['email'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['email']); ?></div>
          <?php } ?>
          <div class="form-floating mb-3">
            <input type="password" class="form-control" name="loginPassword" id="loginPassword" value="<?php if (isset($formVars['loginPassword'])) {
                echo $this->esc($formVars['loginPassword']);
            } ?>" maxlength="100" required />
            <label for="loginPassword">Password</label>
          </div>
          <?php if (isset($errors['loginPassword'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['loginPassword']); ?></div>
          <?php } ?>
        </div>
      </div>
      <div class="row justify-content-center my-2">
        <div class="col-md-6 form-check">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="y" id="rememberme" name="rememberme">
            <label class="form-check-label" for="rememberme">
              Remember Me
            </label>
          </div>
        </div>
      </div>
      <div class="row justify-content-center my-2">
        <div class="col-md-6">
          <button
            type="submit"
            class="g-recaptcha btn btn-primary"
            data-sitekey="<?= $this->esc($recaptchaKey); ?>"
            data-callback='onSubmit'
            data-action='loginwithversion3'
          >
            <i class="bi bi-person"></i> Login To Your Account
          </button>
        </div>
      </div>
    </form>
    <div class="row justify-content-center">
      <div class="col-md-6 text-center p-4 my-4 bg-light border border-dark">
        <p>Don't have an account? <a href="/register">Create one</a> in 30 seconds</p>
        <p><a href="/forgot-password">Forgot your password?</a></p>
      </div>
    </div>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="https://www.google.com/recaptcha/api.js"></script>
  <script>
    function onSubmit(token) {
      document.getElementById("loginForm").submit();
    }
  </script>
</body>
</html>
