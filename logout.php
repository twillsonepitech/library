<?php
include 'includes/db.php';

session_unset();
session_destroy();

header("Location: /library/index.php");
exit();