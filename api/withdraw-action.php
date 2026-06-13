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
    $method = trim($_POST['method']); // বিকাশ, নগদ অথবা রকেট
    $number = trim($_POST['number']); // অ্যাকাউন্ট নাম্বার
    $amount = filter_var(trim($_POST['amount']), FILTER_VALIDATE_INT);

    // ২. প্রাথমিক ভ্যালিডেশন চেক (সর্বনিম্ন ৫০৳ উইথড্র)
    if (empty($method) || empty($number) || $amount === false) {
        header("Location: ../dashboard.php?page=withdraw&status=empty_fields");
        exit();
    }

    if ($amount < 50) {
        header("Location: ../dashboard.php?page=withdraw&status=low_amount");
        exit();
    }

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

        // ৪. চেক করা—'Withdraw_Requests' নামের ট্যাবটি গুগল শিটে আছে কিনা
        $spreadsheet = $service->spreadsheets->get($spreadsheetId);
        $sheets = $spreadsheet->getSheets();
        $sheet_exists = false;
        $target_sheet_name = 'Withdraw_Requests';

        foreach ($sheets as $s) {
            if ($s->getProperties()->getTitle() === $target_sheet_name) {
                $sheet_exists = true;
                break;
            }
        }

        // ৫. যদি ট্যাবটি না থাকে, তবে নতুন ট্যাব তৈরি করে হেডার সেট করা
        if (!$sheet_exists) {
            $newSheetRequest = new Google\Service\Spreadsheet\BatchUpdateSpreadsheetRequest([
                'requests' => [
                    'addSheet' => [
                        'properties' => ['title' => $target_sheet_name]
                    ]
                ]
            ]);
            $service->spreadsheets->batchUpdate($spreadsheetId, $newSheetRequest);

            // নতুন টেবিলের হেডার কলাম
            $header_values = [
                ['UID', 'Worker_Name', 'Gmail', 'Payment_Method', 'Account_Number', 'Amount', 'Date_Time', 'Status']
            ];
            $body = new Google\Service\Spreadsheet\ValueRange(['values' => $header_values]);
            $service->spreadsheets_values->update(
                $spreadsheetId, 
                $target_sheet_name . '!A1', 
                $body, 
                ['valueInputOption' => 'USER_ENTERED']
            );
        }

        // ৬. উইথড্র ডাটা নতুন লাইনে পুশ করা
        $uid         = $_SESSION['user_uid'];
        $worker_name = $_SESSION['user_name'];
        $gmail       = $_SESSION['user_gmail'];
        $date_time   = date('Y-m-d H:i:s');
        $status      = ""; // অ্যাডমিন প্যানেল থেকে 'Done' বা 'Cancel' করার জন্য প্রথম অবস্থায় ফাঁকা থাকবে

        $row_data = [
            [$uid, $worker_name, $gmail, $method, $number, $amount, $date_time, $status]
        ];

        $append_body = new Google\Service\Spreadsheet\ValueRange(['values' => $row_data]);
        $service->spreadsheets_values->append(
            $spreadsheetId, 
            $target_sheet_name . '!A:H', 
            $append_body, 
            ['valueInputOption' => 'USER_ENTERED']
        );

        // সফল হলে মেসেজসহ উইথড্র পেজে ফেরত পাঠানো
        header("Location: ../dashboard.php?page=withdraw&status=success");
        exit();

    } catch (Exception $e) {
        // সার্ভার বা ডাটাবেজ এরর হলে
        header("Location: ../dashboard.php?page=withdraw&status=server_error");
        exit();
    }
} else {
    header("Location: ../dashboard.php");
    exit();
}
