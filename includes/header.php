<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <!-- Navbar Brand or Logo (Optional) -->
            <a class="navbar-brand" href="#">Brand</a>

            <!-- Navbar Toggler for Collapsible Content -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible Navbar Content -->
            
                <!-- Left-Aligned Welcome Message -->
                <span class="navbar-text text-gray-600 me-auto">
                    Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!
                </span>

                <!-- Right-Aligned Dropdown Menu -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                            <img class="img-profile rounded-circle ms-2" src="img/undraw_profile.svg" alt="Profile" style="width: 40px; height: 40px;">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i> Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
