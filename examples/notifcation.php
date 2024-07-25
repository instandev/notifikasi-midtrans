<?php

require_once(dirname(__FILE__) . '/Midtrans.php');
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$serverKey = 'SB-Mid-server-0QkEMWxexzqh3j_G71xMLEew';
$notif = new \Midtrans\Notification();

$transaction = $notif->transaction_status;
$type = $notif->payment_type;
$order_id = $notif->order_id;
$fraud = $notif->fraud_status;

// Inisialisasi Firebase
$serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/path/to/serviceAccount.json');
$firebase = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->create();

$firestore = $firebase->firestore();
$collection = $firestore->collection('notifikasi');
$document = $collection->document($order_id);

function updateFirestore($document, $status) {
    $document->set([
        'status' => $status
    ], ['merge' => true]);
}

if ($transaction == 'capture') {
    if ($type == 'credit_card') {
        if ($fraud == 'accept') {
            updateFirestore($document, 'Success');
            echo "Transaction order_id: " . $order_id . " successfully captured using " . $type;
        }
    }
} else if ($transaction == 'settlement') {
    updateFirestore($document, 'Settlement');
    echo "Transaction order_id: " . $order_id . " successfully transferred using " . $type;
} else if ($transaction == 'pending') {
    updateFirestore($document, 'Pending');
    echo "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
} else if ($transaction == 'deny') {
    updateFirestore($document, 'Denied');
    echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
} else if ($transaction == 'expire') {
    updateFirestore($document, 'Expired');
    echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
} else if ($transaction == 'cancel') {
    updateFirestore($document, 'Canceled');
    echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
}
?>
