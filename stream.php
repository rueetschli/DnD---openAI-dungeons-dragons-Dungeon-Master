<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/event-stream");
header("X-Accel-Buffering: no");
set_time_limit(0);
session_start();
$postData = $_SESSION['data'];
$responsedata = "";
$ch = curl_init();
$OPENAI_API_KEY = "";

// Der folgende Code dient zum Abrufen des API-Schlüssels aus einer Datei und verwendet eine Polling-Methode. Konfigurieren Sie den API-Schlüssel in der Datei "apikey.php".
$content = "<?php header('HTTP/1.1 404 Not Found');exit; ?>\n";
$line = 0;
$handle = fopen(__DIR__ . "/apikey.php", "r") or die("Schreiben der Datei fehlgeschlagen.");
if ($handle) {
    while (($buffer = fgets($handle)) !== false) {
        $line++;
        if ($line == 2) {
            $OPENAI_API_KEY = str_replace("\n", "", $buffer);
        }
        if ($line > 2) {
            $content .= $buffer;
        }
    }
    fclose($handle);
}
$content .= $OPENAI_API_KEY . "\n";
$handle = fopen(__DIR__ . "/apikey.php", "w") or die("Schreiben der Datei fehlgeschlagen.");
if ($handle) {
    fwrite($handle, $content);
    fclose($handle);
}

// Wenn der Benutzer einen benutzerdefinierten API-Schlüssel eingegeben hat, wird dieser verwendet, falls er in der Sitzung vorhanden ist.
if (isset($_SESSION['key'])) {
    $OPENAI_API_KEY = $_SESSION['key'];
}
session_write_close();
$headers  = [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer ' . $OPENAI_API_KEY
];

setcookie("errcode", ""); // EventSource kann keine Fehlermeldungen empfangen, daher wird ein Cookie verwendet
setcookie("errmsg", "");

$callback = function ($ch, $data) {
    global $responsedata;
    $complete = json_decode($data);
    if (isset($complete->error)) {
        setcookie("errcode", $complete->error->code);
        setcookie("errmsg", $data);
        if (strpos($complete->error->message, "Rate limit reached") === 0) { // Behandlung von Fehlercodes für die Überschreitung des Zugriffslimits
            setcookie("errcode", "rate_limit_reached");
        }
        if (strpos($complete->error->message, "Your access was terminated") === 0) { // Behandlung von Fehlercodes für unzulässige Nutzung und Sperrung des Zugriffs
            setcookie("errcode", "access_terminated");
        }
        if (strpos($complete->error->message, "You didn't provide an API key") === 0) { // Fehlercode, wenn kein API-Schlüssel angegeben wurde
            setcookie("errcode", "no_api_key");
        }
        if (strpos($complete->error->message, "You exceeded your current quota") === 0) { // Fehlercode für unzureichendes Kontingent des API-Schlüssels
            setcookie("errcode", "insufficient_quota");
        }
        if (strpos($complete->error->message, "That model is currently overloaded") === 0) { // Fehlercode für überlastetes OpenAI-Modell
            setcookie("errcode", "model_overloaded");
        }
        $responsedata = $data;
    } else {
        echo $data;
        $responsedata .= $data;
        flush();
    }
    return strlen($data);
};

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // Verbindungs-Timeout auf 120 Sekunden setzen
curl_setopt($ch, CURLOPT_MAXREDIRS, 3); // Maximal 3 Weiterleitungen erlauben
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Automatische Weiterleitung zulassen
curl_setopt($ch, CURLOPT_AUTOREFERER, true); // Automatischen Referer setzen
//curl_setopt($ch, CURLOPT_PROXY, "http://127.0.0.1:1081");

curl_exec($ch);
curl_close($ch);

$answer = "";
if (substr(trim($responsedata), -6) == "[DONE]") {
    $responsedata = substr(trim($responsedata), 0, -6) . "{";
}
$responsearr = explode("}\n\ndata: {", $responsedata);

foreach ($responsearr as $msg) {
    $contentarr = json_decode("{" . trim($msg) . "}", true);
    if (isset($contentarr['choices'][0]['delta']['content'])) {
        $answer .= $contentarr['choices'][0]['delta']['content'];
    }
}
$questionarr = json_decode($postData, true);
$filecontent = $_SERVER["REMOTE_ADDR"] . " | " . date("Y-m-d H:i:s") . "\n";
$filecontent .= "F:" . end($questionarr['messages'])['content'] .  "\nA:" . trim($answer) . "\n----------------\n";
$myfile = fopen(__DIR__ . "/chatlog.php", "a") or die("Schreiben der Datei fehlgeschlagen.");
fwrite($myfile, $filecontent);
fclose($myfile);
?>
