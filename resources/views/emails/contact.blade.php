<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Contact Message</title>
</head>
<body style="font-family: Tahoma, sans-serif; background-color: #f9f9f9; padding: 20px;">
<div style="max-width: 600px; margin: auto; background: #ffffff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
    <div style="background-color: #094834; padding: 15px 20px; color: #fff;">
        <h2 style="margin: 0;">New Contact Message</h2>
    </div>
    <div style="padding: 20px;">
        <p><strong>Name:</strong> {{ $name }}</p>
        <p><strong>Email:</strong> {{ $email }}</p>
        @if (!empty($phone))
            <p><strong>Phone:</strong> {{ $phone }}</p>
        @endif
        <p><strong>Message:</strong></p>
        <p style="background: #f3f3f3; padding: 10px; border-radius: 5px;">{{ $message }}</p>
    </div>
    <div style="background-color: #f2f2f2; padding: 15px; text-align: center; font-size: 13px; color: #777;">
        This message was sent by {{ config('app.name') }}.
    </div>
</div>
</body>
</html>
