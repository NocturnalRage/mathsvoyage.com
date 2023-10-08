<?php include __DIR__.'/formQuestion.html.php'; ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="answer" id="answer" value="<?php if (isset($formVars['answer'])) {
                echo $this->esc($formVars['answer']);
            } ?>" maxlength="1000" required>
            <label for="answer">Answer</label>
          </div>
          <?php if (isset($errors['answer'])) { ?>
            <div class="alert alert-danger"><?= $this->esc($errors['answer']); ?></div>
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
            <?= $this->esc($submitButtonText); ?> Skill Kas Answer Question
          </button>
        </div>
      </div>
    </form>

