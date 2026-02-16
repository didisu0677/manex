<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>500 - Terjadi Kesalahan</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    background: rgba(0,0,0,.04);
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    color: #484848;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.container { width: 100%; max-width: 600px; padding: 0 15px; text-align: center; }
.error-number { font-size: 9rem; color: #ccc; text-shadow: .125rem .125rem #fff; line-height: 1; margin-bottom: .5rem; }
.error-message { font-size: 1rem; color: #484848; margin-bottom: .5rem; }
.btn { display: inline-block; padding: 8px 20px; font-size: 14px; color: #fff; background-color: #17a2b8; border: 1px solid #17a2b8; border-radius: 4px; text-decoration: none; transition: background-color .15s; }
.btn:hover { background-color: #138496; border-color: #117a8b; }
p { color: #666; margin-bottom: 1rem; }
</style>
</head>
<body>
<div class="container">
    <div class="error-number">500</div>
    <h6 class="error-message">Terjadi Kesalahan</h6>
    <p>Mohon maaf, terjadi kesalahan pada server. Silakan coba beberapa saat lagi.</p>
    <a href="javascript:history.back()" class="btn">Kembali</a>
</div>
</body>
</html>
