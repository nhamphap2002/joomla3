jQuery(document).ready(function ($) {
    var $action = window.location.href;
    if ($action.indexOf('#__hc-action-complete') != -1) {
        $.ajax({
            url: 'plugins/vmpayment/commweb/loadjs.php',
            success: function (data, textStatus, jqXHR) {
                $('body').append(data)
            }
        })
    }
})

var loadcheckout = setInterval(function (){
    if(typeof Checkout !='defined'){
        
        clearInterval(loadcheckout);
    }
},100)