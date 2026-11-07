<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . '/db.php';

$stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SystemAuthSystem - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .main-navbar {
            background-color: #212529;
            padding: 0.5rem 1rem;
        }
        .nav-text {
            color: #fff;
            font-weight: bold;
        }
        .card-custom {
            border: none;
            border-radius: 8px;
            background-color: #fff;
        }
        .welcome-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: #212529;
            line-height: 1.1;
        }
    </style>
</head>
<body>

    <nav class="navbar main-navbar shadow-sm">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex gap-4 align-items-center">
                <span class="nav-text">SystemAuthSystem</span>
                <span class="nav-text">Dashboard</span>
            </div>
            <a href="logout.php" class="btn btn-danger btn-sm fw-bold">Log Out</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="card card-custom shadow-sm p-5 mb-5">
                    <div class="row align-items-center">
                        <div class="col-md-8 text-start">
                            <h1 class="welcome-title mb-2">Welcome, <?php echo $user['username']; ?>!</h1>
                            <p class="text-muted fs-5 mb-4">This page is protected and only visible to logged-in users.</p>
                            
                            <a href="#" class="btn btn-primary px-4 py-2 fw-bold text-uppercase shadow-sm" style="letter-spacing: 0.5px;">
                                Explore Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-5 text-center">
                    <div class="col-12 col-md-4">
                        <div class="card card-custom shadow-sm p-4 h-100 border-top border-4 border-success">
                            <small class="text-uppercase text-muted fw-bold font-monospace d-block mb-2">Account Status</small>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill d-inline-block">
                                Verified Active
                            </span>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="card card-custom shadow-sm p-4 h-100 border-top border-4 border-primary">
                            <small class="text-uppercase text-muted fw-bold font-monospace d-block mb-2">User Details</small>
                            <div class="text-start small">
                                <p class="mb-1"><strong>Username:</strong> <?php echo $user['username']; ?></p>
                                <p class="mb-1"><strong>Email:</strong> <?php echo $user['email']; ?></p>
                                <p class="mb-0"><strong>Joined:</strong> <?php echo date("M d, Y", strtotime($user['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="card card-custom shadow-sm p-4 h-100 border-top border-4 border-warning">
                            <small class="text-uppercase text-muted fw-bold font-monospace d-block mb-2">Active Session ID</small>
                            <code class="text-break small bg-light p-2 rounded d-block text-warning-emphasis">
                                <?php echo session_id(); ?>
                            </code>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
