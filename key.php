<?php
$content = "";
session_start();
if ((isset($_SESSION['admin'])) && ($_SESSION['admin'] == true)) {
    if (isset($_POST["message"])) {
        if ($_POST["action"] == "save") {
            $handle = fopen(__DIR__ . "/apikey.php", "w") or die("Schreiben der Datei fehlgeschlagen.");
            if ($handle) {
                fwrite($handle, "<?php header('HTTP/1.1 404 Not Found');exit; ?>\n" . $_POST["message"]);
                fclose($handle);
                exit;
            }
        } elseif ($_POST["action"] == "check") {
            $lines = explode("\n", $_POST["message"]);
            $i = 0;
            $validkey = "";
            $invalidkey = "";
            while ($i < count($lines)) {
                $line = $lines[$i];
                $headers  = [
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $line
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/models/gpt-4');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $response = curl_exec($ch);
                curl_close($ch);
                $complete = json_decode($response);
                if (isset($complete->error)) {
                    $invalidkey .= $line . "\n";
                } else {
                    $validkey .= $line . "\n";
                }
                $i++;
            }
            echo $validkey;
            exit;
        }
    }
    $line = 0;
    $handle = @fopen(__DIR__ . "/apikey.php", "r");
    if ($handle) {
        while (($buffer = fgets($handle)) !== false) {
            $line++;
            if ($line > 1) {
                $content .= $buffer;
            }
        }
        fclose($handle);
    }
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API_KEY Konfiguration</title>
    <script src="js/jquery-3.6.4.min.js"></script>
    <script src="js/layer.min.js" type="application/javascript"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        .container {
            margin: 50px auto;
            width: 80%;
            max-width: 800px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        textarea {
            width: calc(100% - 20px);
            height: 200px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            resize: none;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #3e8e41;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>API_KEY Konfiguration</h1>
        <textarea placeholder="Bitte eine Zeile pro Key eingeben" id="tt"><?php echo $content; ?></textarea>
        <button class="btn" onclick="checkit();">Gültigkeit überprüfen</button> <button class="btn" onclick="saveit();">Aktuelle Einstellungen speichern</button>
    </div>
</body>

<script>
    function saveit() {
        $.ajax({
            type: "POST",
            url: "key.php",
            data: {
                message: $("#tt").val(),
                action: "save",
            },
            success: function(results) {
                layer.msg('Einstellungen erfolgreich gespeichert. Bitte aktualisieren Sie die Seite.');
            }
        });
    }

    function checkit() {
        var loading = layer.msg('Überprüfung läuft, dies kann eine Weile dauern. Bitte warten...', {
            icon: 16,
            shade: 0.4,
            time: false // Automatisches Schließen deaktivieren
        });
        $.ajax({
            type: "POST",
            url: "key.php",
            data: {
                message: $("#tt").val(),
                action: "check",
            },
            success: function(results) {
                $("#tt").val(results);
                layer.close(loading);
                layer.msg('Überprüfung abgeschlossen. Ungültige API-Keys wurden entfernt. Bitte denken Sie daran, die Einstellungen zu speichern.');
            }
        });
    }
</script>

</html>

<?php
    exit;
}
// Definieren Sie die Benutzername und Passwort Konstanten
define('USERNAME', 'admin');
define('PASSWORD', 'admin2023');
// Überprüfen Sie, ob das Formular gesendet wurde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Holen Sie sich den Benutzernamen und das Passwort aus dem Formular
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Überprüfen Sie, ob Benutzername und Passwort korrekt sind
    if ($username == USERNAME && $password == PASSWORD) {
        // Anmeldung erfolgreich, weiterleiten zur Startseite
        $_SESSION['admin'] = true;
        header("Location: key.php");
        exit;
    } else {
        // Anmeldung fehlgeschlagen, Fehlermeldung anzeigen
        $error = 'Benutzername oder Passwort falsch';
        $_SESSION['admin'] = false;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anmeldeseite</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center
