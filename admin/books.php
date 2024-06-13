<?php
include('../includes/db.php');

if (isset($_POST["submitBook"])) {
    $new_book_code = $_POST["new_book_code"];
    $new_name = $_POST["new_name"];
    $new_genre = implode(", ", $_POST["new_genre"]);
    $new_rental_price = $_POST["new_rental_price"];
    $new_description = $_POST["new_description"];

    $add_query = "INSERT INTO `books` (`book_code`, `name`, `genre`, `rental_price`, `description`) VALUES ('$new_book_code', '$new_name', '$new_genre', '$new_rental_price', '$new_description')";
    if ($conn->query($add_query) === TRUE) {
        echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Toast.fire({
                            icon: 'success',
                            html: 'New book has been added.'
                        });
                    });
                </script>";
    } else {
        echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Toast.fire({
                            icon: 'error',
                            text: 'Failed to add the new book.'
                        });
                    });
                </script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Management</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/datatable.css">
    <link rel="stylesheet" href="../css/badge.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="../css/input2.css">
    <link rel="stylesheet" href="../css/btn.css">
    <link rel="stylesheet" href="../css/MultiSelect.css" type="text/css">
</head>

<body>
    <div class="d-container">
        <div class="top">
            <input type="text" placeholder="Search by name..." class="search-bar">
            <div>
                <a href="/library/admin/choose.php"><button class="add-new-button blue">View All</button></a>
                <button type="button" name="submit" class="add-new-button green" onclick="openModal()">Add New</button>
            </div>
        </div>

        <form id="mainForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php
            if (!empty($_POST["delete"])) {
                $book_id = (int)$_POST["book_id"];
                $book_code = $_POST["book_code"];
                $deleteBookQuery = "DELETE FROM `books` WHERE `book_id`='$book_id'";
                if ($conn->query($deleteBookQuery) === TRUE) {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'success',
                                        html: 'Book Code <b>$book_code</b> has been deleted.'
                                    });
                                });
                            </script>";
                } else {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'error',
                                        text: 'Failed to delete the book.'
                                    });
                                });
                            </script>";
                }
            } elseif (!empty($_POST["edit"])) {
                $book_id = (int)$_POST["book_id"];
                $book_code = $_POST["book_code"];
                $name = $_POST["name"];
                $genre = $_POST["genre"];
                $rental_price = (float) substr($_POST["rental_price"], 2);
                $description = $_POST["description"];
                $updateBookQuery = "UPDATE `books` SET `book_code`='$book_code', `name`='$name', `genre`='$genre', `rental_price`='$rental_price', `description`='$description' WHERE `book_id`='$book_id'";
                if ($conn->query($updateBookQuery) === TRUE) {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'success',
                                        html: 'Book Code <b>$book_code</b> has been updated.'
                                    });
                                });
                            </script>";
                } else {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'error',
                                        text: 'Failed to update the book.'
                                    });
                                });
                            </script>";
                }
            }
            ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Book ID</th>
                            <th>Name</th>
                            <th>Genre</th>
                            <th>Rental Price</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
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
                        while ($row = $result->fetch_assoc()) { ?>
                            <tr data-book-id="<?php echo htmlspecialchars($row['book_id']); ?>">
                                <td>
                                    <span class="badge blue"><?php echo htmlspecialchars($row["book_id"]); ?></span>
                                </td>
                                <td>
                                    <img src="<?php echo "../" . ($books_images[$row["book_code"]] ?? "img/book.jpeg") ?>" alt="" class="avatar">
                                    <div class="info">
                                        <span class="name editable"><?php echo htmlspecialchars($row["name"]); ?></span>
                                        <span class="book_code editable"><?php echo htmlspecialchars($row["book_code"]); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="genre editable"><?php echo htmlspecialchars($row["genre"]); ?></div>
                                </td>
                                <td><span class="rental_price badge blue editable">RM<?php echo htmlspecialchars($row["rental_price"]); ?></span></td>
                                <td>
                                    <div class="description editable"><?php echo htmlspecialchars($row["description"]); ?></div>
                                </td>
                                <td>
                                    <i class="fa-solid fa-check save-row" style="cursor: pointer; display: none;"></i>
                                    <i class="fa-solid fa-xmark cancel-row" style="cursor: pointer; display: none;"></i>
                                    <i class="fa-solid fa-pencil edit-row" style="cursor: pointer;"></i>
                                    &nbsp;
                                    <button type="button" class="delete-button" data-book-code="<?php echo htmlspecialchars($row['book_code']); ?>" data-book-id="<?php echo htmlspecialchars($row['book_id']); ?>">
                                        <i class="fa-solid fa-trash" style="color: #ff0000;"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="book_id" id="book_id">
            <input type="hidden" name="delete" value="delete">
            <input type="hidden" name="edit" value="edit">
            <input type="hidden" name="book_code" id="book_code">
            <input type="hidden" name="name" id="name">
            <input type="hidden" name="genre" id="genre">
            <input type="hidden" name="rental_price" id="rental_price">
            <input type="hidden" name="description" id="description">
        </form>
    </div>
    <script>
        function openModal() {
            document.getElementById('addBook').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addBook').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('addBook')) {
                closeModal();
            }
        }
    </script>
    <div id="addBook" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <h2 style="color: black;">Enter New Book</h2>
            <br>
            <form style="margin: 0 0; max-width: none;" id="bookForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div style="display: flex;">
                    <div class="input-field">
                        <input class="input-1 input" type="text" id="new_book_code" name="new_book_code" value="<?php echo isset($_POST["new_book_code"]) ? $_POST["new_book_code"] : ""; ?>" required />
                        <label class="label" for="input"><i class="fa-solid fa-book"></i>&nbsp;&nbsp;Book Code</label>
                    </div>
                    <div style="margin-left: 35px;" class="input-field">
                        <input class="input-1 input" type="text" id="new_name" name="new_name" value="<?php echo isset($_POST["new_name"]) ? $_POST["new_name"] : ""; ?>" required />
                        <label class="label" for="input"><i class="fa-solid fa-user"></i>&nbsp;&nbsp;Name</label>
                    </div>
                </div><br>
                <div style="display: flex;">
                    <div class="input-field">
                        <select id="fruits" name="new_genre" data-placeholder="Genres" multiple data-multi-select>
                            <option value="Fantasy" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Fantasy' ? 'selected' : ''; ?>>Fantasy</option>
                            <option value="Adventure" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Adventure' ? 'selected' : ''; ?>>Adventure</option>
                            <option value="Autobiography" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Autobiography' ? 'selected' : ''; ?>>Autobiography</option>
                            <option value="Memoir" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Memoir' ? 'selected' : ''; ?>>Memoir</option>
                            <option value="Fiction" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Fiction' ? 'selected' : ''; ?>>Fiction</option>
                            <option value="Classic" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Classic' ? 'selected' : ''; ?>>Classic</option>
                            <option value="Drama" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Drama' ? 'selected' : ''; ?>>Drama</option>
                            <option value="Philosophical" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Philosophical' ? 'selected' : ''; ?>>Philosophical</option>
                            <option value="Tragedy" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Tragedy' ? 'selected' : ''; ?>>Tragedy</option>
                            <option value="Political" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Political' ? 'selected' : ''; ?>>Political</option>
                        </select>
                        <!-- <select class="input-1 input" id="new_genre" name="new_genre" required>
                            <option value="" disabled selected></option>
                            <option value="Rock" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Rock' ? 'selected' : ''; ?>>Rock</option>
                            <option value="Pop" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Pop' ? 'selected' : ''; ?>>Pop</option>
                            <option value="Jazz" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Jazz' ? 'selected' : ''; ?>>Jazz</option>
                            <option value="Classical" <?php echo isset($_POST["new_genre"]) && $_POST["new_genre"] == 'Classical' ? 'selected' : ''; ?>>Classical</option>
                        </select> -->
                        <!-- <input class="input-1 input" type="text" id="new_genre" name="new_genre" value="<?php echo isset($_POST["new_genre"]) ? $_POST["new_genre"] : ""; ?>" required /> -->
                    </div>
                    <div style="margin-left: 35px;" class="input-field">
                        <input class="input-1 input" type="number" step="0.01" min="0" id="new_rental_price" name="new_rental_price" value="<?php echo isset($_POST["new_rental_price"]) ? $_POST["new_rental_price"] : ""; ?>" required />
                        <label class="label" for="input"><i class="fa-solid fa-tag"></i>&nbsp;&nbsp;Rental Price</label>
                    </div>
                </div><br>
                <div class="input-field">
                    <textarea style="height: 0%; padding-top: 10px;" class="input-1 input" rows="4" id="new_description" name="new_description" value="<?php echo isset($_POST["new_description"]) ? $_POST["new_description"] : ""; ?>" required></textarea>
                    <label class="label" for="input"><i class="fa-solid fa-message"></i>&nbsp;&nbsp;Description</label>
                </div>
                <br><br>
                <button type="submit" name="submitBook" class="btn-submit sign-btn">Done</button>
            </form>
        </div>
    </div>
    <script src="../js/MultiSelect.js"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            color: "white",
            background: "black",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const searchBar = document.querySelector('.search-bar');
            searchBar.addEventListener('input', function() {
                const filter = searchBar.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const name = row.querySelector('.name').innerText.toLowerCase();
                    if (name.includes(filter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            document.querySelectorAll('.edit-row').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    row.querySelectorAll('.editable').forEach(element => {
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.value = element.innerText;
                        input.className = element.className;
                        input.dataset.originalValue = element.innerText;
                        input.style.width = '100%';
                        input.style.boxSizing = 'border-box';
                        element.replaceWith(input);
                    });
                    row.querySelector('.save-row').style.display = 'inline-block';
                    row.querySelector('.cancel-row').style.display = 'inline-block';
                    row.querySelector('.edit-row').style.display = 'none';
                    row.querySelector('.fa-trash').style.display = 'none';
                });
            });

            document.querySelectorAll('.save-row').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const bookId = row.getAttribute('data-book-id');
                    row.querySelectorAll('input[type="text"]').forEach(input => {
                        const span = document.createElement('span');
                        span.innerText = input.value;
                        span.className = input.className;
                        input.replaceWith(span);
                    });
                    row.querySelector('.save-row').style.display = 'none';
                    row.querySelector('.cancel-row').style.display = 'none';
                    row.querySelector('.edit-row').style.display = 'inline-block';
                    row.querySelector('.fa-trash').style.display = 'inline-block';

                    document.getElementById('book_id').value = bookId;
                    document.getElementById('book_code').value = row.querySelector('.book_code').innerText;
                    document.getElementById('name').value = row.querySelector('.name').innerText;
                    document.getElementById('genre').value = row.querySelector('.genre').innerText;
                    document.getElementById('rental_price').value = row.querySelector('.rental_price').innerText;
                    document.getElementById('description').value = row.querySelector('.description').innerText;
                    document.querySelector('[name="delete"]').value = null;
                    document.getElementById('mainForm').submit();
                });
            });

            document.querySelectorAll('.cancel-row').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    row.querySelectorAll('input[type="text"]').forEach(input => {
                        const span = document.createElement('span');
                        span.innerText = input.dataset.originalValue;
                        span.className = input.className;
                        input.replaceWith(span);
                    });
                    row.querySelector('.save-row').style.display = 'none';
                    row.querySelector('.cancel-row').style.display = 'none';
                    row.querySelector('.edit-row').style.display = 'inline-block';
                    row.querySelector('.fa-trash').style.display = 'inline-block';
                });
            });

            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    const bookCode = this.getAttribute('data-book-code');
                    const bookId = this.getAttribute('data-book-id');
                    Swal.fire({
                        icon: "question",
                        html: `Confirm deletion of User <b>${bookCode}</b> ?`,
                        showCloseButton: true,
                        color: "white",
                        background: "black",
                        confirmButtonText: `<i class="fa fa-thumbs-up"></i> Delete`,
                        cancelButtonText: `<i class="fa fa-thumbs-down"></i>`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('book_id').value = bookId;
                            document.getElementById('book_code').value = bookCode;
                            document.querySelector('[name="edit"]').value = null;
                            document.getElementById('mainForm').submit();
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>
