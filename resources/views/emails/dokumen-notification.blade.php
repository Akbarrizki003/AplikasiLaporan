<!DOCTYPE html>
<html>
<head>
    <title>{{ $emailSubject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4a76a8;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            border: 1px solid #ddd;
            border-top: none;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            color: #777;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4a76a8;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            margin-top: 15px;
        }
        .dokumen-info {
            background-color: #f9f9f9;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #4a76a8;
        }
        table {
            width: 100%;
        }
        table td {
            padding: 5px;
        }
        .login-info {
            background-color: #fff8e1;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Notifikasi Dokumen</h2>
    </div>
    
    <div class="content">
        <p>Halo,</p>
        
        <p>{{ $emailMessage }}</p>
        
        <div class="dokumen-info">
            <h3>Informasi Dokumen:</h3>
            <table>
                <tr>
                    <td width="150"><strong>Nama Dokumen:</strong></td>
                    <td>{{ $dokumen->nama_dokumen }}</td>
                </tr>
                <tr>
                    <td><strong>Unit:</strong></td>
                    <td>{{ $dokumen->unit->nama_unit ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Tanggal Upload:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($dokumen->tanggal_upload)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td>{{ $dokumen->status_label }}</td>
                </tr>
            </table>
        </div>
        
        @if(isset($loginData) && $loginData)
        <div class="login-info">
            <h3>Informasi Login:</h3>
            <p>Gunakan informasi berikut untuk login dan meninjau dokumen:</p>
            <table>
                <tr>
                    <td width="150"><strong>Username:</strong></td>
                    <td>{{ $loginData['username'] }}</td>
                </tr>
                <tr>
                    <td><strong>Password:</strong></td>
                    <td>{{ $loginData['password'] }}</td>
                </tr>
            </table>
            
            <a href="{{ $loginData['login_url'] }}" class="btn">Login Sekarang</a>
        </div>
        @endif
        
        <p>Silakan login ke sistem untuk melihat detail dokumen dan melakukan tindakan yang diperlukan.</p>
        
        <a href="{{ route('dokumen.index') }}" class="btn">Lihat Dokumen</a>
    </div>
    
    <div class="footer">
        <p>Email ini dibuat secara otomatis. Mohon tidak membalas email ini.</p>
        <p>&copy; {{ date('Y') }} Sistem Laporan. All rights reserved.</p>
    </div>
</body>
</html>