@extends('layouts.base')
@section('content')
    <div id="loading-overlay" class="loading-overlay" style="display: none;">
        <div id="loading" class="loading">
            <img src="{{ asset('loading.gif') }}" alt="Loading..." />
        </div>
    </div>
    <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold ">Data Absen</h6>
            <button type="button" class="btn btn-outline-primary ml-auto" data-toggle="modal" data-target="#DivisiModal"
                id="#myBtn">
                Absen
            </button>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="dataTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Divisi</th>
                        <th>tanggal absen</th>
                        <th>Waktu absen</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <div class="modal fade" id="DivisiModal" tabindex="-1" role="dialog" aria-labelledby="DivisiModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="DivisiModalLabel">Absen</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>keterangan waktu absen</p>
                        <li>kurang dari 8.00 wib : Hadir </li>
                        <li>lebih dari 8.00 wib : terlambat </li>
                        <li>lebih dari 12.00 wib : tidak hadir </li>
                    </div>
                    <div class="modal-footer">
                        <form id="formTambah">
                            @csrf
                            <input type="hidden" name="uuid" id="uuid">
                            <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-outline-primary">Absen sekarang</button>
                        </form>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var dataTable = $("#dataTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                // "buttons": ["csv", "excel"]
            }).buttons().container().appendTo('#dataTable_wrapper .col-md-6:eq(0)');
            $.ajax({
                url: "{{ url('v2/febba411-89e8-4fb3-9f55-85c56dcff41d/absen/user') }}",
                method: "GET",
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    var tableBody = "";
                    $.each(response.data, function(index, item) {
                        tableBody += "<tr>";
                        tableBody += "<td>" + (index + 1) + "</td>";
                        tableBody += "<td>" + item.users.name + "</td>";
                        tableBody += "<td>" + item.users.divisi.nama_divisi + "</td>";
                        tableBody += "<td>" + item.tanggal + "</td>";
                        tableBody += "<td>" + item.waktu + "</td>";
                        tableBody += "<td>" + item.status + "</td>";
                    });
                    var table = $("#dataTable").DataTable();
                    table.clear().draw();
                    table.rows.add($(tableBody)).draw();
                },
                error: function() {
                    console.log("Failed to get data from server");
                }
            });
        });

        //absen
        $(document).ready(function() {
            var formTambah = $('#formTambah');
            formTambah.on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $('#loading-overlay').show();
                $.ajax({
                    type: 'POST',
                    url: '{{ url('v2/396d6585-16ae-4d04-9549-c499e52b75ea/absen/create') }}',
                    data: formData,
                    dataType: 'JSON',
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        console.log(data);
                        $('#loading-overlay').hide();
                        if (data.code === 400) {
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
                        } else if (data.code === 422) {
                            var errorMessage = data
                                .message;
                            Swal.fire({
                                title: 'Error',
                                html: errorMessage,
                                icon: 'error',
                                timer: 5000,
                                showConfirmButton: true
                            });
                        } else {
                            console.log(data);
                            $('#loading-overlay').hide();
                            Swal.fire({
                                title: 'Success',
                                text: 'Data Success Create',
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
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
    </script>
@endsection
