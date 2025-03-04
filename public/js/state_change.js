
$(document).ready(function () {
    // Set up CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Event listener for state change
    $('.state-change').change(function () {
        handleStateChange(this);
    });
    $(".open-pending-payment-modal").click(function (e) {
        e.preventDefault();
        $("#customerModal").modal("show");
    });
    $('#add-pending-payment').click(function (e) {
        e.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const form = $(this).closest('form'); // Get the closest form element
        const url = '/installment/store'; // Get the form's action URL
        const formData = new FormData(form[0]); // Collect form data
        const grindPrice = $('#grindPrice').val(); // Get the product ID from the data attribute
        formData.append('grindPrice', grindPrice ?? 2);
        // Disable button and show a loader
        const submitButton = $(this);
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');

        // Send AJAX request
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false, // Important for FormData
            contentType: false, // Important for FormData
            success: function (response) {
                handleResponse(response); // Handle success response
            },
            error: function (xhr) {
                const error = xhr.responseJSON?.message || 'An error occurred!';
                handleError(error); // Handle error response

            },
            complete: function () {
                // Enable button and reset text
                submitButton.prop('disabled', false).html('Add Payment');
            }
        });

    });


    async function handleStateChange(element) {
        const unique_id = element.value;
        const model_id = $(element).data('id');
        const model_type = $(element).data('modeltype');

        try {
            const res = await updateState(unique_id, model_id, model_type);
            handleResponse(res);
        } catch (error) {
            handleError(error);
        }
    }

    async function updateState(unique_id, model_id, model_type) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: $('#app').data('state-change-url'),
                type: 'POST',
                data: {
                    model_type: model_type,
                    model_id: model_id,
                    attribute: 'state_id', // Replace with your actual attribute
                    workflow: unique_id, // Assuming unique_id is the workflow state
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    resolve(response);
                },
                error: function (xhr, status, error) {
                    reject(xhr.responseText);
                }
            });
        });
    }

    function handleResponse(response) {
        var toastG = document.getElementById('toastG');
        var toastBody = toastG.querySelector('.toast-body');

        if (response.status === 200) {
            toastG.classList.remove('bg-danger'); // Remove error class if previously set
            toastG.classList.add('bg-success');   // Set success class

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

        toastG.classList.remove('bg-success');  // Remove success class if previously set
        toastG.classList.add('bg-danger');       // Set error class

        // Update toast message
        toastBody.innerText = error;

        // Show toast using Bootstrap's method
        var bsToast = new bootstrap.Toast(toastG);
        bsToast.show();
    }
});
