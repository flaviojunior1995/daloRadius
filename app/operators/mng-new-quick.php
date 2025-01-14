<?php
/*
 *********************************************************************************************************
 * daloRADIUS - RADIUS Web Platform
 * Copyright (C) 2007 - Liran Tal <liran@enginx.com> All Rights Reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 *********************************************************************************************************
 *
 * Authors:    Liran Tal <liran@enginx.com>
 *             Filippo Lauria <filippo.lauria@iit.cnr.it>
 *
 *********************************************************************************************************
 */

    include("library/checklogin.php");
    $operator = $_SESSION['operator_user'];

    include('../common/includes/config_read.php');
    include('library/check_operator_perm.php');

    include_once("lang/main.php");
    include("../common/includes/validation.php");
    include("../common/includes/layout.php");
    include_once("include/management/functions.php");

    require_once('include/phpMailer/src/PHPMailer.php');
    require_once('include/phpMailer/src/SMTP.php');
    require_once('include/phpMailer/src/Exception.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    // init logging variables
    $log = "visited page: ";
    $logAction = "";
    $logDebugSQL = "";

    // if cleartext passwords are not allowed,
    // we remove Cleartext-Password from the $valid_passwordTypes array
    if (isset($configValues['CONFIG_DB_PASSWORD_ENCRYPTION']) &&
        strtolower(trim($configValues['CONFIG_DB_PASSWORD_ENCRYPTION'])) !== 'yes') {
        $valid_passwordTypes = array_values(array_diff($valid_passwordTypes, array("Cleartext-Password")));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (array_key_exists('csrf_token', $_POST) && isset($_POST['csrf_token']) && dalo_check_csrf_token($_POST['csrf_token'])) {

            // required later
            $currDate = date('Y-m-d H:i:s');
            $currBy = $operator;

            // TODO validate user input
            $username = (array_key_exists('username', $_POST) && isset($_POST['username']))
                      ? trim(str_replace("%", "", $_POST['username'])) : "";
            $username_enc = (!empty($username)) ? htmlspecialchars($username, ENT_QUOTES, 'UTF-8') : "";

            // search:  \$([A-Za-z0-9_]+)\s+=\s+\$_POST\[\'([A-Za-z0-9_]+)\'\];
            // replace: $\1 = (array_key_exists('\2', $_POST) && isset($_POST['\2'])) ? $_POST['\2'] : "";

            $password = (array_key_exists('password', $_POST) && isset($_POST['password'])) ? trim($_POST['password']) : "";
            $passwordType = (array_key_exists('passwordType', $_POST) && isset($_POST['passwordType']) &&
                             in_array($_POST['passwordType'], $valid_passwordTypes)) ? $_POST['passwordType'] : "";
            $groups = (array_key_exists('groups', $_POST) && isset($_POST['groups'])) ? $_POST['groups'] : array();
            $maxallsession = (array_key_exists('maxallsession', $_POST) && isset($_POST['maxallsession'])) ? $_POST['maxallsession'] : "";
            $expiration = (array_key_exists('expiration', $_POST) && isset($_POST['expiration'])) ? $_POST['expiration'] : "";
            $sessiontimeout = (array_key_exists('sessiontimeout', $_POST) && isset($_POST['sessiontimeout'])) ? $_POST['sessiontimeout'] : "";
            $idletimeout = (array_key_exists('idletimeout', $_POST) && isset($_POST['idletimeout'])) ? $_POST['idletimeout'] : "";
            $simultaneoususe = (array_key_exists('simultaneoususe', $_POST) && isset($_POST['simultaneoususe'])) ? $_POST['simultaneoususe'] : "";
            $framedipaddress = (array_key_exists('framedipaddress', $_POST) && isset($_POST['framedipaddress'])) ? $_POST['framedipaddress'] : "";

            // search:  isset\(\$_POST\[\'([A-Za-z0-9_]+)\'\]\)\s+\?\s+\$([A-Za-z0-9_]+).*
            // replace: $\2 = (array_key_exists('\1', $_POST) && isset($_POST['\1'])) ? $_POST['\1'] : "";

            $firstname = (array_key_exists('firstname', $_POST) && isset($_POST['firstname'])) ? $_POST['firstname'] : "";
            $lastname = (array_key_exists('lastname', $_POST) && isset($_POST['lastname'])) ? $_POST['lastname'] : "";
            $email = (array_key_exists('email', $_POST) && isset($_POST['email'])) ? $_POST['email'] : "";
            $department = (array_key_exists('department', $_POST) && isset($_POST['department'])) ? $_POST['department'] : "";
            $company = (array_key_exists('company', $_POST) && isset($_POST['company'])) ? $_POST['company'] : "";
            $workphone = (array_key_exists('workphone', $_POST) && isset($_POST['workphone'])) ? $_POST['workphone'] : "";
            $homephone = (array_key_exists('homephone', $_POST) && isset($_POST['homephone'])) ? $_POST['homephone'] : "";
            $mobilephone = (array_key_exists('mobilephone', $_POST) && isset($_POST['mobilephone'])) ? $_POST['mobilephone'] : "";
            $address = (array_key_exists('address', $_POST) && isset($_POST['address'])) ? $_POST['address'] : "";
            $city = (array_key_exists('city', $_POST) && isset($_POST['city'])) ? $_POST['city'] : "";
            $state = (array_key_exists('state', $_POST) && isset($_POST['state'])) ? $_POST['state'] : "";
            $country = (array_key_exists('country', $_POST) && isset($_POST['country'])) ? $_POST['country'] : "";
            $zip = (array_key_exists('zip', $_POST) && isset($_POST['zip'])) ? $_POST['zip'] : "";
            $notes = (array_key_exists('notes', $_POST) && isset($_POST['notes'])) ? $_POST['notes'] : "";

            // first we check user portal login password
            $ui_PortalLoginPassword = (isset($_POST['portalLoginPassword']) && !empty(trim($_POST['portalLoginPassword'])))
                                    ? trim($_POST['portalLoginPassword']) : "";

            // these are forced to 0 (disabled) if user portal login password is empty
            $ui_changeuserinfo = (!empty($ui_PortalLoginPassword) && isset($_POST['changeUserInfo']) && $_POST['changeUserInfo'] === '1')
                               ? '1' : '0';
            $ui_enableUserPortalLogin = (!empty($ui_PortalLoginPassword) && isset($_POST['enableUserPortalLogin']) && $_POST['enableUserPortalLogin'] === '1')
                                      ? '1' : '0';

            include('../common/includes/db_open.php');

            // check if username is already present in the radcheck table
            $userExists = user_exists($dbSocket, $username);

            if ($userExists) {
                $failureMsg = "user already exist in database: <b> $username_enc </b>";
                $logAction .= "Failed adding new user already existing in database [$username] on page: ";
            } else {

                // username and password are required
                if ( empty($username) || empty($password) || empty($email) ) {
                    $failureMsg = "Username and/or Password and/or Email are empty";
                    $logAction .= "Failed adding (possible empty user/pass) new user [$username] on page: ";
                } else {




                    // we "inject" specified attribute in the $_POST array.
                    // handleAttributes() - called later - will take care of it.
                    $injected_attribute = array();
                    // we record which attributes should be Reply instead of Check
                    $reply_attribute_list = array();

                    $injected_attribute[$passwordType] = $password;

                    if ($maxallsession) {
                        $injected_attribute['Max-All-Session'] = $maxallsession;
                    }

                    if ($expiration) {
                        $injected_attribute['Expiration'] = $expiration;
                    }

                    if ($sessiontimeout) {
                        $injected_attribute['Session-Timeout'] = $sessiontimeout;
                        $reply_attribute_list[] = "Session-Timeout";
                    }

                    if ($idletimeout) {
                        $injected_attribute['Idle-Timeout'] = $idletimeout;
                        $reply_attribute_list[] = "Idle-Timeout";
                    }

                    if ($simultaneoususe) {
                        $injected_attribute['Simultaneous-Use'] = $simultaneoususe;
                    }

                    if ($framedipaddress) {
                        $injected_attribute['Framed-IP-Address'] = $framedipaddress;
                        $reply_attribute_list[] = "Framed-IP-Address";
                    }

                     $i = 0;
                     foreach ($injected_attribute as $attribute => $value) {
                         $index = 'injected_attribute' . $i;
                         if (in_array($attribute, $reply_attribute_list)) {
                             $_POST[$index] = array( $attribute, $value, ':=', 'reply' );
                         } else {
                             $_POST[$index] = array( $attribute, $value, ':=', 'check' );
                         }
                         $i++;
                     }

                    include("library/attributes.php");
                    $skipList = array(
                                       "username", "password", "passwordType", "groups", "maxallsession", "expiration",
                                       "sessiontimeout", "idletimeout", "simultaneoususe", "framedipaddress",
                                       "firstname", "lastname", "email", "department", "company", "workphone", "homephone",
                                       "mobilephone", "address", "city", "state", "country", "zip", "notes", "changeuserinfo",
                                       "enableUserPortalLogin", "portalLoginPassword", "bi_contactperson", "bi_company",
                                       "bi_email", "bi_phone", "bi_address", "bi_city", "bi_state", "bi_country", "bi_zip",
                                       "bi_paymentmethod", "bi_cash", "bi_creditcardname", "bi_creditcardnumber",
                                       "bi_creditcardverification", "bi_creditcardtype", "bi_creditcardexp", "bi_notes",
                                       "bi_changeuserbillinfo", "csrf_token", "submit"
                                     );
                    $attributesCount = handleAttributes($dbSocket, $username, $skipList);

                    // check if any group should be added
                    $groupsCount = insert_multiple_user_group_mappings($dbSocket, $username, $groups);

                    // adding user info
                    $params = array(
                                        "firstname" => $firstname,
                                        "lastname" => $lastname,
                                        "email" => $email,
                                        "department" => $department,
                                        "company" => $company,
                                        "workphone" => $workphone,
                                        "homephone" => $homephone,
                                        "mobilephone" => $mobilephone,
                                        "address" => $address,
                                        "city" => $city,
                                        "state" => $state,
                                        "country" => $country,
                                        "zip" => $zip,
                                        "notes" => $notes,
                                        "changeuserinfo" => $ui_changeuserinfo,
                                        "enableportallogin" => $ui_enableUserPortalLogin,
                                        "portalloginpassword" => $ui_PortalLoginPassword,
                                        "creationdate" => $currDate,
                                        "creationby" => $currBy,
                                   );

                    $addedUserInfo = (add_user_info($dbSocket, $username, $params)) ? "stored" : "nothing to store";

					// Send e-mail afeter user creation
					$mail = new PHPMailer;
					$mail->isSMTP();
					$mail->Host = $configValues['CONFIG_MAIL_SMTPADDR'];
					$mail->Port = $configValues['CONFIG_MAIL_SMTPPORT'];
					$mail->SMTPAuth = true;
					$mail->SMTPSecure = "tls";
					$mail->SMTPOptions = array(
						'ssl' => array(
							'verify_peer' => false,
							'verify_peer_name' => false,
							'allow_self_signeed' => true
						)
					);
					$mail->Username = $configValues['CONFIG_MAIL_SMTPAUTH'];
					$mail->Password = $configValues['CONFIG_MAIL_SMTPPASS'];
					$mail->setFrom($configValues['CONFIG_MAIL_SMTPFROM'], '');
					$mail->addAddress($_POST['email'], '');
					if ($mail->addReplyTo($_POST['email'], $_POST['name'])) {
						$mail->IsHTML(true);
						$mail->Subject = 'RADIUS credentials';
						$mail->AddEmbeddedImage('../common/static/images/daloradius_small.jpg', 'logoimg', 'daloRadius.jpg' );
						$mail->Body = <<<EOT
						<p><img src="cid:logoimg" /></p>
						<h1>Radius Credentials</h1>
						User: {$_POST['username']} <br>
						Password: {$_POST['password']}
						EOT;
					}
					
					if (!$mail->send()) {
                    $successMsg = 'Inserted new <strong>user</strong>: '
                                . sprintf('<a href="mng-edit.php?username=%s" title="Edit">%s</a>', $username_enc, $username_enc)
                                . '<ul style="color: black">'
                                . sprintf("<li><strong>attributes count</strong>: %d</li>", $attributesCount)
                                . sprintf("<li><strong>groups count</strong>: %d</li>", $groupsCount)
                                . sprintf("<li><strong>user info</strong>: %s</li>", $addedUserInfo)
                                . sprintf("<li><strong>e-mail send to $email</strong>: failure</li>")
                                . "</ul>";
					} else {
                    $successMsg = 'Inserted new <strong>user</strong>: '
                                . sprintf('<a href="mng-edit.php?username=%s" title="Edit">%s</a>', $username_enc, $username_enc)
                                . '<ul style="color: black">'
                                . sprintf("<li><strong>attributes count</strong>: %d</li>", $attributesCount)
                                . sprintf("<li><strong>groups count</strong>: %d</li>", $groupsCount)
                                . sprintf("<li><strong>user info</strong>: %s</li>", $addedUserInfo)
                                . sprintf("<li><strong>e-mail send to $email</strong>: successfully</li>")
                                . "</ul>";
					}

                    $logAction .= sprintf("Successfully inserted new user [%s] on page: ", $username);
					


                } // if (empty($username) || empty($password)) {

            } // if ($userExists) {

            include('../common/includes/db_close.php');
        } else {
            // csrf
            $failureMsg = "CSRF token error";
            $logAction .= "$failureMsg on page: ";
        }
    }

    $hiddenPassword = (strtolower($configValues['CONFIG_IFACE_PASSWORD_HIDDEN']) == "yes")
                    ? 'password' : 'text';


    // print HTML prologue
    $extra_css = array();

    $extra_js = array(
        "static/js/ajax.js",
        "static/js/ajaxGeneric.js",
        "static/js/productive_funcs.js",
    );

    $title = t('Intro','mngnewquick.php');
    $help = t('helpPage','mngnewquick');

    print_html_prologue($title, $langCode, $extra_css, $extra_js);

    print_title_and_help($title, $help);

    include_once('include/management/actionMessages.php');

    // set navbar stuff
    $navkeys = array( 'AccountInfo', 'UserInfo' );

    // print navbar controls
    print_tab_header($navkeys);

    open_form();

    // open tab wrapper
    open_tab_wrapper();

    // open 0-th tab (shown)
    open_tab($navkeys, 0, true);

    // open 0-th fieldset
    $fieldset0_descriptor = array(
                                    "title" => t('title','AccountInfo'),
                                 );

    open_fieldset($fieldset0_descriptor);


    $input_descriptors0 = array();

    $input_descriptors0[] = array(
                                    "id" => "username",
                                    "name" => "username",
                                    "caption" => t('all','Username'),
                                    "type" => "text",
                                    "value" => "",
                                    "random" => false,
                                    "tooltipText" => t('Tooltip','usernameTooltip')
                                 );

    $input_descriptors0[] = array(
                                    "id" => "password",
                                    "name" => "password",
                                    "caption" => t('all','Password'),
                                    "type" => "password",
                                    "value" => "",
                                    "random" => true,
                                    "tooltipText" => t('Tooltip','passwordTooltip'),
									"readonly" => true
                                 );

    $input_descriptors0[] = array(
                                    "name" => "passwordType",
                                    "caption" => t('all','PasswordType'),
                                    "options" => $valid_passwordTypes,
                                    "type" => "hidden",
									"value" => "Cleartext-Password",
									"readonly" => true
                                );

    include_once('include/management/populate_selectbox.php');
    $options = get_groups();
    array_unshift($options, '');
    $input_descriptors0[] = array(
                                    "type" =>"select",
                                    "name" => "groups[]",
                                    "id" => "groups",
                                    "caption" => t('all','Group'),
                                    "options" => $options,
                                    "multiple" => true,
                                    "size" => 5,
                                    "selected_value" => ((isset($failureMsg)) ? $groups : ""),
                                    "tooltipText" => t('Tooltip','groupTooltip')
                                 );

    foreach ($input_descriptors0 as $input_descriptor) {
        print_form_component($input_descriptor);
    }

    close_fieldset();


    // open 1-th fieldset
    $fieldset1_descriptor = array(
                                 );

    open_fieldset($fieldset1_descriptor);

    $input_descriptors1 = array();

    $input_descriptors1[] = array(
                                    "name" => "csrf_token",
                                    "type" => "hidden",
                                    "value" => dalo_csrf_token(),
                                 );

    foreach ($input_descriptors1 as $input_descriptor) {
        print_form_component($input_descriptor);
    }

    $select_descriptors = array();

    foreach ($select_descriptors as $select_descriptor) {
        print_calculated_select($select_descriptor);
    }

    close_fieldset();

    $button_descriptor = array(
                                'type' => 'submit',
                                'name' => 'submit',
                                'value' => t('buttons','apply'),
                                'onclick' => 'javascript:small_window(document.newuser.username.value,
                                                                      document.newuser.password.value,
                                                                      document.newuser.maxallsession.value)'
                              );

    print_form_component($button_descriptor);

    close_tab($navkeys, 0);

    // open 1-th tab
    open_tab($navkeys, 1);

    $customApplyButton = sprintf('<input type="submit" name="submit" value="%s" ', t('buttons','apply'))
                       . 'onclick="javascript:small_window(document.newuser.username.value, '
                       . 'document.newuser.password.value, document.newuser.maxallsession.value);" '
                       . 'class="button">';
    include_once('include/management/userinfo.php');
	print_form_component($button_descriptor);

    close_tab($navkeys, 1);

    // close tab wrapper
    close_tab_wrapper();

    close_form();

    print_back_to_previous_page();

    include('include/config/logging.php');
    print_footer_and_html_epilogue();
	

?>