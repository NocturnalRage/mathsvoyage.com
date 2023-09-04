<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php include 'profile-menu.html.php'; ?>
      <div class="col-md-9">
        <h1><?= $user['given_name'].' '.$user['family_name']; ?></h1>
        <p>Email: <?= $this->esc($user['email']) ?></p>
        <p>Joined <?= $this->esc($user['memberSince']) ?></p>
        <div>
          <a href="/profile/edit" class="btn btn-success">
            <i class="fa fa-pencil-square-o"></i> Edit Profile</a>
        </div>
      </div>
    </div>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
