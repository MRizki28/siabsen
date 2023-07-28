@extends('layouts.base')
@section('content')
    <div id="loading-overlay" class="loading-overlay" style="display: none;">
        <div id="loading" class="loading">
            <img src="{{ asset('loading.gif') }}" alt="Loading..." />
        </div>
    </div>
    <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold ">Data Divisi</h6>
            <button type="button" class="btn btn-outline-primary ml-auto" data-toggle="modal" data-target="#DivisiModal"
                id="#myBtn">
                Tambah Data
            </button>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="dataTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Divisi</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <!-- /.card-body -->

        {{-- modal create --}}
        <div class="modal fade" id="DivisiModal" tabindex="-1" role="dialog" aria-labelledby="DivisiModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="DivisiModalLabel">Tambah Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formTambah" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="uuid">
                            <div class="form-group">
                                <label for="nama_divisi">Nama Divisi</label>
                                <input type="text" class="form-control" name="nama_divisi" id="nama_divisi"
                                    placeholder="Input Here..">
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-outline-primary">Submit</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="EditModal" tabindex="-1" role="dialog" aria-labelledby="EditModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="EditModalLabel">Edit Author</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formEdit" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="uuid" id="uuid">
                        <div class="form-group">
                            <label for="nama_divisi">Nama Divisi</label>
                            <input type="text" class="form-control" name="nama_divisi" id="enama_divisi"
                                placeholder="Input Here..">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                    <button type="submit" form="formEdit" class="btn btn-outline-primary">Update Data</button>
                </div>

            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // handle cetak data
            var dataTable = $("#dataTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["csv", "excel"]
            }).buttons().container().appendTo('#dataTable_wrapper .col-md-6:eq(0)');
            $.ajax({
                url: "{{ url('v1/febba411-89e8-4fb3-9f55-85c56dcff41d/divisi') }}",
                method: "GET",
                dataType: "json",
                success: function(response) {
                    var tableBody = "";
                    $.each(response.data, function(index, item) {
                        tableBody += "<tr>";
                        tableBody += "<td>" + (index + 1) + "</td>";
                        tableBody += "<td>" + item.nama_divisi + "</td>";
                        tableBody += "<td>" +
                            "<button type='button' class='btn btn-primary edit-modal' data-toggle='modal' data-target='#EditModal' " +
                            "data-uuid='" + item.uuid + "'>" +
                            "<i class='fa fa-edit'></i></button>" +
                            "<button type='button' class='btn btn-danger delete-confirm' data-uuid='" +
                            item.uuid + "'><i class='fa fa-trash'></i></button>" +
                            "</td>";
                        tableBody += "</tr>";
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

        //tambah data
        $(document).ready(function() {
            var formTambah = $('#formTambah');
            formTambah.on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $('#loading-overlay').show();
                $.ajax({
                    type: 'POST',
                    url: '{{ url('v1/396d6585-16ae-4d04-9549-c499e52b75ea/divisi/create') }}',
                    data: formData,
                    dataType: 'JSON',
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#loading-overlay').hide();
                        if (data.message === 'check your validation') {
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

        //edit
        $(document).on('click', '.edit-modal', function() {
            var uuid = $(this).data('uuid');
            $.ajax({
                url: "{{ url('v1/9d97457b-1922-4f4a-b3fa-fcba980633a2/divisi/get') }}/" + uuid,
                type: 'GET',
                dataType: 'JSON',
                success: function(data) {
                    $('#uuid').val(data.data.uuid);
                    $('#enama_divisi').val(data.data.nama_divisi);
                    $('#EditModal').modal('show');
                },
                error: function() {
                    alert("error");
                }
            });
        });

        //update
        $(document).ready(function() {
            var formEdit = $('#formEdit');
            formEdit.on('submit', function(e) {
                e.preventDefault();
                var uuid = $('#uuid').val();
                var formData = new FormData(this);
                $('#loading-overlay').show();
                $.ajax({
                    type: "POST",
                    url: "{{ url('v1/4a3f479a-eb2e-498f-aa7b-e7d6e3f0c5f3/divisi/update/') }}/" +
                        uuid,
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#loading-overlay').hide();
                        if (data.message === 'check your validation') {
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
                            console.log(data);
                            $('#loading-overlay').hide();
                            Swal.fire({
                                title: 'Success',
                                text: 'Data Success Update',
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
                        var errors = data.responseJSON.errors;
                        var errorMessage = "";

                        $.each(errors, function(key, value) {
                            errorMessage += value + "<br>";
                        });

                        Swal.fire({
                            title: "Error",
                            html: errorMessage,
                            icon: "error",
                            timer: 5000,
                            showConfirmButton: true
                        });
                    }
                });
            });
        });

        //delete
        $(document).on('click', '.delete-confirm', function(e) {
            e.preventDefault();
            var uuid = $(this).data('uuid');
            Swal.fire({
                title: 'Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Delete',
                cancelButtonText: 'Cancel',
                resolveButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('v1/83df59b0-7c1a-4944-8fbb-2c06670dfa01/divisi/delete/') }}/" +
                            uuid,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "uuid": uuid
                        },
                        success: function(response) {
                            if (response.code === 200) {
                                Swal.fire({
                                    title: 'Data berhasil dihapus',
                                    icon: 'success',
                                    timer: 5000,
                                    showConfirmButton: true
                                }).then((result) => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal menghapus data',
                                    text: response.message,
                                    icon: 'error',
                                    timer: 5000,
                                    showConfirmButton: true
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Terjadi kesalahan',
                                text: 'Gagal menghapus data',
                                icon: 'error',
                                timer: 5000,
                                showConfirmButton: true
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
