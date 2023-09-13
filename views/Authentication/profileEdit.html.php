<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php include 'profile-menu.html.php'; ?>
      <div class="col-md-9">
        <h1>Edit Your Profile</h1>
        <?php
          include __DIR__.'/../layout/flash.html.php';
?>
        <form id="profileEdit" method="post" action="/profile/edit">
        <?php $this->formMethod('patch'); ?>
        <?php $this->crsfToken(); ?>
        <div class="form-floating mb-3">
          <input type="text" class="form-control" name="given_name" id="given_name" value="<?php if (isset($formVars['given_name'])) {
              echo $this->esc($formVars['given_name']);
          } ?>" maxlength="100" required autofocus>
          <label for="given_name">Given name</label>
        </div>
        <?php if (isset($errors['given_name'])) { ?>
          <div class="alert alert-danger"><?= $this->esc($errors['given_name']); ?></div>
        <?php } ?>

        <div class="form-floating mb-3">
          <input type="text" class="form-control" name="family_name" id="family_name" value="<?php if (isset($formVars['family_name'])) {
              echo $this->esc($formVars['family_name']);
          } ?>" maxlength="100" required>
          <label for="family_name">Family name</label>
        </div>
        <?php if (isset($errors['family_name'])) { ?>
          <div class="alert alert-danger"><?= $this->esc($errors['family_name']); ?></div>
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
        <button
          type="submit"
          class="g-recaptcha btn btn-primary"
          data-sitekey="<?= $this->esc($recaptchaKey); ?>"
          data-callback='onSubmit'
          data-action='loginwithversion3'
        >
            <i class="bi bi-person"></i> Update My Profile
          </button>




      </div>
    </div>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="https://www.google.com/recaptcha/api.js"></script>
  <script>
    function onSubmit(token) {
      document.getElementById("profileEdit").submit();
    }
  </script>
</body>
</html>
