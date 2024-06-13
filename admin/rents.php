<?php

include('../includes/db.php');

session_start();

$user_id = null;
if (isset($_GET["user_id"])) {
    $user_id = $_GET["user_id"];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rents Management</title>
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
            if (!empty($_POST["approve"])) {
                $rental_id = (int) $_POST["rental_id"];
                $updateRentalQuery = "UPDATE `rentals` SET `payment_status`='success' WHERE `rental_id`='$rental_id'";
                if ($conn->query($updateRentalQuery) === TRUE) {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'success',
                                        html: 'Rental ID <b>$rental_id</b> has been approved.'
                                    });
                                });
                            </script>";
                } else {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'error',
                                        text: 'Failed to approve the rental.'
                                    });
                                });
                            </script>";
                }
            } elseif (!empty($_POST["delete"])) {
                $rental_id = (int) $_POST["rental_id"];
                $deleteRentalQuery = "DELETE FROM `rentals` WHERE `rental_id`='$rental_id'";
                if ($conn->query($deleteRentalQuery) === TRUE) {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'success',
                                        html: 'Rental ID <b>$rental_id</b> has been deleted.'
                                    });
                                });
                            </script>";
                } else {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'error',
                                        text: 'Failed to delete the rental.'
                                    });
                                });
                            </script>";
                }
            } elseif (!empty($_POST["edit"])) {
                $rental_id = (int) $_POST["rental_id"];
                $phone = $_POST["phone"];
                $rental_date = $_POST["rental_date"];
                $return_date = $_POST["return_date"];
                $total_fee = (float) substr($_POST["total_fee"], 2);
                $payment_status = $_POST["payment_status"];

                $updateRentalQuery = "UPDATE `rentals` SET `phone`='$phone', `rental_date`='$rental_date', `return_date`='$return_date', `total_fee`='$total_fee', `payment_status`='$payment_status' WHERE `rental_id`='$rental_id'";
                if ($conn->query($updateRentalQuery) === TRUE) {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'success',
                                        html: 'Rental ID <b>$rental_id</b> has been updated.'
                                    });
                                });
                            </script>";
                } else {
                    echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Toast.fire({
                                        icon: 'error',
                                        text: 'Failed to update the rental.'
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
                            <th></th>
                            <th>Rental ID</th>
                            <th>User ID</th>
                            <th>Book ID</th>
                            <th>Phone</th>
                            <th>Rental Date</th>
                            <th>Return Date</th>
                            <th>Total Fee</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (is_null($user_id)) {
                            $sql = "SELECT * FROM `rentals`";
                        } else {
                            $sql = "SELECT * FROM `rentals` WHERE `user_id`='$user_id'";
                        }
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) { ?>
                            <tr data-rental-id="<?php echo htmlspecialchars($row["rental_id"]); ?>">
                                <td>
                                    <img src="<?php echo "../img/rental.png" ?>" alt="" class="avatar">
                                </td>
                                <td>
                                    <span class="rental_id badge blue"><?php echo htmlspecialchars($row["rental_id"]); ?></span>
                                </td>
                                <td>
                                    <span class="user_id badge blue"><?php echo htmlspecialchars($row["user_id"]); ?></span>
                                </td>
                                <td>
                                    <span class="book_id badge blue"><?php echo htmlspecialchars($row["book_id"]); ?></span>
                                </td>
                                <td>
                                    <div class="phone editable"><?php echo htmlspecialchars($row["phone"]); ?></div>
                                </td>
                                <td>
                                    <div class="rental_date editable"><?php echo htmlspecialchars($row["rental_date"]); ?></div>
                                </td>
                                <td>
                                    <div class="return_date editable"><?php echo htmlspecialchars($row["return_date"]); ?></div>
                                </td>
                                <td>
                                    <span class="total_fee badge blue editable">RM<?php echo htmlspecialchars($row["total_fee"]); ?></span>
                                </td>
                                <td>
                                    <?php if ($row["payment_status"] === "pending") { ?>
                                        <span class="payment_status badge orange editable"><?php echo htmlspecialchars($row["payment_status"]); ?></span>
                                        &nbsp;
                                        <button type="button" class="approve-button" data-rental-id="<?php echo htmlspecialchars($row["rental_id"]); ?>">
                                            <i class="fa-solid fa-circle-check" style="color:white; cursor: pointer;"></i>
                                        </button>
                                    <?php } else { ?>
                                        <span class="payment_status badge green editable"><?php echo htmlspecialchars($row["payment_status"]); ?></span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <i class="fa-solid fa-check save-row" style="cursor: pointer; display: none;"></i>
                                    <i class="fa-solid fa-xmark cancel-row" style="cursor: pointer; display: none;"></i>
                                    <i class="fa-solid fa-pencil edit-row" style="cursor: pointer;"></i>
                                    &nbsp;
                                    <button type="button" class="delete-button" data-rental-id="<?php echo htmlspecialchars($row["rental_id"]); ?>">
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
            <input type="hidden" name="rental_id" id="rental_id">
            <input type="hidden" name="delete" value="delete">
            <input type="hidden" name="edit" value="edit">
            <input type="hidden" name="approve" value="approve">
            <input type="hidden" name="user_id" id="user_id">
            <input type="hidden" name="book_id" id="book_id">
            <input type="hidden" name="phone" id="phone">
            <input type="hidden" name="rental_date" id="rental_date">
            <input type="hidden" name="return_date" id="return_date">
            <input type="hidden" name="total_fee" id="total_fee">
            <input type="hidden" name="payment_status" id="payment_status">
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
                    row.querySelector('.fa-trash').style.display = 'none';
                });
            });

            document.querySelectorAll('.save-row').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const rentalId = row.getAttribute('data-rental-id');
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

                    var payment = row.querySelector('.payment_status').innerText;

                    if (['success', 'pending'].includes(payment)) {
                        document.getElementById('rental_id').value = rentalId;
                        document.getElementById('phone').value = row.querySelector('.phone').innerText;
                        document.getElementById('rental_date').value = row.querySelector('.rental_date').innerText;
                        document.getElementById('return_date').value = row.querySelector('.return_date').innerText;
                        document.getElementById('total_fee').value = row.querySelector('.total_fee').innerText;
                        document.getElementById('payment_status').value = payment;
                        document.querySelector('[name="delete"]').value = null;
                        document.querySelector('[name="approve"]').value = null;
                        document.getElementById('mainForm').submit();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            html: 'Payment Status must be either <b>success</b> or <b>pending</b>.'
                        });
                    }
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
                    const rentalId = this.getAttribute('data-rental-id');
                    Swal.fire({
                        icon: "question",
                        html: `Confirm deletion of Rental ID <b>${rentalId}</b> ?`,
                        showCloseButton: true,
                        color: "white",
                        background: "black",
                        confirmButtonText: `<i class="fa fa-thumbs-up"></i> Delete`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('rental_id').value = rentalId;
                            document.querySelector('[name="edit"]').value = null;
                            document.querySelector('[name="approve"]').value = null;
                            document.getElementById('mainForm').submit();
                        }
                    });
                });
            });

            document.querySelectorAll('.approve-button').forEach(button => {
                button.addEventListener('click', function() {
                    const rentalId = this.getAttribute('data-rental-id');
                    Swal.fire({
                        icon: "question",
                        html: `Confirm booking of Rental ID <b>${rentalId}</b> ?`,
                        showCloseButton: true,
                        color: "white",
                        background: "black",
                        confirmButtonText: `<i class="fa fa-thumbs-up"></i> Delete`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('rental_id').value = rentalId;
                            document.querySelector('[name="edit"]').value = null;
                            document.querySelector('[name="delete"]').value = null;
                            document.getElementById('mainForm').submit();
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>
