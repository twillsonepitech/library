<?php
include('includes/db.php');

$nameErr = $emailErr = $addressErr = $passwordErr = "";
$name = $email = $address = $password = "";
$form_type = "";

if (isset($_POST['signup_submit'])) {
    $form_type = 'signup';

    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = validate_input($_POST["name"]);
        if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            $nameErr = "Only letters and white space allowed";
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = validate_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
    } else {
        $address = validate_input($_POST["address"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = validate_input($_POST["password"]);
    }

    if (empty($nameErr) && empty($emailErr) && empty($addressErr) && empty($passwordErr)) {
        $checkUserQuery = "SELECT user_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkUserQuery);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
    
        if ($stmt->num_rows > 0) {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Toast.fire({
                            icon: 'error',
                            html: 'User <b>$email</b> already exists!'
                        });
                    });
                </script>";
        } else {
            $stmt->close();
            $crypted_pw = password_hash($password, PASSWORD_DEFAULT);
            $insertUserQuery = "INSERT INTO users (name, email, address, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertUserQuery);
            $stmt->bind_param('ssss', $name, $email, $address, $crypted_pw);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Toast.fire({
                                icon: 'success',
                                html: 'User registered successfully.'
                            }).then(function() {
                                window.location.href = '/library/books.php?user_id=$user_id';
                            });    
                        });
                    </script>";
            } else {
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Toast.fire({
                                icon: 'error',
                                html: 'Error: " . $stmt->error . "'
                            });
                        });
                    </script>";
            }
            $stmt->close();
        }
    }    
} elseif (isset($_POST['signin_submit'])) {
    $form_type = 'signin';

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = validate_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = validate_input($_POST["password"]);
    }

    if (empty($emailErr) && empty($passwordErr)) {
        $query = "SELECT user_id, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();
    
        if ($stmt->num_rows == 0 || !password_verify($password, $hashed_password)) {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Toast.fire({
                            icon: 'error',
                            html: 'Invalid <b>Email</b> or <b>Password</b>!'
                        });
                    });
                </script>";
        } else {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Toast.fire({
                            icon: 'success',
                            html: 'Signed in successfully'
                        }).then(function() {
                            window.location.href = '/library/books.php?user_id=$user_id';
                        });
                    });
                </script>";
        }
        $stmt->close();
    }    
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book Library Home Page</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/input1.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <main class="<?php echo ($form_type == 'signup') ? 'sign-up-mode' : ''; ?>">
        <div class="box">
            <div class="inner-box">
                <div class="forms-wrap">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="sign-in-form">
                        <input type="hidden" name="signin_submit" value="1">
                        <div class="heading">
                            <h2>Welcome Back</h2>
                            <h6>Not registered yet?</h6>
                            <a href="#" class="toggle">Sign up</a>
                        </div>
                        <div class="actual-form">
                            <div class="input-wrap">
                                <input type="email" class="input-field" autocomplete="off" name="email" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ""; ?>" />
                                <label class="form-label">Email</label>
                                <i class="fa-solid fa-envelope"></i>
                                <span class="input-error"><?php echo ($form_type == 'signin') ? $emailErr : ''; ?></span>
                            </div>
                            <div class="input-wrap">
                                <input type="password" minlength="4" class="input-field" autocomplete="off" name="password" id="password-signin" value="<?php echo isset($_POST["password"]) ? $_POST["password"] : ""; ?>" />
                                <label class="form-label">Password</label>
                                <i class="fas fa-eye" id="togglePassword-signin" style="cursor: pointer;"></i>
                                <span class="input-error"><?php echo ($form_type == 'signin') ? $passwordErr : ''; ?></span>
                            </div>
                            <input type="submit" value="Sign In" class="sign-btn" />
                        </div>
                    </form>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="sign-up-form">
                        <input type="hidden" name="signup_submit" value="1">
                        <div class="heading">
                            <h2>Get Started</h2>
                            <h6>Already have an account?</h6>
                            <a href="#" class="toggle">Sign in</a>
                        </div>
                        <div class="actual-form">
                            <div class="input-wrap">
                                <input type="text" minlength="4" class="input-field" autocomplete="off" name="name" value="<?php echo isset($_POST["name"]) ? $_POST["name"] : ""; ?>"/>
                                <label class="form-label">Name</label>
                                <i class="fa-solid fa-user"></i>
                                <span class="input-error"><?php echo ($form_type == 'signup') ? $nameErr : ''; ?></span>
                            </div>
                            <div class="input-wrap">
                                <input type="email" class="input-field" autocomplete="off" name="email" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ""; ?>"/>
                                <label class="form-label">Email</label>
                                <i class="fa-solid fa-envelope"></i>
                                <span class="input-error"><?php echo ($form_type == 'signup') ? $emailErr : ''; ?></span>
                            </div>
                            <div class="input-wrap">
                                <input type="text" minlength="10" class="input-field" autocomplete="off" name="address" value="<?php echo isset($_POST["address"]) ? $_POST["address"] : ""; ?>"/>
                                <label class="form-label">Address</label>
                                <i class="fa-solid fa-address-card"></i>
                                <span class="input-error"><?php echo ($form_type == 'signup') ? $addressErr : ''; ?></span>
                            </div>
                            <div class="input-wrap">
                                <input type="password" minlength="4" class="input-field" autocomplete="off" name="password" id="password-signup" value="<?php echo isset($_POST["password"]) ? $_POST["password"] : ""; ?>"/>
                                <label class="form-label">Password</label>
                                <i class="fas fa-eye" id="togglePassword-signup" style="cursor: pointer;"></i>
                                <span class="input-error"><?php echo ($form_type == 'signup') ? $passwordErr : ''; ?></span>
                            </div>
                            <input type="submit" value="Sign Up" class="sign-btn" />
                        </div>
                    </form>
                </div>
                <div class="carousel">
                    <div class="images-wrapper">
                        <img src="./img/image1.png" class="image img-1 show" alt="" />
                        <img src="./img/image2.png" class="image img-2" alt="" />
                    </div>
                    <div class="text-slider">
                        <div class="text-wrap">
                            <div class="text-group">
                                <h2>Select a book among a lot of choice</h2>
                                <h2>Rent a book for a period</h2>
                            </div>
                        </div>
                        <div class="bullets">
                            <span class="active" data-value="1"></span>
                            <span data-value="2"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="js/scripts.js"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePasswordSignin = document.querySelector('#togglePassword-signin');
            const passwordSignin = document.querySelector('#password-signin');
            togglePasswordSignin.addEventListener('click', function(e) {
                const type = passwordSignin.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordSignin.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });

            const togglePasswordSignup = document.querySelector('#togglePassword-signup');
            const passwordSignup = document.querySelector('#password-signup');
            togglePasswordSignup.addEventListener('click', function(e) {
                const type = passwordSignup.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordSignup.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>

</html>