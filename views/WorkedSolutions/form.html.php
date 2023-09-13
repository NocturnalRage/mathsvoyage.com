      <div class="row justify-content-center">
        <div class="col-md-6 text-center">
          <?php $this->crsfToken(); ?>
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
            <?php if (isset($workedSolution['question'])) { ?>
              <img src="/uploads/images/worked-questions/<?= $this->esc($workedSolution['question']); ?>" class="img-responsive" />
            <?php } ?>
            <input type="file"
                   name="question"
                   id="quetion_image"
                   class="form-control"
                   <?php if (! isset($workedSolution['question'])) {
                       echo 'required';
                   } ?>
            />
            <label for="question" class="form-label">Question image (.jpg, or .png, and less than 8MB in size):</label>
            <?php if (isset($errors['question'])) { ?>
              <div class="alert alert-danger"><?= $this->esc($errors['question']); ?></div>
            <?php } ?>
          </div>

          <div class="form-floating mb-3">
            <?php if (isset($workedSolution['answer'])) { ?>
              <img src="/uploads/images/worked-questions/<?= $this->esc($workedSolution['answer']); ?>" class="img-responsive" />
            <?php } ?>
            <input type="file"
                   name="answer"
                   id="quetion_image"
                   class="form-control"
                   <?php if (! isset($workedSolution['answer'])) {
                       echo 'required';
                   } ?>
            />
            <label for="answer" class="form-label">Solution image (.jpg, or .png, and less than 8MB in size):</label>
            <?php if (isset($errors['answer'])) { ?>
              <div class="alert alert-danger"><?= $this->esc($errors['answer']); ?></div>
            <?php } ?>
          </div>

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
            <?= $this->esc($submitButtonText); ?> Worked Solution
          </button>
        </div>
      </div>
    </form>

