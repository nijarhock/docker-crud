<?php
include_once("conn.php");
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>TEST</title>
    </head>

    <body>
        <form action="axtambah.php" method="POST">
            <input type="text" id="name" name="name">
            <input type="submit" value="simpan">
        </form>

        <ul>
            <?php
            $q_cek = mysqli_query($conn, "select * from user");
            $r_cek = mysqli_num_rows($q_cek);

            for($i=0;$i<$r_cek;$i++)
            {
                $d_cek = mysqli_fetch_assoc($q_cek);
            ?>
            <li><?php echo $d_cek['name'] ?></li>
            <?php } ?>
        </ul>
    </body>
</html>