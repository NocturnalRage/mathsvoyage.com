      <div class="row justify-content-center">
        <div class="col-md-6 text-center">
          <?php $this->crsfToken(); ?>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="title" id="title" value="<?php if (isset($formVars['title'])) {
                echo $this->esc($formVars['title']);
            } ?>" maxlength="100" required autofocus>
            <label for="title">Title</label>
          </div>
          <?php if (isset($errors['title'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['title']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <select class="form-select" aria-label="Select topic" name="topic_id" id="topic_id">
              <?php foreach ($topics as $topic) { ?>
                <option <?php if ($topic['topic_id'] == ($formVars['topic_id'] ?? '')) {
                    echo 'selected';
                } ?> value="<?= $this->esc($topic['topic_id']); ?>"><?= $this->esc($topic['curriculum_name'].' - '.$topic['title']); ?></option>
              <?php } ?>
            </select>
          </div>
          <?php if (isset($errors['topic_id'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['topic_id']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <input type="number" class="form-control" name="learning_order" id="learning_order" value="<?php if (isset($formVars['learning_order'])) {
                echo $this->esc($formVars['learning_order']);
            } ?>" min="1" max="1000" required>
            <label for="learning_order">Learning Order</label>
          </div>
          <?php if (isset($errors['learning_order'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['learning_order']); ?></div>
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
            <?= $this->esc($submitButtonText); ?> Skill
          </button>
        </div>
      </div>
    </form>

