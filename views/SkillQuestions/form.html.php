<?php include __DIR__.'/formQuestion.html.php'; ?>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="y" id="randomise_options" name="randomise_options" checked>
              <label class="form-check-label" for="randomise_options">
                Randomise Options
              </label>
            </div>
          </div>

          <div class="mb-3">
            <h5>Correct Option</h5>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="correctOption" id="correctOption1" value="1" checked>
              <label class="form-check-label" for="correctOption1">
                Option 1
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="correctOption" id="correctOption2" value="2">
              <label class="form-check-label" for="correctOption2">
                Option 2
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="correctOption" id="correctOption3" value="3">
              <label class="form-check-label" for="correctOption3">
                Option 3
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="correctOption" id="correctOption4" value="4">
              <label class="form-check-label" for="correctOption4">
                Option 4
              </label>
            </div>
          </div>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="option1" id="option1" value="<?php if (isset($formVars['option1'])) {
                echo $this->esc($formVars['option1']);
            } ?>" maxlength="1000" required autofocus>
            <label for="option1">Option 1</label>
          </div>
          <?php if (isset($errors['option1'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['option1']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="option2" id="option2" value="<?php if (isset($formVars['option2'])) {
                echo $this->esc($formVars['option2']);
            } ?>" maxlength="1000" required autofocus>
            <label for="option2">Option 2</label>
          </div>
          <?php if (isset($errors['option2'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['option2']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="option3" id="option3" value="<?php if (isset($formVars['option3'])) {
                echo $this->esc($formVars['option3']);
            } ?>" maxlength="1000" required autofocus>
            <label for="option3">Option 3</label>
          </div>
          <?php if (isset($errors['option3'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['option3']); ?></div>
          <?php } ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="option4" id="option4" value="<?php if (isset($formVars['option4'])) {
                echo $this->esc($formVars['option4']);
            } ?>" maxlength="1000" required autofocus>
            <label for="option4">Option 4</label>
          </div>
          <?php if (isset($errors['option4'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['option4']); ?></div>
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
            <?= $this->esc($submitButtonText); ?> Skill Question
          </button>
        </div>
      </div>
    </form>

