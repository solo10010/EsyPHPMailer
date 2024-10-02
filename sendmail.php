<?php
$message = '';  // Сообщение для отображения результата
$log = '';      // Лог для вывода на экран

if (isset($_POST['email']) && !empty($_POST['email'])) {

  // Логирование параметров перед вызовом mail()
  $log .= "Отправка email на адрес: " . htmlspecialchars($_POST['email']) . "<br>";
  $log .= "Тема: " . htmlspecialchars($_POST['subject']) . "<br>";
  $log .= "Сообщение: " . nl2br(htmlspecialchars($_POST['body'])) . "<br><br>";

  // Попытка отправить почту
  if (mail($_POST['email'], $_POST['subject'], $_POST['body'], '')) {
    $message = "Письмо отправлено на <b>" . htmlspecialchars($_POST['email']) . "</b>.<br>";
  } else {
    // Если отправка не удалась, получаем последнюю ошибку
    $error = error_get_last();
    $message = "Не удалось отправить сообщение на <b>" . htmlspecialchars($_POST['email']) . "</b>.<br>";

    // Логируем ошибку
    if ($error) {
      $log .= "Ошибка: " . $error['message'] . "<br>";
    } else {
      $log .= "Неизвестная ошибка при отправке.<br>";
    }
  }

} else {
  if (isset($_POST['submit'])) {
    $message = "Не указан адрес email!<br>";
  }
}

if (!empty($message)) {
  $message .= "<br><br>\n";
}
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Mail test</title>
  </head>
  <body>
    <h2>Результат отправки почты</h2>
    <?php 
      // Выводим сообщение о результате отправки
      echo $message; 
      
      // Выводим логи
      if (!empty($log)) {
        echo "<h3>Логи:</h3>";
        echo $log;
      }
    ?>
    <form method="post" action="">
      <table>
        <tr>
          <td>e-mail</td>
          <td>
            <input name="email" value="<?php if (isset($_POST['email']) && !empty($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>">
          </td>
        </tr>
        <tr>
          <td>subject</td>
          <td><input name="subject"></td>
        </tr>
        <tr>
          <td>message</td>
          <td><textarea name="body"></textarea></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input type="submit" value="send" name="submit"></td>
        </tr>
      </table>
    </form>
  </body>
</html>
