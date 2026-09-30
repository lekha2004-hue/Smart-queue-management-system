<?php
include 'db.php'; //[cite: 1]

$message = "";
$token_number = null;

if (isset($_POST['submit'])) { //[cite: 1]
    $name = mysqli_real_escape_string($conn, $_POST['user_name']); //[cite: 1]
    $service = mysqli_real_escape_string($conn, $_POST['service_type']); //[cite: 1]
    $email = mysqli_real_escape_string($conn, $_POST['email']); //[cite: 1]

    if (!empty($name) && !empty($service) && !empty($email)) { //[cite: 1]
        $result = $conn->query("SELECT MAX(token_number) AS max_token FROM tokens"); //[cite: 1]
        $row = $result->fetch_assoc(); //[cite: 1]
        $token_number = $row['max_token'] + 1; //[cite: 1]

        $query = "INSERT INTO tokens (user_name, service_type, token_number, status, email) 
                  VALUES ('$name', '$service', '$token_number', 'waiting', '$email')"; //[cite: 1]
        
        if ($conn->query($query)) { //[cite: 1]
            $message = "success"; //[cite: 1]
        } else {
            $message = "error"; //[cite: 1]
        }
    } else {
        $message = "empty"; //[cite: 1]
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take a Token</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --peach-beige: #F8B195; --sea-blue-light: #4ea8de; --sea-blue: #0077b6; --deep-ocean: #03045e; --text-dark: #1d2d44; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, var(--peach-beige) 0%, var(--sea-blue-light) 40%, var(--sea-blue) 75%, var(--deep-ocean) 100%);
            color: var(--text-dark); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.72); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 15px 35px rgba(3, 4, 94, 0.15);
            width: 100%; max-width: 520px; padding: 2.5rem; text-align: center;
        }
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.5); border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 10px 14px; color: var(--text-dark);
        }
        .form-control:focus, .form-select:focus { background: #fff; box-shadow: 0 0 0 0.25rem rgba(0, 119, 182, 0.2); border-color: var(--sea-blue); }
        .btn-submit { background: var(--deep-ocean); color: #fff; font-weight: 700; border: none; padding: 12px; border-radius: 8px; width: 100%; transition: all 0.2s; }
        .btn-submit:hover { background: var(--sea-blue); }
        .token-badge { font-size: 3.5rem; font-weight: 800; color: var(--sea-blue); margin: 1rem 0; text-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="glass-card">
        <h2 class="fw-bold mb-2" style="color: var(--deep-ocean);">Generate Token</h2>
        <p class="text-muted mb-4">Register details to log your tracking number in the system.</p>
        
        <?php if ($message == "success"): ?>
            <div class="p-4 rounded-4 my-3 text-start animate__animated animate__zoomIn" style="background: rgba(255,255,255,0.8); border: 2px dashed var(--sea-blue-light);">
                <h5 class="fw-bold text-success text-center"><i class="bi bi-check-circle-fill"></i> Registration Complete!</h5>
                <div class="token-badge text-center">#<?php echo $token_number; ?></div>
                <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
                <p class="mb-3"><strong>Department:</strong> <?php echo htmlspecialchars($service); ?></p>
                <p class="small text-muted mb-0">A status update update profile trigger has been dispatched to your email address.</p>
            </div>
            <a href="index.php" class="btn btn-outline-secondary w-100 rounded-3 mt-2">← Return to Gateway</a>
        <?php else: ?>
            <?php 
                if ($message == "empty") echo "<div class='alert alert-danger py-2 small'>Please complete all fields.</div>"; //[cite: 1]
                if ($message == "error") echo "<div class='alert alert-danger py-2 small'>Database tracking error. Try again.</div>"; //[cite: 1]
            ?>
            <form action="take_token.php" method="POST" class="text-start">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Your Name</label>
                    <input type="text" name="user_name" class="form-control" placeholder="e.g., Amit Kumar" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="name@domain.com" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Select Service Department</label>
                    <select name="service_type" class="form-select" required>
                        <option value="">-- Choose Category --</option>
                        <option value="Fees counter">Fees & Registration</option>
                        <option value="Certificate Section">Document & Certificate Issuing</option>
                        <option value="General Enquiry">General Information Counter</option>
                    </select>
                </div>
                <button type="submit" name="submit" class="btn-submit mb-3 shadow-sm">Confirm & Issue Ticket</button>
                <div class="text-center"><a href="index.php" class="text-decoration-none small fw-semibold" style="color: var(--sea-blue);">← Cancel and Go Back</a></div>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>