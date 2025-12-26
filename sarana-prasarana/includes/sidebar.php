<nav class="sidebar-menu">
            <ul>
                <li>
                    <a href="../pages/dashboard.php" class="menu-item">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                <li>
                    <a href="../pages/sarana/index.php" class="menu-item">
                        <i class="fas fa-box"></i> Data Sarana
                    </a>
                </li>

                <li>
                    <a href="../pages/maintenance/index.php" class="menu-item">
                        <i class="fas fa-tools"></i> Pemeliharaan
                    </a>
                </li>

                <li>
                    <a href="../pages/laporan/kondisi.php" class="menu-item">
                        <i class="fas fa-file-alt"></i> Laporan
                    </a>
                </li>

                <?php if ($_SESSION['role'] == 'admin'): ?>
                <li>
                    <a href="../pages/user/index.php" class="menu-item">
                        <i class="fas fa-users"></i> Kelola User
                    </a>
                </li>

                <li>
                    <a href="../pages/kategori/index.php" class="menu-item">
                        <i class="fas fa-list"></i> Kategori
                    </a>
                </li>
                
                <?php endif; ?>
            </ul>
        </nav>