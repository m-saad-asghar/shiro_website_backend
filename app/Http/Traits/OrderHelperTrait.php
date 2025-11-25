<?php

namespace App\Http\Traits;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponDetail;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\User;

trait OrderHelperTrait
{
    public function build(User $user, ?Coupon $coupon = null, float $amount = null): ?Order
    {
        return Order::create([
            'user_id'   => $user->id,
            'coupon_id' => $coupon?->id,
            'amount'    => $amount ?? 0,
            'status'    => 'pending',
            'gateway'   => null, // It will be filled later through the payment
        ]);
    }
    public function calculateCartTotals($userId, Coupon $coupon = null)
    {
        $cart_items = Cart::where('user_id', $userId)->get();
        $total_amount = 0;
        $total_discount = 0;

        $discounted_items = [];
        $full_price_items = [];

        foreach ($cart_items as $item) {
            if (!$item->cartable) {
                continue;
            }

            $price = 0;

            if ($item->cartable_type == \App\Models\Course::class) {
                $price = $item->cartable->new_price;
            } elseif ($item->cartable_type == \App\Models\Package::class) {
                $price = $item->cartable->price;
            }

            $is_coupon_applicable = false;

            if ($coupon) {
                $is_coupon_applicable = CouponDetail::where('coupon_id', $coupon->id)
                    ->where('target_type', $item->cartable_type)
                    ->where('target_id', $item->cartable_id)
                    ->exists();
            }

            if ($is_coupon_applicable) {
                $discount = ($price * $coupon->discount_percentage) / 100;
                $total_discount += $discount;
                $price -= $discount;

                $discounted_items[] = [
                    'id' => $item->cartable_id,
                    'type' => strtolower(class_basename($item->cartable_type)),
                    'original_price' => $price + $discount,
                    'discount' => $discount,
                    'final_price' => $price,
                ];
            } else {
                $full_price_items[] = [
                    'id' => $item->cartable_id,
                    'type' => strtolower(class_basename($item->cartable_type)),
                    'price' => $price,
                ];
            }

            $total_amount += $price;
        }

        return [
            'total_amount' => $total_amount, // The final price after the discount
            'total_discount' => $total_discount,
            'total' => $total_amount + $total_discount, // The original full price
            'discounted_items' => $discounted_items,
            'full_price_items' => $full_price_items,
        ];
    }


    public function createOrderDetails($userId, $orderId, Coupon $coupon = null)
    {
        $totals = $this->calculateCartTotals($userId, $coupon);
        $discounted = collect($totals['discounted_items']);
        $full = collect($totals['full_price_items']);

        $cart_items = Cart::where('user_id', $userId)->get();

        foreach ($cart_items as $item) {
            if (!$item->cartable) {
                continue;
            }

            $isCourse  = $item->cartable_type == \App\Models\Course::class;
            $isPackage = $item->cartable_type == \App\Models\Package::class;

            // Determine the price
            $isDiscounted = $discounted->contains(fn($i) =>
                $i['id'] == $item->cartable_id &&
                $i['type'] == $item->cartable_type
            );

            $amount = $isDiscounted ? 0 : (
            $isCourse
                ? $item->cartable->new_price
                : ($isPackage ? $item->cartable->price : 0)
            );

            OrderDetail::create([
                'order_id'   => $orderId,
                'course_id'  => $isCourse ? $item->cartable_id : null,
                'package_id' => $isPackage ? $item->cartable_id : null,
                'amount'     => $amount,
                'status'     => 'accepted',
            ]);
        }
    }



    public function isCartEmpty($userId)
    {
        return Cart::where('user_id', $userId)->count() == 0;
    }

    public function ClassChecker($class): string
    {
        $classType = $class; // 'Course'
        $modelClass = 'App\\Models\\' . ucfirst($classType); // App\Models\Course
        if (!class_exists($modelClass)) {
            return 'Invalid class type';
        }
        return $modelClass;
    }
}
