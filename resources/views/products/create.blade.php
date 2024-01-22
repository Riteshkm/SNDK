@extends('layouts.app')
@section('content')
    <div class="container">
        <h2>Create Product</h2>
        <form  id="productForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="mb-3 col-6">
                    <label for="name" class="form-label">Select Category Name:</label>
                    <select name="category_id" class="form-control" id="category_id" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $key => $item)
                            <option {{(isset($edit) && $key == $product->category_id) ?'selected=selected':''}} value="{{ $key }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 col-6">
                    <label for="name" class="form-label">Sub Category Name:</label>
                    <select name="sub_category_id" class="form-control" id="sub_category_id" required>
                        <option value="">Select Sub Category</option>
                        @if(isset($edit) && $edit)
                        @foreach ($sub_categories as $key => $item)
                            <option {{(isset($edit) &&$key == $product->sub_category_id) ?'selected=selected':''}} value="{{ $key }}">{{ $item }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Product Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="{{isset($edit) ?   $product->name :''}}" required>
            </div>

            <div class="mb-3">
                <label for="regular_price" class="form-label">Regular Price:</label>
                <input type="number" class="form-control" id="regular_price" name="regular_price" value="{{isset($edit) ?   $product->price :''}}" required>
            </div>

            <div class="row sizeDiV">
                <div class="mb-3 col-6">
                    <label for="name" class="form-label">Size:</label>
                    <input type="text" class="form-control" id="size" name="size[]" required>
                </div>
                <div class="mb-3 col-5">
                    <label for="name" class="form-label">Price:</label>
                    <input type="number" class="form-control" id="price" name="price[]" required>
                </div>
                <div class="mb-3 col-1">
                    <input type="hidden" value="0" name="product_size_id">
                    <button type="button" class="btn btn-primary addBtn form-control" style="margin-top: 30px;">Add
                    </button>
                </div>
            </div>
            @if(isset($edit) && $edit)
                @foreach ($productSize as $key => $item)
                <div class="row sizeDiV">
                    <div class="mb-3 col-6">
                        <label for="name" class="form-label">Size:</label>
                        <input type="text" class="form-control" value="{{$item->size}}" id="size" name="size[]" required>
                    </div>
                    <div class="mb-3 col-5">
                        <label for="name" class="form-label">Price:</label>
                        <input type="number" class="form-control" value="{{$item->price}}" id="price" name="price[]" required>
                    </div>
                    <div class="mb-3 col-1">
                        <input type="hidden" value="{{$item->id}}" name="product_size_id">
                        <button type="button" class="btn deleteBtn form-control btn-danger" style="margin-top: 30px;">Delete</button>
                    </div>
                </div>
                @endforeach
            @endif
            <div class="mb-3">
                <label for="images" class="form-label">Product Images:</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple required>
            </div>
            <div class="row">
            @if(isset($edit) && $edit)
                @foreach ($productImage as $key => $item)
                    <div class="col-md-3">
                        <img src="{{asset($item->image)}}" style="width: 100%;height: auto;">
                        <button type="button" class="btn deleteImage form-control btn-danger" style="margin-top: 10px;">Delete</button>
                    </div>
                @endforeach
            @endif
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 20px;">{{(isset($edit) && $edit) ? 'Update' : 'Create'}} Product</button>
        </form>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script>
    $(document).ready(function() {
        $(".deleteBtn").on("click", function() {
            $(this).closest(".sizeDiV").remove();  
        });
        $(".addBtn").on("click", function() {
            var clonedDiv = $(".sizeDiV:first").clone();
            clonedDiv.find("input").val("");
            clonedDiv.find(".addBtn").text("Delete").removeClass("btn-primary").addClass("btn-danger")
                .on("click", function() {
                    $(this).closest(".sizeDiV").remove();
                });
            $(".sizeDiV:last").after(clonedDiv);
        });
        $("#category_id").on("change", function() {
            var categoryId = $(this).val();
            if (categoryId !== "") {
                $.ajax({
                    url: "{{ route('fetch-subcategories') }}",
                    type: "POST",
                    data: {
                        category_id: categoryId,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    success: function(data) {
                        var subCategorySelect = $("#sub_category_id");
                        subCategorySelect.empty();
                        subCategorySelect.append(
                            '<option value="">Select Sub Category</option>');
                        $.each(data, function(key, value) {
                            subCategorySelect.append('<option value="' + key +
                                '">' + value + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                $("#sub_category_id").empty().append('<option value="">Select Sub Category</option>');
            }
        });

        $('#productForm').validate({
            rules: {
                forEach: function(input) {
                    $(input).rules('add', {
                        required: true,
                        messages: {
                            required: 'This field is required',
                        },
                    });
                },
            },
            submitHandler: function(form) {
                var formData = new FormData(form);

                $.ajax({
                    url: '/products',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        alert(response.message)
                        window.location.href = "{{ route('products.index') }}";
                    },
                    error: function(xhr) {
                        alert(xhr)
                    }
                });
            }
        });
    });
</script>
