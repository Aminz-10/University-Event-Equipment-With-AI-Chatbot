<?php
session_start();

// Get staff name from session
$staff_name = $_SESSION['staff_name'] ?? "Admin User";

if (!isset($_SESSION['user_number']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include("db.php");

if (!isset($_GET['club_name'])) {
    die("Invalid club name.");
}

$club_name = mysqli_real_escape_string($connect, $_GET['club_name']);
$result = mysqli_query($connect, "SELECT * FROM club WHERE club_name = '$club_name'");
if (!$result || mysqli_num_rows($result) == 0) {
    die("Club not found.");
}
$club = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = mysqli_real_escape_string($connect, $_POST['club_name']);
    $type = mysqli_real_escape_string($connect, $_POST['type']);
    $adv_name = mysqli_real_escape_string($connect, $_POST['adv_name']);
    $adv_tel = mysqli_real_escape_string($connect, $_POST['adv_tel']);
    $adv_email = mysqli_real_escape_string($connect, $_POST['adv_email']);
    $adv_num = mysqli_real_escape_string($connect, $_POST['adv_num']);
    $status = mysqli_real_escape_string($connect, $_POST['status']);

    $update = "UPDATE club SET 
                club_name='$new_name',
                type='$type',
                adv_name='$adv_name',
                adv_tel='$adv_tel',
                adv_email='$adv_email',
                adv_num='$adv_num',
                status='$status'
               WHERE club_name='$club_name'";

    if (mysqli_query($connect, $update)) {
        header("Location: view_club.php");
        exit;
    } else {
        echo "Error updating club: " . mysqli_error($connect);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Club - UniEquip</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>
        /* Logout Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 10px 10px -5px rgb(0 0 0 / 0.04);
            max-width: 400px;
            width: 90%;
            text-align: center;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: white;
            font-size: 24px;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .modal-message {
            font-size: 16px;
            color: var(--text-secondary);
            margin-bottom: 32px;
            line-height: 1.5;
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .modal-btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 100px;
        }

        .modal-btn-cancel {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .modal-btn-cancel:hover {
            background-color: var(--bg-primary);
        }

        .modal-btn-confirm {
            background-color: #ef4444;
            color: white;
        }

        .modal-btn-confirm:hover {
            background-color: #dc2626;
        }
    </style>
    <script>
        function confirmLogout() {
            showLogoutModal();
            return false;
        }

        function showLogoutModal() {
            document.getElementById('logoutModal').style.display = 'flex';
        }

        function hideLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        function proceedLogout() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'logout.php';
            document.body.appendChild(form);
            form.submit();
        }

        window.onclick = function(event) {
            const logoutModal = document.getElementById('logoutModal');
            if (event.target === logoutModal) {
                hideLogoutModal();
            }
        }
    </script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">UE</div>
                <div class="sidebar-title">UniEquip Dashboard</div>
            </div>

            <div class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-item">
                        <form action="admin_dashboard.php" method="post" style="margin: 0;">
                            <button type="submit" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </button>
                        </form>
                    </div>
                    <div class="nav-item">
                        <form action="admin_view_equipment.php" method="post" style="margin: 0;">
                            <button type="submit" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-tools"></i></div>
                                View Equipment
                            </button>
                        </form>
                    </div>
                    <div class="nav-item">
                        <form action="admin_view_record.php" method="post" style="margin: 0;">
                            <button type="submit" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-clipboard-list"></i></div>
                                Booking Records
                            </button>
                        </form>
                    </div>
                    <div class="nav-item">
                        <form action="view_club.php" method="post" style="margin: 0;">
                            <button type="submit" class="nav-link active">
                                <div class="nav-icon"><i class="fas fa-users"></i></div>
                                Club Details
                            </button>
                        </form>
                    </div>
                    <div class="nav-item">
                        <form action="report.php" method="post" style="margin: 0;">
                            <button type="submit" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-chart-bar"></i></div>
                                Reports
                            </button>
                        </form>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">ACCOUNT PAGES</div>
                    <div class="nav-item">
                        <form action="admin_profile.php" method="post" style="margin: 0;">
                            <button type="submit" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-user"></i></div>
                                Profile
                            </button>
                        </form>
                    </div>
                    <div class="nav-item">
                        <form action="rgstaff.php" method="post" style="margin: 0;">
                            <button type="submit" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-user-plus"></i></div>
                                Register Admin
                            </button>
                        </form>
                    </div>
                </div>

                <div style="margin-top: auto; padding: 16px;">
                    <div style="background: #4f46e5; border-radius: 12px; padding: 16px; text-align: center; color: white;">
                        <i class="fas fa-power-off" style="font-size: 24px; margin-bottom: 8px;"></i>
                        <div style="font-size: 12px; font-weight: 600; margin-bottom: 4px;">Ready to leave?</div>
                        <div style="font-size: 10px; margin-bottom: 12px;">Click logout to exit</div>
                        <form action="logout.php" method="post" style="margin: 0;" onsubmit="return confirmLogout()">
                            <button type="submit" class="btn btn-primary btn-sm" style="background: white; color: #4f46e5; width: 100%; border: none; padding: 8px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <div class="top-header">
                <div class="header-left">
                    <div class="breadcrumb">
                        <i class="fas fa-home"></i>
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-item">Clubs</span>
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-item active">Update Club</span>
                    </div>
                </div>
                <div class="header-right">
                    <div class="header-actions">
                        <form action="logout.php" method="post" style="margin: 0;" onsubmit="return confirmLogout()">
                            <button type="submit" class="btn btn-primary" style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </button>
                        </form>
                        <div class="user-menu">
                            <div class="user-avatar"><?php echo strtoupper(substr($staff_name, 0, 2)); ?></div>
                            <div class="user-info">
                                <div class="user-name"><?php echo $staff_name; ?></div>
                                <div class="user-role"><?php echo ucfirst($_SESSION['role'] ?? 'admin'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Update Club Form -->
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">
                            <i class="fas fa-edit" style="margin-right: 8px; color: var(--primary-color);"></i>
                            Update Club: <?= htmlspecialchars($club['club_name']) ?>
                        </h2>
                        <div class="table-meta">
                            <i class="fas fa-info-circle"></i>
                            Modify club details below
                        </div>
                    </div>

                    <div style="padding: 32px;">
                        <form method="post" style="max-width: 600px;">
                            <div style="display: grid; gap: 24px;">
                                <!-- Club Name -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-users" style="margin-right: 6px; color: var(--primary-color);"></i>
                                        Club Name *
                                    </label>
                                    <input type="text" name="club_name" class="form-input" required 
                                           value="<?= htmlspecialchars($club['club_name']) ?>"
                                           placeholder="Enter club name" 
                                           style="padding: 12px 16px; font-size: 14px;">
                                </div>

                                <!-- Type -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-tag" style="margin-right: 6px; color: var(--primary-color);"></i>
                                        Club Type *
                                    </label>
                                    <select name="type" class="form-select" required 
                                            style="padding: 12px 16px; font-size: 14px;">
                                        <option value="">-- Select Type --</option>
                                        <option value="Open" <?= $club['type'] == 'Open' ? 'selected' : '' ?>>Open</option>
                                        <option value="Close" <?= $club['type'] == 'Close' ? 'selected' : '' ?>>Close</option>
                                    </select>
                                </div>

                                <!-- Advisor Name -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-user-tie" style="margin-right: 6px; color: var(--primary-color);"></i>
                                        Advisor Name *
                                    </label>
                                    <input type="text" name="adv_name" class="form-input" required 
                                           value="<?= htmlspecialchars($club['adv_name']) ?>"
                                           placeholder="Enter advisor name" 
                                           style="padding: 12px 16px; font-size: 14px;">
                                </div>

                                <!-- Advisor Contact -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-phone" style="margin-right: 6px; color: var(--primary-color);"></i>
                                        Advisor Contact *
                                    </label>
                                    <input type="text" name="adv_tel" class="form-input" required 
                                           value="<?= htmlspecialchars($club['adv_tel']) ?>"
                                           placeholder="Enter contact number" 
                                           style="padding: 12px 16px; font-size: 14px;">
                                </div>

                                <!-- Advisor Email -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-envelope" style="margin-right: 6px; color: var(--primary-color);"></i>
                                        Advisor Email *
                                    </label>
                                    <input type="email" name="adv_email" class="form-input" required 
                                           value="<?= htmlspecialchars($club['adv_email']) ?>"
                                           placeholder="Enter email address" 
                                           style="padding: 12px 16px; font-size: 14px;">
                                </div>

                                <!-- Advisor Staff Number -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-id-badge" style="margin-right: 6px; color: var(--primary-color);"></i>
                                        Advisor Staff Number *
                                    </label>
                                    <input type="text" name="adv_num" class="form-input" required 
                                           value="<?= htmlspecialchars($club['adv_num']) ?>"
                                           placeholder="Enter staff number" 
                                           style="padding: 12px 16px; font-size: 14px;">
                                </div>

                                <!-- Status -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-toggle-on" style="margin-right: 6px; color: var(--primary-color);"></i>
                                        Club Status *
                                    </label>
                                    <select name="status" class="form-select" required 
                                            style="padding: 12px 16px; font-size: 14px;">
                                        <option value="">-- Select Status --</option>
                                        <option value="Active" <?= strtolower($club['status']) == 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="Disband" <?= strtolower($club['status']) == 'disband' ? 'selected' : '' ?>>Disband</option>
                                    </select>
                                </div>

                                <!-- Action Buttons -->
                                <div style="display: flex; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border-light);">
                                    <button type="submit" class="btn btn-primary" style="min-width: 140px;">
                                        <i class="fas fa-save"></i>
                                        Update Club
                                    </button>
                                    <a href="view_club.php" class="btn btn-secondary" style="min-width: 140px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                        <i class="fas fa-times"></i>
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div id="logoutModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-power-off"></i>
            </div>
            <h2 class="modal-title">Logout</h2>
            <p class="modal-message">Are you sure you want to logout?</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="hideLogoutModal()">Cancel</button>
                <button class="modal-btn modal-btn-confirm" onclick="proceedLogout()">Logout</button>
            </div>
        </div>
    </div>
</body>
</html>
