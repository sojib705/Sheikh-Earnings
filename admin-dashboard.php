<?php
// ১. সেশন শুরু এবং অ্যাডমিন সিকিউরিটি ডাবল-লক চেক
session_start();

// আপনার নির্দিষ্ট করে দেওয়া স্পেশাল অ্যাডমিন জিমেইল দিয়ে ভ্যালিডেশন
$admin_email = "sojibmk3899@gmail.com";

if (!isset($_SESSION['user_logged_in']) || !isset($_SESSION['user_gmail']) || strcasecmp($_SESSION['user_gmail'], $admin_email) !== 0) {
    // লগইন করা না থাকলে বা জিমেইলটি sojibmk3899@gmail.com না হলে সরাসরি লগইন পেজে ধাক্কা দিয়ে পাঠিয়ে দেবে
    header("Location: login.php?error=unauthorized");
    exit();
}

// ওপরে আপনার তৈরি করা মূল হেডার এবং সাইডবার যুক্ত করা
include('includes/header.php');

// অ্যাডমিন প্যানেলের কোন ট্যাব খোলা আছে তা ডিটেক্ট করা (ডিফল্ট হলো 'dashboard')
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>

<!-- অ্যাডমিন প্যানেলের মূল বডি কন্টেন্ট -->
<main class="main-content">
    
    <!-- ট্যাবগুলোর ওপরের টাইটেল -->
    <h2 class="page-title">
        <?php 
            if($current_tab === 'dashboard') echo "অ্যাডমিন মাস্টার প্যানেল";
            elseif($current_tab === 'tasks') echo "কাজ নিয়ন্ত্রণ কেন্দ্র";
            elseif($current_tab === 'users') echo "ইউজার দৈনিক লিমিট সেটআপ";
            elseif($current_tab === 'withdraw') echo "উইথড্র রিকোয়েস্ট অনুমোদন";
        ?>
    </h2>

    <div style="background: #E8F8F5; color: #2ECC71; padding: 10px 15px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; font-weight: 500;">
        <i class="fa-solid fa-user-shield"></i> লগইন অ্যাকাউন্ট: <?php echo htmlspecialchars($_SESSION['user_gmail']); ?> (অ্যাডমিন)
    </div>

    <?php if ($current_tab === 'dashboard'): ?>
        <!-- ==========================================================================
           ১. মূল মাস্টার ড্যাশবোর্ড গ্রিড (৪টি বাটন সিস্টেম)
           ========================================================================== -->
        <p style="color: #666; font-size: 13px; margin-bottom: 20px;">স্বাগতম সোজিব! নিচের মাস্টার বাটনগুলো ব্যবহার করে আপনার ওয়েবসাইট কন্ট্রোল করুন:</p>
        
        <div class="admin-grid">
            
            <a href="admin-dashboard.php?tab=tasks" class="admin-master-btn">
                <i class="fa-solid fa-list-check"></i>
                <span>কাজ ইডিট/ডিলিট</span>
            </a>
            
            <a href="admin-dashboard.php?tab=users" class="admin-master-btn">
                <i class="fa-solid fa-users-gear"></i>
                <span>ইউজার লিমিট</span>
            </a>
            
            <a href="admin-dashboard.php?tab=withdraw" class="admin-master-btn">
                <i class="fa-solid fa-money-bill-transfer"></i>
                <span>উইথড্র রিকোয়েস্ট</span>
            </a>
            
            <a href="api/logout.php" class="admin-master-btn" style="border-color: #FDEDEC;">
                <i class="fa-solid fa-power-off" style="color: #E74C3C;"></i>
                <span style="color: #E74C3C;">লগআউট করুন</span>
            </a>
            
        </div>


    <?php elseif ($current_tab === 'tasks'): ?>
        <!-- ==========================================================================
           ২. কাজ নিয়ন্ত্রণ কেন্দ্র (Manage Tasks)
           ========================================================================== -->
        <div class="form-box">
            <h3 style="margin-bottom: 15px; font-size: 15px;"><i class="fa-solid fa-plus" style="color: #FF6200;"></i> নতুন কাজ যুক্ত/আপডেট করুন</h3>
            <form action="api/admin-action.php" method="POST">
                <input type="hidden" name="action" value="manage_task">
                
                <div class="input-group">
                    <label>কাজের নাম (Task Name)</label>
                    <input type="text" name="task_name" placeholder="যেমন: 0F-2FA-HOTMAIL" required autocomplete="off">
                </div>
                
                <div class="input-group">
                    <label>কাজের রেট/টাকা (৳)</label>
                    <input type="number" name="task_price" placeholder="যেমন: 10" required autocomplete="off">
                </div>

                <div class="input-group">
                    <label>কাজের বিবরণ/নির্দেশনা</label>
                    <input type="text" name="task_desc" placeholder="কুকি ও অল এক্সেস মেইল লাগবে..." required autocomplete="off">
                </div>

                <button type="submit" class="submit-action-btn">
                    <i class="fa-solid fa-circle-check"></i> কাজ লাইভ করুন
                </button>
            </form>
        </div>
        <p style="text-align: center; margin-top: 15px;"><a href="admin-dashboard.php" style="color: #FF6200; font-size: 13px; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> প্যানেলে ফিরে যান</a></p>


    <?php elseif ($current_tab === 'users'): ?>
        <!-- ==========================================================================
           ৩. ইউজার দৈনিক লিমিট সেটআপ (User Limit Management)
           ========================================================================== -->
        <div class="form-box">
            <h3 style="margin-bottom: 15px; font-size: 15px;"><i class="fa-solid fa-sliders" style="color: #FF6200;"></i> ওয়ার্কারের কাজের লিমিট বদলান</h3>
            <form action="api/admin-action.php" method="POST">
                <input type="hidden" name="action" value="update_limit">
                
                <div class="input-group">
                    <label>ওয়ার্কার জিমেইল (Worker Gmail)</label>
                    <input type="email" name="worker_gmail" placeholder="worker@gmail.com" required autocomplete="off">
                </div>
                
                <div class="input-group">
                    <label>দৈনিক সর্বোচ্চ কাজের লিমিট (Daily Limit)</label>
                    <input type="number" name="daily_limit" placeholder="যেমন: 20" required autocomplete="off">
                </div>

                <button type="submit" class="submit-action-btn">
                    <i class="fa-solid fa-user-check"></i> লিমিট সেভ করুন
                </button>
            </form>
        </div>
        <p style="text-align: center; margin-top: 15px;"><a href="admin-dashboard.php" style="color: #FF6200; font-size: 13px; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> প্যানেলে ফিরে যান</a></p>


    <?php elseif ($current_tab === 'withdraw'): ?>
        <!-- ==========================================================================
           ৪. উইথড্র রিকোয়েস্ট অনুমোদন কেন্দ্র (Withdraw Approval Center)
           ========================================================================== -->
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ওয়ার্কার</th>
                        <th>মেথড ও নাম্বার</th>
                        <th>টাকা</th>
                        <th>অ্যাকশন বাটন</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- ডাটা স্ট্রাকচার উদাহরণ (api/admin-action.php-তে রিকোয়েস্ট পাঠানোর বাটনসহ) -->
                    <tr>
                        <td>
                            <strong>আরিফ হোসেন</strong><br>
                            <span style="font-size: 11px; color:#777;">arif@gmail.com</span>
                        </td>
                        <td>বিকাশ<br>01712345678</td>
                        <td style="font-weight: 700; color: #FF6200;">১২০৳</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <form action="api/admin-action.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="approve_withdraw">
                                    <input type="hidden" name="worker_gmail" value="arif@gmail.com">
                                    <input type="hidden" name="request_time" value="2026-06-13 10:00:00">
                                    <button type="submit" class="badge success" style="border:none; cursor:pointer;">Done</button>
                                </form>
                                
                                <form action="api/admin-action.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="reject_withdraw">
                                    <input type="hidden" name="worker_gmail" value="arif@gmail.com">
                                    <input type="hidden" name="request_time" value="2026-06-13 10:00:00">
                                    <button type="submit" class="badge danger" style="border:none; cursor:pointer;">Cancel</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p style="text-align: center; margin-top: 20px;"><a href="admin-dashboard.php" style="color: #FF6200; font-size: 13px; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> প্যানেলে ফিরে যান</a></p>

    <?php endif; ?>

</main>

<?php
// ফিক্সড বটম বার বা ফুটার স্ক্রিপ্ট যুক্ত করা
include('includes/bottom-nav.php');
?>
