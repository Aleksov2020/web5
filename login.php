<?php

/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Начинаем сессию.
session_start();

$db_user = 'u16671'; // Логин БД
$db_pass = '3137204'; // Пароль БД

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) {
  header('Location: ./');
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>

<form action="" method="post">
  <input name="login" />
  <input name="pass" />
  <input type="submit" value="Войти" />
</form>

<?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {

  $login = $_POST['login'];
  $pass =  hash('sha256', $_POST['pass'], false);

  $db = new PDO('mysql:host=localhost;dbname=u16671', $db_user, $db_pass, array(
    PDO::ATTR_PERSISTENT => true
  ));

  try {
    $stmt = $db->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute(array(
      $login
    ));
    $user = $stmt->fetch();

    if ($pass == $user['pass']) {
      $_SESSION['login'] = $login;
    }
    else {
      echo "Неправильный логин или пароль";
      exit();
    }
  }
  catch(PDOException $e) {
    // При возникновении ошибки отправления в БД, выводим информацию
    echo "Ошибка";
    exit();
  }

  // Делаем перенаправление.
  header('Location: ./');
}
