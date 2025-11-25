<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <title>Password Reset</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal&display=swap');

        body {
            background-color: #f5f5f5; /* خلفية فاتحة محايدة */
            font-family: 'Tajawal', sans-serif;
            margin: 0;
            padding: 0;
            direction: ltr;
        }
        .email-container {
            background-color: #ffffff;
            max-width: 600px;
            margin: 40px auto;
            border: 1px solid #d3c294; /* ذهبي فاتح */
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(159, 129, 81, 0.15); /* ظل بني فاتح */
        }
        .header {
            background-color: #094834; /* أخضر داكن */
            padding: 25px;
            text-align: center;
        }
        .header img {
            max-width: 180px;
            filter: drop-shadow(0 0 5px rgba(0,0,0,0.15));
        }
        .content {
            padding: 35px 25px;
            color: #3e3e3e; /* رمادي غامق مناسب */
            text-align: left;
        }
        .content h1 {
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 700;
            color: #094834; /* أخضر داكن */
        }
        .content p {
            font-size: 17px;
            margin-bottom: 15px;
            line-height: 1.6;
            color: #555555; /* رمادي متوسط */
        }
        .verification-code {
            font-size: 32px;
            font-weight: 800;
            color: #9f8151; /* بني فاتح */
            text-align: center;
            margin: 30px 0;
            letter-spacing: 6px;
            background-color: #f0e6cc; /* ذهبي فاتح */
            padding: 15px 0;
            border-radius: 10px;
            user-select: all;
        }
        .footer {
            background-color: #fafafa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #7b6f47; /* بني داكن هادئ */
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
        <p>You have requested to reset your password at <strong><?php echo e(config('app.name')); ?></strong>.</p>
        <p>Your verification code is:</p>
        <div class="verification-code"><?php echo e($token); ?></div>
        <p>Please enter this code to reset your password.</p>
        <p style="font-size: 14px; color: #777;">If you did not request this, you can safely ignore this email.</p>
    </div>
    <div class="footer">
        This email was sent by <strong><?php echo e(config('app.name')); ?></strong>.
    </div>
</div>
</body>
</html>
<?php /**PATH /home/wimo68zi/api.shiroproperties.com/resources/views/emails/forgot-password.blade.php ENDPATH**/ ?>