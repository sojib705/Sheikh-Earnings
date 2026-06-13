<?php
// ১. সেশন এবং সিকিউরিটি চেক
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'worker') {
    header("Location: ../login.php");
    exit();
}

// Composer-এর মাধ্যমে ইনস্টল হওয়া গুগল এপিআই লাইব্রেরি লোড করা
require_once __DIR__ . '/../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ফর্ম থেকে ডাটা রিসিভ করা
    $task_name = trim($_POST['task_name']); // উদাহরণ: 0F-2FA-HOTMAIL/OUTLOOK
    $uid       = trim($_POST['uid']);
    $password  = trim($_POST['password']);
    $two_fa    = trim($_POST['two_fa']);
    $mail_acc  = trim($_POST['mail_access']);
    $cookie    = trim($_POST['cookie']); // নতুন কুকি ডাটা
    $price     = trim($_POST['price']);

    if (empty($task_name)) {
        header("Location: ../dashboard.php?page=home&status=error");
        exit();
    }

    // ২. ডাইনামিক পেজের নাম তৈরি করা (যেমন: 0F-2FA-HOTMAIL/OUTLOOK 14TH)
    // তারিখের ফরম্যাট: ১ বা ২ তারিখ হলে 1ST/2ND, ১৩ বা ১৪ তারিখ হলে 13TH/14TH
    $day = date('j');
    if (!in_array(($day % 100), array(11, 12, 13))) {
        switch ($day % 10) {
            case 1:  $suffix = 'ST'; break;
            case 2:  $suffix = 'ND'; break;
            case 3:  $suffix = 'RD'; break;
            default: $suffix = 'TH'; break;
        }
    } else {
        $suffix = 'TH';
    }
    $target_sheet_name = $task_name . ' ' . $day . $suffix;

    // ৩. গুগল শিট এপিআই ক্লায়েন্ট সেটআপ
    $spreadsheetId = getenv('GOOGLE_SHEET_ID');
    $credentials = json_decode(getenv('GOOGLE_PRIVATE_KEY'), true);
    if (!$credentials) {
        $credentials = __DIR__ . '/../google-credentials.json';
    }

    try {
        $client = new Google\Client();
        $client->setAuthConfig($credentials);
        $client->addScope(Google\Service\Spreadsheet::SPREADSHEETS);
        $service = new Google\Service\Spreadsheet($client);

        // ৪. চেক করা—গুগল শিটে এই নামের কোনো নির্দিষ্ট পেজ (Tab) অলরেডি আছে কিনা
        $spreadsheet = $service->spreadsheets->get($spreadsheetId);
        $sheets = $spreadsheet->getSheets();
        $sheet_exists = false;

        foreach ($sheets as $s) {
            if ($s->getProperties()->getTitle() === $target_sheet_name) {
                $sheet_exists = true;
                break;
            }
        }

        // ৫. যদি পেজটি না থাকে, তবে নতুন পেজ তৈরি করে হেডার কলামগুলো বসানো
        if (!$sheet_exists) {
            $newSheetRequest = new Google\Service\Spreadsheet\BatchUpdateSpreadsheetRequest([
                'requests' => [
                    'addSheet' => [
                        'properties' => ['title' => $target_sheet_name]
                    ]
                ]
            ]);
            $service->spreadsheets->batchUpdate($spreadsheetId, $newSheetRequest);

            // নতুন পেজের হেডার বা প্রথম লাইনের কলামগুলো সেট করা (Cookie কলামসহ)
            $header_values = [
                ['UID', 'Password', '2FA_Code', 'Mail_Access', 'Cookie', 'Worker_Name', 'Date_Time', 'Status', 'Price']
            ];
            $body = new Google\Service\Spreadsheet\ValueRange(['values' => $header_values]);
            $service->spreadsheets_values->update(
                $spreadsheetId, 
                $target_sheet_name . '!A1', 
                $body, 
                ['valueInputOption' => 'USER_ENTERED']
            );
        }

        // ৬. এবার ওয়ার্কারের সাবমিট করা ডাটা নতুন লাইনে পুশ করা
        $worker_name = $_SESSION['user_name'];
        $date_time   = date('Y-m-d H:i:s');
        $status      = ""; // প্রথম অবস্থায় ব্ল্যাঙ্ক বা পেন্ডিং থাকবে

        $row_data = [
            [$uid, $password, $two_fa, $mail_acc, $cookie, $worker_name, $date_time, $status, $price]
        ];

        $append_body = new Google\Service\Spreadsheet\ValueRange(['values' => $row_data]);
        $service->spreadsheets_values->append(
            $spreadsheetId, 
            $target_sheet_name . '!A:I', 
            $append_body, 
            ['valueInputOption' => 'USER_ENTERED']
        );

        // কাজ সফলভাবে জমা হলে হোমপেজে পাঠানো
        header("Location: ../dashboard.php?page=home&status=success");
        exit();

    } catch (Exception $e) {
        // কোনো সার্ভার বা এপিআই এরর হলে
        header("Location: ../dashboard.php?page=home&status=db_error");
        exit();
    }
} else {
    header("Location: ../dashboard.php");
    exit();
}
