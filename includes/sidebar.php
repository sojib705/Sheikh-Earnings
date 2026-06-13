<!-- সাইড নেভিগেশন মেনু শুরু -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<aside class="sidebar" id="sidebarMenu">
    <!-- ওপরে ইউজারের নাম এবং প্রোফাইল সেকশন -->
    <div class="sidebar-profile">
        <div class="profile-avatar">
            <i class="fa-solid fa-user-tie"></i>
        </div>
        <div class="profile-info">
            <!-- এখানে পরবর্তীতে পিএইচপি সেশন থেকে লগইন করা ইউজারের নাম চলে আসবে -->
            <h3 class="worker-name">ইউজার প্রোফাইল</h3>
            <p class="worker-role">সক্রিয় ওয়ার্কার</p>
        </div>
    </div>

    <!-- মেনু আইটেম সমূহ -->
    <div class="sidebar-menu-items">
        
        <div class="menu-group-title">সাধারণ</div>
        
        <a href="dashboard.php" class="menu-item active">
            <i class="fa-solid fa-house"></i> হোম
        </a>
        
        <a href="#" class="menu-item">
            <i class="fa-solid fa-circle-info"></i> আমাদের সম্পর্কে
        </a>
        
        <a href="#" class="menu-item">
            <i class="fa-solid fa-shield-halved"></i> গোপনীয়তা নীতি
        </a>
        
        <a href="#" class="menu-item">
            <i class="fa-solid fa-gavel"></i> শর্তাবলী ও নিয়মাবলী
        </a>
        
        <a href="#" class="menu-item">
            <i class="fa-solid fa-video"></i> ভিডিও টিউটোরিয়াল <span class="badge-new">NEW</span>
        </a>
        
        <a href="#" class="menu-item">
            <i class="fa-solid fa-headset"></i> সাপোর্ট সেন্টার
        </a>

    </div>

    <!-- একদম নিচের বন্ধ করুন বাটন -->
    <div class="sidebar-footer">
        <button class="close-sidebar-btn" onclick="toggleSidebar()">
            <i class="fa-solid fa-xmark"></i> বন্ধ করুন
        </button>
    </div>
</aside>
<!-- সাইড নেভিগেশন মেনু শেষ -->
