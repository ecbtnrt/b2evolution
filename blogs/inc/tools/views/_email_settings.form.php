<?php
/**
 * This file implements the UI view for the general settings.
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link http://sourceforge.net/projects/evocms/}.
 *
 * @copyright (c)2003-2014 by Francois Planque - {@link http://fplanque.com/}
 * Parts of this file are copyright (c)2004-2006 by Daniel HAHLER - {@link http://thequod.de/contact}.
 *
 * {@internal License choice
 * - If you have received this file as part of a package, please find the license.txt file in
 *   the same folder or the closest folder above for complete license terms.
 * - If you have received this file individually (e-g: from http://evocms.cvs.sourceforge.net/)
 *   then you must choose one of the following licenses before using the file:
 *   - GNU General Public License 2 (GPL) - http://www.opensource.org/licenses/gpl-license.php
 *   - Mozilla Public License 1.1 (MPL) - http://www.opensource.org/licenses/mozilla1.1.php
 * }}
 *
 * {@internal Open Source relicensing agreement:
 * Daniel HAHLER grants Francois PLANQUE the right to license
 * Daniel HAHLER's contributions to this file and the b2evolution project
 * under any OSI approved OSS license (http://www.opensource.org/licenses/).
 *
 * Halton STEWART grants Francois PLANQUE the right to license
 * Halton STEWART's contributions to this file and the b2evolution project
 * under any OSI approved OSS license (http://www.opensource.org/licenses/).
 * }}
 *
 * @package admin
 *
 * @version $Id: _email_settings.form.php 8188 2015-02-07 02:07:55Z fplanque $
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


/**
 * @var User
 */
global $current_User;
/**
 * @var GeneralSettings
 */
global $Settings;

global $baseurl, $admin_url;

global $repath_test_output, $action;


$Form = new Form( NULL, 'settings_checkchanges' );

$Form->begin_form( 'fform' );

$Form->add_crumb( 'emailsettings' );
$Form->hidden( 'ctrl', 'email' );
$Form->hidden( 'tab', 'settings' );
$Form->hidden( 'tab3', get_param( 'tab3' ) );
$Form->hidden( 'action', 'settings' );

if( $Settings->get( 'smtp_enabled' ) )
{ // Only when SMTP gateway is enabled
	$Form->begin_fieldset( T_( 'Email service preferences' ).get_manual_link( 'email-service-preferences' ) );

	$Form->radio( 'email_service', $Settings->get( 'email_service' ), array(
				array( 'mail', T_('Regular PHP "mail" function'), ),
				array( 'smtp', T_('SMTP gateway'), ),
			), T_('Preferred email service'), true );
	$Form->checkbox( 'force_email_sending', $Settings->get( 'force_email_sending' ), T_('Force email sending'), T_('If the preferred email service is not available, the secondary option will be used.') );

	$Form->end_fieldset();
}

$Form->begin_fieldset( T_( 'Email notifications' ).get_manual_link( 'email-notification-settings' ) );
	// Set notes for notifications sender settings which shows the users custom settings information
	$notification_sender_email_note = '';
	$notification_sender_name_note = '';
	if( $current_User->check_perm( 'users', 'edit' ) )
	{ // Show infomration and action buttons only for users with edit users permission
		$users_url = url_add_param( $admin_url, 'ctrl=users&filter=new', '&' );
		$redirect_to = rawurlencode( regenerate_url( '', '', '', '&' ) );
		$remove_customization_url = url_add_param( $admin_url, 'ctrl=users&action=remove_sender_customization&'.url_crumb( 'users' ), '&' );
		$remove_customization = ' - <a href="%s" class="ActionButton" style="float:none">'.T_('remove customizations').'</a>';

		$notification_sender_email = $Settings->get( 'notification_sender_email' );
		$custom_sender_email_count = count_users_with_custom_setting( 'notification_sender_email', $notification_sender_email );
		if( $custom_sender_email_count > 0 )
		{ // There are users with custom sender email settings
			$sender_email_remove_customization = sprintf( $remove_customization, url_add_param( $remove_customization_url, 'type=sender_email&redirect_to='.$redirect_to, '&' ) );
			$notification_sender_email_note = get_icon( 'warning_yellow' ).' '.sprintf( T_('<a href="%s">%d users</a> have different custom address'), url_add_param( $users_url, 'custom_sender_email=1', '&' ), $custom_sender_email_count ).$sender_email_remove_customization;
		}

		$notification_sender_name = $Settings->get( 'notification_sender_name' );
		$custom_sender_name_count = count_users_with_custom_setting( 'notification_sender_name', $notification_sender_name );
		if( $custom_sender_name_count > 0 )
		{ // There are users with custom sender name settings
			$sender_name_remove_customization = sprintf( $remove_customization, url_add_param( $remove_customization_url, 'type=sender_name&redirect_to='.$redirect_to, '&' ) );
			$notification_sender_name_note = get_icon( 'warning_yellow' ).' '.sprintf( T_('<a href="%s">%d users</a> have different custom name'), url_add_param( $users_url, 'custom_sender_name=1', '&' ), $custom_sender_name_count ).$sender_name_remove_customization;
		}
	}

	// Display settings input fields
	$Form->text_input( 'notification_sender_email', $Settings->get( 'notification_sender_email' ), 50, T_( 'Sender email address' ), $notification_sender_email_note, array( 'maxlength' => 127, 'required' => true ) );
	$Form->text_input( 'notification_sender_name', $Settings->get( 'notification_sender_name' ), 50, T_( 'Sender name' ), $notification_sender_name_note, array( 'maxlength' => 127, 'required' => true ) );
	$Form->text_input( 'notification_return_path', $Settings->get( 'notification_return_path' ), 50, T_( 'Return path' ), '', array( 'maxlength' => 127, 'required' => true ) );
	$Form->text_input( 'notification_short_name', $Settings->get( 'notification_short_name' ), 50, T_( 'Short site name' ), T_('Shared with site settings'), array( 'maxlength' => 127, 'required' => true ) );
	$Form->text_input( 'notification_long_name', $Settings->get( 'notification_long_name' ), 50, T_( 'Long site name' ), T_('Shared with site settings'), array( 'maxlength' => 255 ) );
	$Form->text_input( 'notification_logo', $Settings->get( 'notification_logo' ), 50, T_( 'Site logo (URL)' ), T_('Shared with site settings'), array( 'maxlength' => 5000 ) );
$Form->end_fieldset();

if( $current_User->check_perm( 'emails', 'edit' ) )
{
	$Form->end_form( array( array( 'submit', '', T_('Save Changes!'), 'SaveButton' ) ) );
}

?>