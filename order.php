<?php
include('includes/db.php');
session_start();

$user_id = $_SESSION["user_id"];
if (isset($_GET["books"])) {
    unset($_SESSION["books"]);
    $_SESSION["books"] = $_GET["books"];
}
$books_id = $_SESSION["books"];
$id_array = explode(',', $books_id);
$id_array = array_map('intval', $id_array);
$id_list = implode(',', $id_array);

if (isset($_POST["submitRental"])) {
    $rental_date = $_POST["rental-date"];
    $return_date = $_POST["return-date"];
    $phone = $_POST["phone"];

    $total_fee = calculate_total_fee($id_array, $rental_date, $return_date, $conn);
    $conn->begin_transaction();
    try {
        foreach ($id_array as $book_id) {
            $stmt = $conn->prepare("INSERT INTO `rentals` (user_id, book_id, phone, rental_date, return_date, total_fee, payment_status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param('iisssd', $user_id, $book_id, $phone, $rental_date, $return_date, $total_fee);
            $stmt->execute();
            $stmt->close();
        }
        $conn->commit();
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Order received',
                    html: 'Thank you for your purchase, your package will be soon checked and delivered to your parcel',
                    showConfirmButton: true,
                    confirmButtonColor: 'black'
                }).then(function() {
                    window.location.href = '/library/books.php';
                });
            });
        </script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Failed to submit rental: " . $e->getMessage() . "');</script>";
    }
}

function calculate_total_fee($id_array, $rental_date, $return_date, $conn)
{
    $total_fee = 0;
    $day_difference = (strtotime($return_date) - strtotime($rental_date)) / (60 * 60 * 24);
    $id_list = implode(',', $id_array);
    $sql = "SELECT `rental_price` FROM `books` WHERE `book_id` IN ($id_list)";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $total_fee += $row["rental_price"] * $day_difference;
    }
    return $total_fee;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Page</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/footer.css">

    <link rel="stylesheet" href="css/datatable.css">
    <link rel="stylesheet" href="css/badge.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/order.css">
    <link rel="stylesheet" href="css/date.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/input2.css">
    <link rel="stylesheet" href="css/alert.css">
</head>

<body style="padding: 0px; background-color: #ff8c6b;">
    <?php include('includes/header.php'); ?>
    <div class="d-container o-container">
        <form style="max-width: none; display: contents;" id="mainForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="top">
                <input style="color: #374151; background: #f3f3f3;" type="text" placeholder="Search by name..." class="search-bar">
                <button type="button" name="submit" class="add-new-button green" onclick="openModal()">Rent book(s)</button>
            </div>
            <div class="table-container">
                <table>
                    <thead class="thead-color">
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Genre</th>
                            <th>Rental Price</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody style="color: #374151;">
                        <?php
                        $sql = "SELECT * FROM `books` WHERE `book_id` IN ($id_list)";
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
                            $description = substr($row["description"], 0, 75) . "..."; ?>
                            <tr data-book-id="<?php echo htmlspecialchars($row["book_id"]); ?>">
                                <td></td>
                                <td>
                                    <img src="<?php echo ($books_images[$row["book_code"]] ?? "img/book.jpeg") ?>" alt="" class="avatar">
                                    <div class="info">
                                        <span class="name editable"><?php echo htmlspecialchars($row["name"]); ?></span>
                                        <span class="book_code editable"><?php echo htmlspecialchars($row["book_code"]); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="genre editable"><?php echo htmlspecialchars($row["genre"]); ?></div>
                                </td>
                                <td><span class="rental_price badge custom-color editable">RM<?php echo htmlspecialchars($row["rental_price"]); ?></span></td>
                                <td>
                                    <div class="description editable"><?php echo htmlspecialchars($description); ?></div>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="date-position">
                <div class="date-input-container">
                    <label class="font form-label">Rental Date</label>
                    <input min="<?= date('Y-m-d'); ?>" type="date" class="date-input" id="rental-date" name="rental-date" />
                    <span class="calendar-icon">&#x1F4C5;</span>
                </div>
                <div class="date-input-container">
                    <label class="font form-label">Return Date</label>
                    <input min="<?= date('Y-m-d'); ?>" type="date" class="date-input" id="return-date" name="return-date" />
                    <span class="calendar-icon">&#x1F4C5;</span>
                </div>
                <div id="rent-days" class="badge grey">Rent days: 0</div>
                <div id="total-rental-price" class="badge grey">Total Rental Price: RM0.00</div>
            </div>
        </form>
    </div>
    <div id="rentalModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <h2 style="color: black;">Enter Rental Details</h2>
            <br>
            <?php
            $sql = "SELECT * FROM users WHERE user_id=$user_id";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            ?>
            <form style="margin: 0 0; max-width: none;" id="rentalForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div style="display: flex;">
                    <div class="input-field">
                        <input class="input-1 input" type="text" id="name" name="name" value="<?php echo $row["name"]; ?>" required />
                        <label class="label" for="input"><i class="fa-solid fa-user"></i>&nbsp;&nbsp;Name</label>
                    </div>
                    <div style="margin-left: 35px;" class="input-field">
                        <input class="input-1 input" type="email" id="email" name="email" value="<?php echo $row["email"]; ?>" required />
                        <label class="label" for="input"><i class="fa-solid fa-envelope"></i>&nbsp;&nbsp;Email</label>
                    </div>
                </div><br>
                <div style="display: flex;">
                    <div class="input-field">
                        <input class="input-1 input" type="text" id="address" name="address" value="<?php echo $row["address"]; ?>" required />
                        <label class="label" for="input"><i class="fa-solid fa-address-card"></i>&nbsp;&nbsp;Address</label>
                    </div>
                    <div style="margin-left: 35px;" class="input-field">
                        <div style="display: flex; color: black;">
                            <input class="top-btn-input input-1 input" type="tel" id="phone" name="phone" value="<?php echo isset($_POST["phone"]) ? $_POST["phone"] : ""; ?>" required />
                            <button type="text" class="top-btn-input btn-input sign-btn" onclick="process(event)">Verify</button>
                        </div>
                        <div class="alert alert-info" style="display: none"></div>
                        <div class="alert alert-error" style="display: none"></div>
                    </div>
                </div>
                <br><br>
                <input type="hidden" id="hidden-rental-date" name="rental-date">
                <input type="hidden" id="hidden-return-date" name="return-date">
                <button type="submit" name="submitRental" class="btn-submit sign-btn">Done</button>
            </form>
        </div>
    </div>
    <script src="js/order.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
    <?php include('includes/footer.php'); ?>
</body>

</html>
