var UploadHandler = (function () {
    var badges,
        info,
        fetched,
        bSummary,
        bConfirm,
        initial = true;

    return {
        fetchInfo : function() {
            $.ajax({
                url     : '/import/getImporting',
                success : function (data) {
                    fetched = data;
                }
            });
        },
        showInfo      : function () {
            if (!info) {
                info = $('.b-summary--result');
            }
            info.html(fetched);
            info.show();
            info.fadeOut(5000);
        },
        processUpload : function (e, data) {
            if ($.isArray(data.result)) {
                this.changeBadges(data.result[0].imported.new, data.result[0].imported.old, true);
            }
        },
        changeBadges  : function (countNew, countOld, forceSummary) {
            if (!badges) {
                badges = $('.b-summary__item--counter span');
                bSummary = $('.b-summary--badges');
                bConfirm = $('.b-confirm');
            }
            if (!initial) {
                $('#importing-grid').yiiGridView('update');
            }
            initial = false;
            $(badges[0]).text(countNew);
            $(badges[1]).text(countOld);
            bConfirm[countNew ? 'show' : 'hide']();
            bConfirm.find('span').text(countNew);
            bSummary[countNew || forceSummary ? 'fadeIn' : 'fadeOut'](500);
        }
    }
})();