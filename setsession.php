<?php
$context = json_decode($_POST['context'] ?: "[]") ?: [];
if (mb_substr($_POST["message"], 0, 6, 'UTF-8') === 'Zeichne' || mb_substr($_POST["message"], 0, 6, 'UTF-8') === 'zeichne') {
    $postData = [
        "prompt" => $_POST['message'],
        "n" => 1,
        "size" => "1024x1024"
    ];
} else {
    
$postData = [
    "model" => "gpt-4",
    "temperature" => 0.8,
    "stream" => true,
    "messages" => [],
];

$messagesFile = 'messages.txt';
if (file_exists($messagesFile)) {
    $messagesContent = file($messagesFile, FILE_IGNORE_NEW_LINES);
    foreach ($messagesContent as $line) {
        $postData['messages'][] = [
            "role" => "system",
            "content" => $line
        ];
    }
}

// Rest deines Codes...

    if (!empty($context)) {
        $context = array_slice($context, -5);
        foreach ($context as $message) {
            $postData['messages'][] = ['role' => 'user', 'content' => $message[0]];
            $postData['messages'][] = ['role' => 'assistant', 'content' => $message[1]];
        }
    }
    $postData['messages'][] = ['role' => 'user', 'content' => $_POST['message']];
}
$postData = json_encode($postData);
session_start();
$_SESSION['data'] = $postData;
if ((isset($_POST['key'])) && (!empty($_POST['key']))) {
    $_SESSION['key'] = $_POST['key'];
}
echo '{"success":true}';
