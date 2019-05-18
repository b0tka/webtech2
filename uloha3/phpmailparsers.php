<?php
/**
 * Created by PhpStorm.
 * User: Stefan Renczes
 * Date: 5/18/2019
 * Time: 13:52
 */

/*This file can be removed after managing the parsing*/
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
                        $mail_body = $editor_data;
                        for ($i = 0; $i < sizeof($data); $i++) {
                            if(strpos($mail_body, "{{".$csv_header[$i]."}}")){
                                $mail_body = str_replace("{{".$csv_header[$i]."}}", $data[$i], $mail_body);
                            }
                        }
                        $mail_body = str_replace("{{sender}}", $name, $mail_body);

                        //echo $mail_body ."<br><br>";
                    }
                    $index++;
                    echo $mail_body;
                    /*INSERT HERE THE PHPMAILER CODE*/

                    /*------------------------------*/

                }
            }
        }
    }
}

