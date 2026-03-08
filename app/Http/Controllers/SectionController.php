<?php

namespace App\Http\Controllers;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Category;
use Illuminate\Http\Request;

class SectionController extends Controller
{

    // عرض الأقسام
    public function Sections()
    {
        try {

            $sections = Category::all();

            return view('Sections.sections', compact('sections'));

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب الأقسام');

        }
    }


    // صفحة إضافة قسم
    public function AddSection()
    {
        try {

            return view('Sections.addsection');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء فتح الصفحة');

        }
    }


    // حفظ القسم
    public function store(Request $request)
    {

        $request->validate([
            'sectionName' => 'required|max:255'
        ]);

        try {

            /// ترجمة الاسم للإنجليزي
            $tr = new GoogleTranslate('en');
            $name_en = $tr->translate($request->sectionName);

            Category::create([
                'cat_name' => $request->sectionName,
                'cat_name_en' => $name_en
            ]);

            return redirect()->back()->with('success', 'تم إضافة القسم بنجاح');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة القسم');

        }

    }


    // عند الضغط على تعديل
    public function edit($id)
    {
        try {

            // جلب القسم المطلوب تعديله
            $editSection = Category::findOrFail($id);

            // فتح صفحة الفورم مع البيانات
            return view('Sections.addsection', compact('editSection'));

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'القسم غير موجود');

        }
    }


    // تحديث البيانات
    public function update(Request $request, $id)
    {
        $request->validate([
            'sectionName' => 'required|max:255'
        ]);

        try {

            $section = Category::findOrFail($id);
            // ترجمة الاسم للإنجليزي
            $tr = new GoogleTranslate('en');
            $name_en = $tr->translate($request->sectionName);
            $section->update([
                'cat_name' => $request->sectionName,
                'cat_name_en' => $name_en
            ]);

            return redirect('/sections')->with('success', 'تم التعديل بنجاح');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء التعديل');

        }
    }


    // حذف القسم
    public function destroy($id)
    {
        try {

            $section = Category::findOrFail($id);

            $section->delete();

            return redirect()->back()->with('success', 'تم حذف القسم');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف');

        }
    }

}