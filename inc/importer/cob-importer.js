// FILE: /inc/importer/cob-importer.js
// FINAL COMPLETE CODE (No changes from previous version v14.3 needed for these PHP adjustments, but included for completeness)

jQuery(document).ready(function($) {
    const form = $('#cob-importer-form');
    const progressBar = $('#importer-progress-bar');
    const progressContainer = $('#importer-progress-container');
    const stats = $('#importer-stats');
    const log = $('#importer-log');
    const submitButton = form.find('button[type="submit"]');
    const resumeButton = $('#resume-import');
    const cancelButton = $('#cancel-import');
    const languageSelect = $('#import_language');

    function toggleImporterLock(isLocked) {
        submitButton.prop('disabled', isLocked);
        resumeButton.prop('disabled', isLocked);
        if(isLocked && !cancelButton.data('processing')) {
        } else {
            cancelButton.prop('disabled', isLocked);
        }
        $('input[name="import_source"]').prop('disabled', isLocked);
        $('#property_csv').prop('disabled', isLocked);
        $('#server_csv_file').prop('disabled', isLocked);
        if(languageSelect.length) languageSelect.prop('disabled', isLocked);
    }

    $('input[name="import_source"]').on('change', function() {
        if (this.value === 'upload') {
            $('#source-upload-container').show();
            $('#source-server-container').hide();
        } else {
            $('#source-upload-container').hide();
            $('#source-server-container').show();
        }
    }).trigger('change');

    function updateProgress(status) {
        if (!status) return;
        const percent = status.progress || 0;
        progressBar.css('width', percent + '%').text(percent + '%');
        let langText = '';
        if (status.language && status.language !== 'default') { 
            langText = ' للغة: ' + status.language;
        }
        stats.text('تم معالجة ' + status.processed + ' من ' + status.total_rows + '. (تم تخطي ' + status.skipped + ')' + langText);
    }

    function addToLog(messages, isError = false) {
        let messageContent = "";
        if (Array.isArray(messages) && messages.length > 0) {
            messageContent = messages.join("\n") + "\n";
        } else if (typeof messages === 'string' && messages.length > 0) {
            messageContent = messages + "\n";
        }
        const sanitizedMessage = $('<div/>').text(messageContent).html().replace(/\n/g, '<br>');

        if(isError) {
            log.append('<span style="color: #ff6b6b;">' + sanitizedMessage + '</span>');
        } else {
            log.append(sanitizedMessage);
        }
        log.scrollTop(log[0].scrollHeight);
    }

    function runBatch() {
        cancelButton.data('processing', true);
        toggleImporterLock(true);

        $.ajax({
            url: cobImporter.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_run_importer',
                importer_action: 'run',
                nonce: cobImporter.nonce
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateProgress(response.data.status);
                    addToLog(response.data.log);

                    if (response.data.done) {
                        addToLog("\n🎉 اكتملت عملية الاستيراد بنجاح! 🎉");
                        toggleImporterLock(false);
                        cancelButton.data('processing', false);
                         $.post(cobImporter.ajax_url, { 
                            action: 'cob_run_importer',
                            importer_action: 'cancel', 
                            nonce: cobImporter.nonce
                        });
                        // setTimeout(function() { location.reload(); }, 3000); 
                    } else {
                        runBatch();
                    }
                } else {
                    addToLog(response.data.message || 'حدث خطأ غير معروف أثناء المعالجة.', true);
                    toggleImporterLock(false);
                    cancelButton.data('processing', false);
                }
            },
            error: function(xhr) {
                let errorMsg = "\n❌ فشل في الاتصال بالخادم (Error " + xhr.status + "). " + xhr.statusText;
                if(xhr.responseText){
                    try {
                        const errResponse = JSON.parse(xhr.responseText);
                        if(errResponse && errResponse.data && errResponse.data.message){
                            errorMsg += " | رسالة الخادم: " + errResponse.data.message;
                        } else if (errResponse && errResponse.data && Array.isArray(errResponse.data) && errResponse.data.length > 0){
                             errorMsg += " | رسالة الخادم: " + errResponse.data.join(' ');
                        } else if (errResponse && errResponse.data) {
                             errorMsg += " | رسالة الخادم: " + JSON.stringify(errResponse.data);
                        }
                    } catch(e){ }
                }
                addToLog(errorMsg + " يمكنك إعادة تحميل الصفحة والمتابعة.", true);
                toggleImporterLock(false);
                cancelButton.data('processing', false);
            }
        });
    }

    form.on('submit', function(e) {
        e.preventDefault();
        if (!confirm('هل أنت متأكد أنك تريد بدء عملية استيراد جديدة؟ سيتم حذف أي تقدم لعملية سابقة وملف مؤقت إن وجد.')) return;

        const source_type = $('input[name="import_source"]:checked').val();
        const formData = new FormData();
        formData.append('action', 'cob_run_importer');
        formData.append('importer_action', 'prepare');
        formData.append('nonce', cobImporter.nonce);
        formData.append('source_type', source_type);
        
        if (languageSelect.length && languageSelect.val()) {
            formData.append('import_language', languageSelect.val());
        } else {
             formData.append('import_language', 'default');
        }


        if (source_type === 'upload') {
            const fileInput = $('#property_csv')[0];
            if (fileInput.files.length === 0) {
                alert('يرجى اختيار ملف CSV لرفعه.'); return;
            }
            formData.append('csv_file', fileInput.files[0]);
        } else {
            const fileName = $('#server_csv_file').val();
            if (!fileName) {
                alert('يرجى اختيار ملف CSV من القائمة.'); return;
            }
            formData.append('file_name', fileName);
        }

        toggleImporterLock(true);
        progressContainer.show();
        progressBar.css('width', '0%').text('0%');
        stats.text('');
        log.html('');
        addToLog('يتم تحضير العملية...');

        $.ajax({
            url: cobImporter.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    addToLog(response.data.log || response.data.message);
                    updateProgress(response.data.status);
                    runBatch();
                } else {
                    addToLog(response.data.message || 'حدث خطأ غير معروف أثناء التحضير.', true);
                    toggleImporterLock(false);
                }
            },
            error: function(xhr) {
                 let errorMsg = "❌ خطأ في الاتصال (Error " + xhr.status + "): " .concat(xhr.statusText);
                 if(xhr.responseText){
                    try {
                        const errResponse = JSON.parse(xhr.responseText);
                        if(errResponse && errResponse.data && errResponse.data.message){
                            errorMsg += " | رسالة الخادم: " + errResponse.data.message;
                        } else if (errResponse && errResponse.data && Array.isArray(errResponse.data) && errResponse.data.length > 0){
                             errorMsg += " | رسالة الخادم: " + errResponse.data.join(' ');
                        } else if (errResponse && errResponse.data) {
                             errorMsg += " | رسالة الخادم: " + JSON.stringify(errResponse.data);
                        }
                    } catch(e){ }
                }
                 addToLog(errorMsg, true);
                 toggleImporterLock(false);
            }
        });
    });

    resumeButton.on('click', function() {
        if (!confirm('سيتم متابعة عملية الاستيراد من النقطة التي توقفت عندها. هل أنت متأكد؟')) return;
        toggleImporterLock(true);
        progressContainer.show();
        log.html('');
        addToLog('جاري متابعة عملية الاستيراد السابقة...');
        runBatch();
    });

    cancelButton.on('click', function() {
         if (!confirm('هل أنت متأكد أنك تريد إلغاء العملية ومسح التقدم والملف المؤقت؟')) return;
        $.ajax({
            url: cobImporter.ajax_url,
            type: 'POST',
            data: {
                action: 'cob_run_importer',
                importer_action: 'cancel',
                nonce: cobImporter.nonce
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.data.message || 'تم إلغاء العملية بنجاح.');
                    location.reload();
                } else {
                    alert(response.data.message || 'حدث خطأ أثناء الإلغاء.');
                }
            },
            error: function(xhr){
                alert("خطأ في الاتصال أثناء محاولة الإلغاء.");
            }
        });
    });
});