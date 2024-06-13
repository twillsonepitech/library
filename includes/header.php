<?php
session_start();

if (!empty($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
}

?>

<header class="header">
    <div class="logo">
        <h4><a class="title" href="<?php echo "/library/books.php?user_id=$user_id" ?>">Book Library</a></h4>
    </div>
    <nav class="navbar">
        <ul>
            <li><a href="<?php echo "/library/books.php?user_id=$user_id" ?>">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="/library/admin/admin.php" target="_blank">Admin</a></li>
            <li><a href="/library/logout.php">Logout</a></li>
        </ul>
    </nav>
    <input style="position: relative; top: 15px; width: 100px;" id="buy-button" type="submit" value="Order" class="sign-btn" />
</header>
