<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>Times Tables</h1>
        <?php
           include __DIR__.'/../layout/flash.html.php';
?>
        <hr />
        <p>Current Times Table: <?= $this->esc($attempt['title']); ?></p>
        <p>Current Attempt: <?= $this->esc($attempt['attempt']); ?></p>
        <a href="/times-tables/quiz" class="btn btn-primary">Start Next Attempt</a>
        <hr />
        <h1>Scores</h1>
        <table class="table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Times Table</th>
              <th>Attempt</th>
              <th>Percentage</th>
              <th>Seconds<th>
            <tr>
          </thead>
          <tbody>
            <?php foreach ($scores as $score) { ?>
              <tr>
                <td><?= $this->esc($score['quiz_date']); ?></td>
                <td><?= $this->esc($score['title']); ?></td>
                <td><?= $this->esc($score['attempt']); ?></td>
                <td><?= $this->esc($score['percent']); ?>%</td>
                <td><?= $this->esc($score['time_in_seconds']); ?></td>
              <tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
<?php
include __DIR__.'/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
