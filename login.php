<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Worker Login - Sheikh Earning</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="public-body">

    <div class="login-container">
        <h2>ওয়ার্কার লগইন</h2>
        <p class="login-subtitle">আপনার অ্যাকাউন্ট তথ্য দিয়ে প্রবেশ করুন</p>

        <form action="api/login-action.php" method="POST" class="login-form">
            
            <div class="input-group">
                <label><i class="fa-solid fa-envelope"></i> জিমেইল অ্যাকাউন্ট</label>
                <input type="email" name="gmail" placeholder="example@gmail.com" required autocomplete="off">
            </div>
            
            <div class="input-group">
                <label><i class="fa-solid fa-lock"></i> পাসওয়ার্ড</label>
                <input type="password" name="password" placeholder="******" required>
            </div>
            
            <button type="submit" class="submit-login-btn">
                লগইন করুন <i class="fa-solid fa-right-to-bracket"></i>
            </button>
            
        </form>

        <div class="admin-contact-notice">
            <i class="fa-solid fa-circle-exclamation"></i>
            <p>আপনার কি কাজের অ্যাকাউন্ট নেই? নতুন অ্যাকাউন্টের জন্য অনুগ্রহ করে আমাদের অফিশিয়াল অ্যাডমিনের সাথে যোগাযোগ করুন।</p>
        </div>
    </div>

</body>
</html>
