<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load 
require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP

        // Set the SMTP server to send through
        $mail->Host       = 'mail.example.com';

        // Set connection type based on user selection
        if ($_POST['connection_type'] == 'unsecured') {
            $mail->SMTPSecure = false;
            $mail->Port       = 25;
            $connectionType = 'unsecured';
        } elseif ($_POST['connection_type'] == 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $connectionType = 'SSL (SMTPS)';
        } elseif ($_POST['connection_type'] == 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $connectionType = 'TLS (STARTTLS)';
        }

        //Enable SMTP authentication
        $mail->SMTPAuth   = true;
        //SMTP username
        $mail->Username   = 'portal@example.com';
        //SMTP password
        $mail->Password   = 'secret';
        //Recipients
        $mail->setFrom($_POST['from_email'], 'Mailer');
        $mail->addAddress($_POST['to_email']);                     //Add a recipient

        //Content
        $mail->isHTML(true);                                      //Set email format to HTML
        $mail->Subject = $_POST['subject'];
        $mail->Body    = $_POST['message'];

        // Attach file if provided
        if(isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            $mail->addAttachment($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);
        }

        // Send the email
        $mail->send();
        echo 'Message has been sent using ' . $connectionType;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отправка письма</title>
</head>
<body>
    <h2>Отправка письма</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <label for="from_email">От:</label><br>
        <input type="email" id="from_email" name="from_email" required><br><br>

        <label for="to_email">Кому:</label><br>
        <input type="email" id="to_email" name="to_email" required><br><br>

        <label for="subject">Тема:</label><br>
        <input type="text" id="subject" name="subject" required><br><br>

        <label for="message">Сообщение:</label><br>
        <textarea id="message" name="message" rows="4" required></textarea><br><br>

        <label for="attachment">Прикрепить файл:</label><br>
        <input type="file" id="attachment" name="attachment"><br><br>

        <input type="radio" id="unsecured" name="connection_type" value="unsecured" checked>
        <label for="unsecured">Unsecured port 25</label><br>

        <input type="radio" id="ssl" name="connection_type" value="ssl">
        <label for="ssl">SSL (SMTPS) port 465</label><br>

        <input type="radio" id="tls" name="connection_type" value="tls">
        <label for="tls">TLS (STARTTLS) port 587</label><br><br>

        <input type="submit" value="Отправить">
    </form>
</body>
</html>
<?php
// Функция для проверки доступности порта на заданном IP
function checkPort($ip, $port) {
    // Попытка установить соединение с портом
    $connection = @fsockopen($ip, $port, $errno, $errstr, 2);
    if ($connection) {
        // Порт доступен
        fclose($connection);
        return true;
    } else {
        // Порт недоступен
        return false;
    }
}

// IP-адрес для проверки
$ip = '8.2.9.2';

// Порты для проверки
$ports = array(25, 465, 587);

// Проверяем каждый порт
foreach ($ports as $port) {
    if (checkPort($ip, $port)) {
        echo "Порт $port доступен<br>";
    } else {
        echo "Порт $port недоступен<br>";
    }
}
?>
