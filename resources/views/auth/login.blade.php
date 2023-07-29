@extends('layouts.auth')
@section('auth')
    <div id="loading-overlay" class="loading-overlay" style="display: none;">
        <div id="loading" class="loading">
            <img src="{{ asset('loading.gif') }}" alt="Loading..." />
        </div>
    </div>
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <P class="h1"><b>SIAMUD</b></P>
            </div>
            <div class="card-body">
                <div id="error-message" class="error-message"></div>
                <div id="success-message" class="success-message" style="display: none;"></div>

                <form method="post" id="login-form">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                <!-- /.social-auth-links -->

                <p class="mb-1">
                    <a href="{{ url('/forgot') }}">Lupa password</a>
                </p>
                <p class="mb-0">
                    <a href="{{ url('/register') }}" class="text-center">Registrasi</a>
                </p>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <script>
        $(document).ready(function() {
            var formTambah = $('#login-form');
            var errorMessage = $('#error-message');
            var successMessage = $('#success-message');

            formTambah.on('submit', function(e) {
                e.preventDefault();
                errorMessage.empty();
                successMessage.hide();

                var formData = new FormData(this);
                $('#loading-overlay').show();
                $.ajax({
                    type: 'POST',
                    url: '{{ url('v3/396d6585-16ae-4d04-9549-c499e52b75ea/auth/login') }}',
                    data: formData,
                    dataType: 'JSON',
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        console.log(data);
                        $('#loading-overlay').hide();
                        if (data.message === 'Invalid email or password' | data.message ===
                            'Email not verified') {
                            var error = data.errors;
                            var errorMessageText = "Email or password not valid";

                            $.each(error, function(key, value) {
                                errorMessageText += value[0] + "<br>";
                            });

                            showErrorAlert(errorMessageText);
                        } else {
                            console.log(data);
                            localStorage.setItem('access_token', data.access_token);
                            showSuccessAlert('Success login', '/');
                        }
                    },
                    error: function(data) {
                        $('#loading-overlay').hide();
                        var error = data.responseJSON.errors;
                        var errorMessageText = "";

                        $.each(error, function(key, value) {
                            errorMessageText += value[0] + "<br>";
                        });

                        showErrorAlert(errorMessageText);
                    }
                });
            });

            function showErrorAlert(message) {
                $('#loading-overlay').hide();
                errorMessage.html(message);
            }

            function showSuccessAlert(message, redirectUrl) {
                $('#loading-overlay').hide();
                successMessage.html(message).show();
                setTimeout(function() {
                    successMessage.hide();
                }, 3000);

                window.location.href = redirectUrl;
            }
        });
    </script>
@endsection
