<?php
session_start();
include 'connect.php';

// Handle Signup
if (isset($_POST['signup-username'])) {
    $userName = $_POST['signup-username'];
    $emailOrPhone = $_POST['signup-email-phone'];
    $password = $_POST['signup-password'];

    // Validate email or phone format
    if (!filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL) && !preg_match('/^[0-9]{10}$/', $emailOrPhone)) {
        echo "<script>alert('Invalid email or phone number format');</script>";
    } elseif (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!]).{8,}$/', $password)) {
        echo "<script>alert('Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.');</script>";
    } else {
        $passwordHash = md5($password); // Example hashing; consider using password_hash() for better security

        // Check if email or phone already exists
        $checkEmailOrPhone = "SELECT * FROM user WHERE email='$emailOrPhone' OR phone='$emailOrPhone'";
        $result = $conn->query($checkEmailOrPhone);
        if ($result->num_rows > 0) {
            echo "<script>alert('Email Address or Phone Number Already Exists!');</script>";
        } else {
            // Insert new user into database
            $insertQuery = "INSERT INTO user (userName, email, phone, password) VALUES ('$userName', '$emailOrPhone', '$emailOrPhone', '$passwordHash')";
            if ($conn->query($insertQuery) === TRUE) {
                echo "<script>alert('Registration Successful! Please login to continue.');</script>";
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
}

// Handle Login
if (isset($_POST['login-username'])) {
    $username = $_POST['login-username'];
    $password = $_POST['login-password'];

    // Validate username format (if needed)
    if (empty($password)) {
        echo "<script>alert('Password is required');</script>";
    } else {
        $passwordHash = md5($password); // Example hashing; consider using password_hash() for better security

        // Validate user credentials
        $sql = "SELECT * FROM user WHERE userName='$username' AND password='$passwordHash'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Start session and store username
            $_SESSION['username'] = $username;
            header("Location: profile.php"); // Redirect after successful login
            exit();
        } else {
            echo "<script>alert('Incorrect Username or Password');</script>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="#" rel="icon">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .error {
            border-color: red;
        }
    </style>
    <script>
        function validatePassword(password) {
            var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!]).{8,}$/;
            return passwordRegex.test(password);
        }

        function validateEmailOrPhone(emailOrPhone) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var phoneRegex = /^[0-9]{10}$/;
            return emailRegex.test(emailOrPhone) || phoneRegex.test(emailOrPhone);
        }

        function validateForm(formType) {
            if (formType === 'signup') {
                var emailOrPhone = document.getElementById("signup-email-phone").value.trim();
                var password = document.getElementById("signup-password").value;

                if (!validateEmailOrPhone(emailOrPhone)) {
                    alert("Invalid email or phone number format");
                    return false;
                }

                if (!password || password.length < 8 || !validatePassword(password)) {
                    alert("Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.");
                    return false;
                }
            } else if (formType === 'login') {
                var username = document.getElementById("login-username").value.trim();
                var password = document.getElementById("login-password").value;

                if (!username) {
                    alert("Username is required");
                    return false;
                }

                if (!password) {
                    alert("Password is required");
                    return false;
                }
            }

            return true;
        }

        window.onload = function () {
            var signupEmailOrPhone = document.getElementById("signup-email-phone");
            var loginUsername = document.getElementById("login-username");

            signupEmailOrPhone.onblur = function () {
                var emailOrPhone = signupEmailOrPhone.value.trim();
                if (!validateEmailOrPhone(emailOrPhone)) {
                    signupEmailOrPhone.classList.add("error");
                } else {
                    signupEmailOrPhone.classList.remove("error");
                }
            };

            loginUsername.onblur = function () {
                var username = loginUsername.value.trim();
                if (!username) {
                    loginUsername.classList.add("error");
                } else {
                    loginUsername.classList.remove("error");
                }
            };
        };
    </script>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="#" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <p class="m-0 fw-bold" style="font-size: 25px;">Learn<span style="color: #b34688;">Stream</span></p>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" style="border: none;">
            <span class="navbar-toggler-icon"></span>
        </button>
        
    </nav>

    <div class="container-xxl py-2">
        <div class="container">
            <div class="row g-4 wow fadeInUp" data-wow-delay="0.5s">
                <center>
                    <form class="shadow p-4" style="max-width: 550px;" id="signIn" method="post" action="login.php" onsubmit="return validateForm('login')">
                        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                            <h1 class="mb-5 bg-white text-center px-3">Login</h1>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="login-username" name="login-username" placeholder="Username" required>
                                    <label for="login-username">Username</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="login-password" name="login-password" placeholder="Password" required>
                                    <label for="login-password">Password</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <p><a href="#">Forgot password?</a></p>
                            </div>
                            <div class="col-12">
                                <button class="btn w-100 py-3 submit-btn" id="signInButton" type="submit">Login</button>
                            </div>
                            <div class="col-12 text-center">
                                <p>Don't have an account? <a class="text-decoration-none" href="#" id="showSignUp">Signup</a></p>
                            </div>
                        </div>
                    </form>

                    <form class="shadow p-4" style="max-width: 550px; display: none;" id="signup" method="post" action="login.php" onsubmit="return validateForm('signup')">
                        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                            <h1 class="mb-5 bg-white text-center px-3">Sign<span style="color: #b34688;">Up</span></h1>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="signup-username" name="signup-username" placeholder="Username" required>
                                    <label for="signup-username">Username</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="signup-email-phone" name="signup-email-phone" placeholder="Email Address or Phone Number" required>
                                    <label for="signup-email-phone">Email Address or Phone Number</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="signup-password" name="signup-password" placeholder="Password" required>
                                    <label for="signup-password">Password</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn w-100 py-3" id="signUpButton" type="submit" name="signUp">Signup</button>
                            </div>
                            <div class="col-12 text-center">
                                <p>Already have an account? <a class="text-decoration-none" href="#" id="showSignIn">Login</a></p>
                            </div>
                        </div>
                    </form>
                </center>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/form.js"></script>
</body>

</html>
