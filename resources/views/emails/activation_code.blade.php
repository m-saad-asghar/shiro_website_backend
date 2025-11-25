<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رمز تفعيل الحساب</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #f883bb;
            font-family: 'Tajawal', sans-serif;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .email-container {
            background-color: #ffffff;
            max-width: 600px;
            margin: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background-color: #f883bb;
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-width: 200px;
        }
        .content {
            padding: 30px 20px;
            color: #222;
            text-align: right;
        }
        .content h1 {
            font-size: 22px;
            margin-bottom: 15px;
        }
        .content p {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .verification-code {
            font-size: 28px;
            font-weight: bold;
            color: #f883bb;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            background-color: #f2f2f2;
            padding: 15px;
            text-align: center;
            font-size: 13px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <img src="{{ asset('logo.png') }}" alt="Platform Logo">
    </div>
    <div class="content">
        <h1>مرحبًا {{ $user->name }}!</h1>
        <p>شكرًا لتسجيلك في {{ config('app.name') }}.</p>
        <p>رمز تفعيل الحساب الخاص بك:</p>
        <div class="verification-code">{{ $token }}</div>
        <p>يرجى إدخال هذا الرمز لإكمال عملية التفعيل.</p>
    </div>
    <div class="footer">
        تم إرسال هذه الرسالة من قِبل {{ config('app.name') }}.
    </div>
</div>
</body>
</html>
