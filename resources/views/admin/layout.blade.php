<!doctype html>
<html lang="en">
@include('admin.partials.header')

<body>

    <!--wrapper-->
    <div class="wrapper">

        <!--sidebar wrapper -->
        @include('admin.partials.sidebar')
        <!--end sidebar wrapper -->

        <!--start navbar -->
        @include('admin.partials.navbar')
        <!--end navbar -->

        <!--start page wrapper -->
        @yield('content')
        <!--end page wrapper -->

        <!--start overlay-->
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->
        <!--Start Back To Top Button-->
        <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        <footer class="page-footer">
            <p class="mb-0">Copyright © 2023. All right reserved.</p>
        </footer>
    </div>

    <!--end wrapper-->
    @include('admin.partials.footer')

</body>

</html>
