<?php
// ১. সেশন এবং অ্যাডমিন সিকিউরিটি চেক
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Composer-এর মাধ্যমে ইনস্টল হওয়া গুগল এপিআই লাইব্রেরি লোড করা
require_once __DIR__ . '/../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim($_POST['action']); // 'update_limit', 'approve_withdraw', 'reject_withdraw', 'manage_task'

    // গুগল শিট ক্লায়েন্ট সেটআপ
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

        // ==========================================================================
        // কাজ ১: ওয়ার্কারের দৈনিক লিমিট পরিবর্তন করা (Update Worker Daily Limit)
        // ==========================================================================
        if ($action === 'update_limit') {
            $target_gmail = trim($_POST['worker_gmail']);
            $new_limit = filter_var(trim($_POST['daily_limit']), FILTER_VALIDATE_INT);

            // সম্পূর্ণ ইউজার লিস্ট রিড করা
            $range = 'User_Database!A2:E';
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $rows = $response->getValues();

            if (!empty($rows)) {
                $row_index = 2; // ১ নম্বর রো হলো হেডার, তাই ডাটা শুরু ২ থেকে
                foreach ($rows as $row) {
                    $sheet_gmail = isset($row[1]) ? trim($row[1]) : '';
                    if (strcasecmp($sheet_gmail, $target_gmail) === 0) {
                        // দৈনিক লিমিট থাকে E কলামে (কলাম ইনডেক্স ৫) -> অর্থাৎ 'User_Database!E' . $row_index
                        $update_range = 'User_Database!E' . $row_index;
                        $body = new Google\Service\Spreadsheet\ValueRange(['values' => [[$new_limit]]]);
                        
                        $service->spreadsheets_values->update(
                            $spreadsheetId, 
                            $update_range, 
                            $body, 
                            ['valueInputOption' => 'USER_ENTERED']
                        );
                        break;
                    }
                    $row_index++;
                }
            }
            header("Location: ../admin-dashboard.php?tab=users&status=limit_updated");
            exit();
        }

        // ==========================================================================
        // কাজ ২: উইথড্র রিকোয়েস্ট Accept বা Cancel করা (Withdraw Approval Center)
        // ==========================================================================
        if ($action === 'approve_withdraw' || $action === 'reject_withdraw') {
            $request_id = trim($_POST['request_time']); // ইউনিক আইডেন্টিফায়ার হিসেবে রিকোয়েস্ট টাইম ব্যবহার করছি
            $target_worker = trim($_POST['worker_gmail']);
            $status_text = ($action === 'approve_withdraw') ? 'Done' : 'Cancel';

            // উইথড্র লিস্ট রিড করা
            $range = 'Withdraw_Requests!A2:H';
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $rows = $response->getValues();

            if (!empty($rows)) {
                $row_index = 2;
                foreach ($rows as $row) {
                    $sheet_gmail = isset($row[2]) ? trim($row[2]) : '';
                    $sheet_time  = isset($row[6]) ? trim($row[6]) : '';

                    // জিমেইল এবং রিকোয়েস্ট সাবমিশনের সময় মিললে
                    if (strcasecmp($sheet_gmail, $target_worker) === 0 && $sheet_time === $request_id) {
                        // Status থাকে H কলামে (৮ নম্বর কলাম) -> অর্থাৎ 'Withdraw_Requests!H' . $row_index
                        $update_range = 'Withdraw_Requests!H' . $row_index;
                        $body = new Google\Service\Spreadsheet\ValueRange(['values' => [[$status_text]]]);
                        
                        $service->spreadsheets_values->update(
                            $spreadsheetId, 
                            $update_range, 
                            $body, 
                            ['valueInputOption' => 'USER_ENTERED']
                        );
                        break;
                    }
                    $row_index++;
                }
            }
            header("Location: ../admin-dashboard.php?tab=withdraw&status=payout_updated");
            exit();
        }

    } catch (Exception $e) {
        header("Location: ../admin-dashboard.php?status=error");
        exit();
    }
} else {
    header("Location: ../admin-dashboard.php");
    exit();
}
