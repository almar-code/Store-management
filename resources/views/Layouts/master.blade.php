<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Nice</title>

    <meta name="description" content="">
    <meta name="keywords" content="">


    <!-- Favicons -->
    <link href="{{ asset('assets/img/logo.png') }}" rel="icon">
    <link href="{{ asset('assets/img/logo.png') }}" rel="apple-touch-icon">


    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">


    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap"
        rel="stylesheet">


    <!-- Vendor CSS -->

    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">


    <!-- Main CSS -->

    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/css/card.css') }}" rel="stylesheet">


    @yield('link')


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">


    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="index-page">

    @unless (request()->is('login'))
        <header id="header" class="header d-flex align-items-center sticky-top">
            <div class="container position-relative d-flex align-items-center justify-content-between">

                <a href="/" class="logo d-flex align-items-center me-auto me-xl-0">
                    <!-- Uncomment the line below if you also wish to use an image logo -->
                    {{-- <img src="assets/img/ligth-mode-logo.gif" alt="">  --}}
                    <img src="{{asset('assets/img/logo.png')}}" alt="">
                    <h1 class="sitename" style="color:#008870">N<h1 style="color: rgb(0, 0, 0)">ice</h1>
                    </h1>
                </a>

                <nav id="navmenu" class="navmenu">
                    <ul>

                        <li>
                            <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">
                                <div>
                                <span  class="bi bi-house nav-icon"></span>
                                الرئيسية
                                </div>
                            </a>
                        </li>

                        <li class="dropdown">
                            <a href="#">
                                <div class = "toggle-dropdown ">
                                <span class="bi bi-box-seam nav-icon"></span>
                                <span
                                    class="toggle-dropdown {{ request()->is('addproduct') || request()->is('products') ? 'active' : '' }}">المنتجات</span>
                                </div>
                                    <i class="bi bi-chevron-down toggle-dropdown"></i>
                            </a>

                            <ul>
                                <li>
                                    <a href="/addproduct" class="{{ request()->is('addproduct') ? 'active' : '' }}">
                                        <div><span class="bi bi-plus-circle sub-icon"></span>
                                        إضافة منتج
                                  </div>
                                          </a>
                                </li>

                                <li>
                                    <a href="/products" class="{{ request()->is('products') ? 'active' : '' }}">
                                        <div><span class="bi bi-list-ul sub-icon"></span>
                                        قائمة المنتجات
                                 </div>   </a>
                                </li>
                            </ul>
                        </li>


                        <li class="dropdown">
                            <a href="#">
                                <div class = "toggle-dropdown "> <span class="bi bi-tags nav-icon"></span>
                                <span
                                    class="toggle-dropdown {{ request()->is('addcategorie') || request()->is('categorieManagement') ? 'active' : '' }}">الفئات</span>
                               </div>
                                <i class="bi bi-chevron-down toggle-dropdown"></i>
                            </a>

                            <ul>
                                <li>
                                    <a href="/addcategorie" class="{{ request()->is('addcategorie') ? 'active' : '' }}">
                                       <div> <span class="bi bi-plus-circle sub-icon"></span>
                                        إضافة فئة</div>
                                    </a>
                                </li>

                                <li>
                                    <a href="/categorieManagement"
                                        class="{{ request()->is('categorieManagement') ? 'active' : '' }}">
                                        <div><span class="bi bi-list-ul sub-icon"></span>
                                        قائمة الفئات</div>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li class="dropdown">
                            <a href="#">
                                <div class = "toggle-dropdown "><span class="bi bi-grid nav-icon"></span>
                                <span
                                    class="toggle-dropdown {{ request()->is('addsection') || request()->is('sections') ? 'active' : '' }}">الاقسام</span>
                              </div>
                                  <i class="bi bi-chevron-down toggle-dropdown"></i>
                            </a>

                            <ul>
                                <li>
                                    <a href="/addsection" class="{{ request()->is('addsection') ? 'active' : '' }}">
                                        <div><span class="bi bi-plus-circle sub-icon"></span>
                                        إضافة قسم</div>
                                    </a>
                                </li>

                                <li>
                                    <a href="/sections" class="{{ request()->is('sections') ? 'active' : '' }}">
                                       <div> <span class="bi bi-list-ul sub-icon"></span>
                                        قائمة الاقسام</div>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li class="dropdown">
                            <a href="#">
                                <div class="toggle-dropdown ">
                                <span class="bi bi-megaphone nav-icon"></span>
                                <span class="toggle-dropdown">الإعلانات</span>
                                </div>
                                <i class="bi bi-chevron-down toggle-dropdown"></i>
                            </a>

                            <ul>
                                <li>
                                    <a href="/addads">
                                       <div> <span class="bi bi-plus-circle sub-icon"></span>
                                        إضافة إعلان</div>
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <div><span class="bi bi-list-ul sub-icon"></span>
                                        قائمة الإعلانات</div>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li class="dropdown">
                            <a href="#" >
                                <div class="toggle-dropdown ">
                                <span class="bi bi-people nav-icon"></span>
                                <span
                                    class="toggle-dropdown {{ request()->is('addUser') || request()->is('users') || request()->is('permission') ? 'active' : '' }}">المستخدمين</span>
                               </div>
                                 <i class="bi bi-chevron-down toggle-dropdown"></i>
                            </a>

                            <ul>
                                <li>
                                    <a href="/addUser" class="{{ request()->is('addUser') ? 'active' : '' }}">
                                       <div> <span class="bi bi-person-plus sub-icon"></span>
                                        إضافة مستخدم</div>
                                    </a>
                                </li>

                                <li>
                                    <a href="/users" class="{{ request()->is('users') ? 'active' : '' }}">
                                       <div> <span class="bi bi-people sub-icon"></span>
                                        قائمة المستخدمين</div>
                                    </a>
                                </li>

                                <li>
                                    <a href="/permission" class="{{ request()->is('permission') ? 'active' : '' }}">
                                       <div> <span class="bi bi-shield-lock sub-icon"></span>
                                        قائمة الصلاحيات</div>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li>
                            <a href="/orders" class="{{ request()->is('orders') ? 'active' : '' }}">
                                <div><span class="bi bi-bag-check nav-icon"></span>
                                الطلبات</div>
                            </a>
                        </li>


                        <li>
                            <a href="/login">
                                <div><span class="bi bi-box-arrow-right nav-icon"></span>
                                تسجيل خروج</div>
                            </a>
                        </li>

                    </ul>

                    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>

                </nav>

                <!-- <div class="header-social-links">
                                                    <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
                                                    <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                                                    <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                                                    <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                                                  </div> -->

            </div>
        </header>
    @endunless

    <main>
        @yield('content')
    </main>

    @unless (request()->is('login'))
        <footer class="footer mt-auto py-3  text-white-50">
            <div class="container-fluid px-4">
                <div
                    class="row align-items-center justify-content-between flex-column flex-md-row text-center text-md-end ">

                    <div class="col-auto">
                        <div class="small m-0 ">
                            <i class="bi bi-person-circle text-turquoise me-1"></i>
                            المستخدم الحالي:
                            <span class="fw-bold text-turquoise">
                                {{ Auth::user()->name ?? 'مدير النظام' }}
                            </span>
                        </div>
                    </div>

                    <div class="col-auto my-2 my-md-0">
                        <div class="small">
                            <i class="bi bi-clock-fill text-turquoise me-1"></i>
                            <span id="current-time">
                                {{ now()->format('Y-m-d | h:i A') }}
                            </span>
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="small m-0 ">
                            إدارة متجر العبايات &copy; {{ date('Y') }}
                            <span class="text-turquoise mx-1">|</span>
                            الإصدار 1.0.0
                        </div>
                    </div>

                </div>
            </div>
        </footer>
    @endunless
    <script>
        function updateTime() {
            const now = new Date();
            const options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            document.getElementById('current-time').innerText = now.toLocaleString('en-GB', options).replace(',', ' |');
        }
        // تحديث الوقت كل ثانية
        setInterval(updateTime, 1000);
    </script>
    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

    @yield('jsfile')
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'نجاح',
                timer: 3000,
                width: '250px',
                timerProgressBar: true,
                showConfirmButton: false, // إخفاء زر OK
                showCloseButton: true, // إظهار زر X
                text: '{{ session('success') }}',
                customClass: {
                    popup: 'compact-alert'
                }
            })
        </script>F
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                timer: 3000,
                width: '250px',
                timerProgressBar: true,
                showConfirmButton: false, // إخفاء زر OK
                showCloseButton: true, // إظهار زر X
                text: '{{ session('error') }}',
                customClass: {
                    popup: 'compact-alert'
                }
            })
        </script>
    @endif
</body>

</html>
