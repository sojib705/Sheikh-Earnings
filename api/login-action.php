<?php
// ১. সেশন শুরু করা
session_start();

// এটি Vercel বা লোকাল হোস্টে Composer-এর মাধ্যমে ইনস্টল হওয়া গুগল এপিআই লাইব্রেরি লোড করবে
require_once __DIR__ . '/../vendor/autoload.php';

// ২. ফর্ম থেকে ডাটা রিসিভ করা এবং সিকিউর করা
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_gmail = filter_var(trim($_POST['gmail']), FILTER_SANITIZE_EMAIL);
    $input_password = trim($_POST['password']);

    if (empty($input_gmail) || empty($input_password)) {
        header("Location: ../login.php?error=empty_fields");
        exit();
    }

    // ৩. গুগল শিট এপিআই ক্লায়েন্ট সেটআপ করা
    // (Vercel এনভায়রনমেন্ট ভেরিয়েবল থেকে স্বয়ংক্রিয়ভাবে সিক্রেট কি ও আইডি টেনে নেবে)
    $spreadsheetId = getenv('GOOGLE_SHEET_ID');
    $serviceAccountKeyJson = getenv('GOOGLE_PRIVATE_KEY'); 

    // যদি সরাসরি স্ট্রিং না পাওয়া যায়, তবে JSON ফাইল ডিকোড করার লজিক
    $credentials = json_decode($serviceAccountKeyJson, true);
    if (!$credentials) {
        // যদি ভেরিয়েবল ফাইল আকারে থাকে বা সরাসরি প্যাথ হয়
        $credentials = __DIR__ . '/../google-credentials.json'; 
    }

    try {
        $client = new Google\Client();
        $client->setAuthConfig($credentials);
        $client->addScope(Google\Service\Spreadsheet::SPREADSHEETS_READONLY);
        $service = new Google\Service\Spreadsheet($client);

        // ৪. গুগল শিটের 'User_Database' ট্যাব থেকে ডাটা রিড করা
        $range = 'User_Database!A2:E'; // ধরি A=Name, B=Gmail, C=Password, D=UID, E=Status
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $rows = $response->getValues();

        $login_success = false;
        $user_data = [];

        if (!empty($rows)) {
            foreach ($rows as $row) {
                // কলাম ইনডেক্স অনুযায়ী ডাটা ম্যাচিং (০ = Name, ১ = Gmail, ২ = Password)
                $sheet_gmail = isset($row[1]) ? trim($row[1]) : '';
                $sheet_password = isset($row[2]) ? trim($row[2]) : '';

                if (strcasecmp($sheet_gmail, $input_gmail) === 0 && $sheet_password === $input_password) {
                    
                    // ইউজার যদি অ্যাডমিন দ্বারা ব্লক বা ইনঅ্যাক্টিভ থাকে
                    $sheet_status = isset($row[4]) ? strtolower(trim($row[4])) : 'active';
                    if ($sheet_status === 'blocked' || $sheet_status === 'inactive') {
                        header("Location: ../login.php?error=account_disabled");
                        exit();
                    }

                    $login_success = true;
                    $user_data = [
                        'name' => isset($row[0]) ? $row[0] : 'ওয়ার্কার',
                        'gmail' => $sheet_gmail,
                        'uid' => isset($row[3]) ? $row[3] : '0000',
                        'role' => 'worker'
                    ];
                    break;
                }
            }
        }

        // ৫. লগইন সফল হলে সেশন লক করা এবং ড্যাশবোর্ডে পাঠানো
        if ($login_success) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_name'] = $user_data['name'];
            $_SESSION['user_gmail'] = $user_data['gmail'];
            $_SESSION['user_uid'] = $user_data['uid'];
            $_SESSION['user_role'] = $user_data['role'];

            header("Location: ../dashboard.php");
            exit();
        } else {
            // পাসওয়ার্ড বা জিমেইল ভুল হলে ফেরত পাঠানো
            header("Location: ../login.php?error=invalid_credentials");
            exit();
        }

    } catch (Exception $e) {
        // এপিআই কানেকশনে কোনো ত্রুটি হলে
        header("Location: ../login.php?error=server_error");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
