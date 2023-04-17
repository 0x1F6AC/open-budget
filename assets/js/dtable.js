var $dtables = {};
var getLang = function(lng, key) {
    var langdata = {
        'datatable_decimal': '',
        'datatable_emptyTable': "Jadvalda maʼlumotlar yoʻq",
        'datatable_info': "_TOTAL_ dona maʼlumotlardan _START_-dan _END_-gachasi koʻrsatilmoqda",
        'datatable_infoEmpty': "0 dona maʼlumotlardan 0-dan 0-gachasi koʻrsatilmoqda",
        'datatable_infoFiltered': "(_MAX_ donadan saralab olindi)",
        'datatable_infoPostFix': "",
        'datatable_thousands': ",",
        'datatable_lengthMenu': "_MENU_ yozuvni koʻrsatish",
        'datatable_loadingRecords': "Maʼlumotlar yuklanmoqda...",
        'datatable_processing': "Qayta ishlanmoqda...",
        'datatable_search': "",
        'datatable_searchPlaceholder': "Qidirish...",
        'datatable_zeroRecords': "Mos keladigan yozuvlar topilmadi",
        'datatable_first': "Birinchi",
        'datatable_last': "Oxirgi",
        'datatable_next': "<i class=\"fa fa-angle-double-right\" aria-hidden=\"true\"></i>",
        'datatable_previous': "<i class=\"fa fa-angle-double-left\" aria-hidden=\"true\"></i>",
        'datatable_sortAscending': ": ustunini toʻgʻri tartiblash",
        'datatable_sortDescending': ": ustunini teskari tartiblash",
        'datatable_buttons_copyTitle': "Buferga nusxalash",
        'datatable_buttons_copySuccess_d': "Buferga %d qator koʻchirildi",
        'datatable_buttons_copySuccess_1': "Bir satr buferga koʻchirildi",
        'datatable_buttons_pageLength_d': "%d qatordan yozuv",
        'datatable_buttons_pageLength_all': "Barcha yozuvlar",
    };
    return langdata[key];
}
var generateDatableSettings = function(elem) {
    console.log(elem);
    var tableset = {};
    tableset['language'] = {
        "decimal":        getLang('uz', 'datatable_decimal'),
        "emptyTable":     getLang('uz', 'datatable_emptyTable'),
        "info":           getLang('uz', 'datatable_info'),
        "infoEmpty":      getLang('uz', 'datatable_infoEmpty'),
        "infoFiltered":   getLang('uz', 'datatable_infoFiltered'),
        "infoPostFix":    getLang('uz', 'datatable_infoPostFix'),
        "thousands":      getLang('uz', 'datatable_thousands'),
        "lengthMenu":     getLang('uz', 'datatable_lengthMenu'),
        "loadingRecords": getLang('uz', 'datatable_loadingRecords'),
        "processing":     getLang('uz', 'datatable_processing'),
        "search":         getLang('uz', 'datatable_search'),
        "searchPlaceholder": getLang('uz', 'datatable_searchPlaceholder'),
        "zeroRecords":    getLang('uz', 'datatable_zeroRecords'),
        "paginate": {
            "first":      getLang('uz', 'datatable_first'),
            "last":       getLang('uz', 'datatable_last'),
            "next":       getLang('uz', 'datatable_next'),
            "previous":   getLang('uz', 'datatable_previous')
        },
        "aria": {
            "sortAscending":  getLang('uz', 'datatable_sortAscending'),
            "sortDescending": getLang('uz', 'datatable_sortDescending')
        },
        "buttons": {
            "copyTitle": getLang('uz', 'datatable_buttons_copyTitle'),
            "copySuccess": {
                _: getLang('uz', 'datatable_buttons_copySuccess_d'),
                1: getLang('uz', 'datatable_buttons_copySuccess_1')
            },
            "pageLength": {
                _: getLang('uz', 'datatable_buttons_pageLength_d'),
                '-1': getLang('uz', 'datatable_buttons_pageLength_all'),
            }
        }
    }

    var datatableButtons = $('[datatable='+elem+']').attr('datatable-buttons');
    var datatableButtonsDom = $('[datatable='+elem+']').attr('datatable-buttons-dom');
    var datatableProcessing = $('[datatable='+elem+']').attr('datatable-processing');
    var datatableServerSide = $('[datatable='+elem+']').attr('datatable-serverside');
    var datatableserverMethod = $('[datatable='+elem+']').attr('datatable-servermethod');
    var datatableAjax = $('[datatable='+elem+']').attr('datatable-ajax');
    var datatableColumns = $('[datatable='+elem+']').attr('datatable-columns');
    var datatableColumnDefs = $('[datatable='+elem+']').attr('datatable-columndefs');
    var datatableOrder = $('[datatable='+elem+']').attr('datatable-order');
    var datatableResponsive = $('[datatable='+elem+']').attr('datatable-responsive');
    var datatablelengthMenu = $('[datatable='+elem+']').attr('datatable-lengthmenu');
    var datatablefnRowCallback = $('[datatable='+elem+']').attr('datatable-fnrowcallback');
    var datatableSecured = $('[datatable='+elem+']').attr('datatable-secured');

    if (typeof datatableProcessing !== "undefined") {
        tableset.processing = (datatableProcessing == '1') ? true : false;
    }

    if (typeof datatableServerSide !== "undefined") {
        tableset.serverSide = (datatableServerSide == '1') ? true : false;
    }

    if (typeof datatableResponsive !== "undefined") {
        tableset.responsive = (datatableResponsive == '1') ? true : false;
    }

    if (typeof datatableSecured !== "undefined") {
        datatableSecured = (datatableSecured == 'true') ? true : false;
    }

    if (typeof datatablefnRowCallback !== "undefined") {
        datatablefnRowCallback = $.parseJSON(datatablefnRowCallback);
        tableset.fnRowCallback = new Function(datatablefnRowCallback['arguments'], datatablefnRowCallback['body']);
    }

    if (typeof datatableserverMethod !== "undefined") {
        tableset.serverMethod = datatableserverMethod;
    }

    if (typeof datatableAjax !== "undefined") {
        datatableAjax = $.parseJSON(datatableAjax);
        if (datatableSecured) {
            datatableAjax.data = function ( d ) {
                d[backSet.csrf_hash_name] = backSet.csrf_hash;
            };    
            datatableAjax.complete = function(response) {
                var data  = $.parseJSON(response.responseText);
                backSet.csrf_hash = data['hash_token'];
            }
        }
        datatableAjax.error = function ( d ) {
            console.log(d);
        };
        
        tableset.ajax = datatableAjax;
    }

    if (typeof datatableColumns !== "undefined") {
        tableset.columns = $.parseJSON(datatableColumns);
    }

    if (typeof datatablelengthMenu !== "undefined") {
        tableset.lengthMenu = $.parseJSON(datatablelengthMenu);
    }

    if (typeof datatableOrder !== "undefined") {
        tableset.order = $.parseJSON(datatableOrder);
    }

    if (typeof datatableColumnDefs !== "undefined") {
        tableset.columnDefs = $.parseJSON(datatableColumnDefs);
    }

    if (typeof datatableButtonsDom === "undefined") {
        datatableButtonsDom = "Bfrtip";
    }

    if (typeof datatableButtons !== "undefined") {
        datatableButtons = $.parseJSON(datatableButtons);
        if (datatableButtons.length > 0) {
            tableset.dom = datatableButtonsDom;
            tableset.buttons = new Object([]);
            $.each(datatableButtons, function(i, item) {
                if (typeof item === 'string') {
                    tableset.buttons.push(item); 
                }
                if (typeof item === 'object') {
                    let button = {};
                    if ('text' in item) { button.text = item['text'];}
                    if ('className' in item) { button.className = item['className'];}
                    if ('extend' in item) {button.extend = item['extend'];}
                    if ('buttons' in item) {
                        $.each(item['buttons'], function(bi, bitem) {
                            if ('customize' in bitem) {
                                item['buttons'][bi]['customize'] = new Function(bitem['customize']['arguments'], bitem['customize']['body']);
                            }
                            if ('action' in bitem) {
                                item['buttons'][bi]['action'] = new Function(bitem['action']['arguments'], bitem['action']['body']);
                            }
                        });
                        button.buttons = item['buttons'];
                    }
                    if ('action' in item) { 
                        button.action = new Function(item['action']['arguments'], item['action']['body']);
                    }
                    tableset.buttons.push(button); 
                }
            });
            $.extend({}, tableset.buttons);
        }
    }
    return tableset;
}

if ( $( "[datatable]" ).length ) {
    $( "[datatable]" ).each(function( index ) {
        var table = $( this );
        var name = table.attr( 'datatable' );
        $dtables[name] = $('[datatable='+name+']').DataTable(generateDatableSettings(name));
    });
}