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
                <p class="login-box-msg">Register</p>
                <form method="post" id="registration-form">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="name" id="name" class="form-control" placeholder="Full name">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <select name="id_divisi" id="id_divisi" class="form-control">
                            <option value="">-- Pilih --</option>
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-filter"></span>
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
                    <div class="input-group mb-3">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                            placeholder="Password Confirmation">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-primary btn-block">Register</button>
                    </div>
                </form>
                <a href="{{ url('login') }}" class="text-center">Sudah punya akun</a>
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
    <!-- /.login-box -->

    <script>
        $(document).ready(function() {
            $('#registration-form').submit(function(event) {
                event.preventDefault();
                var name = $('#name').val();
                var email = $('#email').val();
                var password = $('#password').val();
                var password_confirmation = $('#password_confirmation').val();
                var id_divisi = $('#id_divisi').val();

                // Show the loading overlay
                $('#loading-overlay').show();

                $.ajax({
                    url: '{{ url('v3/396d6585-16ae-4d04-9549-c499e52b75ea/auth/register') }}',
                    type: 'POST',
                    data: {
                        name: name,
                        email: email,
                        password: password,
                        password_confirmation: password_confirmation,
                        id_divisi:id_divisi,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        $('#loading-overlay').hide();
                        console.log(data);
                        if (data.code === 422) {
                            var error = data.errors;
                            var errorMessage = "";

                            $.each(error, function(key, value) {
                                errorMessage += value[0] + "<br>";
                            });

                            Swal.fire({
                                title: 'Error',
                                html: errorMessage,
                                icon: 'error',
                                timer: 5000,
                                showConfirmButton: true
                            });

                        } else {
                            $('#loading-overlay').hide();
                            console.log(data);
                            Swal.fire({
                                title: 'Success',
                                text: 'Registrasi sukses silahkan check email anda',
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                window.location.href = '/login';
                            });
                        }
                    },
                    error: function(data) {
                        $('#loading-overlay').hide();
                        var error = data.responseJSON.errors;
                        var errorMessage = "";

                        $.each(error, function(key, value) {
                            errorMessage += value[0] + "<br>";
                        });

                        Swal.fire({
                            title: 'Error',
                            html: errorMessage,
                            icon: 'error',
                            timer: 5000,
                            showConfirmButton: true
                        });
                    }
                });
            });
        });

        //get data divisi
        $.ajax({
            url: "{{ url('v1/febba411-89e8-4fb3-9f55-85c56dcff41d/divisi') }}",
            method: "GET",
            dataType: "json",
            success: function(response) {
                var options = '';
                $.each(response.data, function(index, item) {
                    options += '<option value="' + item.id +
                        '">' + item.nama_divisi + '</option>';
                });
                $('#id_divisi').append(options);

            },
            error: function() {
                console.log("Failed to get data from server");
            }
        });
    </script>
@endsection
