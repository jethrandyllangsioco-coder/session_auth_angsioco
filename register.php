<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require __DIR__ . "/db.php";

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || strlen($username) > 100) {
        $error = "Username is required and must be 100 characters or fewer.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
        $error = "Enter a valid email address.";
    } else {
        $checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $checkUsername->bind_param("s", $username);
        $checkUsername->execute();

        if ($checkUsername->get_result()->num_rows > 0) {
            $error = "That username is already registered.";
        } else {
            $passwordHash = md5($password);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $passwordHash);
            $stmt->execute();
            $_SESSION["registration_success"] = "Account created. You can now sign in.";
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row min-vh-100 d-flex align-items-center justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card shadow-sm border-0 rounded-3 my-5">
                <div class="card-body p-4 p-sm-5">
                    <h1 class="card-title text-center mb-5 fw-bold fs-3">Create an Account</h1>
                    <?php if ($error !== ""): ?>
                        <div class="alert alert-danger" role="alert"><?= $error ?></div>
                    <?php endif; ?>
                    <form action="register.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <input type="text" class="form-control form-control-lg" id="username" name="username" autocomplete="username" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email address</label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" autocomplete="email" maxlength="50" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" autocomplete="new-password" required>
                        </div>
                        <div class="d-grid mb-3">
                            <button class="btn btn-primary btn-lg fw-semibold" type="submit">Sign Up</button>
                        </div>
                        <div class="text-center">
                            <span class="text-muted small">Already have an account? </span>
                            <a href="index.php" class="text-decoration-none small fw-semibold">Log In</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
