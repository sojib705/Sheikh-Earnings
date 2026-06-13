<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sheikh Earning - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- টপ ফিক্সড হেডার বার শুরু -->
<header class="top-header">
    <div class="header-container">
        <!-- ৩-ডট বা হ্যামবার্গার মেনু বাটন -->
        <button class="menu-btn" onclick="toggleSidebar()">
            <i class="fa-solid fa-bars"></i>
        </button>
        
        <!-- লোগো বা সাইটের নাম -->
        <div class="logo">
            <h1>SHEIKH EARNING</h1>
        </div>
        
        <!-- নোটিফিকেশন বেল আইকন লাল ডটসহ -->
        <div class="notification-container" id="openNoticeBtn">
            <i class="fa-solid fa-bell"></i>
            <div class="notification-badge"></div>
        </div>
    </div>
</header>
<!-- টপ ফিক্সড হেডার বার শেষ -->

<?php 
// সাইডবারটি হেডারের সাথেই স্বয়ংক্রিয়ভাবে লোড হয়ে যাবে
include('sidebar.php'); 
?>
