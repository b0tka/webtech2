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
    $editor_data = htmlspecialchars($_POST[ 'mailBodyTextArea' ]);

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
            $csv_header = array();
            $index = 0;

            if (($handle = fopen($csv_input, "r")) !== FALSE) {
                /*Each one 'while' cycle generates one mail body for the given user from csv,
                so the specified mail should be sent at the end of while block. The place for
                mail sending code is marked via comment block.*/
                while (($data = fgetcsv($handle, 1000, $_POST["delimiter-mail"])) !== FALSE) {
                    /*The first row is the coulmn header*/
                    if($index == 0) {
                        $csv_header = $data;
                    }
                    /*Columns will be checked using header data whether they are present in
                    the editor data. If yes, they will be replaced and save to $mail_body variable
                    */
                    else{
                        /*Mail body always gets the original value of text from editor, becuase
                        every mail body needs the plain starter text, that could be filled with
                        data*/
                        $mail_body = $editor_data;
                        for ($i = 0; $i < sizeof($data); $i++) {
                            if(strpos($mail_body, "{{".$csv_header[$i]."}}")){
                                $mail_body = str_replace("{{".$csv_header[$i]."}}", $data[$i], $mail_body);
                            }
                            if($csv_header[$i] === 'email' or $csv_header[$i] === 'Email') {
                                $destMail = $data[$i];
                            }
                            if($csv_header[$i] === 'login' or $csv_header[$i] === 'Login') {
                                $ais_login = $data[$i];
                            }
                        }
                        /*Inserting the name of the sender to the and of the mail*/
                        $mail_body = str_replace("{{sender}}", $name, $mail_body);

                    }

                    /*INSERT HERE THE PHPMAILER CODE*/

                    // Load Composer's autoloader
                    require 'vendor/autoload.php';

                    /*Getting the file that is required to be sent as attachment*/
                    $file_tmp  = $_FILES['mailAttachment']['tmp_name'];
                    $file_name = $_FILES['mailAttachment']['name'];

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
                        $mail->setLanguage('sk');
                        $mail->CharSet="UTF-8";

                        //Recipients
                        $mail->setFrom($email, $name);
                        //    $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
                        $mail->addAddress($destMail);               // Name is optional

                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = $subject;
                        $mail->AddAttachment($file_tmp, $file_name);
                        $mail->Body = html_entity_decode($mail_body);
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send();

                        /*Upload history logs of sending email into mail_history table in projekt-uloha1 database*/
                        $date_time = date('Y-m-d H:i:s', time() + 2 * 3600);
                        $sql = "INSERT INTO mail_history (date, username, subject, template_id)
                                        VALUES ('$date_time', '$ais_login', '$subject', '$template_id')";
                        if ($conn->query($sql)) {
                            //echo 'Message has been sent';
                        }
                    } catch (Exception $e) {
                        //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                    $index++;

                }
                fclose($handle);
            }
        }
    }

    //echo $errormsg;
    echo "<script>window.location = 'admin-index.php'</script>";
}