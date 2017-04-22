/* 
 Created on : Mar 14, 2017, 9:30:35 AM
 Author     : Tran Trong Thang
 Email      : trantrongthang1207@gmail.com
 Skype      : trantrongthang1207
 */
jQuery(document).ready(function ($) {
    $('select[name="jform[request][virtuemart_category_id]"]').parent().addClass('jform_virtuemart_category_id');
    var interval1 = setInterval(function () {
        if ($('.jform_virtuemart_category_id .chzn-search input').attr('readonly') == 'readonly') {
            $('.jform_virtuemart_category_id .chzn-search input').removeAttr('readonly');
            clearInterval(interval1);
        }
    }, 1000);
    var interval2 = setInterval(function () {
        if ($('#jform_parent_id_chzn .chzn-search input').attr('readonly') == 'readonly') {
            $('#jform_parent_id_chzn .chzn-search input').removeAttr('readonly');
            clearInterval(interval2);
        }
    }, 1000);
})