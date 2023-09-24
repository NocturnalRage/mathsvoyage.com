<?php
include __DIR__.DIRECTORY_SEPARATOR.'../layout/header.html.php';
include __DIR__.DIRECTORY_SEPARATOR.'../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php
        include __DIR__.DIRECTORY_SEPARATOR.'/profile-menu.html.php';
?>
      <div class="col-md-9">
        <h1>Change Your Password</h1>
        <?php
    include __DIR__.DIRECTORY_SEPARATOR.'../layout/flash.html.php';
?>
        <form method="post" action="/password-validate" class="form-horizontal">
          <div class="mb-3">
            <?php $this->crsfToken(); ?>
              <label for="loginPassword">New password:</label>
                <input type="password" name="loginPassword" id="loginPassword" class="form-control" value="<?php if (isset($formVars['loginPassword'])) {
                    echo $this->esc($formVars['loginPassword']);
                } ?>" maxlength="100" required autofocus />
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

          <div>
            <input type="submit" class="btn btn-success" value="Update My Password" />
          </div>
        </form>
      </div><!-- col-md-9 -->
    </div><!-- row -->

<?php
include __DIR__.DIRECTORY_SEPARATOR.'../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
