@extends('layouts.auth')
@section('auth')
    <div id="loading-overlay" class="loading-overlay" style="display: none;">
        <div id="loading" class="loading">
            <img src="{{ asset('loading.gif') }}" alt="Loading..." />
        </div>
    </div>
    <div class="register-box">
        <div class="card card-outline card-primary">
            
            <div class="card-header text-center">
                <a href="../../index2.html" class="h1"><b>SIAMUD</b></a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Masukkan password baru</p>
                <div id="error-message" class="error-message" style="display: none;"></div>
                <div id="success-message" class="success-message" style="display: none;"></div>
                <form method="post" id="forgot-password-form">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                            placeholder="Konfirmasi Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 ml-auto">
                            <button class="btn btn-primary btn-block">Submit</button>
                        </div> 
                        
                    </div>
                  
                </form>
              
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
    <!-- /.login-box -->

    <script>
        $(function() {
            var resetPasswordToken = getResetPasswordTokenFromUrl();
            $('#forgot-password-form').data('password_reset_token', resetPasswordToken);
            $('#forgot-password-form').submit(function(event) {
                event.preventDefault();
                var password = $('#password').val();
                var token = $('meta[name="csrf-token"]').attr('content');
                var password_confirmation = $('#password_confirmation').val();
                var password_reset_token = $(this).data('password_reset_token');
                $('#loading-overlay').show();
                $.ajax({
                    url: "{{ url('v3/396d6585-16ae-4d04-9549-c499e52b75ea/auth/reset-password') }}/" + password_reset_token,
                    type: 'POST',
                    data: {
                        password: password,
                        password_confirmation: password_confirmation,
                        _token: token
                    },
                    success: function(response) {
                        $('#loading-overlay').hide();
                        console.log(response);
                        $('#error-message').hide();
                        $('#success-message').html(response.message).show(); 
                        window.location.href = "/login";
                    },
                    error: function(response) {
                        $('#loading-overlay').hide();
                        $('#success-message').hide();
                        if (response.status === 422) {
                            var errors = response.responseJSON.errors;
                            var errorMessageText = "Validation errors occurred:<br>";
                            $.each(errors, function(key, value) {
                                errorMessageText += value[0] + "<br>";
                            });
                            $('#error-message').html(errorMessageText).show(); 
                        } else {
                            $('#error-message').html(response.responseJSON.message ||
                                'Terjadi kesalahan. Silakan coba lagi nanti.').show();
                        }
                    }
                });
            });

            function getResetPasswordTokenFromUrl() {
                var url = window.location.href;
                var token = url.split('/').pop();
                return token;
            }
        });
    </script>
@endsection
