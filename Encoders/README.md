# KCK PolyGen (Polymorphic PHP Encoder)

![Language](https://img.shields.io/badge/Language-PHP-777BB4?style=flat-square)
![Category](https://img.shields.io/badge/Category-Obfuscation-orange?style=flat-square)
![Status](https://img.shields.io/badge/Status-Educational-green?style=flat-square)

## ⚠️ Disclaimer

**READ THIS BEFORE USE:**
This tool is developed strictly for **Educational Research** and **Security Analysis** purposes. It is designed to demonstrate how polymorphic code structures work and how they can evade static analysis detection mechanisms.

* **Do not use this tool on unauthorized systems.**
* **The authors (0x6ick & Gemini) are not responsible for any misuse of this code.**
* Use this tool only in your own controlled environment (Localhost/Lab).

---

## 📖 Overview

**KCK PolyGen** is a PHP-based obfuscation tool that generates **polymorphic** stubs for PHP scripts. Unlike traditional encoders that use static decryption keys or fixed structures, KCK PolyGen randomizes the entire stub structure (class names, function names, variables, and logic flow) every time it generates a file.

This "Polymorphism" ensures that every generated file has a unique file hash and signature, making it a valuable subject for studying **Static Analysis Evasion** and **Memory-Resident Payloads**.

## ✨ Key Features

* **🧬 True Polymorphism:** Randomizes Class names, Methods, Variables, and Integrity Keys on every generation. No two outputs are identical.
* **🧠 Memory Safe:** Optimized with `ini_set('memory_limit', '512M')` to handle large scripts/shells without triggering Error 500 (OOM).
* **🛡️ Reverse Logic Chain:** Uses a complex encoding chain (Compression -> XOR -> Hex -> Reverse -> Base64) to mask the payload.
* **👻 Reflection Method Execution:** bypasses standard static code scanners by executing the payload via PHP's `ReflectionMethod` class instead of direct function calls.
* **🌐 Hybrid Support:** Capable of encoding scripts containing mixed HTML, CSS, JS, and PHP.

## 🛠️ Technical Details

### The Obfuscation Chain
The generator applies the following transformation to the source code:
1.  **Compression:** `gzcompress` (Level 9) to reduce size.
2.  **Encryption:** Byte-wise **XOR** with a rotating key.
3.  **Encoding:** `bin2hex` -> `strrev` (String Reversal) -> `base64_encode`.

### The Stub Structure
The generated stub includes a randomized class that performs:
1.  **Integrity Check:** Verifies the payload checksum before execution to prevent corruption.
2.  **Key Reconstruction:** Rebuilds the decryption key from multiple randomized private methods.
3.  **Virtual Execution:** Uses `eval('?>' . $code)` to support virtual file inclusion without writing to disk.

## 🚀 Usage

1.  Host the `KCK_PolyGen.php` file on a local PHP server (e.g., XAMPP, Laragon, or `php -S`).
2.  Open the file in your browser.
3.  Paste your PHP source code into the input area.
4.  Click **Build Encrypted File**.
5.  The tool will generate a unique PHP file (e.g., `void.php`) ready for testing.

---

### 👨‍💻 Credits

* **Concept & Core Logic:** 0x6ick
* **Optimization & Polymorphic Engine:** Gemini (AI Assistant)

---

*"Knowledge is not a crime, but the misuse of it is."*
