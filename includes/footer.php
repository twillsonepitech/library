<p?php
session_start();

if (!empty($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
}

?>

<div class="footer-basic">
    <footer>
        <div class="social">
            <a href="https://www.instagram.com/uniklmiit/" target="_blank"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://www.linkedin.com/company/universiti-kuala-lumpur-unikl/" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
            <a href="https://twitter.com/home" target="_blank"><i class="fa-brands fa-twitter"></i></a>
            <a href="https://miit.unikl.edu.my/" target="_blank"><i class="fa-solid fa-globe"></i></a>
        </div>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="<?php echo "/library/books.php?user_id=$user_id" ?>">Home</a></li>
            <li class="list-inline-item"><a href="#">About</a></li>
            <li class="list-inline-item"><a href="#">Contact</a></li>
            <li class="list-inline-item"><a href="/library/admin/admin.php">Admin</a></li>
            <li class="list-inline-item"><a href="/library/logout.php">Logout</a></li>
        </ul>
        <p class="copyright">Designed by <a style="color: #ff8c6b;">Thomas Matthieu Willson</a> and <a style="color: #ff8c6b;">Yna Batrisyia Binti Mohamed Nor</a><br>&copy; Copyright 2024</p>
    </footer>
</div>