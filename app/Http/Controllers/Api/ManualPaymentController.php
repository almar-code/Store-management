<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ManualPaymentController extends Controller
{
    /**
     * GET /api/store-payment-accounts?type=wallet
     * GET /api/store-payment-accounts?type=bank
     */
    public function accounts(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'type' => 'nullable|in:bank,wallet',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $query = DB::table('store_payment_accounts')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('provider_name');

        if ($request->filled('type')) {
            $query->where('account_type', $request->query('type'));
        }

        return response()->json([
            'success' => true,
            'data' => $query->get([
                'store_payment_account_id',
                'account_type',
                'provider_name',
                'account_holder_name',
                'account_number',
                'iban',
                'instructions',
            ]),
        ]);
    }

    /**
     * POST /api/manual-payment/checkout
     *
     * payment_type:
     * cash | bank_wallet | bank_transfer
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer|exists:customers,customer_id',
            'address_id' => 'required|integer|exists:customer_addresses,address_id',

            'payment_type' => 'required|in:cash,bank_wallet,bank_transfer',

            'store_payment_account_id' => 'nullable|integer',
            'transaction_reference' => 'nullable|string|max:150',
            'bank_name' => 'nullable|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $type = $request->input('payment_type');

        if ($type === 'bank_wallet') {
            $extra = Validator::make($request->all(), [
                'store_payment_account_id' => 'required|integer',
                'transaction_reference' => 'required|string|max:150',
            ]);

            if ($extra->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $extra->errors()->first(),
                ], 422);
            }
        }

        if ($type === 'bank_transfer') {
            $extra = Validator::make($request->all(), [
                'bank_name' => 'required|string|max:150',
                'transaction_reference' => 'required|string|max:150',
            ]);

            if ($extra->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $extra->errors()->first(),
                ], 422);
            }
        }

        try {
            $result = DB::transaction(function () use ($request, $type) {
                $customerId = (int) $request->customer_id;
                $addressId = (int) $request->address_id;

                $address = DB::table('customer_addresses')
                    ->where('address_id', $addressId)
                    ->where('customer_id', $customerId)
                    ->first();

                if (!$address) {
                    throw new \DomainException(
                        'عنوان التوصيل لا يتبع هذا العميل.'
                    );
                }

                $cart = DB::table('carts')
                    ->where('customer_id', $customerId)
                    ->first();

                if (!$cart) {
                    throw new \DomainException('السلة فارغة.');
                }

                $items = DB::table('cart_items as ci')
                    ->join('products as p', 'p.p_id', '=', 'ci.product_id')
                    ->where('ci.cart_id', $cart->cart_id)
                    ->select(
                        'ci.cart_item_id',
                        'ci.cart_id',
                        'ci.product_id',
                        'ci.color_id',
                        'ci.size_id',
                        'ci.quantity',
                        'p.p_price'
                    )
                    ->lockForUpdate()
                    ->get();

                if ($items->isEmpty()) {
                    throw new \DomainException('السلة فارغة.');
                }

                $total = 0;
                $snapshot = [];

                foreach ($items as $item) {
                    $price = (float) $item->p_price;
                    $quantity = (int) $item->quantity;

                    if ($price < 0 || $quantity < 1) {
                        throw new \DomainException(
                            'يوجد سعر أو مقدار غير صالح في السلة.'
                        );
                    }

                    $total += $price * $quantity;

                    $snapshot[] = [
                        'cart_item_id' => (int) $item->cart_item_id,
                        'product_id' => (int) $item->product_id,
                        'quantity' => $quantity,
                        'price' => round($price, 2),
                        'color_id' => $item->color_id,
                        'size_id' => $item->size_id,
                    ];
                }

                $total = round($total, 2);

                if ($total <= 0) {
                    throw new \DomainException(
                        'إجمالي الطلب غير صالح.'
                    );
                }

                $storeAccount = null;

                if ($type === 'bank_wallet') {
                    $storeAccount = DB::table('store_payment_accounts')
                        ->where(
                            'store_payment_account_id',
                            $request->store_payment_account_id
                        )
                        ->whereIn('account_type', ['wallet', 'bank'])
                        ->where('is_active', true)
                        ->first();

                    if (!$storeAccount) {
                        throw new \DomainException(
                            'المحفظة المختارة غير متاحة.'
                        );
                    }

                    $methodName = $storeAccount->account_type === 'bank'
                    ? 'Bank Transfer'
                    : 'Wallet';
                } elseif ($type === 'bank_transfer') {
                    $methodName = 'Bank Transfer';
                } else {
                    $methodName = 'Cash on Delivery';
                }

                $method = DB::table('payment_methods')
                    ->where('method_name', $methodName)
                    ->first();

                if (!$method) {
                    $methodId = DB::table('payment_methods')->insertGetId([
                        'method_name' => $methodName,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $methodId = $method->method_id;
                }

                $orderId = DB::table('orders')->insertGetId([
                    'customer_id' => $customerId,
                    'address_id' => $addressId,
                    'total_price' => $total,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($snapshot as $item) {
                    DB::table('order_items')->insert([
                        'order_id' => $orderId,
                        'p_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'color_id' => $item['color_id'],
                        'size_id' => $item['size_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('payments')->insert([
                    'order_id' => $orderId,
                    'method_id' => $methodId,
                    'amount' => $total,
                    'transaction_id' => $request->input('transaction_reference'),
                    'provider' => $type,
                    'provider_payment_intent_id' => null,
                    'status' => 'pending',

                    'store_payment_account_id' =>
                        $storeAccount?->store_payment_account_id,

                    'store_account_name' =>
                        $storeAccount?->provider_name,

                    'store_account_number' =>
                        $storeAccount?->account_number,

                    'customer_bank_name' =>
                        $type === 'bank_transfer'
                            ? $request->input('bank_name')
                            : null,

                    'customer_transaction_reference' =>
                        $request->input('transaction_reference'),

                    'verification_status' => 'pending',
                    'verified_by' => null,
                    'verified_at' => null,
                    'verification_note' => null,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // حذف عناصر السلة التي أُدرجت في الطلب فقط.
                $cartItemIds = collect($snapshot)
                    ->pluck('cart_item_id')
                    ->all();

                DB::table('cart_items')
                    ->where('cart_id', $cart->cart_id)
                    ->whereIn('cart_item_id', $cartItemIds)
                    ->delete();

                return [
                    'order_id' => $orderId,
                    'amount' => $total,
                    'payment_status' => 'pending',
                    'payment_type' => $type,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الطلب، وحالة الدفع بانتظار التحقق.',
                'data' => $result,
            ], 201);
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'تعذر إنشاء الطلب. تحقق من بيانات الدفع وقاعدة البيانات.',
            ], 500);
        }
    }

    /**
     * POST /api/admin/payments/{paymentId}/verify
     *
     * يجب حماية هذا المسار بصلاحيات لوحة الإدارة.
     */
    public function verify(Request $request, int $paymentId)
    {
        $validator = Validator::make($request->all(), [
            'verification_status' => 'required|in:confirmed,rejected',
            'verification_note' => 'nullable|string|max:1000',
            'admin_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $paymentId) {
                $payment = DB::table('payments')
                    ->where('payment_id', $paymentId)
                    ->lockForUpdate()
                    ->first();

                if (!$payment) {
                    throw new \DomainException('سجل الدفع غير موجود.');
                }

                $order = DB::table('orders')
                    ->where('order_id', $payment->order_id)
                    ->lockForUpdate()
                    ->first();

                if (!$order) {
                    throw new \DomainException('الطلب غير موجود.');
                }

                $confirmed =
                    $request->verification_status === 'confirmed';

                DB::table('payments')
                    ->where('payment_id', $paymentId)
                    ->update([
                        'verification_status' =>
                            $confirmed ? 'confirmed' : 'rejected',
                        'status' => $confirmed ? 'succeeded' : 'failed',
                        'verified_by' => $request->admin_id,
                        'verified_at' => now(),
                        'verification_note' =>
                            $request->verification_note,
                        'updated_at' => now(),
                    ]);

                DB::table('orders')
                    ->where('order_id', $order->order_id)
                    ->update([
                        'payment_status' =>
                            $confirmed ? 'paid' : 'failed',
                        'updated_at' => now(),
                    ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة التحقق من الدفع.',
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'تعذر تحديث حالة الدفع.',
            ], 500);
        }
    }
}
