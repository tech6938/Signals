<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.includes.head')
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f5f5f5;
        }

        .error {
            color: red;
        }

        /* Centering the form */
        .login-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container-center {
            width: 100%;
            max-width: 358px;
        }

        .panel {
            margin: 0 auto;
        }

        #p {
            text-align: center;
        }

        #p a:hover {
            color: blue;
        }
    </style>
    <script>
        $(document).ready(function() {
            @if(session('error'))
            toastr.options = {
                closeButton: true,
                progressBar: true,
                showMethod: 'slideDown',
                timeOut: 1000
            };
            toastr.error("{{ session('error') }}");
            @endif
        });
    </script>
</head>

<body>
    <div class="login-wrapper">
        <div class="container-center">
            <div class="panel panel-bd">
                <div class="panel-heading">
                    <div class="view-header">
                        <div class="header-icon">
                            <i class="pe-7s-unlock"></i>
                        </div>
                        <div class="header-title">
                            <h3>Login</h3>
                            <small><strong>Please enter your credentials to login.</strong></small>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <script>
                        $(function() {
                            toastr.options = {
                                closeButton: true,
                                progressBar: true,
                                positionClass: 'toast-top-right',
                                timeOut: 3000
                            };

                            // Session error
                            @if(session('error'))
                            toastr.error("{{ session('error') }}");
                            @endif

                            // Validation errors
                            @if($errors->any())
                            @foreach($errors->all() as $error)
                            toastr.error("{{ $error }}");
                            @endforeach
                            @endif
                        });
                    </script>

                    <form action="{{route('login.submit')}}" id="loginForm" method="post" novalidate>
                        @csrf
                        <div class="form-group">
                            <label class="control-label" for="email">Email</label>
                            <input type="text" placeholder="Enter your email" title="Please enter your email" required value="{{ old('email') }}" name="email" id="email" class="form-control">
                            <!-- <div class="error">@error('email') {{ $message }} @enderror</div> -->
                        </div>
                        <div class="form-group" style="position: relative;">
                            <label class="control-label" for="password">Password</label>
                            <input type="password" title="Please enter your password" placeholder="******" required value="{{ old('password') }}" name="password" id="password" class="form-control">
                            <span id="togglePassword" style="position: absolute; right: 10px; top: 32px; cursor: pointer;">
                                <i class="fa fa-eye-slash"></i>
                            </span>
                            <!-- <div class="error">@error('password') {{ $message }} @enderror</div> -->
                        </div>
                        {{-- {!! get_recaptcha() !!} --}}
                        <br>
                        <div class="form-group d-flex align-items-center">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label me-4" for="rememberMe">Remember Me</label>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                        </div>
                    </form>
                    <hr>
                    <div class="form-group">
                        <p class="mb-1" id="p">
                            <a href="#">I forgot my password</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        document.getElementById('loginForm').addEventListener('submit', function() {
            if (document.getElementById('rememberMe').checked) {
                localStorage.setItem('email', document.getElementById('email').value);
                localStorage.setItem('password', document.getElementById('password').value);
            } else {
                localStorage.removeItem('email');
                localStorage.removeItem('password');
            }
        });

        window.onload = function() {
            if (localStorage.getItem('email') && localStorage.getItem('password')) {
                document.getElementById('email').value = localStorage.getItem('email');
                document.getElementById('password').value = localStorage.getItem('password');
                document.getElementById('rememberMe').checked = true;
            }
        };
    </script>
</body>

</html>