// ==========================================================================
// ১. ৩-ডট মেনু (Sidebar) ওপেন এবং ক্লোজ করার লজিক
// ==========================================================================
function toggleSidebar() {
    const sidebar = document.getElementById('sidebarMenu');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar && overlay) {
        // active ক্লাসটি অন থাকলে অফ করবে, অফ থাকলে অন করবে
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }
}

// ==========================================================================
// ২. নোটিফিকেশন বেল ক্লিক এবং নোটিশ দেখার লজিক
// ==========================================================================
document.addEventListener("DOMContentLoaded", function() {
    const openNoticeBtn = document.getElementById('openNoticeBtn');
    
    if (openNoticeBtn) {
        openNoticeBtn.addEventListener('click', function() {
            // যখনই ওয়ার্কার নোটিফিকেশনে ক্লিক করবে, লাল ডটটি গায়েব হয়ে যাবে
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                badge.style.display = 'none';
            }
            
            // এখানে অ্যালার্ট বা কাস্টম পপ-আপের মাধ্যমে নোটিশ শো করানো যাবে
            alert("অ্যাডমিন নোটিশ: আজকের কাজের নিয়মাবলি ভিডিও টিউটোরিয়ালে দেওয়া আছে। দয়া করে নিয়ম মেনে কুকিসহ কাজ সাবমিট করুন। ধন্যবাদ!");
        });
    }

    console.log("Sheikh Earning - System JS Loaded Successfully!");
});
