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
JHtml::_('stylesheet', JUri::base() .'components/com_joomla_networksecurity/assets/css/miniorange_boot.css');

class AdvancedBlocking
{
    public static function mo_networksecurity_advanced_ip_blocking_form()
    {
        $db_val = NetworkSecurityUtilities::_get_advance_ip();

        $browser_blk_enable = $db_val['mo_enable_browser_blocking'] ?? 0;
        $medge              = $db_val['mo_medge_blocking'] ?? 0;

        $country = array('A1' => 'ANONYMOUS PROXY', 'A2' => 'SATELLITE PROVIDER', 'O1' => 'OTHER COUNTRY', 'AF' => 'AFGHANISTAN', 'AL' => 'ALBANIA', 'DZ' => 'ALGERIA', 'AS' => 'AMERICAN SAMOA', 'AD' => 'ANDORRA', 'AO' => 'ANGOLA', 'AI' => 'ANGUILLA', 'AQ' => 'ANTARCTICA', 'AG' => 'ANTIGUA AND BARBUDA', 'AR' => 'ARGENTINA', 'AM' => 'ARMENIA', 'AW' => 'ARUBA', 'AU' => 'AUSTRALIA', 'AT' => 'AUSTRIA', 'AZ' => 'AZERBAIJAN', 'BS' => 'BAHAMAS', 'BH' => 'BAHRAIN', 'BD' => 'BANGLADESH', 'BB' => 'BARBADOS', 'BY' => 'BELARUS', 'BE' => 'BELGIUM', 'BZ' => 'BELIZE', 'BJ' => 'BENIN', 'BM' => 'BERMUDA', 'BT' => 'BHUTAN', 'BO' => 'BOLIVIA', 'BA' => 'BOSNIA AND HERZEGOVINA', 'BW' => 'BOTSWANA', 'BV' => 'BOUVET ISLAND', 'BR' => 'BRAZIL', 'IO' => 'BRITISH INDIAN OCEAN TERRITORY', 'BN' => 'BRUNEI DARUSSALAM', 'BG' => 'BULGARIA', 'BF' => 'BURKINA FASO', 'BI' => 'BURUNDI', 'KH' => 'CAMBODIA', 'CM' => 'CAMEROON', 'CA' => 'CANADA', 'CV' => 'CAPE VERDE', 'KY' => 'CAYMAN ISLANDS', 'CF' => 'CENTRAL AFRICAN REPUBLIC', 'TD' => 'CHAD', 'CL' => 'CHILE', 'CN' => 'CHINA', 'CX' => 'CHRISTMAS ISLAND', 'CC' => 'COCOS (KEELING) ISLANDS', 'CO' => 'COLOMBIA', 'KM' => 'COMOROS', 'CG' => 'CONGO', 'CD' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'CK' => 'COOK ISLANDS', 'CR' => 'COSTA RICA', 'CI' => 'COTE D IVOIRE', 'HR' => 'CROATIA', 'CU' => 'CUBA', 'CY' => 'CYPRUS', 'CZ' => 'CZECH REPUBLIC', 'DK' => 'DENMARK', 'DJ' => 'DJIBOUTI', 'DM' => 'DOMINICA', 'DO' => 'DOMINICAN REPUBLIC', 'TP' => 'EAST TIMOR', 'EC' => 'ECUADOR', 'EG' => 'EGYPT', 'SV' => 'EL SALVADOR', 'GQ' => 'EQUATORIAL GUINEA', 'ER' => 'ERITREA', 'EE' => 'ESTONIA', 'ET' => 'ETHIOPIA', 'FK' => 'FALKLAND ISLANDS (MALVINAS)', 'FO' => 'FAROE ISLANDS', 'FJ' => 'FIJI', 'FI' => 'FINLAND', 'FR' => 'FRANCE', 'GF' => 'FRENCH GUIANA', 'PF' => 'FRENCH POLYNESIA', 'TF' => 'FRENCH SOUTHERN TERRITORIES', 'GA' => 'GABON', 'GM' => 'GAMBIA', 'GE' => 'GEORGIA', 'DE' => 'GERMANY', 'GH' => 'GHANA', 'GI' => 'GIBRALTAR', 'GR' => 'GREECE', 'GL' => 'GREENLAND', 'GD' => 'GRENADA', 'GP' => 'GUADELOUPE', 'GU' => 'GUAM', 'GT' => 'GUATEMALA', 'GN' => 'GUINEA', 'GW' => 'GUINEA-BISSAU', 'GY' => 'GUYANA', 'HT' => 'HAITI', 'HM' => 'HEARD ISLAND AND MCDONALD ISLANDS', 'VA' => 'HOLY SEE (VATICAN CITY STATE)', 'HN' => 'HONDURAS', 'HK' => 'HONG KONG', 'HU' => 'HUNGARY', 'IS' => 'ICELAND', 'IN' => 'INDIA', 'ID' => 'INDONESIA', 'IR' => 'IRAN, ISLAMIC REPUBLIC OF', 'IQ' => 'IRAQ', 'IE' => 'IRELAND', 'IL' => 'ISRAEL', 'IT' => 'ITALY', 'JM' => 'JAMAICA', 'JP' => 'JAPAN', 'JO' => 'JORDAN', 'KZ' => 'KAZAKSTAN', 'KE' => 'KENYA', 'KI' => 'KIRIBATI', 'KP' => 'KOREA DEMOCRATIC PEOPLES REPUBLIC OF', 'KR' => 'KOREA REPUBLIC OF', 'KW' => 'KUWAIT', 'KG' => 'KYRGYZSTAN', 'LA' => 'LAO PEOPLES DEMOCRATIC REPUBLIC', 'LV' => 'LATVIA', 'LB' => 'LEBANON', 'LS' => 'LESOTHO', 'LR' => 'LIBERIA', 'LY' => 'LIBYAN ARAB JAMAHIRIYA', 'LI' => 'LIECHTENSTEIN', 'LT' => 'LITHUANIA', 'LU' => 'LUXEMBOURG', 'MO' => 'MACAU', 'MK' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'MG' => 'MADAGASCAR', 'MW' => 'MALAWI', 'MY' => 'MALAYSIA', 'MV' => 'MALDIVES', 'ML' => 'MALI', 'MT' => 'MALTA', 'MH' => 'MARSHALL ISLANDS', 'MQ' => 'MARTINIQUE', 'MR' => 'MAURITANIA', 'MU' => 'MAURITIUS', 'YT' => 'MAYOTTE', 'MX' => 'MEXICO', 'FM' => 'MICRONESIA, FEDERATED STATES OF', 'MD' => 'MOLDOVA, REPUBLIC OF', 'MC' => 'MONACO', 'MN' => 'MONGOLIA', 'MS' => 'MONTSERRAT', 'MA' => 'MOROCCO', 'MZ' => 'MOZAMBIQUE', 'MM' => 'MYANMAR', 'NA' => 'NAMIBIA', 'NR' => 'NAURU', 'NP' => 'NEPAL', 'NL' => 'NETHERLANDS', 'AN' => 'NETHERLANDS ANTILLES', 'NC' => 'NEW CALEDONIA', 'NZ' => 'NEW ZEALAND', 'NI' => 'NICARAGUA', 'NE' => 'NIGER', 'NG' => 'NIGERIA', 'NU' => 'NIUE', 'NF' => 'NORFOLK ISLAND', 'MP' => 'NORTHERN MARIANA ISLANDS', 'NO' => 'NORWAY', 'OM' => 'OMAN', 'PK' => 'PAKISTAN', 'PW' => 'PALAU', 'PS' => 'PALESTINIAN TERRITORY, OCCUPIED', 'PA' => 'PANAMA', 'PG' => 'PAPUA NEW GUINEA', 'PY' => 'PARAGUAY', 'PE' => 'PERU', 'PH' => 'PHILIPPINES', 'PN' => 'PITCAIRN', 'PL' => 'POLAND', 'PT' => 'PORTUGAL', 'PR' => 'PUERTO RICO', 'QA' => 'QATAR', 'RE' => 'REUNION', 'RO' => 'ROMANIA', 'RU' => 'RUSSIAN FEDERATION', 'RW' => 'RWANDA', 'SH' => 'SAINT HELENA', 'KN' => 'SAINT KITTS AND NEVIS', 'LC' => 'SAINT LUCIA', 'PM' => 'SAINT PIERRE AND MIQUELON', 'VC' => 'SAINT VINCENT AND THE GRENADINES', 'WS' => 'SAMOA', 'SM' => 'SAN MARINO', 'ST' => 'SAO TOME AND PRINCIPE', 'SA' => 'SAUDI ARABIA', 'SN' => 'SENEGAL', 'SC' => 'SEYCHELLES', 'SL' => 'SIERRA LEONE', 'SG' => 'SINGAPORE', 'SK' => 'SLOVAKIA', 'SI' => 'SLOVENIA', 'SB' => 'SOLOMON ISLANDS', 'SO' => 'SOMALIA', 'ZA' => 'SOUTH AFRICA', 'GS' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'ES' => 'SPAIN', 'LK' => 'SRI LANKA', 'SD' => 'SUDAN', 'SR' => 'SURINAME', 'SJ' => 'SVALBARD AND JAN MAYEN', 'SZ' => 'SWAZILAND', 'SE' => 'SWEDEN', 'CH' => 'SWITZERLAND', 'SY' => 'SYRIAN ARAB REPUBLIC', 'TW' => 'TAIWAN, PROVINCE OF CHINA', 'TJ' => 'TAJIKISTAN', 'TZ' => 'TANZANIA, UNITED REPUBLIC OF', 'TH' => 'THAILAND', 'TG' => 'TOGO', 'TK' => 'TOKELAU', 'TO' => 'TONGA', 'TT' => 'TRINIDAD AND TOBAGO', 'TN' => 'TUNISIA', 'TR' => 'TURKEY', 'TM' => 'TURKMENISTAN', 'TC' => 'TURKS AND CAICOS ISLANDS', 'TV' => 'TUVALU', 'UG' => 'UGANDA', 'UA' => 'UKRAINE', 'AE' => 'UNITED ARAB EMIRATES', 'GB' => 'UNITED KINGDOM', 'US' => 'UNITED STATES', 'UM' => 'UNITED STATES MINOR OUTLYING ISLANDS', 'UY' => 'URUGUAY', 'UZ' => 'UZBEKISTAN', 'VU' => 'VANUATU', 'VE' => 'VENEZUELA', 'VN' => 'VIET NAM', 'VG' => 'VIRGIN ISLANDS, BRITISH', 'VI' => 'VIRGIN ISLANDS, U.S.', 'WF' => 'WALLIS AND FUTUNA', 'EH' => 'WESTERN SAHARA', 'YE' => 'YEMEN', 'YU' => 'YUGOSLAVIA', 'ZM' => 'ZAMBIA', 'ZW' => 'ZIMBABWE');
        ?>
        <div class="mo_boot_col-sm-12">
        <h3 class="mo_boot_mt-3">Browser Blocking </h3>
        <hr>
        <form name="mo_jnsp_browser_blocking" method="post"
              action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=advanceipblocking._save_browser_blocking'); ?>">

                    <input type="checkbox" <?php if ($browser_blk_enable == 1) echo "checked"; ?>
                           class="mo_enable_brwoser_blocking checkbox_style" value="1" name="mo_enable_browser_blocking"
                           onclick="enable_checkbox_advance_ip()">Enable Browser blocking.<br><br>
                    <div>Select the Browser you need to block.</div><br>
                    <div><span class="mo_boot_text-red">Note: </span>You can enable Microsoft Edge for testing purposes.
                        If you want to use other browser then you need to upgrade our <a href='index.php?option=com_joomla_networksecurity&view=licensing'><strong>PREMIUM</strong></a> version of the plugin.</div>
                    <br>

                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-3 mo_boot_text-center">
                            <input type="checkbox" name="mo_medge_blocking" class="mo_medge_blocking" value="1" <?php if ($medge == 1 && $browser_blk_enable == 1) echo "checked"; ?>>
                            <img src="<?php echo JURI::base().'/components/com_joomla_networksecurity/assets/images/ms-edge.png' ?>" style="width: 28px;"/>
                        </div>
                    </div><br>

                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-3 mo_boot_text-center">
                            <input type="checkbox" name="mo_chrome_blocking" class="mo_chrome_blocking" value="1" disabled>
                            <img src="<?php echo JURI::base().'/components/com_joomla_networksecurity/assets/images/chrome.png' ?>" style="width: 25px;"/>
                        </div>

                        <div class="mo_boot_col-sm-3 mo_boot_text-center">
                            <input type="checkbox" name="mo_firefox_blocking" class="mo_firefox_blocking" value="1" disabled>
                            <img src="<?php echo JURI::base().'/components/com_joomla_networksecurity/assets/images/firefox.png' ?>" style="width: 28px;"/>
                        </div>

                        <div class="mo_boot_col-sm-3 mo_boot_text-center">
                            <input type="checkbox" name="mo_safari_blocking" class="mo_safari_blocking" value="1" disabled>
                            <img src="<?php echo JURI::base().'/components/com_joomla_networksecurity/assets/images/safari.png' ?>" style="width: 35px;"/>
                        </div>

                        <div class="mo_boot_col-sm-3 mo_boot_text-center">
                            <input type="checkbox" name="mo_opera_blocking" class="mo_opera_blocking" value="1" disabled>
                            <img src="<?php echo JURI::base().'/components/com_joomla_networksecurity/assets/images/opera.png' ?>" style="width: 30px;"/>
                        </div>
                    </div>
                    <br>

                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                            <input type="submit" name="submit" value="Save" class="mo_boot_btn mo_boot_btn-primary">
                        </div>
                    </div>
        </form><br><br>

        <h3>IP Address Range Blocking <sup><a href='index.php?option=com_joomla_networksecurity&view=licensing'><strong>Premium Feature</strong></a></sup></h3>
        <hr>
        <div>You can block any range of IP addresses here ( Examples: 192.168.0.100 - 192.168.0.190 )</div>
        <br>
        <form class="hidden" id="unblockiprange" method="POST">
            <input type="hidden" name="option" value="mo_unblock_ip_range">
            <input type="hidden" name="entryidr" value="" id="unblockiprangeval">
        </form>
        <form name="mo_ip_range_blocking" method="post">
            <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-5">
                        Enter IP Address Range:
                    </div>
                    <div class="mo_boot_col-sm-6">
                        <input class="form-control mo_security_textfield mo_boot_form-control" name="ip_address_range_blocking"
                               type="text" placeholder="IP address range" disabled />
                    </div>
                </div><br>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                        <input type="submit" name="submit" class="mo_boot_btn mo_boot_btn-primary" value="Block" disabled>
                    </div>
                </div><br>

            <div class="table-responsive" style="font-family: sans-serif;font-size: 12px;">
                <table class="mo_myTable">
                    <tr class="header mo_boot_text-white mo_boot_text-center" style="line-height: 10px;background-color: #001b4c;">
                        <th class="mo_boot_text-center" width="33%">IP Address</th>
                        <th class="mo_boot_text-center" width="33%">Date</th>
                        <th class="mo_boot_text-center" width="33%">Action</th>
                    </tr>
                    <tr style="background-color: #fff;">
                        <td class="mo_boot_text-center" colspan="3" style="user-select: none;">
                            No IP ranges are blocked at this moment.
                        </td>
                    </tr>
                </table>
            </div>
        </form><br><br>

        <h3>Country Blocking <sup><a href="index.php?option=com_joomla_networksecurity&view=licensing"><strong>Premium Feature</strong></a></sup></h3>
        <hr>
        <p>Select countries from below which you want to block.</p>
        <a href="#!country_blocking_id" onclick="collapse_link('country_blocking_id')"><strong>Click here to see the list of countries >></strong></a><br><br>
        <div id="country_blocking_id" style="display: none;">
            <form name="mo_jnsp_country_blocking" method="post" id="countryblockingform">
                <table style="width:100%;">
                    <?php
                    foreach ($country as $key => $value)
                        echo '<tr style="width:33%;float: left;">
                    <td style="padding:0px 10px">
                        <input type="checkbox" style="float: left;margin-right: 10px;" disabled name="' . $key . '" >' . $value . '
                    </td>
                </tr>'; ?>
                </table><br><br>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                        <input type="submit" class="mo_boot_btn mo_boot_btn-primary" value="Save" disabled>
                    </div>
                </div>
            </form>
        </div>

        <script>
            enable_checkbox_advance_ip();

            function enable_checkbox_advance_ip() {
                var is_enable = document.getElementsByClassName('mo_enable_brwoser_blocking')[0];
                var medge = document.getElementsByClassName('mo_medge_blocking')[0];

                medge.disabled = is_enable.checked !== true;
            }

        </script>
        </div>
        <?php

    }
}