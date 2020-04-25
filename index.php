<?php
/**
 * Задание 5. Овдиенко Александр. Группа 25/1.
 * Реализовать возможность входа с паролем и логином с использованием
 * сессии для изменения отправленных данных в предыдущей задаче,
 * пароль и логин генерируются автоматически при первоначальной отправке формы.
 */

// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Инициализируем переменные для подключения к базе данных.
$db_user = 'u16671';   // Логин БД
$db_pass = '3137204';  // Пароль БД

// Подключаемся к базе данных на сервере.
$db = new PDO('mysql:host=localhost;dbname=u16671', $db_user, $db_pass, array(
  PDO::ATTR_PERSISTENT => true
));

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();
  $messages['save'] = '';     // Сообщение об успешном отправлении данных
  $messages['notsave'] = '';  // Сообщение об ошибке отправления данных
  $messages['name'] = '';     // Сообщение об ошибке в имени
  $messages['email'] = '';    // Сообщение об ошибке в email
  $messages['powers'] = '';   // Сообщение об ошибке в способностях
  $messages['bio'] = '';      // Сообщение об ошибке в биографии
  $messages['check'] = '';    // Сообщение об ошибке в согласии с контрактом

  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  // Функция empty возвращает true, если кука пустая.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куки, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    // Выводим сообщение пользователю.
    $messages['save'] = 'Спасибо, результаты отправлены на сервер.';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['pass'])) {
      $messages['savelogin'] = sprintf(' Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['pass']));
    }
  }

  // Выдаем сообщение об ошибке сохранения.
  if (!empty($_COOKIE['notsave'])) {
    // Удаляем куки, указывая время устаревания в прошлом.
    setcookie('notsave', '', 100000);
    $messages['notsave'] = strip_tags($_COOKIE['notsave']);
  }

  // Складываем признаки ошибок в массив.
  $errors = array();
  // Используем тернарный оператор. Если кука с ошибкой пустая, то присваиеваем
  // пустую строку, иначе присваиеваем значение этой куки.
  $errors['name'] = empty($_COOKIE['name_error']) ? '' : $_COOKIE['name_error'];
  $errors['email'] = empty($_COOKIE['email_error']) ? '' : $_COOKIE['email_error'];
  $errors['powers'] = empty($_COOKIE['powers_error']) ? '' : $_COOKIE['powers_error'];
  $errors['bio'] = empty($_COOKIE['bio_error']) ? '' : $_COOKIE['bio_error'];
  $errors['check'] = empty($_COOKIE['check_error']) ? '' : $_COOKIE['check_error'];

  // Проверка на ошибки в имени.
  if ($errors['name'] == 'null') {
    setcookie('name_error', '', 100000);
    $messages['name'] = 'Заполните имя.';
  }
  else if ($errors['name'] == 'incorrect') {
      setcookie('name_error', '', 100000);
      $messages['name'] = 'Недопустимые символы. Введите имя заново.';
  }

  // Проверка ошибок в email.
  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    $messages['email'] = 'Заполните почту.';
  }

  // Проверка ошибок в способностях.
  if ($errors['powers']) {
    setcookie('powers_error', '', 100000);
    $messages['powers'] = 'Выберите хотя бы одну сверхспособность.';
  }

  // Проверка ошибок с биографии.
  if ($errors['bio']) {
    setcookie('bio_error', '', 100000);
    $messages['bio'] = 'Напишите что-нибудь о себе.';
  }

  // Проверка ошибок согласия с контрактом.
  if ($errors['check']) {
    setcookie('check_error', '', 100000);
    $messages['check'] = 'Вы не можете отправить форму не согласившись с контрактом!';
  }

  // Создаем массив для способностей.
  $powers = array();

  // Заполняем массив способностей. Ключ - будет отправляться в базу данных,
  // а значение по этому ключу будет отображаться в форме.
  $powers['tp'] = "Телепортация";
  $powers['levit'] = "Левитация";
  $powers['vision'] = "Ночное зрение";

  // Складываем предыдущие значения полей в массив, если есть.
  // При этом санитизуем все данные для безопасного отображения в браузере.
  $values = array();

  // Для этого используем тернарный оператор. Если кука пустая, присваиеваем
  // значение по умолчанию или пустую строку. Иначе присваем значение этой куки.
  $values['name'] = empty($_COOKIE['name_value']) ? '' : strip_tags($_COOKIE['name_value']);
  $values['email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
  $values['year'] = empty($_COOKIE['year_value']) ? '' : strip_tags($_COOKIE['year_value']);
  $values['sex'] = empty($_COOKIE['sex_value']) ? 'male' : strip_tags($_COOKIE['sex_value']);
  $values['limbs'] = empty($_COOKIE['limbs_value']) ? '4' : strip_tags($_COOKIE['limbs_value']);
  $values['bio'] = empty($_COOKIE['bio_value']) ? '' : strip_tags($_COOKIE['bio_value']);
  $powers_value = empty($_COOKIE['powers_value']) ? '' : json_decode($_COOKIE['powers_value']);

  // Для способностей инициализация происходит немного по-другому.
  // Необходимо в $values по ключу powers создать массив и заполнить его
  // значениями из массива $powers.
  $values['powers'] = [];

  // Проверяем является ли $powers_value массивом и он не равен null.
  if (isset($powers_value) && is_array($powers_value)) {
    foreach ($powers_value as $power) {
      if (!empty($powers[$power])) {
        $values['powers'][$power] = $power;
      }
    }
  }

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {

    $messages['save'] = ' ';
    $messages['savelogin'] = 'Вход с логином '.$_SESSION['login'];

    // Пытаемся достать данные пользователя из БД.
    try {
      // Подготавливаем SQL запрос, где вопросы заменятся на значения.
      // Запрос направляется в таблицу form5 с данными пользователей
      // и достает из нее все значения, где uid = логину пользователя.
      $stmt = $db->prepare("SELECT * FROM form5 WHERE uid = ?");
      // Заменяем вопросы на значения.
      $stmt->execute(array(
        $_SESSION['login']
      ));
      // Получаем данные в виде массива из БД.
      $user_data = $stmt->fetch();

      // Инициализируем $values значениями из массива полученного из БД
      // предварительно их санитизовав.
      $values['name'] = strip_tags($user_data['name']);
      $values['email'] = strip_tags($user_data['email']);
      $values['year'] = strip_tags($user_data['age']);
      $values['sex'] = strip_tags($user_data['sex']);
      $values['limbs'] = strip_tags($user_data['limbs']);
      $values['bio'] = strip_tags($user_data['bio']);
      $powers_value = explode(", ", $user_data['powers']);

      // Как и в предыдущем разе заполняем массив способностей.
      $values['powers'] = [];
      foreach ($powers_value as $power) {
        if (!empty($powers[$power])) {
          $values['powers'][$power] = $power;
        }
      }

    } catch(PDOException $e) {
      // При возникновении ошибки получения данных из БД, выводим информацию
      // об ошибке пользователю.
      setcookie('notsave', 'Ошибка: ' . $e->getMessage());
      // Прекращаем работу скрипта
      exit();
    }
  }

  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить
// в базу данных.
else {
  // Проверяем ошибки.
  // Для этого создаем логическую переменную и по умолчанию устанавливаем false.
  $errors = FALSE;
  if (empty($_POST['name'])) {
    // Выдаем куку на день с флажком об ошибке в поле name.
    setcookie('name_error', 'null', time() + 24 * 60 * 60);
    // Помечаем наличие ошибки.
    $errors = TRUE;
  }
  else if (!preg_match("#^[aA-zZ0-9-]+$#", $_POST["name"])) {
      setcookie('name_error', 'incorrect', time() + 24 * 60 * 60);
      $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('name_value', $_POST['name'], time() + 30 * 24 * 60 * 60);
  }

  // Аналогично проверяем ошибки для всех остальных полей.
  if (empty($_POST['email'])) {
    // Выдаем куку на день с флажком об ошибке в поле name.
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
  }

  // Для способностей опять другой метод проверки на ошибки.
  // Созадем массив способностей.
  $powers = array();

  // Заполняем его значениями из глобальной переменной $_POST.
  foreach ($_POST['powers'] as $key => $value) {
      $powers[$key] = $value;
  }

  // Если размер массива нулевой, значит пользователь не выбрал ни одной
  // способности. Соответственно, обрабатываем эту ошибку.
  if (!sizeof($powers)) {
    setcookie('powers_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('powers_value', json_encode($powers), time() + 30 * 24 * 60 * 60);
  }

  if (empty($_POST['bio'])) {
    // Выдаем куку на день с флажком об ошибке в поле name.
    setcookie('bio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('bio_value', $_POST['bio'], time() + 30 * 24 * 60 * 60);
  }

  if (empty($_POST['check'])) {
    // Выдаем куку на день с флажком об ошибке в поле name.
    setcookie('check_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }

  // Для значений, где ошибок не должно быть, т.к. там есть значения по
  // умолчанию просто создаем куки и заполняем их.
  setcookie('year_value', $_POST['year'], time() + 30 * 24 * 60 * 60);
  setcookie('sex_value', $_POST['sex'], time() + 30 * 24 * 60 * 60);
  setcookie('limbs_value', $_POST['limbs'], time() + 30 * 24 * 60 * 60);

  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }
  else {
    // Иначе удаляем Cookies с признаками ошибок.
    setcookie('name_error', '', 100000);
    setcookie('name_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('powers_error', '', 100000);
    setcookie('bio_error', '', 100000);
    setcookie('check_error', '', 100000);
  }

  // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
  if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {

    // Пытаемся отправить новые данные пользователя в базу данных.
    try {
      // Подготавливаем SQL запрос, где вопросы заменятся на значения.
      // Запрос направляется в таблицу form5 с данными пользователей
      // и обновляет значения, кроме uid, где uid = логину пользователя.
      $stmt = $db->prepare("UPDATE form5 SET name = ?, email = ?, age = ?, sex = ?, limbs = ?, powers = ?, bio = ? WHERE uid = ?");
      // Заменяем вопросы на значения.
      $stmt->execute(array(
        $_POST['name'],
        $_POST['email'],
        $_POST['year'],
        $_POST['sex'],
        $_POST['limbs'],
        implode(', ', $_POST['powers']),
        $_POST['bio'],
        $_SESSION['login']
      ));
    } catch(PDOException $e) {
      // При возникновении ошибки получения данных из БД, выводим информацию
      // об ошибке пользователю.
      setcookie('notsave', 'Ошибка: ' . $e->getMessage());
      // Прекращаем работу скрипта.
      exit();
    }

  }
  else {
    // Иначе, если отправляются новые данные.
    // Генерируем уникальный логин и пароль.
    $login = uniqid("id");
    $pass = rand(123456, 999999);
    // Сохраняем в Cookies.
    setcookie('login', $login);
    setcookie('pass', $pass);

    // Пытаемся отправить данные в базу данных.
    try {
      // Подготавливаем SQL запрос, где вопросы заменятся на значения.
      // Запрос направляется в таблицу form5 с данными пользователей.
      $stmt_form = $db->prepare("INSERT INTO form5 SET uid = ?, name = ?, email = ?, age = ?, sex = ?, limbs = ?, powers = ?, bio = ?");
      // Заменяем вопросы на значения.
      $stmt_form->execute(array(
        $login,
        $_POST['name'],
        $_POST['email'],
        $_POST['year'],
        $_POST['sex'],
        $_POST['limbs'],
        implode(', ', $_POST['powers']),
        $_POST['bio']
      ));
      // Подготавливаем еще один SQL запрос, где вопросы заменятся на значения.
      // Запрос направляется в таблицу users с данными пользователей.
      $stmt_user = $db->prepare("INSERT INTO users SET login = ?, pass = ?");
      $stmt_user->execute(array(
        $login,
        // В базу отправляем пароль шифруя его с помощью алгоритма sha256.
        hash('sha256', $pass, false)
      ));
    } catch(PDOException $e) {
      // При возникновении ошибки получения данных из БД, выводим информацию
      // об ошибке пользователю.
      setcookie('notsave', 'Ошибка: ' . $e->getMessage());
      // Прекращаем работу скрипта.
      exit();
    }
  }

  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: ./');
}
