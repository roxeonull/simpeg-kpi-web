<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
            color: #333333;
        }
        .container {
            max-width: 560px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 6px 0 0;
            font-size: 13px;
            opacity: 0.85;
        }
        .content {
            padding: 35px 30px;
            text-align: center;
        }
        .greeting {
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: left;
        }
        .message {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 25px;
            text-align: left;
        }
        .otp-box {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            display: inline-block;
            width: 80%;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #1e3c72;
            margin: 0;
        }
        .expiry-info {
            font-size: 12.5px;
            color: #ef4444;
            font-weight: 600;
            margin-top: 10px;
        }
        .warning-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            border-radius: 6px;
            text-align: left;
            font-size: 12.5px;
            color: #92400e;
            margin-top: 25px;
            line-height: 1.5;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SIMPEG KPI Mobile</h1>
            <p>Verifikasi Keamanan Akun Anda</p>
        </div>
        <div class="content">
            <div class="greeting">Halo {{ $userName }},</div>
            <div class="message">
                Kami menerima permintaan reset kata sandi untuk akun SIMPEG KPI Anda. Gunakan kode OTP di bawah ini untuk melanjutkan proses verifikasi di aplikasi mobile:
            </div>
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <div class="expiry-info">Kode ini berlaku selama 15 menit</div>
            </div>
            <div class="warning-box">
                <strong>Penting:</strong> Jangan berikan kode ini kepada siapa pun, termasuk pihak yang mengatasnamakan SIMPEG KPI. Jika Anda tidak pernah meminta reset kata sandi, abaikan email ini.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Komisi Penyiaran Indonesia. All rights reserved.<br>
            Email ini dikirim secara otomatis, harap tidak membalas email ini.
        </div>
    </div>
</body>
</html>
