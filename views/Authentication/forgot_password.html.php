<?php
include __DIR__.DIRECTORY_SEPARATOR.'../layout/header.html.php';
include __DIR__.DIRECTORY_SEPARATOR.'../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1 class="text-center">You've Forgot Your Password</h1>
        <p class="text-center">
        No problems. Just enter the email address you used to create your account
        and we'll send you a link to reset your password.
        </p>

        <?php
          include __DIR__.DIRECTORY_SEPARATOR.'../layout/flash.html.php';
?>
      </div><!-- col-md-12 -->
    </div><!-- row -->
    <div class="row justify-content-center">
      <div class="col-md-6">
        <form method="post" action="/forgot-password-validate">
          <div class="form-floating mb-3">
            <?php $this->crsfToken(); ?>
              <input type="email" name="email" id="email" class="form-control" value="<?php if (isset($formVars['email'])) {
                  echo $this->esc($formVars['email']);
              } ?>" maxlength="255" required autofocus />
              <label for="email">Email:</label>
              <?php if (isset($errors['email'])) { ?>
                <div class="alert alert-danger"><?= $this->esc($errors['email']); ?></div>
              <?php } ?>
          </div>
          <div>
            <input type="submit" class="btn btn-success" value="Send me a password reset link" />
          </div>
        </form>
      </div><!-- col-md-12 -->
    </div><!-- row -->

<?php
include __DIR__.DIRECTORY_SEPARATOR.'../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
