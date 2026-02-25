<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Nice</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="assets/img/ligth-mode-logo.gif" rel="icon">
    <link href="assets/img/ligth-mode-logo.gif" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/card.css" rel="stylesheet">
    @yield('link')
</head>

<body class="index-page">

    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container position-relative d-flex align-items-center justify-content-between">

            <a href="/" class="logo d-flex align-items-center me-auto me-xl-0">
                <!-- Uncomment the line below if you also wish to use an image logo -->
                 {{-- <img src="assets/img/ligth-mode-logo.gif" alt="">  --}}
                 <img src="assets/img/logo.png" alt=""> 
                <h1 class="sitename" style="color:#008870">N<h1 style="color: rgb(0, 0, 0)">ice</h1></h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul >
                    <li><a href="/"class="{{ request()->is('/') ? 'active' : '' }}">الرئيسية</a></li>
                    <li class="dropdown"><a href="#" ><span class="toggle-dropdown {{ request()->is('addproduct') || request()->is('products') ? 'active' : '' }}">المنتجات</span> <i
                                class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li></i><a href="/addproduct" class="{{ request()->is('addproduct') ? 'active' : '' }}">إضافة منتج</a></li>
                            <li><a href="/products" class="{{ request()->is('products') ? 'active' : '' }}">قائمة المنتجات</a></li>

                        </ul>
                    </li>
                    <li class="dropdown"><a href="#"><span  class="toggle-dropdown {{ request()->is('addcategorie') || request()->is('categorieManagement') ? 'active' : '' }}">الفئات</span> <i
                                class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li></i><a href="/addcategorie" class="{{ request()->is('addcategorie') ? 'active' : '' }}">إضافة فئة</a></li>
                            <li><a href="/categorieManagement" class="{{ request()->is('categorieManagement') ? 'active' : '' }}">قائمة الفئات</a></li>

                        </ul>
                    </li>
                    <li class="dropdown"><a href="#"><span  class="toggle-dropdown {{ request()->is('addsection') || request()->is('sections') ? 'active' : '' }}">الاقسام</span> <i
                                class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li></i><a href="/addsection" class="{{ request()->is('addsection') ? 'active' : '' }}">إضافة قسم</a></li>
                            <li><a href="/sections" class="{{ request()->is('sections') ? 'active' : '' }}">قائمة الاقسام</a></li>

                        </ul>
                    </li>
                    <li class="dropdown"><a href="#"><span  class="toggle-dropdown {{ request()->is('') || request()->is('') ? 'active' : '' }}">المقاسات</span> <i
                                class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li></i><a href="/addproduct" class="{{ request()->is('') ? 'active' : '' }}">إضافة مقاس</a></li>
                            <li><a href="#" class="{{ request()->is('') ? 'active' : '' }}">قائمة المقاسات</a></li>

                        </ul>
                    </li>
                    <li class="dropdown"><a href="#"><span  class="toggle-dropdown {{ request()->is('') || request()->is('') ? 'active' : '' }}" >الالوان</span> <i
                                class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li></i><a href="/" >إضافة لون</a></li>
                            <li><a href="#" class="{{ request()->is('') ? 'active' : '' }}">قائمة الالوان</a></li>

                        </ul>
                    </li>
                    <li class="dropdown"><a href="#"><span  class="toggle-dropdown {{ request()->is('') || request()->is('') ? 'active' : '' }}">الإعلانات</span> <i
                                class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li></i><a href="/" class="{{ request()->is('') ? 'active' : '' }}">إضافة إعلان</a></li>
                            <li><a href="#" class="{{ request()->is('') ? 'active' : '' }}">قائمة الإعلانات</a></li>

                        </ul>
                    </li>
                     <li><a href="/orders" class="{{ request()->is('orders') ? 'active' : '' }}">الطلبات</a></li>
                   


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
<main>
@yield('content')
</main>
   <footer class="footer mt-auto py-3  text-white-50">
    <div class="container-fluid px-4">
        <div class="row align-items-center justify-content-between flex-column flex-md-row text-center text-md-end ">
            
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

<script>
    function updateTime() {
        const now = new Date();
        const options = { 
            year: 'numeric', month: '2-digit', day: '2-digit',
            hour: '2-digit', minute: '2-digit', second: '2-digit',
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
    <script src="assets/js/main.js"></script>


</body>

</html>