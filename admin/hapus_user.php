<?php

include "auth_admin.php";
include "../config/koneksi.php";

$id=$_GET['id'];

mysqli_query($conn,

"DELETE FROM users
WHERE id='$id'"

);

header("Location: user.php");

?>