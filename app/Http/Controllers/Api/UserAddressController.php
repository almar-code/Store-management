<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    /**
     * عرض جميع عناوين المستخدم المسجل حالياً
     */
   public function index(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
        ]);

        $customerId =$request->query('customer_id');
        $addresses = CustomerAddress::where('customer_id',$customerId)->get();

        return response()->json([
            'status' => true,
            'message' => 'تم جلب العناوين بنجاح',
            'data' => $addresses
        ], 200);
    }

    /**
     * إضافة عنوان جديد لعميل محدد
     */
    public function store(Request $request)
    {
        $validated =$request->validate([
            'customer_id' => 'required|integer', // إجباري عند الحفظ
            'title'       => 'required|string|max:255',
            'country'     => 'required|string|max:255',
            'city'        => 'required|string|max:255',
            'street'      => 'required|string|max:255',
            'building'    => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'is_default'  => 'nullable|boolean',
        ]);

        $validated['is_default'] =$request->boolean('is_default');

        $address = CustomerAddress::create($validated);

        if ($validated['is_default']) {$this->setDefaultAddressInternal($validated['customer_id'],$address->address_id);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة العنوان بنجاح',
            'data' => $address->fresh()
        ], 201);
    }
    /**
     * عرض عنوان معين بواسطة المعرف
     */
    public function show(Request $request, string $id)
    {
        $customerId = $request->user()->customer_id;

        $address = CustomerAddress::where('customer_id', $customerId)
            ->where('address_id', $id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'العنوان غير موجود'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $address
        ], 200);
    }

    /**
     * تعديل بيانات عنوان محدد
     */
    public function update(Request $request, string $id)
    {
        $address = CustomerAddress::where('address_id', $id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'العنوان غير موجود'
            ], 404);
        }

        $validated = $request->validate([
            'customer_id' => 'required|integer',
            'title'       => 'sometimes|required|string|max:255',
            'country'     => 'sometimes|required|string|max:255',
            'city'        => 'sometimes|required|string|max:255',
            'street'      => 'sometimes|required|string|max:255',
            'building'    => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'is_default'  => 'nullable|boolean',
        ]);

        $address->update($validated);

        if ($request->has('is_default') && $request->boolean('is_default')) {
            $this->setDefaultAddressInternal($validated['customer_id'], $address->address_id);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تعديل العنوان بنجاح',
            'data' => $address->fresh()
        ], 200);
    }

    /**
     * حذف عنوان محدد
     */
    public function destroy(Request $request, string $id)
    {

        $address = CustomerAddress::where('address_id', $id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'العنوان غير موجود'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف العنوان بنجاح'
        ], 200);
    }

    /**
     * API Route Endpoint: دالة لتغيير العنوان الافتراضي فقط عن طريق رقم العنوان (ID)
     */
    public function setDefault(Request $request, string $id)
    {
        $request->validate([
            'customer_id' => 'required|integer|exists:customers,customer_id',
        ]);

        $address = CustomerAddress::where('customer_id', $request->customer_id)
            ->where('address_id', $id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'العنوان غير موجود'
            ], 404);
        }

        $this->setDefaultAddressInternal($request->customer_id, $id);

        return response()->json([
            'status' => true,
            'message' => 'تم تعيين العنوان كافتراضي بنجاح',
            'data' => $address->fresh()
        ], 200);
    }
 /**
     * دالة داخلية لإلغاء الافتراضي عن باقي العناوين وتعيين العنوان الحالي كافتراضي
     */
    private function setDefaultAddressInternal($customerId, $addressId)
    {
        CustomerAddress::where('customer_id', $customerId)
            ->update(['is_default' => false]);

        CustomerAddress::where('customer_id', $customerId)
            ->where('address_id', $addressId)
            ->update(['is_default' => true]);
    }

   
   
}