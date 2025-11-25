<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة الطلب</title>
</head>
<body style="font-family: Tahoma, sans-serif; direction: rtl; background-color: #ffffff; padding: 0; margin: 0;">

<table width="600" align="center" cellpadding="0" cellspacing="0" style="border:1px solid #eee; background-color: #fff; margin-top: 20px;">
    <!-- الهيدر -->
    <tr>
        <td style="text-align: center; padding: 20px;">
            <img src="<?php echo e(url('/logo.png')); ?>" alt="logo" style="max-width: 150px;">
            <h2 style="color: #f883bb; margin-top: 10px;"><?php echo e(env('APP_NAME')); ?></h2>
        </td>
    </tr>

    <!-- بيانات المستخدم -->
    <tr>
        <td style="padding: 15px;">
            <h3 style="color: #f883bb; border-bottom: 1px solid #f883bb; padding-bottom: 5px;">تفاصيل المستخدم</h3>
            <table width="100%" cellpadding="5" cellspacing="0">
                <tr>
                    <td><strong>الاسم:</strong></td>
                    <td><?php echo e($order->user->name); ?></td>
                </tr>
                <tr>
                    <td><strong>البريد الإلكتروني:</strong></td>
                    <td><?php echo e($order->user->email); ?></td>
                </tr>
                <tr>
                    <td><strong>رقم الهاتف:</strong></td>
                    <td><?php echo e($order->user->prefix); ?> <?php echo e($order->user->phone_number); ?></td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- تفاصيل الطلب -->
    <tr>
        <td style="padding: 15px;">
            <h3 style="color: #f883bb; border-bottom: 1px solid #f883bb; padding-bottom: 5px;">تفاصيل الطلب</h3>
            <table width="100%" cellpadding="5" cellspacing="0">
                <tr>
                    <td><strong>رقم الطلب:</strong></td>
                    <td><?php echo e($order->id); ?></td>
                </tr>
                <tr>
                    <td><strong>طريقة الدفع:</strong></td>
                    <td><?php echo e($order->payment_method ?? 'غير محددة'); ?></td>
                </tr>
                <tr>
                    <td><strong>المبلغ:</strong></td>
                    <td>$<?php echo e(number_format($order->amount, 2)); ?></td>
                </tr>
                <tr>
                    <td><strong>الحالة:</strong></td>
                    <td><?php echo e($order->status); ?></td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- الكورسات -->
    <tr>
        <td style="padding: 15px;">
            <h3 style="color: #f883bb; border-bottom: 1px solid #f883bb; padding-bottom: 5px;">الدورات</h3>
            <?php $__currentLoopData = $order->orderDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($detail->course): ?>
                    <table width="100%" cellpadding="10" cellspacing="0" style="border:1px solid #ccc; margin-bottom: 10px;">
                        <tr>
                            <td width="100" style="border-left:1px solid #ccc;">
                                <img src="<?php echo e(url('storage/' . $detail->course->cover_image)); ?>" alt="صورة الدورة" width="100" height="100" style="object-fit: contain;">
                            </td>
                            <td style="font-size: 14px;">
                                <strong>📘 <?php echo e($detail->course->title); ?></strong><br>
                                💵 السعر: $<?php echo e(number_format($detail->course->new_price, 2)); ?><br>
                                🕐 المدة: <?php echo e($detail->course->duration); ?>

                            </td>
                        </tr>
                    </table>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </td>
    </tr>

    <!-- قسم التواصل -->
    <tr>
        <td style="padding: 15px;">
            <h3 style="color: #f883bb; border-bottom: 1px solid #f883bb; padding-bottom: 5px;">تابعنا وتواصل معنا</h3>
            <?php $contact = \App\Models\ContactInfo::first(); ?>
            <?php if($contact): ?>
                <table width="100%" cellpadding="6" cellspacing="0">
                    <tr>
                        <td>
                            <img src="https://img.icons8.com/ios-filled/50/000000/phone.png" width="18" style="vertical-align: middle;">
                            <span style="margin-right: 5px;"><?php echo e($contact->phone); ?></span>
                        </td>
                        <td>
                            <img src="https://img.icons8.com/color/48/000000/whatsapp--v1.png" width="18" style="vertical-align: middle;">
                            <span style="margin-right: 5px;"><?php echo e($contact->whatsapp); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img src="https://img.icons8.com/ios-filled/50/000000/new-post.png" width="18" style="vertical-align: middle;">
                            <span style="margin-right: 5px;"><?php echo e($contact->email); ?></span>
                        </td>
                        <td>
                            <img src="https://img.icons8.com/color/48/000000/facebook-new.png" width="18" style="vertical-align: middle;">
                            <a href="<?php echo e($contact->facebook); ?>" style="margin-right: 5px; text-decoration: none; color: #000;">فيسبوك</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img src="https://img.icons8.com/color/48/000000/instagram-new.png" width="18" style="vertical-align: middle;">
                            <a href="<?php echo e($contact->instagram); ?>" style="margin-right: 5px; text-decoration: none; color: #000;">إنستغرام</a>
                        </td>
                        <td>
                            <img src="https://img.icons8.com/color/48/000000/twitter--v1.png" width="18" style="vertical-align: middle;">
                            <a href="<?php echo e($contact->twitter); ?>" style="margin-right: 5px; text-decoration: none; color: #000;">تويتر</a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <img src="https://img.icons8.com/color/48/000000/youtube-play.png" width="18" style="vertical-align: middle;">
                            <a href="<?php echo e($contact->youtube); ?>" style="margin-right: 5px; text-decoration: none; color: #000;">يوتيوب</a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <img src="https://img.icons8.com/ios-filled/50/000000/marker.png" width="18" style="vertical-align: middle;">
                            <span style="margin-right: 5px;"><?php echo e($contact->location); ?></span>
                        </td>
                    </tr>
                </table>
            <?php endif; ?>
        </td>
    </tr>

    <!-- الفوتر -->
    <tr>
        <td style="text-align: center; padding: 20px; font-size: 12px; color: #666;">
            هذا البريد مرسل تلقائياً من نظام <strong><?php echo e(env('APP_NAME')); ?></strong>، شكراً لك!
        </td>
    </tr>
</table>

</body>
</html>
<?php /**PATH /home/wimo68zi/api.shiroproperties.com/resources/views/emails/invoice.blade.php ENDPATH**/ ?>