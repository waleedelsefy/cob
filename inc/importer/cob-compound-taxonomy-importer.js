/**
 * AJAX Importer for Compound Taxonomies in WordPress.
 *
 * Place this file in your theme, e.g., /js/cob-compound-taxonomy-importer.js
 * Ensure the path in cob_cti_enqueue_assets() in PHP is correct.
 */
jQuery(document).ready(function($) {
    const form = $('#cob-cti-importer-form');
    const progressBar = $('#cob-cti-importer-progress-bar');
    const progressContainer = $('#cob-cti-progress-container');
    const statsElement = $('#cob-cti-importer-stats');
    const logElement = $('#cob-cti-importer-log');

    const startNewButton = $('#cob-cti-start-new');
    const resumeButton = $('#cob-cti-resume');
    const cancelButton = $('#cob-cti-cancel');

    const fileInput = $('#compound_csv_file');
    const languageSelect = $('#target_language_selector');

    // Localized strings from PHP
    const i18n = cobCTIAjax.i18n || {};

    function toggleImporterLock(isLocked) {
        startNewButton.prop('disabled', isLocked);
        resumeButton.prop('disabled', isLocked);
        cancelButton.prop('disabled', isLocked); // Always lock cancel during processing
        fileInput.prop('disabled', isLocked);
        languageSelect.prop('disabled', isLocked);
    }

    function updateProgress(status) {
        if (!status) return;
        const percent = parseInt(status.progress, 10) || 0;
        progressBar.css('width', percent + '%').text(percent + '%');

        let langText = '';
        if (status.language && status.language !== 'default') {
            langText = ' ' + i18n.for_language + ' ' + status.language;
        }
        statsElement.text(
            i18n.processed_of + ' ' + (status.processed_rows || 0) + ' ' +
            i18n.from + ' ' + (status.total_rows || 0) + '. ' +
            '(' + i18n.skipped + ': ' + (status.failed_count || 0) + ')' + // Using failed_count as skipped for simplicity here
            langText
        );
    }

    function addToLog(messages, type = 'info') { // type can be 'info', 'error', 'success', 'warning'
        let messageContent = "";
        if (Array.isArray(messages)) {
            messageContent = messages.map(msg => typeof msg === 'string' ? msg : JSON.stringify(msg)).join("\n");
        } else if (typeof messages === 'string') {
            messageContent = messages;
        } else {
            messageContent = JSON.stringify(messages);
        }

        // Sanitize HTML, then replace newlines with <br>
        const sanitizedMessage = $('<div/>').text(messageContent).html().replace(/\n/g, '<br>');

        let color = '#f1f1f1'; // Default for info
        if (type === 'error') color = '#ff6b6b'; // Red for errors
        else if (type === 'success') color = '#86E7B8'; // Green for success
        else if (type === 'warning') color = '#FFD700'; // Yellow for warnings

        logElement.append('<div style="color:' + color + '; margin-bottom: 3px;">' + sanitizedMessage + '</div>');
        logElement.scrollTop(logElement[0].scrollHeight);
    }

    function runBatch() {
        toggleImporterLock(true); // Lock UI

        $.ajax({
            url: cobCTIAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_cti_ajax_handler',
                importer_action: 'run',
                nonce: cobCTIAjax.nonce
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.status) {
                    updateProgress(response.data.status);
                    if(response.data.log && response.data.log.length > 0) {
                        addToLog(response.data.log);
                    }

                    if (response.data.done) {
                        addToLog(i18n.import_complete, 'success');
                        toggleImporterLock(false);
                        cancelButton.show(); // Show cancel to allow cleanup if desired
                        resumeButton.hide();
                        // Optionally, trigger cancel after a delay to clean up server status
                        // setTimeout(function() { $('#cob-cti-cancel').trigger('click'); }, 5000);
                    } else {
                        runBatch(); // Continue with the next batch
                    }
                } else {
                    addToLog(response.data.message || i18n.error_unknown_processing, 'error');
                    toggleImporterLock(false);
                }
            },
            error: function(xhr) {
                let errorMsg = i18n.connection_error + " (Error " + xhr.status + "): " + xhr.statusText;
                if(xhr.responseText){
                    try {
                        const errResponse = JSON.parse(xhr.responseText);
                        if(errResponse && errResponse.data && errResponse.data.message){
                            errorMsg += " | Server: " + errResponse.data.message;
                        }
                    } catch(e){ /* ignore parsing error */ }
                }
                addToLog(errorMsg, 'error');
                addToLog("يمكنك محاولة استئناف العملية أو إلغائها.", 'warning');
                toggleImporterLock(false);
            }
        });
    }

    form.on('submit', function(e) { // Handles "Start New Import"
        e.preventDefault();
        if (!confirm(i18n.confirm_new_import)) return;

        const formData = new FormData();
        formData.append('action', 'cob_cti_ajax_handler');
        formData.append('importer_action', 'prepare');
        formData.append('nonce', cobCTIAjax.nonce);

        if (languageSelect.length && languageSelect.val()) {
            formData.append('import_language', languageSelect.val());
        } else {
            formData.append('import_language', 'en'); // Default if not found
        }

        if (fileInput[0].files.length === 0) {
            alert(i18n.error_selecting_file); return;
        }
        formData.append('csv_file', fileInput[0].files[0]);

        toggleImporterLock(true);
        progressContainer.show();
        progressBar.css('width', '0%').text('0%');
        statsElement.text('');
        logElement.html(''); // Clear previous logs
        addToLog(i18n.preparing_import);
        resumeButton.hide();
        cancelButton.show();


        $.ajax({
            url: cobCTIAjax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.status) {
                    if(response.data.log && response.data.log.length > 0) {
                        addToLog(response.data.log);
                    }
                    updateProgress(response.data.status);
                    if (response.data.status.total_rows > 0) {
                        runBatch(); // Start processing batches
                    } else {
                        addToLog("لا توجد صفوف لمعالجتها في الملف.", 'warning');
                        toggleImporterLock(false);
                    }
                } else {
                    addToLog(response.data.message || i18n.error_unknown_prepare, 'error');
                    toggleImporterLock(false);
                }
            },
            error: function(xhr) {
                let errorMsg = i18n.connection_error + " (Error " + xhr.status + "): " + xhr.statusText;
                if(xhr.responseText){
                    try {
                        const errResponse = JSON.parse(xhr.responseText);
                        if(errResponse && errResponse.data && errResponse.data.message){
                            errorMsg += " | Server: " + errResponse.data.message;
                        }
                    } catch(e){ /* ignore parsing error */ }
                }
                addToLog(errorMsg, 'error');
                toggleImporterLock(false);
            }
        });
    });

    resumeButton.on('click', function() {
        if (!confirm(i18n.confirm_resume)) return;

        toggleImporterLock(true);
        progressContainer.show();
        logElement.append("<div>----------------------------------</div>");
        addToLog(i18n.resuming_import);

        // Fetch current status to update UI before starting batch
        $.ajax({
            url: cobCTIAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_cti_ajax_handler',
                importer_action: 'get_status', // New action to fetch status
                nonce: cobCTIAjax.nonce
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.status) {
                    updateProgress(response.data.status);
                    if(response.data.log && response.data.log.length > 0) {
                        addToLog(response.data.log);
                    }
                    runBatch(); // Start processing batches from where it left off
                } else {
                    addToLog(response.data.message || "فشل في استرجاع حالة الاستيراد للاستئناف.", 'error');
                    toggleImporterLock(false);
                }
            },
            error: function() {
                addToLog("خطأ في الاتصال عند محاولة استرجاع حالة الاستيراد.", 'error');
                toggleImporterLock(false);
            }
        });
    });

    cancelButton.on('click', function() {
        if (!confirm(i18n.confirm_cancel)) return;
        toggleImporterLock(true); // Lock while cancelling

        $.ajax({
            url: cobCTIAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_cti_ajax_handler',
                importer_action: 'cancel',
                nonce: cobCTIAjax.nonce
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    addToLog(response.data.message || i18n.import_cancelled_successfully, 'success');
                    progressBar.css('width', '0%').text('0%');
                    statsElement.text('تم إلغاء العملية.');
                    progressContainer.hide();
                    resumeButton.hide();
                    // No location.reload() to allow user to see the cancellation message.
                } else {
                    addToLog(response.data.message || i18n.error_cancelling, 'error');
                }
            },
            error: function(){
                addToLog(i18n.error_connecting_cancel, 'error');
            },
            complete: function() {
                toggleImporterLock(false); // Unlock after attempt
            }
        });
    });

    // Check for resumable import on page load
    function checkResumable() {
        $.ajax({
            url: cobCTIAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_cti_ajax_handler',
                importer_action: 'get_status',
                nonce: cobCTIAjax.nonce
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.status && response.data.status.progress < 100 && response.data.status.total_rows > 0) {
                    $('#cob-cti-resume-notice').show();
                    resumeButton.show();
                    cancelButton.show();
                    updateProgress(response.data.status); // Update UI with saved progress
                    // progressContainer.show(); // Optionally show progress immediately
                } else {
                    $('#cob-cti-resume-notice').hide();
                    resumeButton.hide();
                    // cancelButton.hide(); // Hide if no active/resumable import
                }
            },
            error: function() {
                // console.error("Could not check for resumable import status.");
                $('#cob-cti-resume-notice').hide();
                resumeButton.hide();
            }
        });
    }
    checkResumable();

});
