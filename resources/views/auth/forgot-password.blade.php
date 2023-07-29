@extends('layouts.auth')
@section('auth')
    <div id="loading-overlay" class="loading-overlay" style="display: none;">
        <div id="loading" class="loading">
            <img src="{{ asset('loading.gif') }}" alt="Loading..." />
        </div>
    </div>
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <P class="h1"><b>SIAMUD</b></P>
            </div>
            <div class="card-body">
                <div id="error-message" class="error-message"></div>
                <div id="success-message" class="success-message" style="display: none;"></div>

                <form method="post" id="forgot-password-form">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email"
                            required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <p class="mb-0 pl-2">
                            <a href="{{ url('/login') }}" class="text-center">Login</a>
                        </p>
                        <!-- /.col -->
                        <div class="col-4 ml-auto">
                            <button type="submit" class="btn btn-primary btn-block">Send</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <script>
        $(function() {
            $('#forgot-password-form').submit(function(event) {
                event.preventDefault();
                var email = $('#email').val();
                var token = $('meta[name="csrf-token"]').attr('content');
                $('#loading-overlay').show();
                $.ajax({
                    url: '{{ url('v3/396d6585-16ae-4d04-9549-c499e52b75ea/auth/forgot-password') }}',
                    type: 'POST',
                    data: {
                        email: email,
                        _token: token
                    },
                    success: function(response) {
                        $('#loading-overlay').hide();
                        console.log(response);
                        $('#error-message').hide();
                        $('#success-message').html(response.message).show();
                    },
                    error: function(xhr) {
                        $('#loading-overlay').hide();
                        $('#success-message').hide();

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessageText = "Validation errors occurred:<br>";

                            $.each(errors, function(key, value) {
                                errorMessageText += value[0] + "<br>";
                            });

                            $('#error-message').html(errorMessageText).show();
                        } else {
                            $('#loading-overlay').hide();
                            $('#error-message').html(xhr.responseJSON.message ||
                                'An error occurred. Please try again later.').show();
                        }
                    }
                });
            });
        });
    </script>
@endsection
