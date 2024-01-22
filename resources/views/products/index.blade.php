@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body table-responsive">
                        <a href="{{ route('products.create') }}" style="margin-bottom: 20px; float: right;" type="button" class="btn btn-primary"
                            id="saveBtn">
                            Add Products
                        </a>

                        <table id="data-table" class="table table-striped table-bordered"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
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
                'url': "{{ route('products.index') }}",
                'data': {},
            },
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'price',
                    name: 'price'
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

    function deleteProduct(productsId) {
        var confirmDelete = confirm('Are you sure you want to delete this products?');
        if (confirmDelete) {
            $.ajax({
                type: 'DELETE',
                url: '/products/' + productsId,
                success: function(response) {
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
    });
</script>
