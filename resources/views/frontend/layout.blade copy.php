<!doctype html>
<html lang="ar" dir="rtl">
@include('frontend.partials.header')

<body>

    <!-- Start Preloader Area -->
    <div class="preloader-area">
        <div class="spinner">
            <div class="inner">
                <div class="disc"></div>
                <div class="disc"></div>
                <div class="disc"></div>
            </div>
        </div>
    </div>
    <!-- End Preloader Area -->

    <!-- Start Main Content Wrapper Area -->
    <div class="main-content-wrapper d-flex flex-column">

        <!-- Start Navbar Area -->
        @include('frontend.partials.navbar')
        <!-- End Navbar Area -->

        <!-- Start leftsidebar Area -->
        @include('frontend.partials.left-sidebar')
        <!-- End leftsidebar Area -->

        <!-- Start Content body -->
        @yield('content')
        <!-- End Content body -->

        <!-- Start Right Sidebar Area -->
        @include('frontend.partials.right-sidebar')
        <!-- End Right Sidebar Area -->

    </div>
    <!-- End Main Content Wrapper Area -->


    {{-- Footer start --}}
    @include('frontend.partials.footer')
    {{-- Footer end --}}

</body>

</html>
