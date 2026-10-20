# Introduction

REST API untuk sistem tiket dukungan. Dapatkan token lewat POST /api/v1/login, lalu kirim sebagai Bearer token.

<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>

    Dokumentasi ini menjelaskan endpoint REST API HelpDesk Pro.

    <aside>Contoh kode ada di area gelap sebelah kanan (atau di dalam konten pada layar kecil). Ganti bahasa lewat tab di kanan atas.</aside>

    **Alur singkat:** panggil <code>POST /api/v1/login</code> untuk mendapat token, lalu kirim header <code>Authorization: Bearer {token}</code> pada setiap request lain.

