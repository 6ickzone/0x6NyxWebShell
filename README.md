# ⛩️ YamiRoot VoidGate

> ⚠️ **IMPORTANT WARNING:** This tool is built **strictly for authorized educational research and legal penetration testing.** Using it on systems you don't own is illegal and completely unsupported by the author.

![Status](https://img.shields.io/badge/status-Active-purple)
![License](https://img.shields.io/badge/license-MIT-yellow)
![Built By](https://img.shields.io/badge/Built%20by-0x6ick-blueviolet)

---

## 💡 Quick Overview

**VoidGate** is a lightweight PHP-based root file manager and command shell designed for system interaction and post-exploitation research. It gives you a clean, terminal-style panel with a custom dark UI (our signature "calm glow" theme) to manage files and run commands efficiently.

---

## ✨ Core Functionality

VoidGate lets you handle key system tasks right from your browser:

* **🖥️ Command Execution:** Full remote command support via `shell_exec`.
* **📁 File Management:** Upload, create, zip, and manage files easily.
* **🧨 Mass Operations:** Includes optional **Mass Defacement** tools.
* **🌐 Network Analysis:** Built-in tools like Port Scanner, Ping, WHOIS, DNS Lookup, and cURL Client.
* **🪜 Traversal:** Symlink tools to navigate the server file system.

[VoidGate Screenshot](/void.png)

---

## 📂 Project Structure and Detailed Documentation

This repository is divided into two main subfolders, each with its own specific `README.md` for in-depth instructions:

| Folder | Content Focus | Where to find details |
| :--- | :--- | :--- |
| **`YamiRoot_Series`** | Contains the various versions of VoidGate (DX, Mini, Bypass) and their unique features. | [Go to YamiRoot_Series/README.md](YamiRoot_Series/README.md) |
| **`tools_helper`** | Houses specialized post-exploitation modules like Mass File Utilities, SMTP Tester, and advanced networking tools. | [Go to tools_helper/README.md](tools_helper/README.md) |

---

## ⚙️ Requirements

* A web server running **PHP 5.6+**.
* Crucial PHP functions (like `shell_exec`, `scandir`, etc.) **must not be disabled** for full functionality.

---

## 🚀 Get Started

1.  Upload **`void.php`** to your target environment.
2.  Access it via your browser: `http://target.com/void.php`

---

## 🔒 Legal and Ethical Use

Use VoidGate only where you have explicit, legal permission. This tool is for learning and authorized testing. **Use Responsibly.**

---

## 👤 Author

Coded with 🖤 by [0x6ick](https://github.com/6ickzone)

---
