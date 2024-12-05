<!doctype html>
<html lang="zxx">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Links of CSS files -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/magnific-popup.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/metismenu.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/dark.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/responsive.css') }}">
    <title>SEO Expate Bangladesh Ltd.</title>
    <link rel="icon" type="image/png" href="{{ asset('frontend/assets/images/favicon.png') }}">
</head>

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

    <!-- Start Preloader Area -->
    <div class="profile-authentication-area">
        <div class="container my-5">
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="profile-authentication-image">
                        <div class="content-image">
                            <div class="logo">
                                <a href="index.html">
                                    <img src="{{ asset('frontend/assets/images/logo.png') }}" alt="Zust">
                                </a>
                            </div>
                            <div class="vector-image">
                                <img src="{{ asset('frontend/assets/images/vector.png') }}" alt="image">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="login-form">
                        <h2>Login</h2>

                        <form method="POST" action="">
                            @csrf

                            <!-- Username or Email -->
                            <div class="form-group">
                                <label for="email">Username or email</label>
                                <input id="email" type="email" name="email" :value="old('email')" required
                                    autofocus autocomplete="username" class="form-control">
                                @if ($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <!-- Password -->
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input id="password" type="password" name="password" required
                                    autocomplete="current-password" class="form-control">
                                @if ($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                            <!-- Remember Me -->
                            <div class="remember-me-wrap d-flex justify-content-between align-items-center">
                                <p>
                                    <input id="remember_me" type="checkbox" name="remember" class="rounded">
                                    <label for="remember_me">Remember me</label>
                                </p>

                                <div class="lost-your-password-wrap">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="lost-your-password">Forgot
                                            password?</a>
                                    @endif
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="default-btn">Login</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Preloader Area -->

    <!-- Links of JS files -->
    <script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/metismenu.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/main.js') }}"></script>
</body>

</html>
