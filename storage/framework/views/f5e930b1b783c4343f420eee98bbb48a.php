<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <title>New Message to Agent</title>
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
        .message-box {
            background-color: #f0e6cc;
            padding: 15px 20px;
            border-radius: 8px;
            font-size: 16px;
            color: #3e3e3e;
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
        <h1>New Message for You</h1>
        <p><strong>First Name:</strong> <?php echo e($first_name); ?></p>
        <p><strong>Second Name:</strong> <?php echo e($second_name); ?></p>
        <p><strong>Phone One:</strong> <?php echo e($phone_one); ?></p>
        <?php if(!empty($phone_two)): ?>
            <p><strong>Phone Two:</strong> <?php echo e($phone_two); ?></p>
        <?php endif; ?>

        <p><strong>Message:</strong></p>
        <div class="message-box">
            <?php echo e($message); ?>

        </div>

        <?php if(!empty($property)): ?>
            <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
            <h2 style="font-size: 20px; color: #094834;">Property Information</h2>
            <p><strong>Property Name:</strong> <?php echo e($property->name ?? 'N/A'); ?></p>
            <p><strong>Address:</strong> <?php echo e($property->address ?? 'N/A'); ?></p>
            
        <?php endif; ?>
    </div>
    <div class="footer">
        This message was sent by <strong><?php echo e(config('app.name')); ?></strong>.
    </div>
</div>
</body>
</html>
<?php /**PATH /home/wimo68zi/api.shiroproperties.com/resources/views/emails/contact_agent.blade.php ENDPATH**/ ?>