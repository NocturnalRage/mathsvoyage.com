<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>General Arithmetic</h1>
        <?php
           include __DIR__.'/../layout/flash.html.php';
?>
        <hr />
        <a href="/general-arithmetic/quiz" class="btn btn-primary">Start Next Attempt</a>
        <hr />
        <h1>Scores</h1>
        <table class="table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Correct</th>
              <th># Questions</th>
              <th>Percentage</th>
              <th>Seconds<th>
            <tr>
          </thead>
          <tbody>
            <?php foreach ($scores as $score) { ?>
              <tr>
                <td><?= $this->esc($score['quiz_date']); ?></td>
                <td><?= $this->esc($score['correct']); ?></td>
                <td><?= $this->esc($score['question_count']); ?></td>
                <td><?= $this->esc($score['percentage']); ?>%</td>
                <td><?= $this->esc($score['seconds']); ?></td>
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
