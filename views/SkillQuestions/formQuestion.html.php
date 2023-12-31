      <div class="row justify-content-center">
        <div class="col-md-6 text-center">
          <?php $this->crsfToken(); ?>
          <div class="mb-3">
            <label for="question" class="form-label">Question</label>
            <textarea class="form-control" name="question" id="question" rows="4" required><?= $this->esc($formVars['question'] ?? '<p class="stem">Apply the distributive property to factor out the greatest common factor.</p>
{IMAGE}
<p>\(60m - 40\) = {MATHFIELD}</p>') ?></textarea>

          </div>
          <?php if (isset($errors['question'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['question']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <select class="form-select" aria-label="Select skill" name="skill_id" id="skill_id">
              <?php foreach ($skills as $skill) { ?>
                <option <?php if ($skill['skill_id'] == ($formVars['skill_id'] ?? '')) {
                    echo 'selected';
                } ?> value="<?= $this->esc($skill['skill_id']); ?>"><?= $this->esc($skill['curriculum_name'].' - '.$skill['topic_title'].' - '.$skill['title']); ?></option>
              <?php } ?>
            </select>
            <label for="skill_id" class="form-label">Skill</label>
          </div>
          <?php if (isset($errors['skill_id'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['skill_id']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <select class="form-select" aria-label="Select category" name="skill_question_category_id" id="skill_question_category_id">
              <?php foreach ($categories as $category) { ?>
                <option <?php if ($category['skill_question_category_id'] == ($formVars['skill_question_category_id'] ?? '')) {
                    echo 'selected';
                } ?> value="<?= $this->esc($category['skill_question_category_id']); ?>"><?= $this->esc($category['description']); ?></option>
              <?php } ?>
            </select>
            <label for="skill_question_category_id" class="form-label">Category</label>
          </div>
          <?php if (isset($errors['skill_question_category_id'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['skill_question_category_id']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <?php if (isset($skillQuestion['question_image'])) { ?>
              <img src="/uploads/skill-questions/<?= $this->esc($skillQuestion['question_image']); ?>" class="img-responsive" />
            <?php } ?>
            <input type="file"
                   name="question_image"
                   id="quetion_image"
                   class="form-control"
                   <?php if (! isset($skillQuestion['question_image'])) {
                       echo 'required';
                   } ?>
            />
            <label for="question_image" class="form-label">Question image (.jpg, or .png, and less than 8MB in size):</label>
            <?php if (isset($errors['question_image'])) { ?>
              <div class="alert alert-danger"><?= $this->esc($errors['question_image']); ?></div>
            <?php } ?>
          </div>


