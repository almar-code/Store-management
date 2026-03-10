<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Subcategory;
use App\Models\Category;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Storage;



class CategorieController extends Controller
{

    public function categorieManagement(){
        try {
           $Subcategory = Subcategory::with('category')->get();
        return view('Categories.categorieManagement', compact('Subcategory'));
           
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب الفئات');
            
        }
    }


    public function AddCategorie(){
        try {
            $categories = Category::all();
            return view('Categories.addcategorie', compact('categories'));

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب الفئات');

        }
    }

    public function store(Request $request){

         $request->validate([
            'subcat_name' => 'required',
            'subcat_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'cat_id' => 'required'
        ]);
        try {
            $tr = new GoogleTranslate('en');
            $subcat_name_en = $tr->translate($request->subcat_name);

            $imageName = time().'_'.$request->file('subcat_image')->getClientOriginalName();
            $path = $request->file('subcat_image')->storeAs('uploads/subcategory', $imageName, 'public');

            Subcategory::create([
                'subcat_name' => $request->subcat_name,
                'subcat_name_en'=> $subcat_name_en,
                'subcat_image' => $imageName,
                'cat_id' => $request->cat_id
            ]);
            return redirect()->back()->with('success', 'تم إضافة الفئه بنجاح');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الفئه');
        }
    }


    public function edit($id)
    {
        try {
            $editCategory = Subcategory::findOrFail($id);
            $categories = Category::all();

            return view('Categories.addcategorie', compact('editCategory','categories'));

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'الفئه غير موجود');

        }
    }

    public function update(Request $request, $id)
    {
            $request->validate([
                'subcat_name' => 'required',
                'subcat_image' => 'image|mimes:jpg,jpeg,png|max:2048',
                'cat_id' => 'required'
            ]);

            try {
                $subcategory = Subcategory::findOrFail($id);

                $tr = new GoogleTranslate('en');
                $subcat_name_en = $tr->translate($request->subcat_name);

                $data = [
                    'subcat_name' => $request->subcat_name,
                    'subcat_name_en' => $subcat_name_en,
                    'cat_id' => $request->cat_id
                ];

                // إذا رفع صورة جديدة
                if ($request->hasFile('subcat_image')) {
                    // حذف الصورة القديمة
                     Storage::disk('public')->delete('uploads/subcategory/'.$subcategory->subcat_image);

                    // حفظ الصورة الجديدة
                    $imageName = time().'_'.$request->file('subcat_image')->getClientOriginalName();
                    $request->file('subcat_image')->storeAs('uploads/subcategory', $imageName, 'public');
                    $data['subcat_image'] = $imageName;
                }
                $subcategory->update($data);
                return redirect('/categorieManagement')->with('success', 'تم التعديل بنجاح');

            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'حدث خطأ أثناء التعديل');
            }
    }

    public function destroy($id)
    {

        try {

            $subcategory = Subcategory::findOrFail($id);
            Storage::disk('public')->delete('uploads/subcategory/'.$subcategory->subcat_image);

            $subcategory->delete();

            return redirect()->back()->with('success', 'تم حذف الفئه بنجاح');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف');

        }
    }

    
}
