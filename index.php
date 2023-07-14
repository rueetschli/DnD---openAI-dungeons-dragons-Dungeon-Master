<?php
$type = "Persönlich";
if (substr($_SERVER["REMOTE_ADDR"],0,9)!="127.0.0.1"){
    if (strpos($_SERVER["HTTP_USER_AGENT"],"MicroMessenger")){
        echo "<div style='height:100%;width:100%;text-align:center;margin-top:30%;'><h1>Bitte klicken Sie oben rechts und wählen Sie \"Im Browser öffnen\"</h1></div>";
        exit;
    }
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="Bitte Benutzername und Passwort eingeben."');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Auf Wiedersehen, Liebling.';
        exit;
    } else {
        if (($_SERVER['PHP_AUTH_USER']=="admin")&&($_SERVER['PHP_AUTH_PW']=="admin2023")){
            $type = "Externes Netzwerk";
        } else {
            echo 'Falsches Passwort, auf Wiedersehen...';
            exit;
        }
    }
} else {
    $type = "Internes Netzwerk";
}
?>
<html lang="de-DE">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>ChatGPT<?= $type ?>-Version</title>
    <link rel="stylesheet" href="css/common.css?v1.1">
    <link rel="stylesheet" href="css/wenda.css?v1.1">
    <link rel="stylesheet" href="css/hightlight.css">
</head>

<body>
    <div class="layout-wrap">
        <header class="layout-header">
            <div class="container" data-flex="main:justify cross:start">
                <div class="header-logo">
                    <h2 class="logo"><a class="links" id="clean" title="D&D dungeon master"><span class="logo-title">D&D dungeon master</span></a></h2>
                </div>
                <div class="header-logo">
                    <h2 class="logo"><a class="links" href="https://github.com/rueetschli/DnD---openAI-dungeons-dragons-Dungeon-Master"><span class="logo-title">Quellcode</span></a></h2>
                </div>
            </div>
        </header>
        <div class="layout-content">
            <div class="container">
                <article class="article" id="article">
                    <div class="article-box">
                        <div class="precast-block" data-flex="main:left">
                            <!--
                            <div class="input-group">
                                <span style="text-align: center;color:#9ca2a8">&nbsp;&nbsp;API-Schlüssel&nbsp;&nbsp;</span>
                                <input type="password" id="key" style="border:1px solid grey;display:block;max-width:270px;width:calc(100% - 70px);" onload="this.focus();">
                            </div>
-->
                            <div class="input-group">
                                <span style="text-align: center;color:#9ca2a8">&nbsp;&nbsp;<a href="edit.php" target="_blank">Editiere dein Setting.</a></span>
                                
                                
                            </div>
                            <div class="input-group">
                                <span style="text-align: center;color:#9ca2a8">&nbsp;&nbsp;</span>
                                
                            </div>
                        </div>
                        <ul id="article-wrapper">
                        </ul>
                        <div class="creating-loading" data-flex="main:center dir:top cross:center">
                            <div class="semi-circle-spin"></div>
                        </div>
                        <div id="fixed-block">
                            <div class="precast-block" id="kw-target-box" data-flex="main:left cross:center">
                                <div id="target-box" class="box">
                                    <textarea name="kw-target" placeholder="Hier eingeben und mit Strg+Enter senden" id="kw-target" autofocus rows=1></textarea>
                                </div>
                                <div class="right-btn layout-bar">
                                    <p class="btn ai-btn bright-btn" id="ai-btn" data-flex="main:center cross:center"><i class="iconfont icon-wuguan"></i>Senden</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <script src="js/remarkable.js"></script>
    <script src="js/jquery-3.6.4.min.js"></script>
    <script src="js/jquery.cookie.min.js"></script>
    <script src="js/layer.min.js"></script>
    <script src="js/chat.js?v2.8"></script>
    <script src="js/highlight.min.js"></script>
    <script src="//cdn.bootcss.com/mathjax/2.7.0/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
    <script type="text/x-mathjax-config">
        MathJax.Hub.Config({
        showProcessingMessages: false,
        messageStyle: "none",
        extensions: ["tex2jax.js"],
        jax: ["input/TeX", "output/HTML-CSS"],
        tex2jax: {
            inlineMath:  [ ["$", "$"] ],
        displayMath: [ ["$$","$$"] ],
        skipTags: ['script', 'noscript', 'style', 'textarea', 'pre','code','a'],
        ignoreClass:"comment-content"
            },
        "HTML-CSS": {
            availableFonts: ["STIX","TeX"],
        showMathMenu: false
            }
        });
    </script>
    <script>
        if ($('#key').length) {
            $(document).ready(function() {
                var key = $.cookie('key');
                if (key) {
                    $('#key').val(key);
                }
                $('#key').on('input', function() {
                    var inputVal = $(this).val();
                    $.cookie('key', inputVal, {
                        expires: 365
                    });
                });
            });
        }
    </script>
</body>

</html>
