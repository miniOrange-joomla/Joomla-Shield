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
    moJoomShieldInitAccordions();
});

function moJoomShieldInitAccordions() {
    var storagePrefix = 'joomshield.accordion.';
    var accordions = document.querySelectorAll('details.mo_js_server_notes_accordion[id]');

    accordions.forEach(function (accordion) {
        var storageKey = storagePrefix + accordion.id;

        try {
            var stored = window.sessionStorage.getItem(storageKey);

            if (stored === '1') {
                accordion.open = true;
            } else if (stored === '0') {
                accordion.open = false;
            }
        } catch (error) {
        }

        accordion.addEventListener('toggle', function () {
            try {
                window.sessionStorage.setItem(storageKey, accordion.open ? '1' : '0');
            } catch (error) {
            }
        });
    });
}

function redirect_after_failure_dropdown(value){
    var requiredMarker = document.getElementById("custom_fail_dest_required");

    if ( value === "custom_redirect_url") {
        jQuery("#custom_fail_dest").show();
        jQuery("#custom_message").hide();
        document.getElementById('custom_fail_dest_id').required = true;
        if (requiredMarker) requiredMarker.hidden = false;
    } else if ( value === "404_custom_message") {
        jQuery("#custom_fail_dest").hide();
        jQuery("#custom_message").show();
        document.getElementById("custom_fail_dest_id").required = false;
        if (requiredMarker) requiredMarker.hidden = true;
    } else {
        jQuery("#custom_fail_dest").hide();
        jQuery("#custom_message").hide();
        document.getElementById("custom_fail_dest_id").required = false;
        if (requiredMarker) requiredMarker.hidden = true;
    }
}

function login_url_key(value){
    jQuery('#custom_admin_url').html(jQuery("#currentAdminUrl").html() + "/?" + value);
}

function moJoomShieldLimitLoginUrlKey(field, event) {
    var maxLength = 20;
    var notice = document.getElementById('custom_login_url_key_limit');
    var key = '';

    if (!field) {
        return;
    }

    if (field.getAttribute('maxlength')) {
        maxLength = parseInt(field.getAttribute('maxlength'), 10) || maxLength;
    }

    function setNotice(show) {
        if (notice) {
            notice.hidden = !show;
        }
    }

    if (event && event.type === 'keydown') {
        if (field.value.length < maxLength) {
            return;
        }

        if (typeof field.selectionStart === 'number' && field.selectionStart !== field.selectionEnd) {
            return;
        }

        key = event.key || '';

        if (key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
            setNotice(true);
            event.preventDefault();
        }

        return;
    }

    if (event && event.type === 'paste') {
        var clipboard = event.clipboardData || window.clipboardData;
        var pasted = clipboard ? String(clipboard.getData('text') || '') : '';
        var start = typeof field.selectionStart === 'number' ? field.selectionStart : field.value.length;
        var end = typeof field.selectionEnd === 'number' ? field.selectionEnd : field.value.length;
        var combined = field.value.slice(0, start) + pasted + field.value.slice(end);

        if (combined.replace(/\s/g, '').length > maxLength) {
            setNotice(true);
        }

        return;
    }

    if (field.value.length > maxLength) {
        if (event) {
            field.value = field.value.substring(0, maxLength);

            if (typeof login_url_key === 'function') {
                login_url_key(field.value);
            }
        }

        setNotice(true);
        return;
    }

    if (field.value.length < maxLength) {
        setNotice(false);
    }
}

function moJoomShieldBindLoginUrlKeyLimit() {
    var field = document.getElementById('custom_login_url_key');

    if (!field) {
        return;
    }

    field.addEventListener('keydown', function (event) {
        moJoomShieldLimitLoginUrlKey(field, event);
    });
    field.addEventListener('paste', function (event) {
        moJoomShieldLimitLoginUrlKey(field, event);
    });
    field.addEventListener('input', function (event) {
        moJoomShieldLimitLoginUrlKey(field, event);
    });

    if (field.value.length > 20) {
        moJoomShieldLimitLoginUrlKey(field);
    }
}

function moJoomShieldBindAdminPasswordToggle() {
    var button = document.getElementById('admin_http_password_toggle');
    var field = document.getElementById('admin_http_password');

    if (!button || !field) {
        return;
    }

    button.addEventListener('click', function () {
        var showPassword = field.getAttribute('type') === 'password';
        var showLabel = button.getAttribute('data-show-label') || 'Show';
        var hideLabel = button.getAttribute('data-hide-label') || 'Hide';

        field.setAttribute('type', showPassword ? 'text' : 'password');
        button.setAttribute('aria-pressed', showPassword ? 'true' : 'false');
        button.setAttribute('aria-label', showPassword ? hideLabel : showLabel);
        button.textContent = showPassword ? hideLabel : showLabel;
    });
}

function mo_show_tab(tab_id) {
    var tabNames = {
        websecurity_tab_1: 'admin_security',
        websecurity_tab_2: 'login_security',
        websecurity_tab_3: 'register_security',
        websecurity_tab_4: 'ip_blocking',
        websecurity_tab_5: 'ip_reports',
        websecurity_tab_6: 'advanced_ip_blocking',
        websecurity_tab_8: 'email_notifications',
        websecurity_tab_10: 'advanced_features',
        websecurity_tab_13: 'nginx_rules',
        websecurity_tab_11: 'license_plans'
    };

    jQuery(".mini_websecurity_tab").css("background",'none');
    jQuery(".mini_websecurity_tab").css("color",'white');
    jQuery(".mo_websecurity_tab").css('display','none');
    jQuery("#"+tab_id).css('display','block');
    jQuery("#mo_"+tab_id).css("background",'white');
    jQuery("#mo_"+tab_id).css("color",'black');

    if (tabNames[tab_id] && window.history && window.history.replaceState) {
        var url = new URL(window.location.href);
        url.searchParams.set('tab', tabNames[tab_id]);
        window.history.replaceState({}, '', url.toString());

        var returnTab = document.querySelector('input[name="return_tab"]');
        if (returnTab) returnTab.value = tabNames[tab_id];
    }
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

function moJoomShieldCopySelector(selector, trigger){
    var target = document.querySelector(selector);

    if (!target) {
        return;
    }

    if (typeof target.select === 'function') {
        target.select();
        document.execCommand('copy');
        moJoomShieldCopyFeedback(trigger);
        return;
    }

    copyToClipboard(selector);
    moJoomShieldCopyFeedback(trigger);
}

function moJoomShieldCopyFeedback(trigger) {
    if (!trigger) {
        return;
    }

    var original = trigger.getAttribute('data-original-label') || trigger.textContent;
    var copied = trigger.getAttribute('data-copied-text') || 'Copied';

    trigger.setAttribute('data-original-label', original);
    trigger.textContent = copied;

    window.setTimeout(function () {
        trigger.textContent = original;
    }, 1600);
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
    initFeatureLockTimer();
    moJoomShieldToggleAdminHttpRequired();
    moJoomShieldBindLoginUrlKeyLimit();
    moJoomShieldBindAdminPasswordToggle();
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

    if (!custom_log_url) return;

    var isEnabled = custom_log_url.checked === true;

    if (admin_log_url) {
        admin_log_url.required = isEnabled;
    }
}

function moJoomShieldToggleAdminHttpRequired() {
    var enabledField = document.getElementById('admin_http_auth');
    var username = document.getElementById('admin_http_user');
    var password = document.getElementById('admin_http_password');

    if (!enabledField || !username || !password) return;

    var enabled = enabledField.checked === true;
    var passwordRequired = enabled && password.getAttribute('data-has-password') !== '1';
    var usernameMarker = document.getElementById('admin_http_user_required');
    var passwordMarker = document.getElementById('admin_http_password_required');

    username.required = enabled;
    password.required = passwordRequired;
    if (usernameMarker) usernameMarker.hidden = !enabled;
    if (passwordMarker) passwordMarker.hidden = !passwordRequired;
}

function enable_checkbox_blk_email() {
    var block_fake_emails = document.getElementsByClassName('mo_blk_fake_emails')[0];
    var mo_enable_blk_email = document.getElementsByClassName('mo_enable_blk_email')[0];

    if (!block_fake_emails || !mo_enable_blk_email) return;

    var isEnabled = block_fake_emails.checked === true;
    var requiredMarker = document.getElementById('mo_email_domains_required');

    mo_enable_blk_email.disabled = !isEnabled;
    mo_enable_blk_email.required = isEnabled;
    if (requiredMarker) requiredMarker.hidden = !isEnabled;
}

function initFeatureLockTimer() {
    var status = document.querySelector('.mo_js_feature_lock_status');

    if (!status) return;

    var expiresAt = parseInt(status.getAttribute('data-expires-at'), 10) * 1000;
    var countdown = status.querySelector('.mo_js_feature_lock_countdown');

    function updateTimer() {
        var remaining = Math.max(0, Math.ceil((expiresAt - Date.now()) / 1000));

        if (remaining <= 0) {
            window.location.reload();
            return;
        }

        if (countdown) {
            var minutes = Math.floor(remaining / 60);
            var seconds = String(remaining % 60).padStart(2, '0');
            countdown.textContent = ' (' + minutes + ':' + seconds + ')';
        }

        window.setTimeout(updateTimer, 1000);
    }

    updateTimer();
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