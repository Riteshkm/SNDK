@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body table-responsive">
                        <button style="margin-bottom: 20px; float: right;" type="button" class="btn btn-primary"
                            id="saveBtn">
                            Add Sub Category
                        </button>

                        <table id="data-table" class="table table-striped table-bordered"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category Name</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Sub Category Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm" method="POST" action="/categories">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Select Category Name:</label>
                            <select name="category_id" class="form-control" id="category_id" required>
                                @foreach ($categories as $key=>$item)
                                    <option value="{{$key}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Sub Category Name:</label>
                            <input type="text" class="form-control" id="name" name="name">
                            <span class="error" id="nameError"></span>
                        </div>
                        <input type="hidden" id="sub_category_id" name="sub_category_id">
                        <button type="submit" class="btn btn-primary" id="saveCategoryBtn">Save Sub Category</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script type="text/javascript">
    function getData() {
        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            "bDestroy": true,
            "pageLength": 50,
            "oLanguage": {
                "sEmptyTable": "No Data Available"
            },
            "order": [
                [1, "desc"]
            ],
            "ajax": {
                'url': "{{ route('sub_categories.index') }}",
                'data': {},
            },
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'category_name',
                    name: 'category_name'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
                },
                {
                    data: 'action',
                    name: 'id',
                    searchable: false
                },
            ]
        });
    }

    function editSubcategory(subcategoryId) {
        $.get('/sub_categories/' + subcategoryId + '/edit', function(subcategory) {
            $('#categoryModal').modal('show');
            $('#name').val(subcategory.name);
            $('#category_id').val(subcategory.category_id);
            $('#sub_category_id').val(subcategoryId);
            $('#saveCategoryBtn').html('Update Sub Category');
            $('#categoryForm').attr('method', 'PUT');
            $('#categoryForm').attr('action', '/sub_categories/' + subcategoryId);
        });
    }

    function deleteSubcategor(subcategoryId) {
        var confirmDelete = confirm('Are you sure you want to delete this sub category?');
        if (confirmDelete) {
            $.ajax({
                type: 'DELETE',
                url: '/sub_categories/' + subcategoryId,
                success: function(response) {
                    $('#categoryModal').modal('hide');
                    getData();
                },
                error: function(xhr) {
                    alert(xhr.responseText);
                }
            });
        }
    }
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        getData();
        $('#saveBtn').click(function() {
            $('#categoryModal').modal('show');
            $('#categoryForm').attr('method', 'POST');
            $('#categoryForm').attr('action', '/sub_categories');
            $('#name').val('');
            $('#category_id').val('');
        });
        $('#categoryForm').validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 255,
                },
                category_id: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: 'Please enter a sub category name',
                    maxlength: 'Category name must not exceed 255 characters',
                },
                category_id: {
                    required: 'Please select a category name',
                },
            },
            submitHandler: function (form) {
                // This function will be called when the form is submitted and passes validation
                var actionUrl = $(form).attr('action');
                var methodType = $(form).attr('method');
                $.ajax({
                    type: methodType,
                    url: actionUrl,
                    data: $(form).serialize(),
                    success: function (response) {
                        getData();
                    },
                    error: function (xhr) {
                        var errors = xhr.responseJSON.errors;
                        if (errors.name) {
                            $('#nameError').text(errors.name[0]);
                        }
                    }
                });
            }
        });
    });
</script>
