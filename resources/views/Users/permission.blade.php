@extends('layouts.master')
@section('link')
    <link href="assets/css/order.css" rel="stylesheet">
    <style>
        /* Custom Animated Checkbox - Scoped */
        .ux-checkbox-wrapper {
  display: inline-block; /* يأخذ حجم المحتوى فقط */
}
.ux-checkbox {
  display: block;
  position: relative;
  cursor: pointer;
  font-size: 20px;
  user-select: none;
  border: 3px solid #beddd0;
  border-radius: 10px;
  overflow: hidden;
}

/* Hide default checkbox */
.ux-checkbox input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Custom box */
.ux-checkmark {
  position: relative;
  height: 1.3em;
  width: 1.3em;
  background-color: #2dc38c;
  border-bottom: 1.5px solid #2dc38c;
  box-shadow: 0 0 1px #cef1e4, 
              inset 0 -2.5px 3px #62eab8,
              inset 0 3px 3px rgba(0, 0, 0, 0.34);
  border-radius: 8px;
  transition: transform 0.3s ease-in-out;
}

/* Checked state */
.ux-checkbox input:checked ~ .ux-checkmark {
  transform: translateY(40px);
  animation: ux-wipeDown 0.6s ease-in-out forwards;
}

/* Unchecked state */
.ux-checkbox input:not(:checked) ~ .ux-checkmark {
  transform: translateY(-40px);
  animation: ux-wipeUp 0.6s ease-in-out forwards;
}

/* Animations */
@keyframes ux-wipeDown {
  0% { transform: translateY(0); }
  100% { transform: translateY(40px); }
}

@keyframes ux-wipeUp {
  0% { transform: translateY(40px); }
  100% { transform: translateY(0px); }
}

/* Check icon */
.ux-checkmark:before {
  content: "";
  position: absolute;
  left: 10px;
  top: 4px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
  box-shadow: 0 4px 2px rgba(0, 0, 0, 0.34);
}
        
    </style>
@endsection
@section('content')
    <div class="container my-4  px-2 px-md-2" dir="rtl">
        <div class="container">

            <div class="controls-container">

                <div class="section-ar">

                    <div class="section-title" style="margin-bottom: 0px;">
                        <h3 style="font-size: 20px">قائمة <span class="orange-text">الصلاحيات</span></h3>
                    </div>
                </div>
                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search">
                </div>

                <a href="/permission" class="btn-add-product shadow-sm" style="justify-content: center ;">

                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>الاسم</th>
                            <th>اسم المستخدم</th>
                            <th>كلمة السر</th>
                            <th>النشاط</th>
                            <th>عمليات</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                         <tr>
                            <td>1</td>
                            <td>سارة محمد</td>
                            <td>sara</td>
                            <td>22210Sara</td>
                            <td><!-- From Uiverse.io by elijahgummer --> 
<div class="ux-checkbox-wrapper">
  <label class="ux-checkbox">
    <input type="checkbox" />
    <div class="ux-checkmark"></div>
  </label>
</div>
</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn text-dark p-0 border-0 shadow-none " type="button"
                                        data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical fs-4"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-start shadow border-light">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                href="#">
                                                <span class="ms-2">تعديل</span>
                                                <i class="bi bi-pencil-square text-turquoise"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-danger font-weight-bold text-end"
                                                href="#">
                                                <span class="ms-2">حذف الصلاحية</span>
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
