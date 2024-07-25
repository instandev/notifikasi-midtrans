<?php

require 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Google\Cloud\Firestore\FirestoreClient;

// Inisialisasi Firebase
$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/serviceAccountKey.json');

// Inisialisasi Firestore
$firestore = $factory->createFirestore()->database();

// Data yang akan diunggah
$data = [
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'age' => 30
];

// Menambahkan data ke koleksi "users"
$collectionReference = $firestore->collection('notifikasi');
$documentReference = $collectionReference->add($data);

echo 'Data berhasil diunggah dengan ID dokumen: ' . $documentReference->id();

?>
