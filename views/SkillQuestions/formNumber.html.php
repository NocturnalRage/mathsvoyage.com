      <div class="row justify-content-center">
        <div class="col-md-6 text-center">
          <?php $this->crsfToken(); ?>
          <div class="mb-3">
            <label for="question" class="form-label">Question</label>
            <textarea class="form-control" name="question" id="question" rows="4" required><?= $this->esc($formVars['question'] ?? '') ?></textarea>

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
          <?php if (isset($errors['topic_id'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['topic_id']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <?php if (isset($skillQuestion['question_image'])) { ?>
              <img src="/uploads/images/skill-questions/<?= $this->esc($skillQuestion['question_image']); ?>" class="img-responsive" />
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

          <div class="form-floating mb-3">
            <input type="number" class="form-control" name="answer" id="answer" value="<?php if (isset($formVars['answer'])) {
                echo $this->esc($formVars['answer']);
            } ?>" maxlength="1000" required autofocus>
            <label for="answer">Answer</label>
          </div>
          <?php if (isset($errors['answer'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['answer']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="hint1" id="hint1" value="<?php if (isset($formVars['hint1'])) {
                echo $this->esc($formVars['hint1']);
            } ?>" maxlength="1000" required autofocus>
            <label for="hint1">Hint 1</label>
          </div>
          <?php if (isset($errors['hint1'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['hint1']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="hint2" id="hint2" value="<?php if (isset($formVars['hint2'])) {
                echo $this->esc($formVars['hint2']);
            } ?>" maxlength="1000" required autofocus>
            <label for="hint2">Hint 2</label>
          </div>
          <?php if (isset($errors['hint2'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['hint2']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="hint3" id="hint3" value="<?php if (isset($formVars['hint3'])) {
                echo $this->esc($formVars['hint3']);
            } ?>" maxlength="1000" required autofocus>
            <label for="hint3">Hint 3</label>
          </div>
          <?php if (isset($errors['hint3'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['hint3']); ?></div>
          <?php } ?>

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
            <?= $this->esc($submitButtonText); ?> Skill Number Question
          </button>
        </div>
      </div>
    </form>

