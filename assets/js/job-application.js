jQuery(document).ready(function($) {
    $('#jobApplicationForm').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'submit_job_application');

        $.ajax({
            url: myAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    $('#jobApplicationForm').fadeOut();
                    $('#jobPopup').html('<p class="bg-job-success">' + response.data.message + '</p>');
                } else {
                    $('#jobPopup').prepend('<p class="bg-job-success">' + response.data + '</p>');
                }
            },
            error: function(xhr, status, error) {
                $('#jobPopup').prepend('<p class="error">حدث خطأ أثناء الإرسال، يرجى المحاولة لاحقاً.</p>');
                console.log('AJAX Error:', error);
            }
        });
    });

    $('#toggleJobPopup').on('click', function() {
        $('.job-popup, .job-overlay').fadeIn();
    });
    $('#closeJobPopup, .job-overlay').on('click', function() {
        $('.job-popup, .job-overlay').fadeOut();
    });
});
