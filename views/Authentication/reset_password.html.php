<?php
include __DIR__.DIRECTORY_SEPARATOR.'../layout/header.html.php';
include __DIR__.DIRECTORY_SEPARATOR.'../layout/navbar.html.php';
?>
  <div class="container">
  
    <h1 class="text-center">You're Nearly Done</h1>
    <p class="text-center">
    You can now reset your password by entering a new one below.
    </p>
    <?php
      include __DIR__.DIRECTORY_SEPARATOR.'../layout/flash.html.php';
?>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <form method="post" action="/reset-password-validate" class="form-horizontal">
          <div class="form-floating mb-3">
            <?php $this->crsfToken(); ?>
            <input type="hidden" name="token" value="<?= $this->esc($token); ?>" />
            <input type="password" name="loginPassword" id="loginPassword" class="form-control" placeholder="New password" value="<?php if (isset($formVars['loginPassword'])) {
                echo $this->esc($formVars['loginPassword']);
            } ?>" maxlength="30" required autofocus />
            <label for="loginPassword" class="control-label col-md-4">New password:</label>
            <?php if (isset($errors['loginPassword'])) { ?>
              <div class="alert alert-danger"><?= $this->esc($errors['loginPassword']); ?></div>
            <?php } ?>
          </div>
          <div class="mb-3">
            <label>
              <input type="checkbox" onchange="togglePassword(this)" />
              Show password
            </label>
          </div>
          <script>
          function togglePassword(val) {
            document.getElementById('loginPassword').type = val.checked ? 'text' : 'password';
          }
          </script>
          <input type="submit" class="btn btn-primary" value="Reset My Password" />
          </div>
        </form>
      </div><!-- col-md-8 col-md-offset-2 -->
    </div><!-- row -->

<?php
include __DIR__.DIRECTORY_SEPARATOR.'../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
