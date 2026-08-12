function mojspSetBrowserTimezone() {
    var browserTimezone = '';
    var timezoneField = document.getElementById('mojsp_browser_timezone');

    if (window.Intl && Intl.DateTimeFormat) {
        browserTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
    }

    if (timezoneField) {
        timezoneField.value = browserTimezone;
    }

    if (browserTimezone) {
        document.cookie = 'mojsp_browser_timezone=' + encodeURIComponent(browserTimezone) + '; path=/; SameSite=Lax';
    }
}

mojspSetBrowserTimezone();

document.addEventListener('DOMContentLoaded', function () {
    mojspSetBrowserTimezone();
});

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
var no_of_entry="20";

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

function toggleFeatureList(headerId) {
    const list = document.getElementById(headerId);
    const arrow = list.previousElementSibling.querySelector(".mo_js_pricing_table_feature_arrow i");

    if (list.style.display === "none") {
        list.style.display = "block";
        arrow.classList.remove("fa-chevron-down");
        arrow.classList.add("fa-chevron-up");
    } else {
        list.style.display = "none";
        arrow.classList.remove("fa-chevron-up");
        arrow.classList.add("fa-chevron-down");
    }
}

if (window.jQuery) {
    jQuery(document).ready(function (){
        next_or_prev_page('next');
    });
}

function list_of_entry(){
    no_of_entry=jQuery("#select_number").val();
    next_or_prev_page('on');
}

function sort(button){
    var order ="";
    if(clock)
    {
        clock = 0;
        order = 'up';
    }
    else
    {
        clock = 1;
        order = 'down';
    }
    next_or_prev_page(button,order);
}

function search()
{
    var value="";
    value=jQuery("#search_text").val().toLowerCase();
    $("#myTable tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
}

function next_or_prev_page(button , order='down') {
    var page = document.getElementById('next_page').value;
    var orderBY='down';
    if(button =='on')
        page=0;
    if(order == 'up')
        orderBY='up';
    if(order =='preserve'){
        orderBY = clock===1?'down':'up';
    }
    page = parseInt(page);
    if (button == 'pre' && page != 0) {
        page -= 2;
        document.getElementById('next_page').value = page;
        document.getElementById('next_btn').style.display = "inline";
    }
    if (page == 0) {
        document.getElementById('pre_btn').style.display = "none";
        document.getElementById('next_btn').style.display = "inline";
    }
    else
        document.getElementById('pre_btn').style.display = "inline";
    jQuery.ajax({
        url: (typeof moJoomShieldPaginationUrl !== 'undefined' && moJoomShieldPaginationUrl)
            ? moJoomShieldPaginationUrl
            : 'index.php?option=com_joomshield&task=loginsecurity.joomlapagination',
        dataType: "text",
        method: "POST",
        data: {'page': page,'orderBY':orderBY,'no_of_entry':no_of_entry},
        success: function (data) {
            var arr = data.split("separator_for_count");
            jQuery("#show_paginations").html(arr[0]);
            if (arr[1] == 0) {
                document.getElementById('next_page').value = 0;
                next_or_prev_page('next','preserve');
            }
        }
    });
    page += 1;
    document.getElementById('next_page').value = page;
}

document.addEventListener('DOMContentLoaded', function () {
    enable_checkbox_url();
    enable_checkbox_blk_email();
    enable_checkbox_advance_ip();
    initCountryMultiselect();
});

function enable_checkbox_advance_ip() {
    var is_enable = document.getElementsByClassName('mo_enable_brwoser_blocking')[0];
    var medge = document.getElementsByClassName('mo_medge_blocking')[0];

    if (!is_enable || !medge) {
        return;
    }

    medge.disabled = is_enable.checked !== true;
}

function enable_checkbox_url() {
    var custom_log_url = document.getElementsByClassName('enable_custom_login')[0];
    var admin_log_url = document.getElementsByClassName('admin_log_url')[0];
    var custom_redirect_url = document.getElementsByClassName('redirect_after_failure')[0];
    var custom_url_failure = document.getElementsByClassName('custom_url_after_failure')[0];
    var error_msg = document.getElementsByClassName('404_message')[0];

    if (!custom_log_url) return;

    var isEnabled = custom_log_url.checked === true;

    admin_log_url.disabled = !isEnabled;
    custom_redirect_url.disabled = !isEnabled;
    custom_url_failure.disabled = !isEnabled;
    error_msg.disabled = !isEnabled;
}

function enable_checkbox_blk_email() {
    var block_fake_emails = document.getElementsByClassName('mo_blk_fake_emails')[0];
    var mo_enable_blk_email = document.getElementsByClassName('mo_enable_blk_email')[0];

    if (!block_fake_emails || !mo_enable_blk_email) return;

    mo_enable_blk_email.disabled = block_fake_emails.checked !== true;
}

function initCountryMultiselect() {
    var container = document.getElementById('mo_country_multiselect');

    if (!container) {
        return;
    }

    var trigger = document.getElementById('mo_country_multiselect_trigger');
    var panel = document.getElementById('mo_country_multiselect_panel');
    var searchInput = document.getElementById('mo_country_multiselect_search');
    var label = document.getElementById('mo_country_multiselect_label');
    var noResults = document.getElementById('mo_country_multiselect_no_results');
    var options = container.querySelectorAll('.mo_country_multiselect_option');
    var checkboxes = container.querySelectorAll('.mo_country_multiselect_option input[type="checkbox"]');
    var placeholder = label ? label.textContent : 'Select countries...';
    var isDisabled = trigger && trigger.classList.contains('mo_country_multiselect_disabled');

    function updateSelectedLabel() {
        if (!label) {
            return;
        }

        var selected = [];

        checkboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                var optionLabel = checkbox.parentElement;

                if (optionLabel) {
                    selected.push(optionLabel.textContent.trim());
                }
            }
        });

        if (selected.length === 0) {
            label.textContent = placeholder;
            label.className = 'mo_country_multiselect_placeholder';
        } else if (selected.length <= 2) {
            label.textContent = selected.join(', ');
            label.className = 'mo_country_multiselect_selected';
        } else {
            label.textContent = selected.length + ' countries selected';
            label.className = 'mo_country_multiselect_selected';
        }
    }

    function filterCountries() {
        if (!searchInput) {
            return;
        }

        var query = searchInput.value.toLowerCase().trim();
        var visibleCount = 0;

        options.forEach(function (option) {
            var countryName = option.getAttribute('data-country-name') || '';
            var isMatch = query === '' || countryName.indexOf(query) !== -1;

            if (isMatch) {
                option.classList.remove('mo_country_multiselect_hidden');
                visibleCount++;
            } else {
                option.classList.add('mo_country_multiselect_hidden');
            }
        });

        if (noResults) {
            if (visibleCount === 0) {
                noResults.classList.remove('mo_boot_d-none');
            } else {
                noResults.classList.add('mo_boot_d-none');
            }
        }
    }

    function closePanel() {
        if (!panel) {
            return;
        }

        panel.classList.add('mo_boot_d-none');

        if (searchInput) {
            searchInput.value = '';
            filterCountries();
        }
    }

    function openPanel() {
        if (!panel || isDisabled) {
            return;
        }

        panel.classList.remove('mo_boot_d-none');

        if (searchInput) {
            searchInput.focus();
        }
    }

    if (trigger) {
        trigger.addEventListener('click', function (event) {
            event.stopPropagation();

            if (isDisabled) {
                return;
            }

            if (panel.classList.contains('mo_boot_d-none')) {
                openPanel();
            } else {
                closePanel();
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterCountries);
        searchInput.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    }

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updateSelectedLabel);
    });

    document.addEventListener('click', function (event) {
        if (!container.contains(event.target)) {
            closePanel();
        }
    });

    updateSelectedLabel();
}