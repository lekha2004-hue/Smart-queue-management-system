<?php
session_start(); //[cite: 3]
include 'db.php'; //[cite: 3]

$error = "";

if (isset($_POST['login'])) { //[cite: 3]
    $username = mysqli_real_escape_string($conn, $_POST['username']); //[cite: 3]
    $password = md5($_POST['password']); //[cite: 3]

    if (!empty($username) && !empty($password)) { //[cite: 3]
        $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'"; //[cite: 3]
        $result = $conn->query($query); //[cite: 3]

        if ($result->num_rows > 0) { //[cite: 3]
            $_SESSION['admin_user'] = $username; //[cite: 3]
            header("Location: admin_dashboard.php"); //[cite: 3]
            exit(); //[cite: 3]
        } else {
            $error = "Invalid Username or Password!"; //[cite: 3]
        }
    } else {
        $error = "Please fill in all fields."; //[cite: 3]
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Portal Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --peach-beige: #F8B195; --sea-blue-light: #4ea8de; --sea-blue: #0077b6; --deep-ocean: #03045e; --text-dark: #1d2d44; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, var(--peach-beige) 0%, var(--sea-blue-light) 40%, var(--sea-blue) 75%, var(--deep-ocean) 100%);
            color: var(--text-dark); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.72); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; padding: 2.5rem; width: 100%; max-width: 440px; box-shadow: 0 15px 35px rgba(3, 4, 94, 0.15);
        }
        .form-control { background: rgba(255, 255, 255, 0.5); border-radius: 8px; padding: 10px 14px; }
        .btn-login { background: var(--deep-ocean); color: white; font-weight: 700; width: 100%; padding: 12px; border: none; border-radius: 8px; transition: all 0.2s; }
        .btn-login:hover { background: var(--sea-blue); color: white; }
    </style>
</head>
<body>

    <div class="login-card text-center">
        <div class="mb-3"><i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem; color: var(--deep-ocean) !important;"></i></div>
        <h3 class="fw-bold mb-1" style="color: var(--deep-ocean);">Admin Access</h3>
        <p class="text-muted small mb-4">Provide parameters to open workflow management grids.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small"><?php echo $error; ?></div> //[cite: 3]
        <?php endif; ?>

        <form action="admin_login.php" method="POST" class="text-start">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Admin Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold">System Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter security key" required>
            </div>
            <button type="submit" name="login" class="btn-login mb-3 shadow-sm">Secure Authentication</button>
            <div class="text-center"><a href="index.php" class="text-decoration-none small" style="color: var(--sea-blue);">← Return to Gateway</a></div>
        </form>
    </div>

</body>
</html>