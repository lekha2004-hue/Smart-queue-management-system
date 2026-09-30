<?php
include 'db.php'; //[cite: 5]

$serving_result = $conn->query("SELECT * FROM tokens WHERE status='serving' ORDER BY token_number ASC LIMIT 1"); //[cite: 5]
$current_serving = $serving_result->fetch_assoc(); //[cite: 5]

$waiting_result = $conn->query("SELECT * FROM tokens WHERE status='waiting' ORDER BY token_number ASC LIMIT 5"); //[cite: 5]

$count_result = $conn->query("SELECT COUNT(*) AS total_waiting FROM tokens WHERE status='waiting'"); //[cite: 5]
$count_row = $count_result->fetch_assoc(); //[cite: 5]
$total_waiting = $count_row['total_waiting']; //[cite: 5]
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Display Board</title>
    <meta http-equiv="refresh" content="5"> <!--[cite: 5] -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --peach-beige: #F8B195; --sea-blue-light: #4ea8de; --sea-blue: #0077b6; --deep-ocean: #03045e; --text-dark: #1d2d44; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, var(--peach-beige) 0%, var(--sea-blue-light) 40%, var(--sea-blue) 75%, var(--deep-ocean) 100%);
            color: var(--text-dark); min-height: 100vh; padding: 40px 20px;
        }
        .main-container { max-width: 900px; margin: 0 auto; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 15px 35px rgba(3, 4, 94, 0.1); padding: 2rem;
        }
        .serving-hero { background: var(--deep-ocean); color: white; border-radius: 16px; padding: 2.5rem; text-align: center; }
        .big-num { font-size: 5rem; font-weight: 800; color: #2ecc71; margin: 0.5rem 0; line-height: 1; }
        .sub-box { background: rgba(255, 255, 255, 0.6); border-radius: 14px; padding: 1.5rem; height: 100%; border: 1px solid rgba(255,255,255,0.4); }
        .table { --bs-table-bg: transparent; }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="glass-panel">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div>
                    <h2 class="fw-bold mb-0" style="color: var(--deep-ocean);"><i class="bi bi-broadcast me-2"></i>Live Monitor Board</h2>
                    <small class="text-muted">Lobby display sync array system</small>
                </div>
                <a href="index.php" class="btn btn-outline-secondary btn-sm mt-2 mt-sm-0 rounded-3">← Leave Monitor Screen</a>
            </div>

            <div class="serving-hero mb-4 shadow-sm">
                <span class="text-uppercase fw-bold text-white-50 small tracking-wider">Now Serving at Counter</span>
                <?php if ($current_serving): ?>
                    <div class="big-num">#<?php echo $current_serving['token_number']; ?></div> <!--[cite: 5] -->
                    <h3 class="fw-bold text-white mb-1"><?php echo htmlspecialchars($current_serving['user_name']); ?></h3> <!--[cite: 5] -->
                    <p class="mb-0 text-white-50 small"><i class="bi bi-door-open me-1"></i> Area: <?php echo htmlspecialchars($current_serving['service_type']); ?></p> <!--[cite: 5] -->
                <?php else: ?>
                    <div class="big-num text-muted" style="font-size: 4rem;">--</div>
                    <p class="mb-0 text-white-50">Operational counters currently idle.</p> <!--[cite: 5] -->
                <?php endif; ?>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="sub-box">
                        <h5 class="fw-bold mb-3" style="color: var(--deep-ocean);"><i class="bi bi-clock-history me-2"></i>Up Next</h5>
                        <table class="table align-middle">
                            <thead><tr><th>Token</th><th>Department</th></tr></thead>
                            <tbody>
                                <?php if ($waiting_result->num_rows > 0): //[cite: 5]
                                    while($row = $waiting_result->fetch_assoc()): ?> <!--[cite: 5] -->
                                        <tr>
                                            <td><span class="badge bg-primary px-3 rounded-pill">#<?php echo $row['token_number']; ?></span></td> <!--[cite: 5] -->
                                            <td class="small fw-semibold text-muted"><?php echo htmlspecialchars($row['service_type']); ?></td> <!--[cite: 5] -->
                                        </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="2" class="text-center text-muted py-4 small">Lobby queue is clear.</td></tr> <!--[cite: 5] -->
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="sub-box d-flex flex-column justify-content-between text-center">
                        <div>
                            <h5 class="fw-bold text-start mb-4" style="color: var(--deep-ocean);"><i class="bi bi-bar-chart-steps me-2"></i>Queue Summary</h5>
                            <div class="my-4">
                                <span class="fs-6 text-muted">Total Waiting:</span>
                                <div class="display-6 fw-bold text-danger my-1"><?php echo $total_waiting; ?> People</div> <!--[cite: 5] -->
                            </div>
                        </div>
                        <div class="p-3 rounded-3 text-start" style="background: rgba(248, 177, 149, 0.25); border: 1px solid rgba(248, 177, 149, 0.4);">
                            <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-hourglass-split me-1"></i>Est. Wait Time</h6>
                            <h3 class="fw-bold text-danger mb-0"><?php echo ($total_waiting * 5); ?> Mins</h3> <!--[cite: 5] -->
                            <small class="text-muted" style="font-size:11px;">Calculated dynamically at ~5m per record.</small> <!--[cite: 5] -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 text-muted small"><i class="bi bi-arrow-repeat animate__spin me-1"></i> Content updates automatically every 5 seconds.</div> <!--[cite: 5] -->
        </div>
    </div>

</body>
</html>