<?php

if (isset($_POST["books"])) {
    header("Location: /library/admin/books.php");
    exit;
} elseif (isset($_POST["users"])) {
    header("Location: /library/admin/users.php");
    exit;
} elseif (isset($_POST["rents"])) {
    header("Location: /library/admin/rents.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/adminform.css">
</head>

<body>
    <form class="form-control" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <button type="submit" name="books" class="submit-btn">Books Management&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa-solid fa-arrow-right-long"></i></button>
        <button type="submit" name="users" class="submit-btn">Users Management&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa-solid fa-arrow-right-long"></i></button>
        <button type="submit" name="rents" class="submit-btn">Rents Management&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa-solid fa-arrow-right-long"></i></button>
    </form>
</body>

</html>