/**
 * AJAX Importer for Developer Taxonomy in WordPress.
 *
 * Place this file in your theme, e.g., /js/cob-developer-importer.js
 * Ensure the path in cob_dev_importer_enqueue_assets() in PHP is correct.
 */
jQuery(document).ready(function($) {
    // Use more specific selectors if you have multiple importers on the same page,
    // though typically one importer tool is on its own admin page.
    const form = $('#cob-dev-importer-form'); // Specific ID for developer importer form
    const progressBar = $('#cob-dev-importer-progress-bar');
    const progressContainer = $('#cob-dev-importer-progress-container');
    const statsElement = $('#cob-dev-importer-stats');
    const logElement = $('#cob-dev-importer-log');

    const startNewButton = $('#cob-dev-importer-start-new');
    const resumeButton = $('#cob-dev-importer-resume');
    const cancelButton = $('#cob-dev-importer-cancel');

    const fileInput = $('#developer_csv_file'); // Specific ID for developer file input
    const languageSelect = $('#dev_target_language_selector'); // Specific ID for developer language select

    const ajaxConfig = cobDevImporterAjax; // Use the localized object specific to this importer
    const i18n = ajaxConfig.i18n || {};

    function toggleImporterLock(isLocked) {
        startNewButton.prop('disabled', isLocked);
        resumeButton.prop('disabled', isLocked);
        cancelButton.prop('disabled', isLocked);
        fileInput.prop('disabled', isLocked);
        if (languageSelect.length) languageSelect.prop('disabled', isLocked);
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
            '(' + i18n.skipped + ': ' + (status.failed_count || 0) + ')' +
            langText
        );
    }

    function addToLog(messages, type = 'info') {
        let messageContent = "";
        if (Array.isArray(messages)) {
            messageContent = messages.map(msg => typeof msg === 'string' ? msg : JSON.stringify(msg)).join("\n");
        } else if (typeof messages === 'string') {
            messageContent = messages;
        } else {
            messageContent = JSON.stringify(messages);
        }
        const sanitizedMessage = $('<div/>').text(messageContent).html().replace(/\n/g, '<br>');

        let color = '#f1f1f1';
        if (type === 'error') color = '#ff6b6b';
        else if (type === 'success') color = '#86E7B8';
        else if (type === 'warning') color = '#FFD700';

        logElement.append('<div style="color:' + color + '; margin-bottom: 3px;">' + sanitizedMessage + '</div>');
        logElement.scrollTop(logElement[0].scrollHeight);
    }

    function runBatch() {
        toggleImporterLock(true);

        $.ajax({
            url: ajaxConfig.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_dev_importer_ajax_handler', // Unique AJAX action
                importer_action: 'run',
                nonce: ajaxConfig.nonce
            },
            dataType: 'json',
            timeout: 360000, // 6 minutes timeout for each batch (client-side)
            success: function(response) {
                if (response.success && response.data.status) {
                    updateProgress(response.data.status);
                    if(response.data.log && response.data.log.length > 0) {
                        addToLog(response.data.log);
                    }

                    if (response.data.done) {
                        addToLog(i18n.import_complete, 'success');
                        toggleImporterLock(false);
                        cancelButton.show();
                        resumeButton.hide();
                    } else {
                        runBatch();
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

    form.on('submit', function(e) {
        e.preventDefault();
        if (!confirm(i18n.confirm_new_import)) return;

        const formData = new FormData();
        formData.append('action', 'cob_dev_importer_ajax_handler'); // Unique AJAX action
        formData.append('importer_action', 'prepare');
        formData.append('nonce', ajaxConfig.nonce);

        if (languageSelect.length && languageSelect.val()) {
            formData.append('import_language', languageSelect.val());
        } else {
            formData.append('import_language', 'en');
        }

        if (fileInput[0].files.length === 0) {
            alert(i18n.error_selecting_file); return;
        }
        formData.append('csv_file', fileInput[0].files[0]);

        toggleImporterLock(true);
        progressContainer.show();
        progressBar.css('width', '0%').text('0%');
        statsElement.text('');
        logElement.html('');
        addToLog(i18n.preparing_import);
        resumeButton.hide();
        cancelButton.show();

        $.ajax({
            url: ajaxConfig.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            timeout: 360000, // 6 minutes for preparation step
            success: function(response) {
                if (response.success && response.data.status) {
                    if(response.data.log && response.data.log.length > 0) {
                        addToLog(response.data.log);
                    }
                    updateProgress(response.data.status);
                    if (response.data.status.total_rows > 0) {
                        runBatch();
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
                let errorMsg = i18n.connection_error + " (Error " + xhr.status + "): " .concat(xhr.statusText);
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

        $.ajax({
            url: ajaxConfig.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_dev_importer_ajax_handler', // Unique AJAX action
                importer_action: 'get_status',
                nonce: ajaxConfig.nonce
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.status) {
                    updateProgress(response.data.status);
                    if(response.data.log && response.data.log.length > 0) {
                        addToLog(response.data.log);
                    }
                    runBatch();
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
        toggleImporterLock(true);

        $.ajax({
            url: ajaxConfig.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_dev_importer_ajax_handler', // Unique AJAX action
                importer_action: 'cancel',
                nonce: ajaxConfig.nonce
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    addToLog(response.data.message || i18n.import_cancelled_successfully, 'success');
                    progressBar.css('width', '0%').text('0%');
                    statsElement.text('تم إلغاء العملية.');
                    progressContainer.hide();
                    resumeButton.hide();
                } else {
                    addToLog(response.data.message || i18n.error_cancelling, 'error');
                }
            },
            error: function(){
                addToLog(i18n.error_connecting_cancel, 'error');
            },
            complete: function() {
                toggleImporterLock(false);
            }
        });
    });

    function checkResumable() {
        $.ajax({
            url: ajaxConfig.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_dev_importer_ajax_handler', // Unique AJAX action
                importer_action: 'get_status',
                nonce: ajaxConfig.nonce
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.status && response.data.status.progress < 100 && response.data.status.total_rows > 0) {
                    $('#cob-dev-importer-resume-notice').show();
                    resumeButton.show();
                    cancelButton.show();
                    updateProgress(response.data.status);
                } else {
                    $('#cob-dev-importer-resume-notice').hide();
                    resumeButton.hide();
                }
            },
            error: function() {
                $('#cob-dev-importer-resume-notice').hide();
                resumeButton.hide();
            }
        });
    }
    checkResumable();

});
