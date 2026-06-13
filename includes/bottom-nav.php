<!-- বটম নেভিগেশন বার শুরু -->
<nav class="bottom-nav">
    <a href="dashboard.php?page=home" class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'active' : ''; ?>">
        <i class="fa-solid fa-house"></i>
        <span>হোম</span>
    </a>
    
    <a href="dashboard.php?page=history" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'history') ? 'active' : ''; ?>">
        <i class="fa-solid fa-file-invoice"></i>
        <span>কাজের হিস্ট্রি</span>
    </a>
    
    <a href="dashboard.php?page=withdraw" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'withdraw') ? 'active' : ''; ?>">
        <i class="fa-solid fa-wallet"></i>
        <span>লেনদেন</span>
    </a>
    
    <a href="dashboard.php?page=profile" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'profile') ? 'active' : ''; ?>">
        <i class="fa-solid fa-user"></i>
        <span>প্রোফাইল</span>
    </a>
</nav>
<!-- মেইন জাভাস্ক্রিপ্ট ফাইল লিঙ্ক -->
<script src="js/main.js"></script>
</body>
</html>
