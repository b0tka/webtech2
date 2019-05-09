<?php
if(!empty($_FILES)) {
    $csv = $_FILES['csv_file'];

    //echo $_FILES['csv_file']['tmp_name'];
    $file_target = "files/" . $csv['name'];

    if($csv['size'] < 2 * 1024 * 1024) {
        move_uploaded_file($csv['tmp_name'], $file_target);
    }

    $row = 1;
    if (($handle = fopen($file_target, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $num = count($data);
            echo "<p> $num fields in line $row: <br /></p>\n";
            $row++;
            for ($c=0; $c < $num; $c++) {
                echo $data[$c] . " ";
            }
        }
        fclose($handle);
    }
}

?>

<html>
<head>

</head>
<body>

<form action="admin.php" method="post" enctype="multipart/form-data"><br>
    Vyber školský rok:
    <select name="year">
        <option value="2016">2016</option>
        <option value="2017">2017</option>
        <option value="2018">2018</option>
        <option value="2019">2019</option>
    </select><br>
    Zadaj názov predmetu:
    <input type="text" name="subject"><br>

    Oddeľovač v CSV súbore:blablabla
    <select name="delimiter">
        <option value="period">.</option>
        <option value="semicolon">;</option>
    </select><br>

    Vyber CSV súbor s výsledkami:<br>
    <input type="file" name="csv_file"><br>

    <input type="submit" value="Upload" name="submit">
</form>

</body>
</html>