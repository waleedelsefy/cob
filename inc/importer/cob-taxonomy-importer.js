/**
 * JavaScript for the AJAX Taxonomy Importer.
 *
 * This script handles the frontend logic for the taxonomy importer page,
 * including file selection, AJAX communication with the PHP backend,
 * and updating the UI with progress and log messages.
 *
 * It communicates with the PHP functions via the 'cobTaxonomyImporterAjax' object
 * localized by wp_localize_script.
 */
jQuery(document).ready(function($) {

    // --- Element Selectors ---
    const form = $('#cob-taxonomy-importer-form');
    const fileInput = $('#csv_file');
    const taxonomySelector = $('#taxonomy_selector');
    const languageSelector = $('#target_language_selector');
    const startButton = $('#cob-taxonomy-importer-start-new');
    const resumeButton = $('#cob-taxonomy-importer-resume');
    const cancelButton = $('#cob-taxonomy-importer-cancel');
    const progressContainer = $('#cob-taxonomy-importer-progress-container');
    const progressBar = $('#cob-taxonomy-importer-progress-bar');
    const progressStats = $('#cob-taxonomy-importer-stats');
    const logContainer = $('#cob-taxonomy-importer-log');

    // --- State Variables ---
    let isImporting = false;

    // --- UI Update Functions ---

    /**
     * Appends a message to the log container.
     * @param {string} message - The message to log.
     * @param {boolean} isError - If true, the message will be styled as an error.
     */
    function addToLog(message, isError = false) {
        const color = isError ? 'color:red;' : 'color:inherit;';
        logContainer.append('<div style="' + color + '">' + message + '</div>');
        logContainer.scrollTop(logContainer[0].scrollHeight); // Auto-scroll to bottom
    }

    /**
     * Updates the entire UI based on the current import status.
     * @param {object} status - The status object received from the server.
     */
    function updateUI(status) {
        if (!status) {
            resetUI();
            return;
        }

        const progress = status.progress || 0;
        progressBar.css('width', progress + '%').text(progress + '%');

        const statsText = `Processed: ${status.processed_rows} / ${status.total_rows} | ` +
            `Imported: ${status.imported_count} | ` +
            `Updated: ${status.updated_count} | ` +
            `Failed: ${status.failed_count}`;
        progressStats.text(statsText);
    }

    /**
     * Resets the UI to its initial state before an import starts or after it's cancelled.
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
    }

    // --- AJAX Functions ---

    /**
     * Processes a single batch of the import.
     * This function calls itself upon success until the import is complete.
     */
    function processBatch() {
        if (!isImporting) return;

        $.ajax({
            url: cobTaxonomyImporterAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_taxonomy_importer_ajax_handler',
                nonce: cobTaxonomyImporterAjax.nonce,
                importer_action: 'run'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Log messages from the backend
                    if (response.data.log && response.data.log.length > 0) {
                        response.data.log.forEach(msg => addToLog(msg));
                    }
                    // Update UI with new status
                    updateUI(response.data.status);

                    // Check if the import is finished
                    if (response.data.done) {
                        isImporting = false;
                        addToLog('<strong>' + cobTaxonomyImporterAjax.i18n.import_complete + '</strong>');
                        startButton.prop('disabled', false);
                        cancelButton.text('Clear Status').show();
                    } else {
                        // If not done, process the next batch
                        processBatch();
                    }
                } else {
                    isImporting = false;
                    addToLog(response.data.message || 'An unknown error occurred during processing.', true);
                    startButton.prop('disabled', false);
                }
            },
            error: function(xhr) {
                isImporting = false;
                addToLog(cobTaxonomyImporterAjax.i18n.connection_error + ': ' + xhr.statusText, true);
                startButton.prop('disabled', false);
            }
        });
    }

    // --- Event Handlers ---

    /**
     * Handles the "Start New Import" button click.
     */
    form.on('submit', function(e) {
        e.preventDefault();

        if (isImporting) return;

        if (!fileInput[0].files.length) {
            alert(cobTaxonomyImporterAjax.i18n.error_selecting_file);
            return;
        }

        if (!confirm(cobTaxonomyImporterAjax.i18n.confirm_new_import)) {
            return;
        }

        isImporting = true;
        logContainer.html('');
        progressContainer.show();
        startButton.prop('disabled', true);
        cancelButton.show();
        resumeButton.hide();
        addToLog('Preparing import...');

        const formData = new FormData();
        formData.append('action', 'cob_taxonomy_importer_ajax_handler');
        formData.append('nonce', cobTaxonomyImporterAjax.nonce);
        formData.append('importer_action', 'prepare');
        formData.append('csv_file', fileInput[0].files[0]);
        formData.append('taxonomy_slug', taxonomySelector.val());
        formData.append('import_language', languageSelector.val());

        $.ajax({
            url: cobTaxonomyImporterAjax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.data.log && response.data.log.length > 0) {
                        response.data.log.forEach(msg => addToLog(msg));
                    }
                    updateUI(response.data.status);
                    addToLog('Preparation complete. Starting import...');
                    processBatch(); // Start the first batch
                } else {
                    isImporting = false;
                    addToLog(response.data.message || 'An unknown error occurred during preparation.', true);
                    if (response.data.log && response.data.log.length > 0) {
                        response.data.log.forEach(msg => addToLog(msg, true));
                    }
                    startButton.prop('disabled', false);
                }
            },
            error: function(xhr) {
                isImporting = false;
                addToLog(cobTaxonomyImporterAjax.i18n.connection_error + ': ' + xhr.statusText, true);
                startButton.prop('disabled', false);
            }
        });
    });

    /**
     * Handles the "Resume Import" button click.
     */
    resumeButton.on('click', function() {
        if (isImporting) return;
        if (!confirm(cobTaxonomyImporterAjax.i18n.confirm_resume)) return;

        isImporting = true;
        logContainer.html('');
        progressContainer.show();
        startButton.prop('disabled', true);
        cancelButton.show();
        resumeButton.hide();
        addToLog('Attempting to resume previous import...');

        $.ajax({
            url: cobTaxonomyImporterAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_taxonomy_importer_ajax_handler',
                nonce: cobTaxonomyImporterAjax.nonce,
                importer_action: 'get_status'
            },
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
            },
            error: function(xhr) {
                isImporting = false;
                addToLog(cobTaxonomyImporterAjax.i18n.connection_error + ': ' + xhr.statusText, true);
                resetUI();
            }
        });
    });

    /**
     * Handles the "Cancel and Reset" button click.
     */
    cancelButton.on('click', function() {
        if (!confirm(cobTaxonomyImporterAjax.i18n.confirm_cancel)) return;

        isImporting = false; // Stop any ongoing loops

        addToLog('Cancelling import...');

        $.ajax({
            url: cobTaxonomyImporterAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_taxonomy_importer_ajax_handler',
                nonce: cobTaxonomyImporterAjax.nonce,
                importer_action: 'cancel'
            },
            dataType: 'json',
            success: function(response) {
                if(response.success){
                    addToLog(response.data.message || 'Import cancelled.');
                } else {
                    addToLog(response.data.message || 'Error during cancellation.', true);
                }
                resetUI();
                $('#cob-taxonomy-importer-resume-notice').hide();
            },
            error: function(xhr) {
                addToLog(cobTaxonomyImporterAjax.i18n.connection_error + ': ' + xhr.statusText, true);
                resetUI();
            }
        });
    });
});
