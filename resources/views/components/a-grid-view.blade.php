<!-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" /> -->
<table id="{{ $id }}" class="table">
    <thead>
        <tr>
            @foreach ($columns as $column)
            <th>
                @if (method_exists($model, 'attributeLabels'))
                {{ $model->attributeLabels($column) }}
                @else
                @if (is_array($column))
                @if (isset($column['label']))
                {{ $column['label'] }}
                @else
                {{ ucwords(str_replace('_', ' ', $column['attribute'])) }}
                @endif
                @else
                {{ ucwords(str_replace('_', ' ', $column)) }}
                @endif
                @endif
            </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>


<script>
    (function($) {
        'use strict';
        // Ensure $id is valid before using it in a jQuery selector
        var tableId = '{{ $id }}';
        var searching = '{{ $searching }}';
        var paging = '{{ $paging }}';
        var info = '{{ $info }}';
        if (tableId) { // Added check to ensure tableId is not empty
            var table = $('#' + tableId).DataTable({
                order: [],
                lengthMenu: [
                    [10, 25, 50, 100, 500],
                    [10, 25, 50, 100, 500]
                ],
                processing: true,
                serverSide: true,
                autoWidth: false,
                searching: searching, // Disable search
                info: info, // Disable search
                paging: paging,
                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                language: {
                    processing: '<div class="spinner-border" style="width: 50px; height: 50px;" role="status"><span class="visually-hidden">Loading...</span></div>'
                },
                ajax: {
                    url: "{{ $url }}",
                    type: "GET",
                    data: function(d) {
                        @foreach ($customfilterIds as $customfilterId)
                            var filterId = '{{ $customfilterId }}';
                            if (filterId && $('#' + filterId)
                                .length) { // Check if filterId element exists
                                d[filterId] = $('#' + filterId).val();
                            }
                        @endforeach
                    },
                    error: function(xhr, error, thrown) {
                        console.log('Error details:', xhr.responseText);
                        alert(
                            'An error occurred while loading the data. Please check the console for more details.'
                        );
                    }
                },
                columns: [
                    @foreach ($columns as $column)
                        {
                            @if (is_array($column))
                                data: '{{ $column['attribute'] ?? '' }}',
                                name: '{{ $column['attribute'] ?? '' }}'
                            @else
                                data: '{{ $column }}',
                                name: '{{ $column }}'
                            @endif
                        },
                    @endforeach
                ],
                buttons: [
                    @foreach ($buttons as $button)
                        @if (is_array($button))
                            {
                                @foreach ($button as $key => $value)
                                    '{{ $key }}': '{{ $value }}',
                                @endforeach
                            },
                        @else
                            '{{ $button }}',
                        @endif
                    @endforeach
                ]
            });





            // Ensure $filterButtonId is valid before using it in a jQuery selector
            var filterButtonId = '{{ $filterButtonId }}';
            if (filterButtonId) { // Added check to ensure filterButtonId is not empty
                $('#' + filterButtonId).on('click', function() {
                    table.ajax.reload();
                });
            }
        }


        function tableReload() {
            table.ajax.reload();

        }
        var cart_table_reload = 'cart_table_reload';
        if (cart_table_reload) { // Added check to ensure cart_table_reload is not empty
            $('#' + cart_table_reload).on('click', function() {
                tableReload()
            });
        }

        $(document).on('click', '#placeOrder', function(e) {

            // this.disabled = true;
            e.preventDefault();

            if (tableId == 'cart_checkout') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/order/add",
                    type: 'POST',
                    success: function(res) {
                        if (res.status == 200) {
                            $('#placeOrder').prop('disabled', false);
                            handleResponse(res);
                            let table = $('#order_product_table').DataTable(); // Get the DataTable instance
                            table.ajax.reload();
                            let cart_list = $('#cart_list').DataTable(); // Get the DataTable instance
                            cart_list.ajax.reload();
                            tableReload()


                        }
                        if (res.status == 422) {
                            $('#placeOrder').prop('disabled', false);
                            handleResponse(res);
                            tableReload()

                        }

                    }
                });
            }


        });

        if (tableId == 'cart_list' || tableId == 'cart_checkout') {

            let keyupTimeout; // Declare a timeout variable

            $(document).on('keyup', '#form1', function(e) {
                // Clear any existing timeout to debounce
                clearTimeout(keyupTimeout);

                // Set a new timeout for 1 second (1000 ms)
                keyupTimeout = setTimeout(() => {
                    // Prevent default action (if needed)
                    e.preventDefault();
                    if (tableId === 'cart_list') {

                        // `this` refers to the button that was clicked
                        const product = JSON.parse(this.getAttribute("data-product"));
                        const cartid = JSON.parse(this.getAttribute("data-cartid"));
                        const product_id = product?.product?.id || 0;
                    const grindPrice = $('#grindPrice').val(); // Get the product ID from the data attribute

                        updateQuantity(product_id, this.value, cartid,grindPrice);
                    }
                    tableReload();

                    // Reload the table
                }, 500); // Delay of 1 second

            });
            $(document).on('keyup', '#grindPrice', function(e) {
                // Clear any existing timeout to debounce
                clearTimeout(keyupTimeout);

                // Set a new timeout for 1 second (1000 ms)
                keyupTimeout = setTimeout(() => {
                    // Prevent default action (if needed)
                    e.preventDefault();
                    if (tableId === 'cart_list') {

                    const grindPrice = $('#grindPrice').val(); // Get the product ID from the data attribute
                    $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "/cart/update-grind-price",
                type: 'POST',
                data: {
                    grindPrice:grindPrice ?? 2,
                },
                success: function(res) {
                    if (res.status == 200) {
                        handleResponse(res);
                    }
                    if (res.status == 422) {
                        handleResponse(res);
                    }
                }
            });

                    }
                    tableReload();

                    // Reload the table
                }, 500); // Delay of 1 second

            });


            $(document).on('click', '.changeQuantity', function(e) {


                if (tableId == 'cart_list') {

                    // Prevent default action (if needed)
                    e.preventDefault();

                    // `this` refers to the button that was clicked
                    var product = JSON.parse(this.getAttribute("data-product"));
                    var cartid = JSON.parse(this.getAttribute("data-cartid"));
                    var product_id = product?.product?.id || 0;
                    var type_id = this.getAttribute("data-type");
                    const grindPrice = $('#grindPrice').val(); // Get the product ID from the data attribute

                    // Call your function with the appropriate arguments
                    setQuantity(product_id, type_id, cartid,grindPrice);
                }
                tableReload()

            });

            $(document).on('click', '.deleteCartItem', function(e) {


                if (tableId == 'cart_list') {
                    e.preventDefault();
                    var cartid = JSON.parse(this.getAttribute("data-cartid"));
                    const grindPrice = $('#grindPrice').val(); // Get the product ID from the data attribute

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "/cart/delete-cart-item",
                        type: 'POST',
                        data: {
                            cartid: cartid,
                            grindPrice:grindPrice
                        },
                        success: function(res) {
                            if (res.status == 200) {
                                handleResponse(res);
                            }
                            if (res.status == 422) {
                                handleResponse(res);
                            }
                        }
                    });
                }
                tableReload()

            });









        }
        if (tableId == 'cart_list' || tableId == 'cart_checkout') {



            $(document).on('click', '#submit-button', function(e) {
                e.preventDefault(); // Prevent the default form submission behavior
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                const form = $(this).closest('form'); // Get the closest form element
                const url = "{{url('cart/custom-product')}}"; // Get the form's action URL
                const formData = new FormData(form[0]); // Collect form data
                const grindPrice = $('#grindPrice').val(); // Get the product ID from the data attribute
                formData.append('grindPrice', grindPrice ?? 2);
                // Disable button and show a loader
                const submitButton = $(this);
                submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');
                if (tableId == 'cart_list') {

                    // Send AJAX request
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        processData: false, // Important for FormData
                        contentType: false, // Important for FormData
                        success: function(response) {

                            handleResponse(response); // Handle success response
                        },
                        error: function(xhr) {
                            const error = xhr.responseJSON?.message || 'An error occurred!';
                            handleError(error); // Handle error response

                        },
                        complete: function() {
                            // Enable button and reset text
                            submitButton.prop('disabled', false).html('Submit');
                        }
                    });
                }
                tableReload()
            });
        }

        $(document).on('click', '.select-product', function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            e.preventDefault();
            const productId = $(this).data('product_id'); // Get the product ID from the data attribute
            const grindPrice = $('#grindPrice').val(); // Get the product ID from the data attribute
            const isChecked = $(this).is(':checked')
            this.disabled = true;
            let type_id = ''; // Declared as const
            if (isChecked) {
                type_id = '1'; // Trying to reassign a const variable
            } else {
                type_id = '0'; // Trying to reassign a const variable
            }
            if (tableId == 'order_product_table') {


                $.ajax({
                    url: '/cart/add', // Replace with your API endpoint
                    method: 'POST',
                    data: {
                        product_id: productId,
                        type_id: type_id,
                        grindPrice:grindPrice ?? 2
                    },
                    success: function(response) {

                        handleResponse(response);
                    },
                    error: function(xhr) {
                        handleResponse(response);

                    }
                });
            }


            tableReload()

        });


        async function updateQuantity(product_id, quantity, cartid,grindPrice) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "/cart/update-quantity",
                type: 'POST',
                data: {
                    product_id: product_id,
                    quantity: quantity,
                    cartid: cartid,
                    grindPrice:grindPrice,
                },
                success: function(res) {
                    if (res.status == 200) {
                        handleResponse(res);
                    }
                    if (res.status == 422) {
                        handleResponse(res);
                    }
                }
            });


        }

        async function setQuantity(product_id, type_id, cartid = 0,grindPrice) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "/cart/change-quantity",
                type: 'POST',
                data: {
                    product_id: product_id,
                    type_id: type_id,
                    cartid: cartid,
                    grindPrice: grindPrice,
                },
                success: function(res) {
                    if (res.status == 200) {
                        handleResponse(res);
                    }
                    if (res.status == 422) {
                        handleResponse(res);
                    }
                }
            });


        }




        function handleResponse(response) {
            var toastG = document.getElementById('toastG');
            var toastBody = toastG.querySelector('.toast-body');
            if (response.status === 200) {
                toastG.classList.remove('bg-danger'); // Remove error class if previously set
                toastG.classList.add('bg-success'); // Set success class
                // Update toast message
                toastBody.innerText = response.message;
                // Show toast using Bootstrap's method
                var bsToast = new bootstrap.Toast(toastG);
                bsToast.show();
            } else {
                handleError(response.message);
            }
        }


        function handleError(error) {
            var toastG = document.getElementById('toastG');
            var toastBody = toastG.querySelector('.toast-body');
            toastG.classList.remove('bg-success'); // Remove success class if previously set
            toastG.classList.add('bg-danger'); // Set error class
            // Update toast message
            toastBody.innerText = error;
            // Show toast using Bootstrap's method
            var bsToast = new bootstrap.Toast(toastG);
            bsToast.show();
        }

    })(jQuery);
</script>