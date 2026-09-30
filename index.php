<?php
include 'db.php'; // Includes XAMPP port config connection object[cite: 4]

// 1. Fetch current live status values
$serving_result = $conn->query("SELECT token_number FROM tokens WHERE status='serving' ORDER BY token_number ASC LIMIT 1");
$current_serving = $serving_result->fetch_assoc();

$count_result = $conn->query("SELECT COUNT(*) AS total_waiting FROM tokens WHERE status='waiting'");
$count_row = $count_result->fetch_assoc();
$total_waiting = $count_row['total_waiting'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Queue Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --peach-beige: #F8B195; --sea-blue-light: #4ea8de; --sea-blue: #0077b6;
            --deep-teal: #0096c7; --deep-ocean: #03045e; --text-dark: #1d2d44;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, var(--peach-beige) 0%, var(--sea-blue-light) 40%, var(--sea-blue) 75%, var(--deep-ocean) 100%);
            color: var(--text-dark); min-height: 100vh; margin: 0; background-attachment: fixed; overflow-x: hidden;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.35); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4); padding: 1rem 0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .navbar-brand { font-weight: 700; color: var(--deep-ocean) !important; font-size: 1.4rem; letter-spacing: 0.5px; }
        .nav-link { color: var(--text-dark) !important; font-weight: 600; margin: 0 10px; cursor: pointer; }
        .nav-link:hover { color: var(--deep-ocean) !important; }
        .hero-container {
            background: rgba(255, 255, 255, 0.68); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 24px; box-shadow: 0 20px 40px rgba(3, 4, 94, 0.15);
            padding: 4rem 3rem; margin-top: 3rem; margin-bottom: 3rem;
        }
        .hero-title { font-weight: 800; color: var(--deep-ocean); line-height: 1.2; }
        .hero-title span { color: var(--sea-blue); font-weight: 700; font-size: 0.85em; display: block; margin-top: 0.5rem; }
        .btn-token {
            background: var(--deep-ocean); color: #FFFFFF; font-weight: 700; border-radius: 8px;
            padding: 0.75rem 1.75rem; border: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(3, 4, 94, 0.3);
        }
        .btn-token:hover { background: var(--sea-blue); transform: translateY(-3px) scale(1.02); color: #FFFFFF; }
        .btn-live-board {
            background-color: transparent; color: var(--deep-ocean); font-weight: 700; border: 2px solid var(--deep-ocean);
            border-radius: 8px; padding: 0.75rem 1.75rem; transition: all 0.3s ease; text-decoration: none; display: inline-block;
        }
        .btn-live-board:hover { background-color: rgba(3, 4, 94, 0.08); transform: translateY(-3px) scale(1.02); color: var(--deep-ocean); }
        @keyframes floatEffect { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
        .floating-vector-container { animation: floatEffect 5s ease-in-out infinite; }
        .queue-display-box {
            background: rgba(255, 255, 255, 0.88); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 12px; color: var(--text-dark); padding: 1rem; position: absolute; top: 5%; right: 8%;
            box-shadow: 0 10px 25px rgba(3, 4, 94, 0.1); min-width: 170px;
        }
        .admin-showcase-box { background: rgba(255, 255, 255, 0.4); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 16px; transition: all 0.3s ease; }
        .admin-showcase-box:hover { border-color: var(--sea-blue-light); background: rgba(255, 255, 255, 0.6); transform: translateY(-2px); }
        .btn-admin { border: none; color: #FFFFFF; background: var(--deep-teal); font-weight: 700; transition: all 0.3s; text-decoration: none; }
        .btn-admin:hover { background: var(--sea-blue); color: #FFFFFF; }
        .feature-icon-wrapper {
            width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; margin-bottom: 1rem; background: rgba(0, 150, 199, 0.15); color: var(--sea-blue); border: 1px solid rgba(0, 150, 199, 0.25);
        }
        .modal-content { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); border: 1px solid var(--peach-beige); color: var(--text-dark); }
        .modal-header { border-bottom: 1px solid rgba(0, 0, 0, 0.08); } .modal-footer { border-top: 1px solid rgba(0, 0, 0, 0.08); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top animate__animated animate__fadeInDown">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php"><i class="bi bi-ui-checks-grid me-2"></i> SMART QUEUE</a>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="modal" data-bs-target="#aboutModal">About</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="modal" data-bs-target="#servicesModal">Services</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="modal" data-bs-target="#contactModal">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="hero-container animate__animated animate__fadeInUp">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <h1 class="hero-title display-5 mb-3">Welcome to <span>Smart Queue Management System</span></h1>
                    <p class="hero-subtitle mb-4">Say goodbye to long lines. Generate digital tokens instantly and track your waiting spot live from your phone or digital boards.</p>
                    <div class="d-flex gap-3 mb-5">
                        <a href="take_token.php" class="btn btn-token px-4 shadow-sm">Take a Token</a>
                        <a href="live_board.php" class="btn btn-live-board px-4">Live Queue Board</a>
                    </div>
                    <div class="row g-4 pt-4 border-top border-light-subtle" style="border-color: rgba(0,0,0,0.08) !important;">
                        <div class="col-sm-4">
                            <div class="feature-icon-wrapper"><i class="bi bi-lightning-charge-fill"></i></div>
                            <div class="fw-bold" style="color: var(--deep-ocean);">Easy Token</div>
                            <div class="feature-text text-muted small">Claim tokens inside seconds.</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="feature-icon-wrapper"><i class="bi bi-broadcast"></i></div>
                            <div class="fw-bold" style="color: var(--deep-ocean);">Live Tracking</div>
                            <div class="feature-text text-muted small">Real-time room syncing logs.</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="feature-icon-wrapper"><i class="bi bi-people-fill"></i></div>
                            <div class="fw-bold" style="color: var(--deep-ocean);">Lobby Status</div>
                            <div class="feature-text text-primary small fw-bold"><?php echo $total_waiting; ?> waiting in line</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 position-relative text-center d-flex flex-column gap-4 align-items-center">
                    <div class="w-100 position-relative d-none d-lg-block mb-2 floating-vector-container">
                        <div class="queue-display-box text-start">
                            <div class="small text-muted">Serving Live <i class="bi bi-arrow-right mx-1"></i></div>
                            <div class="fw-bold mt-1" style="font-size: 1.15rem; color: var(--sea-blue);">
                                <i class="bi bi-circle-fill text-success me-1 small"></i> <?php echo $current_serving ? "#".$current_serving['token_number'] : "Idle"; ?>
                            </div>
                        </div>
                        <svg viewBox="0 0 500 200" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="250" cy="100" r="70" fill="rgba(255,255,255,0.4)" stroke="rgba(255,255,255,0.6)"/>
                            <rect x="230" y="25" width="45" height="120" rx="10" fill="rgba(255, 255, 255, 0.7)" stroke="var(--sea-blue-light)" stroke-width="1.5"/>
                            <rect x="236" y="35" width="33" height="22" rx="4" fill="var(--sea-blue)"/>
                            <text x="242" y="49" fill="#FFFFFF" font-size="8" font-family="sans-serif" font-weight="bold">Q</text>
                        </svg>
                    </div>

                    <div class="admin-showcase-box p-4 w-100 text-start animate__animated animate__fadeInUp animate__delay-1s">
                        <div class="d-flex align-items-center justify-content-between flex-wrap g-3">
                            <div>
                                <h5 class="fw-bold mb-1" style="color: var(--deep-ocean);"><i class="bi bi-shield-lock-fill me-2"></i>Administration Portal</h5>
                                <p class="text-muted small mb-0">Authorized counter setup control parameters and active counter clear modules.</p>
                            </div>
                            <a href="admin_login.php" class="btn btn-admin btn-sm px-4 py-2 mt-2 mt-sm-0 rounded-3">Enter Portal</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALS -->
    <div class="modal fade" id="aboutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content p-4"><h3>About</h3><p>Smart Queue Management streamlines public lobby systems by eliminating rigid physical tracking.</p></div></div>
    </div>
    <div class="modal fade" id="servicesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content p-4"><h3>Workflows</h3><p>Dynamic Ticket Dispensing and Active Dashboard Synchronization.</p></div></div>
    </div>
    <div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content p-4"><h3>Support</h3><p>Hotline Desk: +91 80 2666 2226<br>Operations Center: operational@smartqueue.system</p></div></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>