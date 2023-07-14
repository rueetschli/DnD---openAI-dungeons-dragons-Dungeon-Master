var contextarray = [];

var defaults = {
html: false, // HTML-Tags in der Quelle aktivieren
xhtmlOut: false, // Verwende '/' um einzelne Tags zu schließen (<br />)
breaks: false, // Konvertiere '\n' in Absätzen in <br>
langPrefix: 'language-', // CSS-Sprachpräfix für eingezäunte Blöcke
linkify: true, // URL-ähnliche Texte automatisch in Links umwandeln
linkTarget: '', // Ziel festlegen, um Link zu öffnen
typographer: true, // Aktiviere smartypants und andere süße Transformationen
_highlight: true,
_strict: false,
_view: 'html'
};
defaults.highlight = function (str, lang) {
if (!defaults._highlight || !window.hljs) { return ''; }

var hljs = window.hljs;
if (lang && hljs.getLanguage(lang)) {
try {
return hljs.highlight(lang, str).value;
} catch (__) { }
}

try {
return hljs.highlightAuto(str).value;
} catch (__) { }

return '';
};
mdHtml = new window.Remarkable('full', defaults);

mdHtml.renderer.rules.table_open = function () {
return '<table class="table table-striped">\n';
};

mdHtml.renderer.rules.paragraph_open = function (tokens, idx) {
var line;
if (tokens[idx].lines && tokens[idx].level === 0) {
line = tokens[idx].lines[0];
return '<p class="line" data-line="' + line + '">';
}
return '<p>';
};

mdHtml.renderer.rules.heading_open = function (tokens, idx) {
var line;
if (tokens[idx].lines && tokens[idx].level === 0) {
line = tokens[idx].lines[0];
return '<h' + tokens[idx].hLevel + ' class="line" data-line="' + line + '">';
}
return '<h' + tokens[idx].hLevel + '>';
};
function getCookie(name) {
var cookies = document.cookie.split(';');
for (var i = 0; i < cookies.length; i++) {
var cookie = cookies[i].trim();
if (cookie.indexOf(name + '=') === 0) {
return cookie.substring(name.length + 1, cookie.length);
}
}
return null;
}

function isMobile() {
const userAgent = navigator.userAgent.toLowerCase();
const mobileKeywords = ['iphone', 'ipod', 'ipad', 'android', 'windows phone', 'blackberry', 'nokia', 'opera mini', 'mobile'];
for (let i = 0; i < mobileKeywords.length; i++) {
if (userAgent.indexOf(mobileKeywords[i]) !== -1) {
return true;
}
}
return false;
}

function insertPresetText() {
$("#kw-target").val($('#preset-text').val());
autoresize();
}

function initcode() {
console['\x6c\x6f\x67']("Der Code dieser Seite wurde modifiziert von\x68\x74\x74\x70\x3a\x2f\x2f\x67\x69\x74\x68\x75\x62\x2e\x63\x6f\x6d\x2f\x64\x69\x72\x6b\x31\x39\x38\x33\x2f\x63\x68\x61\x74\x67\x70\x74");
}

function copyToClipboard(text) {
var input = document.createElement('textarea');
input.innerHTML = text;
document.body.appendChild(input);
input.select();
var result = document.execCommand('copy');
document.body.removeChild(input);
return result;
}

function copycode(obj) {
copyToClipboard($(obj).closest('code').clone().children('button').remove().end().text());
layer.msg("Kopieren abgeschlossen!");
}

function autoresize() {
var textarea = $('#kw-target');
var width = textarea.width();
var content = (textarea.val() + "a").replace(/\\n/g, '<br>');
var div = $('<div>').css({
'position': 'absolute',
'top': '-99999px',
'border': '1px solid red',
'width': width,
'font-size': '15px',
'line-height': '20px',
'white-space': 'pre-wrap'
}).html(content).appendTo('body');
var height = div.height();
var rows = Math.ceil(height / 20);
div.remove();
textarea.attr('rows', rows);
$("#article-wrapper").height(parseInt($(window).height()) - parseInt($("#fixed-block").height()) - parseInt($(".layout-header").height()) - 80);
}

$(document).ready(function () {
initcode();
autoresize();
$("#kw-target").on('keydown', function (event) {
if (event.keyCode == 13 && event.ctrlKey) {
send_post();
return false;
}
});

$(window).resize(function () {
autoresize();
});

$('#kw-target').on('input', function () {
autoresize();
});

$("#ai-btn").click(function () {
if ($("#kw-target").is(':disabled')) {
clearInterval(timer);
$("#kw-target").val("");
$("#kw-target").attr("disabled", false);
autoresize();
$("#ai-btn").html('<i class="iconfont icon-wuguan"></i>Senden');
if (!isMobile()) $("#kw-target").focus();
} else {
send_post();
}
return false;
});

$("#clean").click(function () {
$("#article-wrapper").html("");
contextarray = [];
layer.msg("Reinigung abgeschlossen!");
return false;
});

$("#showlog").click(function () {
let btnArry = ['Gelesen'];
layer.open({ type: 1, title: 'Alle Chatprotokolle', area: ['80%', '80%'], shade: 0.5, scrollbar: true, offset: [($(window).height() * 0.1), ($(window).width() * 0.1)], content: '<iframe src="chat.txt?' + new Date().getTime() + '" style="width: 100%; height: 100%;"></iframe>', btn: btnArry });
return false;
});

function send_post() {
if (($('#key').length) && ($('#key').val().length != 51)) {
layer.msg("Bitte geben Sie den korrekten API-Schlüssel ein", { icon: 5 });
return;
}

var prompt = $("#kw-target").val();

if (prompt == "") {
layer.msg("Bitte geben Sie Ihre Frage ein", { icon: 5 });
return;
}

var loading = layer.msg('Sprache wird organisiert, bitte warten Sie einen Moment...', {
icon: 16,
shade: 0.4,
time: false //Automatisches Schließen abbrechen
});

function draw() {
$.get("getpicture.php", function (data) {
layer.close(loading);
layer.msg("Verarbeitung erfolgreich!");
answer = randomString(16);
$("#article-wrapper").append('<li class="article-title" id="q' + answer + '"><pre></pre></li>');
for (var j = 0; j < prompt.length; j++) {
$("#q" + answer).children('pre').text($("#q" + answer).children('pre').text() + prompt[j]);
}
$("#article-wrapper").append('<li class="article-content" id="' + answer + '"><img onload="document.getElementById(\'article-wrapper\').scrollTop=100000;" src="pictureproxy.php?url=' + encodeURIComponent(data.data[0].url) + '"></li>');
$("#kw-target").val("");
$("#kw-target").attr("disabled", false);
autoresize();
$("#ai-btn").html('<i class="iconfont icon-wuguan"></i>Senden');
if (!isMobile()) $("#kw-target").focus();
}, "json");
}
function streaming() {
var es = new EventSource("stream.php");
var isstarted = true;
var alltext = "";
var isalltext = false;
es.onerror = function (event) {
layer.close(loading);
var errcode = getCookie("errcode");
switch (errcode) {
case "invalid_api_key":
layer.msg("API-Schlüssel ist ungültig");
break;
case "context_length_exceeded":
layer.msg("Die Länge der Frage und des Kontexts überschreitet das Limit, bitte stellen Sie die Frage erneut");
break;
case "rate_limit_reached":
layer.msg("Zu viele gleichzeitige Benutzer, bitte versuchen Sie es später erneut");
break;
case "access_terminated":
layer.msg("Missbräuchliche Nutzung, API-Schlüssel wurde gesperrt");
break;
case "no_api_key":
layer.msg("Kein API-Schlüssel angegeben");
break;
case "insufficient_quota":
layer.msg("Unzureichendes Guthaben für den API-Schlüssel");
break;
case "account_deactivated":
layer.msg("Konto wurde deaktiviert");
break;
case "model_overloaded":
layer.msg("OpenAI-Modell überlastet, bitte stellen Sie die Anfrage erneut");
break;
case null:
layer.msg("Zugriff auf den OpenAI-Server hat Zeitüberschreitung oder unbekannter Fehlertyp");
break;
default:
layer.msg("Fehler im OpenAI-Server, Fehlertyp: " + errcode);
}
es.close();
if (!isMobile()) $("#kw-target").focus();
return;
}
es.onmessage = function (event) {
if (isstarted) {
layer.close(loading);
$("#kw-target").val("Bitte warten Sie geduldig, bis AI fertig gesprochen hat...");
$("#kw-target").attr("disabled", true);
autoresize();
$("#ai-btn").html('<i class="iconfont icon-wuguan"></i>Abbrechen');
layer.msg("Verarbeitung erfolgreich!");
isstarted = false;
answer = randomString(16);
$("#article-wrapper").append('<li class="article-title" id="q' + answer + '"><pre></pre></li>');
for (var j = 0; j < prompt.length; j++) {
$("#q" + answer).children('pre').text($("#q" + answer).children('pre').text() + prompt[j]);
}
$("#article-wrapper").append('<li class="article-content" id="' + answer + '"></li>');
let str_ = '';
let i = 0;
let strforcode = '';
timer = setInterval(() => {
let newalltext = alltext;
let islastletter = false;
//Manchmal gibt der Server fälschlicherweise \\n als Zeilenumbruch zurück, insbesondere bei Fragen, die einen Kontext enthalten. Dieser Code kann das behandeln.
if (newalltext.split("\n").length == 1) {
newalltext = newalltext.replace(/\\n/g, '\n');
}
if (str_.length < (newalltext.length - 3)) {
str_ += newalltext[i++];
strforcode = str_;
if ((str_.split("```").length % 2) == 0) {
strforcode += "\n```\n";
} else {
strforcode += "_";
}
} else {
if (isalltext) {
clearInterval(timer);
strforcode = newalltext;
islastletter = true;
$("#kw-target").val("");
$("#kw-target").attr("disabled", false);
autoresize();
$("#ai-btn").html('<i class="iconfont icon-wuguan"></i>Senden');
if (!isMobile()) $("#kw-target").focus();
}
}
//let arr = strforcode.split("```");
//for (var j = 0; j <= arr.length; j++) {
// if (j % 2 == 0) {
// arr[j] = arr[j].replace(/\n\n/g, '\n');
// arr[j] = arr[j].replace(/\n/g, '\n\n');
// arr[j] = arr[j].replace(/\t/g, '\\t');
// arr[j] = arr[j].replace(/\n {4}/g, '\n\\t');
// arr[j] = $("<div>").text(arr[j]).html();
// }
//}

//var converter = new showdown.Converter();
//newalltext = converter.makeHtml(arr.join("```"));
newalltext = mdHtml.render(strforcode);
//newalltext = newalltext.replace(/\\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
$("#" + answer).html(newalltext);
if (islastletter) MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
//if (document.querySelector("[id='" + answer + "']" + " pre code")) document.querySelectorAll("[id='" + answer + "']" + " pre code").forEach(el => { hljs.highlightElement(el); });
$("#" + answer + " pre code").each(function () {
$(this).html("<button onclick='copycode(this);' class='codebutton'>Kopieren</button>" + $(this).html());
});
document.getElementById("article-wrapper").scrollTop = 100000;
}, 30);
}
if (event.data == "[DONE]") {
isalltext = true;
contextarray.push([prompt, alltext]);
contextarray = contextarray.slice(-5); //Behalte nur die letzten 5 Dialoge als Kontext bei, um das maximale Token-Limit nicht zu überschreiten
es.close();
return;
}
var json = eval("(" + event.data + ")");
if (json.choices[0].delta.hasOwnProperty("content")) {
if (alltext == "") {
alltext = json.choices[0].delta.content.replace(/^\n+/, ''); //Entferne gelegentlich auftretende fortlaufende Zeilenumbrüche am Anfang der Antwortnachricht
} else {
alltext += json.choices[0].delta.content;
}
}
}
}


if (prompt.charAt(0) === 'zeichnen') {
$.ajax({
cache: true,
type: "POST",
url: "setsession.php",
data: {
message: prompt,
context: '[]',
key: ($("#key").length) ? ($("#key").val()) : '',
},
dataType: "json",
success: function (results) {
draw();
}
});
} else {
$.ajax({
cache: true,
type: "POST",
url: "setsession.php",
data: {
message: prompt,
context: (!($("#keep").length) || ($("#keep").prop("checked"))) ? JSON.stringify(contextarray) : '[]',
key: ($("#key").length) ? ($("#key").val()) : '',
},
dataType: "json",
success: function (results) {
streaming();
}
});
}


}

function randomString(len) {
len = len || 32;
var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678'; /****Standardmäßig werden die leicht zu verwechselnden Zeichen oOLl,9gq,Vv,Uu,I1 weggelassen****/
var maxPos = $chars.length;
var pwd = '';
for (i = 0; i < len; i++) {
pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
}
return pwd;
}

});