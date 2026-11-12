<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    // صفحة مراجعة المدفوعات
    public function index()
    {
        $payments = Payment::with(['order.customer', 'method', 'verifier', 'storeAccount'])
             ->latest()
            ->get();
            

        return view('Payments.payments', compact('payments'));
    }

    // تأكيد او رفض الدفع
    public function verify(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|in:confirmed,rejected',
            'note'   => 'nullable|string|max:1000',
        ]);

        $payment = Payment::with('order')->findOrFail($id);
        $confirmed = $data['status'] === 'confirmed';

        DB::transaction(function () use ($payment, $data, $confirmed) {
            $payment->update([
                'verification_status' => $data['status'],
                'status'              => $confirmed ? 'succeeded' : 'failed',
                'verified_by'         => auth()->id(),
                'verified_at'         => now(),
                'verification_note'   => $data['note'] ?? null,
            ]);

            // تحديث حالة الدفع في الطلب
            $payment->order?->update([
                'payment_status' => $confirmed ? 'paid' : 'failed',
            ]);
        });

        return response()->json([
            'success'     => true,
            'state'       => $data['status'],
            'label'       => Payment::REVIEW_STATES[$data['status']],
            'verified_by' => auth()->user()->full_name ?? '',
            'verified_at' => now()->format('Y-m-d h:i A'),
        ]);
    }
}