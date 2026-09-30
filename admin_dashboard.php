<?php
session_start(); //
include 'db.php'; //
include 'mailer.php'; //

// Route the user back to login if they try to bypass the login portal
if (!isset($_SESSION['admin_user'])) { //
    header("Location: admin_login.php"); //
    exit(); //
}

$msg = ""; //

// LOGIC MODULE: 1. CALL NEXT TOKEN
if (isset($_GET['action']) && $_GET['action'] == 'call_next') { //
    // Automatically mark whatever token is currently 'serving' as 'completed' first
    $conn->query("UPDATE tokens SET status='completed' WHERE status='serving'"); //
    
    // Grab user_name, service_type, and email along with the id
    $next_result = $conn->query("SELECT id, token_number, user_name, service_type, email FROM tokens WHERE status='waiting' ORDER BY token_number ASC LIMIT 1"); //
    
    if ($next_result->num_rows > 0) { //
        $next_token = $next_result->fetch_assoc(); //
        $next_id = $next_token['id']; //
        
        // Update its status to 'serving'
        $conn->query("UPDATE tokens SET status='serving' WHERE id=$next_id"); //
        $msg = "Next token called successfully!"; //

        // Send "Now Serving" email to the user called to the counter
        if (!empty($next_token['email'])) { //
            sendQueueEmail( //
                $next_token['email'], //
                $next_token['user_name'], //
                $next_token['token_number'], //
                $next_token['service_type'] //
            );
            $msg .= " Call notification sent."; //
        }

        // LOOP THROUGH EVERYONE ELSE STILL WAITING TO SEND POSITION UPDATES
        $remaining_waiting = $conn->query("SELECT token_number, user_name, email FROM tokens WHERE status='waiting' ORDER BY token_number ASC"); //
        
        if ($remaining_waiting->num_rows > 0) { //
            $positionCounter = 1; //
            
            while ($waiting_user = $remaining_waiting->fetch_assoc()) { //
                if (!empty($waiting_user['email'])) { //
                    // FIXED: Now passing the 5th argument ($next_token['service_type']) to resolve the argument count crash
                    sendPositionUpdateEmail(
                        $waiting_user['email'], //
                        $waiting_user['user_name'], //
                        $waiting_user['token_number'], //
                        $positionCounter, //
                        $next_token['service_type']
                    );
                }
                $positionCounter++; //
            }
            $msg .= " Remaining queue position updates dispatched."; //
        }
        
    } else {
        $msg = "No customers currently waiting in the queue."; //
    }
}

// LOGIC MODULE: 2. COMPLETE CURRENT TOKEN
if (isset($_GET['action']) && $_GET['action'] == 'complete') { //
    $conn->query("UPDATE tokens SET status='completed' WHERE status='serving'"); //
    $msg = "Current token marked as completed."; //
}

// LOGIC MODULE: 3. RESET DAILY QUEUE
if (isset($_GET['action']) && $_GET['action'] == 'reset') { //
    $conn->query("TRUNCATE TABLE tokens"); //
    $msg = "Queue layout flushed and fully reset for the day."; //
}

// FETCH CURRENT ACTIVE DATA TO UPDATE THE PANEL GRID
$serving_res = $conn->query("SELECT * FROM tokens WHERE status='serving' LIMIT 1"); //
$current_serving = $serving_res->fetch_assoc(); //

$all_waiting = $conn->query("SELECT * FROM tokens WHERE status='waiting' ORDER BY token_number ASC"); //
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --peach-beige: #F8B195; --sea-blue-light: #4ea8de; --sea-blue: #0077b6; 
            --deep-teal: #0096c7; --deep-ocean: #03045e; --text-dark: #1d2d44; 
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, var(--peach-beige) 0%, var(--sea-blue-light) 40%, var(--sea-blue) 75%, var(--deep-ocean) 100%);
            color: var(--text-dark); min-height: 100vh; padding: 40px 20px; background-attachment: fixed;
        }
        .dashboard-panel {
            background: rgba(255, 255, 255, 0.72); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; padding: 2.5rem; max-width: 900px; margin: 0 auto; box-shadow: 0 15px 35px rgba(3, 4, 94, 0.12);
        }
        .serving-card { background: var(--deep-ocean); color: white; border-radius: 14px; padding: 2rem; text-align: center; }
        .control-btn { border-radius: 8px; font-weight: 700; padding: 10px 18px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: transform 0.2s; border: none; }
        .control-btn:hover { transform: translateY(-2px); color: white; }
        .table { --bs-table-bg: transparent; }
    </style>
</head>
<body>

    <div class="dashboard-panel">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0" style="color: var(--deep-ocean);"><i class="bi bi-shield-lock-fill me-2"></i>Admin Control Grid</h2>
                <small class="text-muted">Active Operator: <strong><?php echo $_SESSION['admin_user']; ?></strong></small>
            </div>
            <a href="logout.php" class="btn btn-danger btn-sm rounded-3 fw-bold px-3">Logout →</a>
        </div>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-info py-2 shadow-sm mb-4"><i class="bi bi-info-circle-fill me-2"></i><?php echo $msg; ?></div>
        <?php endif; ?>

        <div class="serving-card mb-4 shadow-sm">
            <h6 class="text-white-50 text-uppercase tracking-wider small mb-2">Active Target Processing</h6>
            <div class="display-4 fw-bold mb-1 text-success">
                <?php echo $current_serving ? "#".$current_serving['token_number'] . " (".$current_serving['user_name'].")" : "No Active Token"; ?>
            </div>
            <p class="mb-0 text-white-50 small">Service Category: <?php echo $current_serving ? $current_serving['service_type'] : "None"; ?></p>
        </div>

        <div class="d-flex gap-2 justify-content-center flex-wrap mb-5">
            <a href="admin_dashboard.php?action=call_next" class="control-btn bg-success text-white"><i class="bi bi-megaphone"></i> Call Next</a>
            <a href="admin_dashboard.php?action=complete" class="control-btn bg-warning text-dark"><i class="bi bi-check-circle"></i> Complete Current</a>
            <a href="admin_dashboard.php?action=reset" class="control-btn bg-secondary text-white" onclick="return confirm('Flush database logs?');"><i class="bi bi-trash"></i> Reset Queue</a>
        </div>

        <h4 class="fw-bold mb-3" style="color: var(--deep-ocean);">Lobby Waiting Backlog</h4>
        <div class="table-responsive bg-white bg-opacity-50 rounded-3 p-2 border">
            <table class="table align-middle mb-0">
                <thead><tr><th>Token</th><th>Name</th><th>Department</th><th>Status</th></tr></thead>
                <tbody>
                    <?php if ($all_waiting->num_rows > 0): ?>
                        <?php while($row = $all_waiting->fetch_assoc()): ?>
                            <tr>
                                <td><span class="fw-bold text-primary">#<?php echo $row['token_number']; ?></span></td>
                                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td class="small text-muted"><?php echo htmlspecialchars($row['service_type']); ?></td>
                                <td><span class="badge bg-secondary text-uppercase" style="font-size:10px;"><?php echo $row['status']; ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-muted py-4 small">Lobby tracking backlog is clear.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>