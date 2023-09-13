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
            <input type="text" class="form-control" name="youtube_id" id="youtube_id" value="<?php if (isset($formVars['youtube_id'])) {
                echo $this->esc($formVars['youtube_id']);
            } ?>" maxlength="100" required autofocus>
            <label for="youtube_id">YouTube ID</label>
          </div>
          <?php if (isset($errors['youtube_id'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['youtube_id']); ?></div>
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
            <?= $this->esc($submitButtonText); ?> Video
          </button>
        </div>
      </div>
    </form>

