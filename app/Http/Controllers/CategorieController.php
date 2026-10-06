<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subcategory;
use App\Models\Category;
use App\Services\TranslationService;
use Illuminate\Support\Facades\Storage;

class CategorieController extends Controller
{
    // عرض الفئات الفرعية للتطبيق
    public function index(Request $request)
    {
        try {

            // استقبال ID القسم الرئيسي
            $categoryId = $request->query('category_id');

            // بناء الاستعلام
            $query = Subcategory::query();

            if ($categoryId) {

                // جلب الفئات التابعة للقسم المحدد فقط
                $query->where('cat_id', $categoryId);

            } else {

                // إذا لم يتم إرسال القسم، عرض الفئات بشكل عشوائي
                $query->inRandomOrder();
            }

            $subcategories = $query->get()->map(function ($subcategory) {

                // إضافة الرابط الكامل للصورة
                if ($subcategory->subcat_image) {

                    $subcategory->subcat_image =
                        asset(
                            'storage/uploads/subcategory/' .
                            $subcategory->subcat_image
                        );
                }

                return $subcategory;
            });


            return response()->json([
                'status' => true,
                'subcategories' => $subcategories
            ], 200);


        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // إدارة الفئات
    public function categorieManagement()
    {
        try {

            $Subcategory = Subcategory::with('category')->get();

            return view(
                'Categories.categorieManagement',
                compact('Subcategory')
            );


        } catch (\Throwable $th) {

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء جلب الفئات');
        }
    }


    // صفحة إضافة فئة
    public function AddCategorie()
    {
        try {

            $categories = Category::all();

            return view(
                'Categories.addcategorie',
                compact('categories')
            );


        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء جلب الفئات');
        }
    }


    // إضافة فئة جديدة
    public function store(Request $request)
    {
        $request->validate([
            'subcat_name' => 'required|max:255',
            'subcat_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'cat_id' => 'required'
        ]);

        try {

            /*
             * التحقق من عدم وجود نفس اسم الفئة
             * داخل نفس القسم الرئيسي فقط.
             *
             * يمكن استخدام نفس الاسم في قسم رئيسي آخر.
             */
            $exists = Subcategory::where(
                'subcat_name',
                $request->subcat_name
            )
                ->where(
                    'cat_id',
                    $request->cat_id
                )
                ->exists();


            if ($exists) {

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'الفئة موجودة مسبقًا في هذا القسم');
            }


            // ترجمة اسم الفئة
            // في حالة فشل الترجمة يتم استخدام الاسم العربي
            $subcat_name_en = TranslationService::translate(
                $request->subcat_name
            );


            // تجهيز اسم الصورة
            $imageName = null;

            if ($request->hasFile('subcat_image')) {

                $imageName =
                    time() . '_' .
                    $request->file('subcat_image')
                        ->getClientOriginalName();


                // نقل الصورة إلى مجلد الفئات
                $request->file('subcat_image')->move(
                    public_path('storage/uploads/subcategory'),
                    $imageName
                );
            }


            // إنشاء الفئة
            Subcategory::create([
                'subcat_name' => $request->subcat_name,
                'subcat_name_en' => $subcat_name_en,
                'subcat_image' => $imageName,
                'cat_id' => $request->cat_id
            ]);


            return redirect()->back()
                ->with('success', 'تم إضافة الفئة بنجاح');


        } catch (\Throwable $th) {

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الفئة');
        }
    }


    // صفحة تعديل الفئة
    public function edit($id)
    {
        try {

            $editCategory = Subcategory::findOrFail($id);

            $categories = Category::all();

            return view(
                'Categories.addcategorie',
                compact('editCategory', 'categories')
            );


        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'الفئة غير موجودة');
        }
    }


    // تحديث الفئة
    public function update(Request $request, $id)
    {
        $request->validate([
            'subcat_name' => 'required|max:255',
            'subcat_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'cat_id' => 'required'
        ]);

        try {

            // جلب الفئة الحالية
            $subcategory = Subcategory::findOrFail($id);


            /*
             * التحقق من وجود نفس الاسم
             * في نفس القسم الرئيسي.
             *
             * نستثني الفئة الحالية حتى يمكن للمستخدم
             * حفظ الاسم نفسه بدون ظهور رسالة خطأ.
             */
            $exists = Subcategory::where(
                'subcat_name',
                $request->subcat_name
            )
                ->where(
                    'cat_id',
                    $request->cat_id
                )
                ->where(
                    'subcat_id',
                    '!=',
                    $id
                )
                ->exists();


            if ($exists) {

                return redirect()->back()
                    ->withInput()
                    ->with(
                        'error',
                        'الفئة موجودة مسبقًا في هذا القسم'
                    );
            }


            // ترجمة اسم الفئة
            // إذا فشلت الترجمة يتم استخدام الاسم العربي
            $subcat_name_en = TranslationService::translate(
                $request->subcat_name
            );


            // البيانات الأساسية للتحديث
            $data = [
                'subcat_name' => $request->subcat_name,
                'subcat_name_en' => $subcat_name_en,
                'cat_id' => $request->cat_id
            ];


            // إذا تم رفع صورة جديدة
            if ($request->hasFile('subcat_image')) {

                // حذف الصورة القديمة
                if (!empty($subcategory->subcat_image)) {

                    $oldImagePath = public_path(
                        'storage/uploads/subcategory/' .
                        $subcategory->subcat_image
                    );

                    if (\File::exists($oldImagePath)) {

                        \File::delete($oldImagePath);
                    }
                }


                // إنشاء اسم الصورة الجديدة
                $imageName =
                    time() . '_' .
                    $request->file('subcat_image')
                        ->getClientOriginalName();


                // نقل الصورة الجديدة
                $request->file('subcat_image')->move(
                    public_path('storage/uploads/subcategory'),
                    $imageName
                );


                // إضافة الصورة إلى بيانات التحديث
                $data['subcat_image'] = $imageName;
            }


            // تحديث الفئة
            $subcategory->update($data);


            return redirect('/categorieManagement')
                ->with('success', 'تم التعديل بنجاح');


        } catch (\Throwable $th) {

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء التعديل');
        }
    }


    // حذف الفئة
    public function destroy($id)
    {
        try {

            $subcategory = Subcategory::findOrFail($id);

            $imageName = $subcategory->subcat_image;


            // حذف الصورة
            if (!empty($imageName)) {

                if (app()->environment('local')) {

                    // الحذف في البيئة المحلية
                    Storage::disk('public')->delete(
                        'uploads/subcategory/' . $imageName
                    );

                } else {

                    // الحذف في الاستضافة
                    $imagePath = public_path(
                        'storage/uploads/subcategory/' .
                        $imageName
                    );

                    if (\File::exists($imagePath)) {

                        \File::delete($imagePath);
                    }
                }
            }


            // حذف الفئة من قاعدة البيانات
            $subcategory->delete();


            return redirect()->back()
                ->with('success', 'تم حذف الفئة بنجاح');


        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء الحذف');
        }
    }
}