<?php
require_once '../config/database.php';

// Cek apakah admin sudah login
if (!isAdminLoggedIn()) {
    header('Location: ../admin-login.php');
    exit();
}

// Ambil data admin dari session
$admin_id = $_SESSION['admin_id'] ?? 0;
$admin_nama = $_SESSION['admin_nama'] ?? 'Admin';
$admin_role = $_SESSION['admin_role'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Aplikasi Kelulusan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            overflow-x: hidden;
        }
        
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            color: #fff;
            transition: all 0.3s;
            position: fixed;
            z-index: 1000;
        }
        
        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        #sidebar ul.components {
            padding: 20px 0;
        }
        
        #sidebar ul li a {
            padding: 12px 20px;
            display: block;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        #sidebar ul li a:hover,
        #sidebar ul li a.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-left: 3px solid var(--secondary-color);
        }
        
        #sidebar ul li a i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
        }
        
        #content {
            width: calc(100% - var(--sidebar-width));
            margin-left: var(--sidebar-width);
            transition: all 0.3s;
            min-height: 100vh;
        }
        
        .admin-profile {
            text-align: center;
            padding: 20px;
        }
        
        .admin-avatar {
            width: 80px;
            height: 80px;
            background: var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            border: 3px solid rgba(255, 255, 255, 0.2);
        }
        
        .admin-avatar i {
            font-size: 2rem;
            color: white;
        }
        
        .navbar-top {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 20px;
        }
        
        .page-title {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .stat-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .badge-admin {
            background: linear-gradient(45deg, #8e44ad, #9b59b6);
        }
        
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -var(--sidebar-width);
            }
            
            #content {
                width: 100%;
                margin-left: 0;
            }
            
            #sidebar.active {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <div class="admin-profile">
                <div class="admin-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h5 class="mb-1"><?php echo htmlspecialchars($admin_nama); ?></h5>
                <p class="mb-0 text-muted small">
                    <span class="badge badge-admin"><?php echo $admin_role; ?></span>
                </p>
            </div>
        </div>
        
        <ul class="list-unstyled components">
            <li>
                <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="mt-3">
                <a href="#siswaSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                    <i class="fas fa-users"></i> Kelola Siswa
                </a>
                <ul class="collapse list-unstyled" id="siswaSubmenu">
                    <li>
                        <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                            <i class="fas fa-list"></i> Daftar Siswa
                        </a>
                    </li>
                    <li>
                        <a href="add-user.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add-user.php' ? 'active' : ''; ?>">
                            <i class="fas fa-user-plus"></i> Tambah Siswa
                        </a>
                    </li>
                </ul>
            </li>
            <li class="mt-2">
                <a href="#adminSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                    <i class="fas fa-user-cog"></i> Kelola Admin
                </a>
                <ul class="collapse list-unstyled" id="adminSubmenu">
                    <li>
                        <a href="admins.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admins.php' ? 'active' : ''; ?>">
                            <i class="fas fa-list"></i> Daftar Admin
                        </a>
                    </li>
                    <li>
                        <a href="add-admin.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add-admin.php' ? 'active' : ''; ?>">
                            <i class="fas fa-user-plus"></i> Tambah Admin
                        </a>
                    </li>
                </ul>
            </li>
            <li class="mt-2">
                <a href="timer-setting.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'timer-setting.php' ? 'active' : ''; ?>">
                    <i class="fas fa-clock"></i> Setting Timer
                </a>
            </li>
            <li class="mt-4">
                <a href="../../logout.php" class="text-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
        
        <div class="sidebar-footer text-center p-3">
            <small class="text-muted">v1.0.0 &copy; 2024</small>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div id="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand navbar-top">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-outline-secondary d-md-none">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>
                            <?php echo htmlspecialchars($admin_nama); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="../../logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Page Content -->
        <div class="container-fluid p-4">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="page-title">
                                <i class="fas fa-<?php 
                                $page_icon = '';
                                switch(basename($_SERVER['PHP_SELF'])) {
                                    case 'dashboard.php': $page_icon = 'tachometer-alt'; break;
                                    case 'users.php': 
                                    case 'add-user.php': 
                                    case 'edit-user.php': $page_icon = 'users'; break;
                                    case 'admins.php': 
                                    case 'add-admin.php': 
                                    case 'edit-admin.php': $page_icon = 'user-cog'; break;
                                    case 'timer-setting.php': $page_icon = 'clock'; break;
                                    default: $page_icon = 'cog';
                                }
                                echo $page_icon;
                                ?> me-2"></i>
                                <?php 
                                $page_title = '';
                                switch(basename($_SERVER['PHP_SELF'])) {
                                    case 'dashboard.php': $page_title = 'Dashboard'; break;
                                    case 'users.php': $page_title = 'Daftar Siswa'; break;
                                    case 'add-user.php': $page_title = 'Tambah Siswa'; break;
                                    case 'edit-user.php': $page_title = 'Edit Siswa'; break;
                                    case 'admins.php': $page_title = 'Daftar Admin'; break;
                                    case 'add-admin.php': $page_title = 'Tambah Admin'; break;
                                    case 'edit-admin.php': $page_title = 'Edit Admin'; break;
                                    case 'timer-setting.php': $page_title = 'Setting Timer'; break;
                                    case 'export-csv.php': $page_title = 'Export Data'; break;
                                }
                                echo $page_title;
                                ?>
                            </h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Admin</a></li>
                                    <li class="breadcrumb-item active"><?php echo $page_title; ?></li>
                                </ol>
                            </nav>
                        </div>
                        
                        <div class="btn-toolbar">
                            <?php if(basename($_SERVER['PHP_SELF']) == 'users.php'): ?>
                            <div class="btn-group me-2">
                                <a href="add-user.php" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-1"></i>Tambah Siswa
                                </a>
                                <a href="export-csv.php" class="btn btn-success">
                                    <i class="fas fa-file-export me-1"></i>Export CSV
                                </a>
                                <a href="import-csv.php" class="btn btn-info">
                                    <i class="fas fa-file-import me-1"></i>Import CSV
                                </a>
                            </div>
                            <?php elseif(basename($_SERVER['PHP_SELF']) == 'admins.php'): ?>
                            <a href="add-admin.php" class="btn btn-primary">
                                <i class="fas fa-user-plus me-1"></i>Tambah Admin
                            </a>
                            <?php endif; ?>
                            
                            <button class="btn btn-outline-secondary ms-2" onclick="window.print()">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content Area -->
            <div class="row">
                <div class="col-12">