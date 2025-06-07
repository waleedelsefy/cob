/**
 * JavaScript for the AJAX Property Importer.
 * Handles the frontend logic for the property importer page, including
 * file source selection (upload vs. server), AJAX communication, and updating the UI.
 */
jQuery(document).ready(function($) {

    // --- Element Selectors ---
    const form = $('#cob-importer-form');
    const fileInput = $('#property_csv');
    const serverFileInput = $('#server_csv_file');
    const languageSelector = $('#import_language');
    const sourceRadio = $('input[name="import_source"]');
    const startButton = form.find('button[type="submit"]');
    const resumeButton = $('#resume-import');
    const cancelButton = $('#cancel-import');
    const progressContainer = $('#importer-progress-container');
    const progressBar = $('#importer-progress-bar');
    const progressStats = $('#importer-stats');
    const logContainer = $('#importer-log');

    let isImporting = false;

    // --- UI Functions ---

    /**
     * Toggles visibility of file source inputs based on radio button selection.
     */
    sourceRadio.on('change', function() {
        if (this.value === 'upload') {
            $('#source-upload-container').show();
            $('#source-server-container').hide();
        } else {
            $('#source-upload-container').hide();
            $('#source-server-container').show();
        }
    }).trigger('change'); // Trigger on page load to set initial state

    /**
     * Appends a message to the log container.
     * @param {string} message The message to log.
     * @param {boolean} isError If true, styles the message as an error.
     */
    function addToLog(message, isError = false) {
        const color = isError ? 'color:red;' : 'inherit';
        logContainer.append('<div style="color:' + color + ';">' + message + '</div>');
        logContainer.scrollTop(logContainer[0].scrollHeight); // Auto-scroll to the bottom
    }

    /**
     * Updates the UI elements based on the status object from the server.
     * @param {object} status The status object.
     */
    function updateUI(status) {
        if (!status) return;

        const i18n = cobPropImporter.i18n;
        const progress = status.progress || 0;
        progressBar.css('width', progress + '%').text(progress + '%');

        const statsText = `${i18n.processed} ${status.processed} ${i18n.of} ${status.total_rows} | ` +
            `${i18n.imported}: ${status.imported_count} | ` +
            `${i18n.updated}: ${status.updated_count} | ` +
            `${i18n.failed}: ${status.failed_count}`;
        progressStats.text(statsText);
    }

    /**
     * Resets the UI to its initial state.
     */
    function resetUI() {
        isImporting = false;
        form[0].reset();
        progressContainer.hide();
        progressBar.css('width', '0%').text('0%');
        progressStats.text('');
        logContainer.html('');
        startButton.prop('disabled', false);
        cancelButton.hide();
        resumeButton.hide();
        $('#resume-notice').hide();
    }

    // --- AJAX Functions ---

    /**
     * Processes a single batch of the import via an AJAX call.
     * It calls itself recursively upon success until the import is complete.
     */
    function processBatch() {
        if (!isImporting) return;

        $.ajax({
            url: cobPropImporter.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_run_property_importer',
                nonce: cobPropImporter.nonce,
                importer_action: 'run'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.data.log && response.data.log.length > 0) {
                        response.data.log.forEach(msg => addToLog(msg));
                    }
                    updateUI(response.data.status);

                    if (response.data.done) {
                        isImporting = false;
                        addToLog('<strong>' + cobPropImporter.i18n.import_complete + '</strong>');
                        startButton.prop('disabled', false);
                        cancelButton.text('Clear Status');
                    } else {
                        setTimeout(processBatch, 100); // Small delay to prevent server overload
                    }
                } else {
                    isImporting = false;
                    addToLog(response.data.message || 'An unknown error occurred.', true);
                    startButton.prop('disabled', false);
                }
            },
            error: function(xhr) {
                isImporting = false;
                addToLog(cobPropImporter.i18n.connection_error + ': ' + xhr.statusText, true);
                startButton.prop('disabled', false);
            }
        });
    }

    /**
     * Prepares the import by sending the file to the backend.
     */
    function prepareImport() {
        const source = $('input[name="import_source"]:checked').val();

        isImporting = true;
        logContainer.html('');
        progressContainer.show();
        startButton.prop('disabled', true);
        cancelButton.show();
        resumeButton.hide();

        const formData = new FormData();
        formData.append('action', 'cob_run_property_importer');
        formData.append('nonce', cobPropImporter.nonce);
        formData.append('importer_action', 'prepare');
        formData.append('source_type', source);
        formData.append('import_language', languageSelector.val());

        if (source === 'upload') {
            formData.append('csv_file', fileInput[0].files[0]);
        } else {
            formData.append('file_name', serverFileInput.val());
        }

        $.ajax({
            url: cobPropImporter.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    addToLog(cobPropImporter.i18n.preparing_import);
                    if (response.data.log && response.data.log.length > 0) {
                        response.data.log.forEach(msg => addToLog(msg));
                    }
                    updateUI(response.data.status);
                    processBatch();
                } else {
                    isImporting = false;
                    addToLog(response.data.message || 'An unknown preparation error occurred.', true);
                    startButton.prop('disabled', false);
                }
            },
            error: function(xhr) {
                isImporting = false;
                addToLog(cobPropImporter.i18n.connection_error + ': ' + xhr.statusText, true);
                startButton.prop('disabled', false);
            }
        });
    }

    // --- Event Handlers ---
    form.on('submit', function(e) {
        e.preventDefault();
        if (isImporting) return;

        const source = $('input[name="import_source"]:checked').val();
        if (source === 'upload' && !fileInput[0].files.length) {
            alert(cobPropImporter.i18n.error_selecting_file);
            return;
        }
        if (source === 'server' && !serverFileInput.val()) {
            alert(cobPropImporter.i18n.error_selecting_file);
            return;
        }
        if (!confirm(cobPropImporter.i18n.confirm_new_import)) return;

        prepareImport();
    });

    resumeButton.on('click', function() {
        if (isImporting) return;
        if (!confirm(cobPropImporter.i18n.confirm_resume)) return;

        isImporting = true;
        progressContainer.show();
        startButton.prop('disabled', true);
        cancelButton.show();
        resumeButton.hide();
        addToLog('Attempting to resume previous import...');

        // Directly get status and then process batch
        $.ajax({
            url: cobPropImporter.ajax_url,
            type: 'POST',
            data: { action: 'cob_run_property_importer', nonce: cobPropImporter.nonce, importer_action: 'get_status' },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    updateUI(response.data.status);
                    addToLog('Resumed successfully. Continuing import...');
                    processBatch();
                } else {
                    isImporting = false;
                    addToLog(response.data.message || 'Could not find a resumable import.', true);
                    resetUI();
                }
            }
        });
    });

    cancelButton.on('click', function() {
        if (!confirm(cobPropImporter.i18n.confirm_cancel)) return;
        isImporting = false;
        addToLog('Cancelling import...');

        $.ajax({
            url: cobPropImporter.ajax_url,
            type: 'POST',
            data: { action: 'cob_run_property_importer', nonce: cobPropImporter.nonce, importer_action: 'cancel' },
            dataType: 'json',
            success: function(response) {
                addToLog(response.data.message || 'Import cancelled.');
                resetUI();
            },
            error: function(xhr) {
                addToLog(cobPropImporter.i18n.connection_error + ': ' + xhr.statusText, true);
                resetUI();
            }
        });
    });
});
