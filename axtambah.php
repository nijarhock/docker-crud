<?php
    include 'conn.php';
    $name = $_POST['name'];

    mysqli_query($conn, "insert into user set
                        name = '$name'");

    header('Location: '.WEB_URL);
?>