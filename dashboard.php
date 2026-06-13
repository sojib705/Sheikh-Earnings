<?php
// ১. সেশন শুরু এবং সিকিউরিটি লক চেক
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'worker') {
    // লগইন করা না থাকলে সরাসরি লগইন পেজে রিডাইরেক্ট করবে
    header("Location: login.php");
    exit();
}

// ২. ইউজার ডাটা ভেরিয়েবলে সেট করা
$worker_name = $_SESSION['user_name'];
$worker_uid  = $_SESSION['user_uid'];
$worker_gmail = $_SESSION['user_gmail'];

// ৩. টপ হেডার ও সাইডবার মেনু যুক্ত করা
include('includes/header.php');

// URL থেকে কোন পেজ ওপেন হবে তা ডিটেক্ট করা (ডিফল্ট হলো 'home')
$current_page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>

<main class="main-content">

    <?php if ($current_page === 'home'): ?>
        <h2 class="page-title">আজকের এভেইলেবল কাজ</h2>
        
        <div class="job-card">
            <div class="job-info">
                <h3>0F-2FA-HOTMAIL/OUTLOOK</h3>
                <p>কুকি ফাইলসহ এবং অল এক্সেস মেল সাবমিট করতে হবে।</p>
            </div>
            <div class="job-price">১০৳</div>
        </div>

        <div class="form-box">
            <form action="api/submit-work-action.php" method="POST">
                <input type="hidden" name="task_name" value="0F-2FA-HOTMAIL">
                <input type="hidden" name="price" value="10">

                <div class="input-group">
                    <label><i class="fa-solid fa-fingerprint"></i> UID / অ্যাকাউন্ট আইডি</label>
                    <input type="text" name="uid" placeholder="UID দিন" required autocomplete="off">
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-key"></i> পাসওয়ার্ড</label>
                    <input type="text" name="password" placeholder="অ্যাকাউন্টের পাসওয়ার্ড দিন" required autocomplete="off">
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-shield-keyhole"></i> 2FA কোড / কি (Key)</label>
                    <input type="text" name="two_fa" placeholder="2FA সিক্রেট কোড দিন" autocomplete="off">
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-envelope-open"></i> মেইল এক্সেস পাসওয়ার্ড (যদি থাকে)</label>
                    <input type="text" name="mail_access" placeholder="Mail Access Password" autocomplete="off">
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-cookie-bite"></i> কুকি ডাটা (Cookie Text)</label>
                    <textarea name="cookie" rows="4" placeholder="এখান সম্পূর্ণ কুকি কোডটি পেস্ট করুন..." required></textarea>
                </div>

                <button type="submit" class="submit-action-btn">
                    <i class="fa-solid fa-paper-plane"></i> কাজ সাবমিট করুন
                </button>
            </form>
        </div>


    <?php elseif ($current_page === 'history'): ?>
        <h2 class="page-title">আপনার কাজের হিস্ট্রি</h2>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>তারিখ ও সময়</th>
                        <th>কাজের নাম</th>
                        <th>UID</th>
                        <th>স্ট্যাটাস</th>
                        <th>টাকা</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2026-06-13 10:30</td>
                        <td>0F-2FA-HOTMAIL</td>
                        <td>uid_884732</td>
                        <td><span class="badge success">Done</span></td>
                        <td>১০৳</td>
                    </tr>
                    <tr>
                        <td>2026-06-13 09:15</td>
                        <td>0F-2FA-HOTMAIL</td>
                        <td>uid_110293</td>
                        <td><span class="badge pending">Pending</span></td>
                        <td>১০৳</td>
                    </tr>
                </tbody>
            </table>
        </div>


    <?php elseif ($current_page === 'withdraw'): ?>
        <h2 class="page-title">টাকা উত্তোলন (Withdraw)</h2>
        
        <div class="form-box">
            <form action="api/withdraw-action.php" method="POST">
                
                <div class="input-group">
                    <label><i class="fa-solid fa-money-check-dollar"></i> পেমেন্ট মেথড সিলেক্ট করুন</label>
                    <select name="method" required>
                        <option value="বিকাশ">বিকাশ (Personal)</option>
                        <option value="নগদ">নগদ (Personal)</option>
                        <option value="রকেট">রকেট (Personal)</option>
                    </select>
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-phone"></i> অ্যাকাউন্ট নাম্বার</label>
                    <input type="number" name="number" placeholder="017XXXXXXXX" required autocomplete="off">
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-bangladeshi-taka-sign"></i> টাকার পরিমাণ (সর্বনিম্ন ৫০৳)</label>
                    <input type="number" name="amount" min="50" placeholder="৫০ বা তার বেশি দিন" required autocomplete="off">
                </div>

                <button type="submit" class="submit-action-btn">
                    <i class="fa-solid fa-wallet"></i> উইথড্র রিকোয়েস্ট পাঠান
                </button>
            </form>
        </div>


    <?php elseif ($current_page === 'profile'): ?>
        <h2 class="page-title">আপনার প্রোফাইল</h2>
        
        <div class="form-box" style="text-align: center;">
            <div class="profile-avatar" style="margin: 0 auto 15px auto; background-color: #FFF0E6; color: #FF6200;">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <h3 style="margin-bottom: 5px;"><?php echo htmlspecialchars($worker_name); ?></h3>
            <p style="color: #777; font-size: 13px; margin-bottom: 20px;">ID: <?php echo htmlspecialchars($worker_uid); ?></p>
            
            <div style="text-align: left; background: #F8F9FA; padding: 15px; border-radius: 8px; font-size: 14px;">
                <p style="margin-bottom: 8px;"><strong>অ্যাকাউন্ট জিমেইল:</strong> <?php echo htmlspecialchars($worker_gmail); ?></p>
                <p style="margin-bottom: 8px;"><strong>অ্যাকাউন্ট টাইপ:</strong> সক্রিয় ওয়ার্কার</p>
                <p><strong>দৈনিক কাজের লিমিট:</strong> অ্যাডমিন নির্ধারিত</p>
            </div>
        </div>

    <?php endif; ?>

</main>

<?php
// ৪. স্ক্রিনের নিচের ৪টি ফিক্সড বাটন সমৃদ্ধ বটম বার ইনক্লুড করা
include('includes/bottom-nav.php');
?>
