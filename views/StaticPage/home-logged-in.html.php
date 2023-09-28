<?php
include __DIR__.'/../layout/header.html.php';
include __DIR__.'/../layout/navbar.html.php';
?>
  <div class="container">
    <?php include __DIR__.'/../layout/flash.html.php'; ?>
    <div class="row">
      <div class="col-md-12">
        <h1>Your Quiz Results</h1>
        <?php if ($quizResults) { ?>
          <table class="table">
            <thead>
              <tr>
                <th>Curriculum</th>
                <th>Topic</th>
                <th>Skill</th>
                <th>Correct</th>
                <th>Total Questions</th>
                <th>Percentage</th>
              <tr>
            </thead>
            <tbody>
              <?php foreach ($quizResults as $quizResult) { ?>
                <tr>
                  <td><?= $this->esc($quizResult['curriculum_name']); ?></td>
                  <td><?= $this->esc($quizResult['topic_title']); ?></td>
                  <td><?= $this->esc($quizResult['skill_title']); ?></td>
                  <td><?= $this->esc($quizResult['correct_unaided']); ?></td>
                  <td><?= $this->esc($quizResult['question_count']); ?></td>
                  <td><?= $this->esc($quizResult['percentage']); ?>%</td>
                <tr>
              <?php } ?>
            </tbody>
          </table>
        <?php } else { ?>
          <p>You have not attempted any quizzes yet. Once you do your results will be displayed here.</p>
        <?php } ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <h1>Your Times Tables Results</h1>
         <a href="/times-tables/quiz" class="btn btn-primary">Start Next Times Tables Attempt</a>
        <?php if ($ttResults) { ?>
          <table class="table">
            <thead>
              <tr>
                <th>Questions Correct</th>
                <th>Total Questions Attempted</th>
                <th>Percent Correct</th>
              <tr>
            </thead>
            <tbody>
              <tr>
                <td><?= $this->esc($ttResults['correct']); ?></td>
                <td><?= $this->esc($ttResults['attempted']); ?></td>
                <td><?= $this->esc($ttResults['percent']); ?>%</td>
              <tr>
            </tbody>
          </table>
        <?php } else { ?>
          <p>You have not attempted any times tables questions yet. Once you do your results will be displayed here.</p>
        <?php } ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <h1>Your General Arithmetic Results</h1>
        <a href="/general-arithmetic/quiz" class="btn btn-primary">Start Next General Arithmetic Attempt</a>
        <?php if ($gaResults) { ?>
          <table class="table">
            <thead>
              <tr>
                <th>Questions Correct</th>
                <th>Total Questions Attempted</th>
                <th>Percent Correct</th>
              <tr>
            </thead>
            <tbody>
              <tr>
                <td><?= $this->esc($gaResults['correct']); ?></td>
                <td><?= $this->esc($gaResults['attempted']); ?></td>
                <td><?= $this->esc($gaResults['percent']); ?>%</td>
              <tr>
            </tbody>
          </table>
        <?php } else { ?>
          <p>You have not attempted any general arithmetic questions yet. Once you do your results will be displayed here.</p>
        <?php } ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <a class="btn btn-primary" href="/arithmetic">Practice your fundamentals</a>
        <a class="btn btn-success" href="/curriculum">Dive into our lessons</a>
      </div>
    </div>
    <div class="row my-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <figure>
              <blockquote class="blockquote">
                <p>
                  <i class="bi bi-quote"></i>
                  We act as though comfort and luxury were the chief requirements of life. All that we need to make us happy is something to be enthusiastic about.
                </p>
              </blockquote>
              <figcaption class="blockquote-footer">
                Albert Einstein
              </figcaption>
            </figure>
          </div>
        </div>
      </div>
    </div>
    <?php include __DIR__.'/../layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
