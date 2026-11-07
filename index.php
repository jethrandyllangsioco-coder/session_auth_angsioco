<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
$success = $_SESSION["registration_success"] ?? "";
unset($_SESSION["registration_success"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require __DIR__ . "/db.php";

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Enter your username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && md5($password) === $user["password"]) {
            session_regenerate_id(true);
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];
            header("Location: dashboard.php");
            exit;
        }

        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<main class="d-flex align-items-center justify-content-center min-vh-100 bg-body-tertiary">
    <div class="card shadow-sm" style="width: 380px">
        <div class="card-body p-4">
            <h1 class="fs-4 fw-semibold text-center mb-1">Welcome back</h1>
            <p class="text-body-secondary text-center small mb-4">Sign in to your account</p>

            <?php if ($success !== ""): ?>
                <div class="alert alert-success" role="status"><?= $success ?></div>
            <?php endif; ?>
            <?php if ($error !== ""): ?>
                <div class="alert alert-danger" role="alert"><?= $error ?></div>
            <?php endif; ?>
            <form action="index.php" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" autocomplete="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Sign in</button>
            </form>
            <p class="text-center small text-body-secondary mt-4 mb-0">No account? <a href="register.php" class="text-decoration-none">Register</a></p>
        </div>
    </div>
</main>
</body>
</html>
