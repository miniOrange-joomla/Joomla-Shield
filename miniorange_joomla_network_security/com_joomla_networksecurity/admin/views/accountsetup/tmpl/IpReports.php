<?php

/**
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
 *
 *
 * This file is part of miniOrange Joomla Network / Website Security plugin.
 *
 * miniOrange Joomla Network / Website Security plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * miniOrange Joomla Network / Website Security plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with miniOrange Joomla Network / Website Security plugin.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die;
//define("JOOMLA_REPORTS_FEEDBACK", JUri::base() . "/joomla_report");
JHtml::_('jquery.framework');
JHtml::stylesheet('https://use.fontawesome.com/19afe6f2b6.css');
JHtml::script('https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js');
JHtml::script('https://code.jquery.com/jquery-3.5.1.js');
JHtml::script('https://use.fontawesome.com/19afe6f2b6.js');
class IpReports
{
    public static function mo_networksecurity_login_reports_form()
    {
        $base_url = JUri::base().'index.php?option=com_joomla_networksecurity&task=loginsecurity.joomlapagination';
        ?>
        <div class="mo_boot_col-sm-12">
        <h3 class=" mo_boot_mt-3">Login Transactions Report</h3><hr>
            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-1 mo_boot_px-0">
                    <select class="mo_boot_form-control" id="select_number" onchange="list_of_entry()" style="height: 35px !important;">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="mo_boot_col-sm-3">
                    <input type="text" id="search_text" class="mo_boot_form-control" onkeyup="search()" placeholder="Search" style="height: 35px !important;"/> <br>
                </div>
                <div class="mo_boot_col-sm-8 mo_boot_text-right">
                    <form name="mo_ip_login" method="post" id="jnsp_clear_values" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=moipblocking.clear_reports'); ?>">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-12">
                                <input type="submit" name="refresh_page" class="mo_boot_btn mo_boot_btn-primary"  value="Refresh Page">
                                <input type="submit" name="clear_val" class="mo_boot_btn mo_boot_btn-danger" value="Clear Reports" onclick="ClearReports();">
                                <input type="submit" name="download_reports" class="mo_boot_btn mo_boot_btn-primary" value="Download Reports">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <script>
            function ClearReports(){
                if(confirm("Do you really want to delete the report?")){
                    jQuery('clear_val').attr('value', 'Clear Reports');
                }
                else {
                    $('#jnsp_clear_values').submit(function() {
                        return false;
                    });
                }
            }
        </script>
        <div id="show_paginations"></div>
        <input type="hidden" id="next_page" value="0"><br>
        <div>
            <div id="next_btn">
                <input type="submit" name="mo_next" class="mo_boot_btn mo_boot_btn-primary"
                       style="float: right;" onclick="next_or_prev_page('next','preserve');" value="Next">
            </div>
            <div id="pre_btn">
                <input type="submit" name="mo_next" class="mo_boot_btn mo_boot_btn-primary mo_boot_mr-1"
                       style="float: right;" onclick="next_or_prev_page('pre','preserve');" value="Prev">
            </div>
        </div>
        <script>
            jQuery(document).ready(function (){
                next_or_prev_page('next');
            });

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
                    url: '<?php echo $base_url; ?>',
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
        </script>
        </div>
        <?php
    }
}