<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\TranslationService;

class SectionController extends Controller
{
    // عرض الأقسام بصيغة JSON
    public function index()
    {
        try {

            $sections = Category::inRandomOrder()->get();

            return response()->json([
                'status' => true,
                'sections' => $sections
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // عرض الأقسام
    public function Sections()
    {
        try {

            $sections = Category::all();

            return view('Sections.sections', compact('sections'));

        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء جلب الأقسام');
        }
    }


    // صفحة إضافة قسم
    public function AddSection()
    {
        try {

            return view('Sections.addsection');

        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء فتح الصفحة');
        }
    }


    // حفظ القسم
    public function store(Request $request)
    {
        $request->validate([
            'sectionName' => 'required|max:255'
        ]);

        try {

            // التحقق من وجود القسم مسبقًا
            $exists = Category::where(
                'cat_name',
                $request->sectionName
            )->exists();

            if ($exists) {

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'القسم موجود مسبقًا');
            }


            // ترجمة اسم القسم
            // إذا فشلت الترجمة يتم إرجاع الاسم العربي تلقائيًا
            $name_en = TranslationService::translate(
                $request->sectionName
            );


            // إنشاء القسم
            Category::create([
                'cat_name' => $request->sectionName,
                'cat_name_en' => $name_en
            ]);


            return redirect()->back()
                ->with('success', 'تم الإضافة بنجاح');


        } catch (\Exception $e) {

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء الإضافة');
        }
    }


    // عند الضغط على تعديل
    public function edit($id)
    {
        try {

            // جلب القسم المطلوب تعديله
            $editSection = Category::findOrFail($id);

            // فتح صفحة التعديل مع البيانات
            return view(
                'Sections.addsection',
                compact('editSection')
            );

        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'القسم غير موجود');
        }
    }


    // تحديث البيانات
    public function update(Request $request, $id)
    {
        $request->validate([
            'sectionName' => 'required|max:255'
        ]);

        try {

            // جلب القسم
            $section = Category::findOrFail($id);


            // التحقق من وجود نفس الاسم في قسم آخر
            $exists = Category::where(
                'cat_name',
                $request->sectionName
            )
            ->where(
                'cat_id',
                '!=',
                $id
            )
            ->exists();


            if ($exists) {

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'القسم موجود مسبقًا');
            }


            // ترجمة اسم القسم
            // إذا فشلت الترجمة يتم استخدام الاسم العربي
            $name_en = TranslationService::translate(
                $request->sectionName
            );


            // تحديث القسم
            $section->update([
                'cat_name' => $request->sectionName,
                'cat_name_en' => $name_en
            ]);


            return redirect('/sections')
                ->with('success', 'تم التعديل بنجاح');


        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء التعديل');
        }
    }


    // حذف القسم
    public function destroy($id)
    {
        try {

            // جلب القسم
            $section = Category::findOrFail($id);

            // حذف القسم
            $section->delete();


            return redirect()->back()
                ->with('success', 'تم حذف القسم بنجاح');


        } catch (\Exception $e) {

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء الحذف');
        }
    }
}
