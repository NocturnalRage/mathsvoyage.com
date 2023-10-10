<?php include __DIR__.'/formQuestion.html.php'; ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="answer0" id="answer0" value="<?php if (isset($formVars['answer0'])) {
                echo $this->esc($formVars['answer0']);
            } ?>" maxlength="1000" required>
            <label for="answer0">Answer</label>
          </div>
          <?php if (isset($errors['answer0'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['answer0']); ?></div>
          <?php } ?>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="y" id="form0" name="form0" checked>
              <label class="form-check-label" for="form0">
                Must be same form
              </label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="y" id="simplify0" name="simplify0" checked>
              <label class="form-check-label" for="simplify0">
                Must be simplified
              </label>
            </div>
          </div>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="answer1" id="answer1" value="<?php if (isset($formVars['answer1'])) {
                echo $this->esc($formVars['answer1']);
            } ?>" maxlength="1000" required>
            <label for="answer1">Answer</label>
          </div>
          <?php if (isset($errors['answer1'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['answer1']); ?></div>
          <?php } ?>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="y" id="form1" name="form1" checked>
              <label class="form-check-label" for="form1">
                Must be same form
              </label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="y" id="simplify1" name="simplify1" checked>
              <label class="form-check-label" for="simplify0">
                Must be simplified
              </label>
            </div>
          </div>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="answer2" id="answer2" value="<?php if (isset($formVars['answer2'])) {
                echo $this->esc($formVars['answer2']);
            } ?>" maxlength="1000" required>
            <label for="answer2">Answer</label>
          </div>
          <?php if (isset($errors['answer2'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['answer1']); ?></div>
          <?php } ?>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="y" id="form2" name="form2" checked>
              <label class="form-check-label" for="form2">
                Must be same form
              </label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="y" id="simplify2" name="simplify2" checked>
              <label class="form-check-label" for="simplify2">
                Must be simplified
              </label>
            </div>
          </div>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="answer3" id="answer3" value="<?php if (isset($formVars['answer3'])) {
                echo $this->esc($formVars['answer3']);
            } ?>" maxlength="1000" required>
            <label for="answer3">Answer</label>
          </div>
          <?php if (isset($errors['answer3'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['answer3']); ?></div>
          <?php } ?>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="y" id="form3" name="form3" checked>
              <label class="form-check-label" for="form3">
                Must be same form
              </label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="y" id="simplify3" name="simplify3" checked>
              <label class="form-check-label" for="simplify3">
                Must be simplified
              </label>
            </div>
          </div>

<?php include __DIR__.'/hints.html.php'; ?>


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
            <?= $this->esc($submitButtonText); ?> Skill Kas Answer Question
          </button>
        </div>
      </div>
    </form>

