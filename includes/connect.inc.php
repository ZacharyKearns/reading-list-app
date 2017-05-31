<?php

// connect to the database, store the connection
// info in the $db variable
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die(mysqli_connect_error());
