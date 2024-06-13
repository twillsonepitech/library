<?php include('../includes/db.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/datatable.css">
    <link rel="stylesheet" href="../css/badge.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="d-container">
        <div class="top">
            <input type="text" placeholder="Search by name..." class="search-bar">
            <a href="/library/admin/choose.php"><button class="add-new-button blue">View All</button></a>
        </div>

        <form id="mainForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php
            if (!empty($_POST["delete"])) {
                $user_id = (int)$_POST["user_id"];
                $mail = $_POST["email"];
                $deleteUserQuery = "DELETE FROM `users` WHERE `user_id`='$user_id'";
                if ($conn->query($deleteUserQuery) === TRUE) {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'success',
                                        html: 'User <b>$mail</b> has been deleted.'
                                    });
                                });
                            </script>";
                } else {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'error',
                                        text: 'Failed to delete the user.'
                                    });
                                });
                            </script>";
                }
            } elseif (!empty($_POST["edit"])) {
                $user_id = (int)$_POST["user_id"];
                $name = validate_input($_POST["name"]);
                $email = validate_input($_POST["email"]);
                $address = validate_input($_POST["address"]);
                $password = validate_input($_POST["password"]);
                $crypted_pw = password_hash($password, PASSWORD_DEFAULT);
                $updateUserQuery = "UPDATE `users` SET `name`='$name', `email`='$email', `address`='$address', `password`='$crypted_pw' WHERE `user_id`='$user_id'";
                if ($conn->query($updateUserQuery) === TRUE) {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'success',
                                        html: 'User <b>$email</b> has been updated.'
                                    });
                                });
                            </script>";
                } else {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'error',
                                        text: 'Failed to update the user.'
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
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Password</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM users";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            $user_id = $row["user_id"];
                            ?>
                            <tr data-user-id="<?php echo htmlspecialchars($row['user_id']); ?>">
                                <td>
                                    <span class="badge blue"><?php echo htmlspecialchars($row["user_id"]); ?></span>
                                </td>
                                <td>
                                    <img src="<?php echo "../img/avatar.jpeg" ?>" alt="" class="avatar">
                                    <div class="info">
                                        <span class="name editable"><?php echo htmlspecialchars($row["name"]); ?></span>
                                        <span class="mail editable"><?php echo htmlspecialchars($row["email"]); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="address editable"><?php echo htmlspecialchars($row["address"]); ?></div>
                                </td>
                                <td>
                                    <div class="password editable"><?php echo htmlspecialchars($row["password"]); ?></div>
                                </td>
                                <td>
                                    <a href="<?php echo "/library/admin/rents.php?user_id=$user_id" ?>"><i class="fa-solid fa-eye view-row" style="color:white; cursor: pointer;"></i></a>
                                    &nbsp;
                                    <i class="fa-solid fa-check save-row" style="cursor: pointer; display: none;"></i>
                                    <i class="fa-solid fa-xmark cancel-row" style="cursor: pointer; display: none;"></i>
                                    <i class="fa-solid fa-pencil edit-row" style="cursor: pointer;"></i>
                                    &nbsp;
                                    <button type="button" class="delete-button" data-user-mail="<?php echo htmlspecialchars($row['email']); ?>" data-user-id="<?php echo htmlspecialchars($row['user_id']); ?>">
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
            <input type="hidden" name="user_id" id="user_id">
            <input type="hidden" name="delete" value="delete">
            <input type="hidden" name="edit" value="edit">
            <input type="hidden" name="name" id="name">
            <input type="hidden" name="email" id="email">
            <input type="hidden" name="address" id="address">
            <input type="hidden" name="password" id="password">
        </form>
    </div>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            color: "white",
            background: "black",
            showConfirmButton: false,
            timer: 2500,
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
                    row.querySelector('.view-row').style.display = 'none';
                    row.querySelector('.fa-trash').style.display = 'none';
                });
            });

            document.querySelectorAll('.save-row').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const userId = row.getAttribute('data-user-id');
                    row.querySelectorAll('input[type="text"]').forEach(input => {
                        const span = document.createElement('span');
                        span.innerText = input.value;
                        span.className = input.className;
                        input.replaceWith(span);
                    });
                    row.querySelector('.save-row').style.display = 'none';
                    row.querySelector('.cancel-row').style.display = 'none';
                    row.querySelector('.edit-row').style.display = 'inline-block';
                    row.querySelector('.view-row').style.display = 'inline-block';
                    row.querySelector('.fa-trash').style.display = 'inline-block';

                    document.getElementById('user_id').value = userId;
                    document.getElementById('name').value = row.querySelector('.name').innerText;
                    document.getElementById('email').value = row.querySelector('.mail').innerText;
                    document.getElementById('address').value = row.querySelector('.address').innerText;
                    document.getElementById('password').value = row.querySelector('.password').innerText;
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
                    row.querySelector('.view-row').style.display = 'inline-block';
                    row.querySelector('.fa-trash').style.display = 'inline-block';
                });
            });

            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    const userEmail = this.getAttribute('data-user-mail');
                    const userId = this.getAttribute('data-user-id');
                    Swal.fire({
                        icon: "question",
                        html: `Confirm deletion of User <b>${userEmail}</b> ?`,
                        showCloseButton: true,
                        color: "white",
                        background: "black",
                        confirmButtonText: `<i class="fa fa-thumbs-up"></i> Delete`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('user_id').value = userId;
                            document.getElementById('email').value = userEmail;
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
