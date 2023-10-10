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
