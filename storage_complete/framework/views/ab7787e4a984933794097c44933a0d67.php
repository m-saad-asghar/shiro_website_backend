<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <title>Email Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal&display=swap');

        body {
            background-color: #f5f5f5;
            font-family: 'Tajawal', sans-serif;
            margin: 0;
            padding: 0;
            direction: ltr;
        }
        .email-container {
            background-color: #ffffff;
            max-width: 600px;
            margin: 40px auto;
            border: 1px solid #d3c294;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(159, 129, 81, 0.15);
        }
        .header {
            background-color: #094834;
            padding: 25px;
            text-align: center;
        }
        .header img {
            max-width: 180px;
            filter: drop-shadow(0 0 5px rgba(0,0,0,0.15));
        }
        .content {
            padding: 35px 25px;
            color: #3e3e3e;
            text-align: left;
        }
        .content h1 {
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 700;
            color: #094834;
        }
        .content p {
            font-size: 17px;
            margin-bottom: 15px;
            line-height: 1.6;
            color: #555555;
        }
        .verification-code {
            font-size: 32px;
            font-weight: 800;
            color: #9f8151;
            text-align: center;
            margin: 30px 0;
            letter-spacing: 6px;
            background-color: #f0e6cc;
            padding: 15px 0;
            border-radius: 10px;
            user-select: all;
        }
        .footer {
            background-color: #fafafa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #7b6f47;
            font-weight: 600;
            border-top: 1px solid #d3c294;
        }
        a {
            color: #094834;
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <img src="<?php echo e(asset('logo.png')); ?>" alt="Platform Logo" />
    </div>
    <div class="content">
        <h1>Hello <?php echo e($user->name); ?>!</h1>
        <p>Thank you for signing up on <strong><?php echo e(config('app.name')); ?></strong>.</p>
        <p>To complete your registration, please use the following verification code:</p>
        <div class="verification-code"><?php echo e($token); ?></div>
        <p>Enter this code in the app to verify your email address.</p>
        <p style="font-size: 14px; color: #777;">If you didn't create this account, you can safely ignore this message.</p>
    </div>
    <div class="footer">
        This email was sent by <strong><?php echo e(config('app.name')); ?></strong>.
    </div>
</div>
</body>
</html>
<?php /**PATH /home/wimo68zi/api.shiroproperties.com/resources/views/emails/verify-email.blade.php ENDPATH**/ ?>