<?php
try {
    echo 'host=' . gethostbyname('db') . PHP_EOL;
    $pdo = new PDO('mysql:host=db;port=3306;dbname=cdattg','cdattg_user','password');
    echo 'pdo=ok' . PHP_EOL;
} catch (Throwable $e) {
    echo 'pdo=' . $e->getMessage() . PHP_EOL;
}
