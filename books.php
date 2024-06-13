<?php
include('includes/db.php');
session_start();

if (isset($_GET["user_id"])) {
    $user_id = $_GET["user_id"];
    unset($_SESSION["user_id"]);
    $_SESSION["user_id"] = $user_id;
}
elseif (!empty($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book List</title>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/cards.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/toggle.css">
    <link rel="stylesheet" href="css/badge.css">
    <link rel="stylesheet" href="css/footer.css">
    <style>
        .gray-line {
            position: relative;
            top: 25px;
            border-top: 2px solid rgb(191 191 191);
            width: 100%;
        }
    </style>
</head>

<body class="blog-body">
    <?php include('includes/header.php'); ?>
    <div class="blog-slider">
        <div class="button-container">
            <button id="check-all-button" class="btn blue">Check all</button>
            <button id="uncheck-all-button" class="btn red">Uncheck all</button>
        </div>
        <div class="gray-line"></div>
        <div class="blog-slider__wrp swiper-wrapper">
            <?php
            $sql = "SELECT * FROM books";
            $result = $conn->query($sql);
            $books_images = array(
                "BC001" => "img/harrypotter.jpg",
                "BC002" => "img/la-promesse-de-l-aube.jpg",
                "BC003" => "img/Petit-Prince.jpg",
                "BC004" => "img/les-miserables.jpg",
                "BC005" => "img/etranger.jpg",
                "BC006" => "img/great-gatsby.jpg",
                "BC007" => "img/1948.jpg",
                "BC008" => "img/mockingbird.jpg",
                "BC009" => "img/hobbit.jpg",
                "BC010" => "img/mobydick.jpg",
            );
            while ($row = $result->fetch_assoc()) {
                $genres = explode(",", $row["genre"]); ?>
                <div class="blog-slider__item swiper-slide">
                    <div class="blog-slider__img">
                        <img src="<?php echo $books_images[$row["book_code"]] ?? "img/book.jpeg" ?>" alt="">
                    </div>
                    <div class="blog-slider__content">
                        <span class="blog-slider__code"><?php echo $row["book_code"]; ?></span>
                        <div style="display: inline">
                            <div style="display: inline-block;" class="blog-slider__title"><?php echo $row["name"]; ?></div>
                            <label class="toggle-switch">
                                <input type="checkbox" class="toggle-checkbox" data-book-id="<?php echo $row['book_id']; ?>">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="blog-slider__text"><?php echo $row["description"]; ?></div>
                        <div style="display: inline">
                            <div style="display: inline-block;" class="tags">
                                <?php foreach ($genres as $genre) : ?>
                                    <span class="badge grey"><?php echo htmlspecialchars($genre); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="flex">
                                <span style="position: relative; left: 15px; padding: 5px 10px;" class="badge custom-color">RM<?php echo htmlspecialchars($row["rental_price"]); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
        <div class="blog-slider__pagination"></div>
    </div>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var swiper = new Swiper('.blog-slider', {
                spaceBetween: 30,
                effect: 'fade',
                loop: true,
                mousewheel: {
                    invert: false,
                },
                pagination: {
                    el: '.blog-slider__pagination',
                    clickable: true,
                }
            });

            var selectedBooks = [];

            document.querySelectorAll('.toggle-checkbox').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    var bookId = this.getAttribute('data-book-id');
                    if (this.checked) {
                        selectedBooks.push(bookId);
                    } else {
                        var index = selectedBooks.indexOf(bookId);
                        if (index !== -1) {
                            selectedBooks.splice(index, 1);
                        }
                    }
                });
            });

            document.getElementById('buy-button').addEventListener('click', function() {
                if (selectedBooks.length === 0) {
                    Toast.fire({
                        icon: "warning",
                        html: "Please make sure to select at least one book !"
                    });
                } else {
                    var userId = "<?php echo $_SESSION["user_id"]; ?>";
                    window.open(`/library/order.php?books=${selectedBooks.join(',')}`, "_blank");
                }
            });

            document.getElementById('check-all-button').addEventListener('click', function() {
                document.querySelectorAll('.toggle-checkbox').forEach(function(checkbox) {
                    checkbox.checked = true;
                    var bookId = checkbox.getAttribute('data-book-id');
                    if (!selectedBooks.includes(bookId)) {
                        selectedBooks.push(bookId);
                    }
                });
            });

            document.getElementById('uncheck-all-button').addEventListener('click', function() {
                document.querySelectorAll('.toggle-checkbox').forEach(function(checkbox) {
                    checkbox.checked = false;
                });
                selectedBooks = [];
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
    <?php include('includes/footer.php'); ?>
</body>

</html>
