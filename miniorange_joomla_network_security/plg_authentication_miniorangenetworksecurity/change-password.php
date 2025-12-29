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

echo '
	<html>
		<head>
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
   			<meta name="viewport" content="width=device-width, initial-scale=1">
    			<link rel="stylesheet" type="text/css" href="' . JURI::root() . 'plugins/authentication/miniorangenetworksecurity/media/css/mo_customer_validation_style.css"/ />
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
		</head>
		<body>
			<div class="mo-modal-backdrop">
				<div class="mo_jnsp_modal" tabindex="-1" role="dialog">
					<div class="mo_jnsp_modal_backdrop"></div>
					<div class="mo_jnsp_modal_dialog mo_jnsp_modal_md">
						<div class="login mo_jnsp_modal_content">
							<div class="mo_jnsp_modal_header">
								<b style="color: black">Strong Password Recommended</b>
							</div>
							<div class="mo_jnsp_modal_body center">
								<div class="modal_err_message" id="error_message">Please enter a stronger password.</div> 
			        				A new security system has been enabled for you.
					    			It is recommended for you to use a stronger password. Please update your password.';
                            $post = JFactory::getApplication()->input->post->getArray();
                            $username = $post['username'] ?? '';
                            $password = $post['password'] ?? '';
                            if (!empty($username))
                            {
                                $action = JRoute::_('index.php?option=com_joomla_networksecurity&task=registersecurity.saveRegisterSecuritySettings');
                                echo '	
	                                <div class="mo_jnsp_login_container">
	                                    <form name="f" method="post" id="change_password_form"
                                            action="'.$action.'">
										    <input type="hidden" name="option_change_password" value="mo_jnsp_change_password" />
											<input type="hidden" name="username" value="' . $username . '" />
											<input type="hidden" name="password" value="' . $password . '" />
											<input type="password" name="new_password" id="new_password" class="mo_jnsp_textbox" placeholder="New Password" required />
											<i class="bi bi-eye-slash" style="margin-left: -30px;cursor: pointer;" id="togglePassword"></i>
											<input type="password" style="margin-left: 10px;" name="confirm_password" id="confirm_password" class="mo_jnsp_textbox" placeholder="Confirm Password" required />
											<i class="bi bi-eye-slash" style="margin-left: -30px;cursor: pointer;" id="toggleConfirmPassword"></i>
											<div class="registrationFormAlert" id="divCheckPasswordMatch"></div><br>
											<input type="submit" name="change_password_btn" id="change_password_btn" class="btn"  value="Update Password" style="margin-left: 33%;"/>
										</form>
									</div>';
                            }
                            else
                            {
                                echo '
                                    <script>
										window.location.href = window.location.protocol +\'//\'+ window.location.host + window.location.pathname;
									</script>';
                            }
                            echo '
   						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
                const togglePassword = document.querySelector("#togglePassword");
                const password = document.querySelector("#new_password");
                
                togglePassword.addEventListener("click", function () {
                    const type = password.getAttribute("type") === "password" ? "text" : "password";
                    password.setAttribute("type", type);
                    this.classList.toggle("bi-eye");
                });
                
                const toggleConfirmPassword = document.querySelector("#toggleConfirmPassword");
                const confirm_password = document.querySelector("#confirm_password");
                
                toggleConfirmPassword.addEventListener("click", function () {
                    const type = confirm_password.getAttribute("type") === "password" ? "text" : "password";
                    confirm_password.setAttribute("type", type)
                    this.classList.toggle("bi-eye");
                });
             
            
                $(function() {
                    $("#confirm_password").keyup(function() {
                        var password = $("#new_password").val();
                        $("#divCheckPasswordMatch").html(password == $(this).val() ? "Passwords match." : "Both passwords do not match!");
                    });
                });
            
				jQuery(document).ready(function () {
					$("#change_password_form").submit(function(ev) {
						ev.preventDefault(); 
						
						var score   = 0;

						var txtpass = $("#new_password").val();
						var confirmPass = $("#confirm_password").val();
						if(txtpass!=confirmPass){
							$("#error_message").html("Both Passwords do not match.")
							return;
						}
						
						var errormessage = "<b>Please select strong password.</b><br>";
						if (txtpass.length > 11) score++;
						else errormessage += "<li>Password Should be Minimum 12 Characters</li>";
					
						if ( ( txtpass.match(/[a-z]/) ) && ( txtpass.match(/[A-Z]/) ) ) score++;
						else errormessage += "<li>Password should contain at least one Capital and one Small Letter.</li>";
						
						if (txtpass.match(/\d+/)) score++;
						else errormessage += "<li>Password should contain at least one Numeric Character.</li>";
							
						if ( txtpass.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) ) score++;
						else errormessage += "<li>Password should contain at least one Special Character (!,@,#,$,%,^,&,*,?,_,~,-) .</li>";
							
						if (txtpass.length < 12) {
							$("#error_message").html("Password Should be Minimum 12 Characters")
							return;
						} else if (score < 4) {
							$("#error_message").html(errormessage);
							return;
						} else
							this.submit();
					});
				});
			</script>
	</body>
</html>';