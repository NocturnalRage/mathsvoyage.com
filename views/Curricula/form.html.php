      <div class="row justify-content-center">
        <div class="col-md-6 text-center">
          <?php $this->crsfToken(); ?>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="curriculum_name" id="curriculum_name" value="<?php if (isset($formVars['curriculum_name'])) {
                echo $this->esc($formVars['curriculum_name']);
            } ?>" maxlength="25" required autofocus>
            <label for="curriculum_name">Curriculum Name</label>
          </div>
          <?php if (isset($errors['curriculum_name'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['curriculum_name']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <input type="number" class="form-control" name="display_order" id="display_order" value="<?php if (isset($formVars['display_order'])) {
                echo $this->esc($formVars['display_order']);
            } ?>" min="1" max="255" required>
            <label for="display_order">Display Order</label>
          </div>
          <?php if (isset($errors['display_order'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['display_order']); ?></div>
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
            <?= $this->esc($submitButtonText); ?> Curriculum
          </button>
        </div>
      </div>
    </form>

