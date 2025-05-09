<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $emailSubject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-bottom: 3px solid #007bff;
        }
        .content {
            padding: 20px;
            background-color: #ffffff;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }
        .credentials {
            background-color: #f8f9fa;
            padding: 15px;
            margin-top: 20px;
            border-left: 4px solid #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $emailSubject }}</h2>
        </div>
        
        <div class="content">
            <p>Halo,</p>
            
            <p>{{ $emailMessage }}</p>
            
            <p><strong>Detail Dokumen:</strong></p>
            <ul>
                <li>Nama Dokumen: {{ $dokumen->nama_dokumen }}</li>
                <li>Tanggal Upload: {{ $dokumen->tanggal_upload }}</li>
                <li>Unit: {{ $dokumen->unit->nama_unit }}</li>
                <li>Status: {{ $dokumen->status }}</li>
            </ul>
            
            @if ($loginData)
            <div class="credentials">
                <p><strong>Berikut adalah informasi login Anda:</strong></p>
                <p>Email: {{ $loginData['email'] }}</p>
                <p>Password: {{ $loginData['password'] }}</p>
                <p><em>Catatan: Password ini bersifat sementara dan akan berubah setiap kali ada dokumen baru.</em></p>
            </div>
            
            <p>
                <a href="{{ $loginData['login_url'] }}" class="button">Login Sekarang</a>
            </p>
            <p>Setelah login, Anda dapat melihat dokumen tersebut dan mengambil tindakan yang diperlukan.</p>
            @endif
        </div>
        
        <div class="footer">
            <p>Ini adalah email notifikasi otomatis. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} Sistem Dokumen. All rights reserved.</p>
        </div>
    </div>
</body>
</html>