function redirect_after_failure_dropdown(value){
    if ( value === "custom_redirect_url") {
        jQuery("#custom_fail_dest").show();
        jQuery("#custom_message").hide();
        document.getElementById('custom_fail_dest_id').required = true;
    } else if ( value === "404_custom_message") {
        jQuery("#custom_fail_dest").hide();
        jQuery("#custom_message").show();
        document.getElementById("custom_fail_dest_id").required = false;
    } else {
        jQuery("#custom_fail_dest").hide();
        jQuery("#custom_message").hide();
        document.getElementById("custom_fail_dest_id").required = false;
    }
}

function login_url_key(value){
    jQuery('#custom_admin_url').html(jQuery("#currentAdminUrl").html() + "/?" + value);
}

function mo_show_tab(tab_id) {
    jQuery(".mini_websecurity_tab").css("background",'none');
    jQuery(".mini_websecurity_tab").css("color",'white');
    jQuery(".mo_websecurity_tab").css('display','none');
    jQuery("#"+tab_id).css('display','block');
    jQuery("#mo_"+tab_id).css("background",'white');
    jQuery("#mo_"+tab_id).css("color",'black');
}

var clock=1;
var no_of_entry="10";

function resend_otp(){
    jQuery('#mo_otp_cancel_form').submit();
}

function collapse_link(element_id){
    var link = document.getElementById(element_id);
    if (link.style.display === "none") {
        link.style.display = "block";
    } else {
        link.style.display = "none";
    }
}

function nospaces(t, msg){
    if(t.value.match(/\s/g)){
        alert(msg);
        t.value=t.value.replace(/\s/g,'');
    }
}

function show_import_export() {
    jQuery("#import_export_form").show();
    jQuery("#main-div").hide();
    jQuery("#import_export_btn").hide();
}

function hide_import_export() {
    jQuery("#import_export_form").hide();
    jQuery("#main-div").show();
    jQuery("#import_export_btn").show();
}

function copyToClipboard(element) {
    var temp = jQuery("<input>");
    jQuery("body").append(temp);
    temp.val(jQuery(element).text().trim()).select();
    document.execCommand("copy");
    temp.remove();
}

function copyElement(){
    var copyInput = document.querySelector('#custom_login_url_key')
    copyInput.select()
    document.execCommand("copy")
}