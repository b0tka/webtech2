<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['submitMail'])) {
    $passwd = htmlspecialchars($_POST['password']);
    $email = htmlspecialchars($_POST['email']);
    $username = substr($email, 0, strpos($email, "@"));
    $name = htmlspecialchars($_POST['name']);
    $subject = htmlspecialchars($_POST['subjectMail']);
    $template_id = htmlspecialchars($_POST['templateSelection']);

    if (!empty($_POST) && !empty($_FILES)) {

        $extension = pathinfo($_FILES["mail-file"]["name"], PATHINFO_EXTENSION);
        $errormsg = "";

        /*If file has no .csv extension, cannot be parsed*/
        if ($extension != "csv") {
            $errormsg = "Súbor nie je typu .CSV <br>";
        } /*Check whether the delimiter is set*/
        else if ($_POST["delimiter-mail"] == "none") {
            $errormsg = "Oddeľovač nebol vybraný<br>";
        } /*If everything is OK then continue*/
        else {
            /*Initializing variables for the file that is being parsed and
            declaring target path to which file will be saved*/
            $csv = $_FILES['mail-file'];
            $csv_input = "files/" . $csv['name'];

            /*Checking size of file and saving to the target folder*/
            if ($csv['size'] < 2 * 1024 * 1024) {
                move_uploaded_file($_FILES['mail-file']["tmp_name"], $csv_input);
            }

            include_once("../config.php");
            /*Setting upp connection to projekt-uloha1 database*/
            $conn = new mysqli($serverName, $userName, $password, $dbName);
            if ($conn->connect_error) {
                die("Connection to MySQL server failed. " . $conn->connect_error);
            }

            /*Setting up UTF8 for database entries*/
            $stmt = $conn->prepare("SET NAMES 'utf8';");
            $stmt->execute();

            /*Initializing array that will handle the file*/
            $csv_array = array();
            $index = 0;

            if (($handle = fopen($csv_input, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, $_POST["delimiter-mail"])) !== FALSE) {
                    /*The first row is the coulmn header*/
                    if ($index == 0) {
                        $nameOfFirstRow = $data[0];
                        $nameOfSecondRow = $data[1];
                        $nameOfThirdRow = $data[2];
                        $nameOfFourthRow = $data[3];
                        $nameOfFifthRow = $data[4];
                    } else {

                        $valueOfFirstRow = $data[0];
                        $valueOfSecondRow = $data[1];
                        $valueOfThirdRow = $data[2];
                        $valueOfFourthRow = $data[3];
                        $valueOfFifthRow = $data[4];


                        // Load Composer's autoloader
                        require 'vendor/autoload.php';

                        // Instantiation and passing `true` enables exceptions
                        $mail = new PHPMailer(true);

                        try {
                            //Server settings
                            $mail->SMTPDebug = 1;                                       // Enable verbose debug output
                            $mail->isSMTP();                                            // Set mailer to use SMTP
                            $mail->Host = 'mail.stuba.sk';                        // Specify main and backup SMTP servers
                            $mail->SMTPAuth = true;                                   // Enable SMTP authentication
                            $mail->Username = $username;                               // SMTP username
                            $mail->Password = $passwd;                        // SMTP password
                            $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
                            $mail->Port = 587;                                    // TCP port to connect to
                            $mail->Charset = 'utf-8';

                            //Recipients
                            $mail->setFrom($email, $name);
                            //    $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
                            $mail->addAddress($valueOfThirdRow);               // Name is optional
                            //    $mail->addReplyTo('info@example.com', 'Information');
                            //    $mail->addCC('cc@example.com');
                            //    $mail->addBCC('bcc@example.com');

                            // Attachments
                            //    $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                            //    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

                            // Content
                            $mail->isHTML(true);                                  // Set email format to HTML
                            $mail->Subject = $subject;
                            $mail->Body = $nameOfFirstRow . ' je ' . $valueOfFirstRow . "<br>" .
                                            $nameOfSecondRow . ' je ' . $valueOfSecondRow . "<br>";
                            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                            $mail->send();

                            /*Upload history logs of sending email into mail_history table in projekt-uloha1 database*/
                            $sql = "INSERT INTO mail_history (date, username, subject, template_id) 
                                        VALUES (now(), '$valueOfFourthRow', '$subject', '$template_id')";
                            if ($conn->query($sql)) {
                                echo 'Message has been sent';
                            }
                        } catch (Exception $e) {
                            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        }



                    }
                    $index++;
                }
                fclose($handle);
            }
        }
    }

echo $errormsg;
}