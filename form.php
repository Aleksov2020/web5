<html lang="ru">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Задание 5</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.2/css/bulma.min.css">
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <form id="form" action="" method="POST">
      <!-- Вывод сообщений об отправке -->
      <?php
      if ($messages['save'] != '') {
        print '<div class="notification has-text-primary">'.$messages["save"].'</div>';
      } else if ($messages['notsave'] != '') {
        print '<div class="notification has-text-danger">'.$messages["notsave"].'</div>';
      }
      ?>
      <!-- Имя -->
      <div class="field">
        <label for="nameInput" class="label">Имя</label>
        <div class="control">
          <input id="nameInput" class="input <?php if ($errors['name']) print 'is-danger'; else print 'is-info' ?>" type="text" name="name" placeholder="Ваше имя" value="<?php print $values['name']; ?>" />
        </div>
        <?php print '<p class="help is-danger">'.$messages['name'].'</p>'; ?>
      </div>
      <!-- ./Имя -->
      <!-- Email -->
      <div class="field">
        <label for="emailInput" class="label">Email</label>
        <div class="control">
          <input id="emailInput" class="input <?php if ($errors['email']) print 'is-danger'; else print 'is-info' ?>" type="email" name="email" placeholder="Ваше Email" value="<?php print $values['email']; ?>" />
        </div>
        <?php print '<p class="help is-danger">'.$messages['email'].'</p>'; ?>
      </div>
      <!-- ./Email -->
      <!-- Year -->
      <div class="field">
        <label for="yearSelect" class="label">Год рождения</label>
        <div class="control">
          <div class="select is-info">
            <select name="year" id="yearSelect">
              <?php
              for ($i = 2014; $i > 1955; $i--) {
                print('<option value="'.$i.'"');
                if ($values['year'] == $i) {
                  print('selected');
                }
                print('>'.$i.'</option>');
              }
              ?>
            </select>
          </div>
        </div>
      </div>
      <!-- ./Year -->
      <!-- Sex -->
      <div class="field">
        <label class="label">Пол</label>
        <div class="control">
          <label class="radio">
            <input type="radio" name="sex" value="male" <?php if ($values['sex'] == 'male') print(' checked'); ?> />
            Мужской
          </label>
          <label class="radio">
            <input type="radio" name="sex" value="female" <?php if ($values['sex'] == 'female') print(' checked'); ?> />
            Женский
          </label>
        </div>
      </div>
      <!-- ./Sex -->
      <!-- Limbs -->
      <div class="field">
        <label class="label">Количество конечностей</label>
        <div class="control">
          <label class="radio">
            <input type="radio" name="limbs" value="2" <?php if ($values['limbs'] == 2) print(" checked "); ?> />
            2
          </label>
          <label class="radio">
            <input type="radio" name="limbs" value="4" <?php if ($values['limbs'] == 4) print(" checked "); ?>  />
            4
          </label>
          <label class="radio">
            <input type="radio" name="limbs" value="8" <?php if ($values['limbs'] == 8) print(" checked "); ?>  />
            8
          </label>
        </div>
      </div>
      <!-- ./Limbs -->
      <!-- Powers -->
      <div class="field">
        <label for="limbsSelect" class="label">Сверхспособности</label>
        <div class="control">
          <div class="select is-multiple <?php if ($errors['powers']) print 'is-danger'; else print 'is-info' ?>">
            <select id="limbsSelect" name="powers[]" multiple size="3">
            <?php
            foreach ($powers as $key => $value) {
              $selected = empty($values['powers'][$key]) ? '' : ' selected="selected" ';
              printf('<option value="%s",%s>%s</option>', $key, $selected, $value);
            }
            ?>
            </select>
          </div>
        </div>
        <?php print '<p class="help is-danger">'.$messages['powers'].'</p>'; ?>
      </div>
      <!-- ./Powers -->
      <!-- Bio -->
      <div class="field">
        <label for="bioText" class="label">Биография</label>
        <div class="control">
          <textarea id="bioText" name="bio" class="textarea <?php if ($errors['bio']) print 'is-danger'; else print 'is-info' ?>" placeholder="Напишите здесь немного о себе..."><?php print $values['bio']; ?></textarea>
        </div>
        <?php print '<p class="help is-danger">'.$messages['bio'].'</p>'; ?>
      </div>
      <!-- ./Bio -->
      <!-- Checkbox -->
      <div class="field">
        <div class="control">
          <label class="checkbox">
            <input type="checkbox" name="check" value="ok">
              С <a href="#" class="has-text-info">контрактом</a> ознакомлен(а).
          </label>
        </div>
        <?php print '<p class="help is-danger">'.$messages['check'].'</p>'; ?>
      </div>
      <!-- ./Checkbox -->
      <!-- Button -->
      <div class="control">
        <button id="btn" type="submit" class="button is-light is-info">Отправить</button>
      </div>
      <!-- ./Button -->
    </form>
    <!-- ./Form -->
  </body>
</html>
