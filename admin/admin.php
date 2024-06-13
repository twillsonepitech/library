<?php

$name = $password = "";

if (isset($_POST["submit"])) {

    if (!empty($_POST["name"])) {
        $name = validate_input($_POST["name"]);
    }
    if (!empty($_POST["password"])) {
        $password = validate_input($_POST["password"]);
    }

    if ($name === "admin" && $password === "admin") {
        echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Toast.fire({
                            icon: 'success',
                            html: 'Admin registered successfully'
                        }).then(function() {
                            window.location.href = '/library/admin/choose.php';
                        });
                    });
                </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Toast.fire({
                    icon: 'error',
                    html: 'Admin not found !'
                });
            });
        </script>";
    }
}

function validate_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Connection</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/adminform.css">
    <link rel="stylesheet" href="../css/input2.css">
</head>

<body>
    <form class="form-control" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <p class="title">Login</p>
        <div class="input-field">
            <input required="" name="name" class="input" type="text" />
            <label class="label" for="input"><i class="fa-solid fa-user"></i>&nbsp;&nbsp;Enter Admin Name</label>
        </div>
        <div class="input-field">
            <input required="" name="password" class="input" type="password" />
            <label class="label" for="input"><i class="fa-solid fa-lock"></i>&nbsp;&nbsp;Enter Admin Password</label>
        </div>
        <button type="submit" name="submit" class="submit-btn">Sign In</button>
    </form>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
    </script>
</body>

</html>