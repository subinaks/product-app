<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-top: 0;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        button[type="submit"] {
            background-color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Product</h1>
        <form id="productForm" enctype="multipart/form-data">
            @csrf
            <div>
                <label for="title">Title</label>
                <input type="text" id="title" name="title">
            </div>
            <div>
                <label for="description">Description</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <div>
                <label for="main_image">Main Image</label>
                <input type="file" id="main_image" name="main_image">
            </div>
            <div id="variantsContainer">
        
            </div>
            <button type="button" id="addVariant">Add Variant</button>
            <button type="submit">Save Product</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            var variantIndex = 0;

            $('#addVariant').click(function() {
                var variantHtml = `
                    <div>
                        <input type="text" name="variants[${variantIndex}][size]" placeholder="Size">
                        <input type="text" name="variants[${variantIndex}][color]" placeholder="Color">
                        <button type="button" class="removeVariant">Remove</button>
                    </div>
                `;
                $('#variantsContainer').append(variantHtml);
                variantIndex++;
            });

            $('#variantsContainer').on('click', '.removeVariant', function() {
                $(this).closest('div').remove();
            });

            $('#productForm').submit(function(event) {
                event.preventDefault();
                var formData = new FormData(this);
                var url = '/products';
                var method = 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#productForm')[0].reset();
                        $('#variantsContainer').empty();
                        Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.success,
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/products'; 
                        }
                    });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = "Error:<br>";
                        $.each(errors, function(key, value) {
                            errorMessage += value + "<br>";
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: errorMessage,
                        });
                    }
                });
            });
        });
    </script>

</body>
</html>