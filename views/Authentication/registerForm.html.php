<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <h1 class="text-center">Register</h1>
    <form id="registerForm" method="post" action="/register">
      <div class="row justify-content-center">
        <div class="col-md-6 text-center">
          <?php
            include __DIR__.'/../layout/flash.html.php';
?>
          <p>Let's set up your free account. Already have one? <a href="/login">Login now</a></p>
          <?php $this->crsfToken(); ?>
          <div class="form-floating mb-3">
            <input type="givenName" class="form-control" name="givenName" id="givenName" value="<?php if (isset($formVars['givenName'])) {
                echo $this->esc($formVars['givenName']);
            } ?>" maxlength="100" required autofocus>
            <label for="givenName">Given name</label>
          </div>
          <?php if (isset($errors['givenName'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['givenName']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <input type="familyName" class="form-control" name="familyName" id="familyName" value="<?php if (isset($formVars['familyName'])) {
                echo $this->esc($formVars['familyName']);
            } ?>" maxlength="100" required>
            <label for="familyName">Family name</label>
          </div>
          <?php if (isset($errors['familyName'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['familyName']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <input type="email" class="form-control" name="email" id="email" value="<?php if (isset($formVars['email'])) {
                echo $this->esc($formVars['email']);
            } ?>" maxlength="100" required>
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
        <div class="col-md-6">
          <button
            type="submit"
            class="g-recaptcha btn btn-primary"
            data-sitekey="<?= $this->esc($recaptchaKey); ?>"
            data-callback='onSubmit'
            data-action='loginwithversion3'
          >
            <i class="bi bi-person"></i> Create My Free Account
          </button>
        </div>
      </div>
    </form>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="https://www.google.com/recaptcha/api.js"></script>
  <script>
    function onSubmit(token) {
      document.getElementById("registerForm").submit();
    }
  </script>
</body>
</html>
