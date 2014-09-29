<?php
define( 'WP_USE_THEMES', false );
require_once dirname(__FILE__).'/../../../wp-blog-header.php';
require_once dirname(__FILE__).'/contrib/PHPMailer/PHPMailerAutoload.php';
require_once dirname(__FILE__).'/contrib/snoopy/Snoopy.class.php';

if( current_user_can( DEVEL_CAPABILITY ) ){
	print_r( $_POST );
}

extract( $_POST );
$entry = get_event( $id );

if( !$id ) {
	$result = 'ID is missing.';
} else if( ( $type == 'email' && !$entry->contact_email ) || ( $type == 'sms' && !$entry->contact_phone ) ) {
	$result = 'Host contact is missing.';
} else if( !$sender ) {
	$result = 'Sender contact is required.';
} else if( ( $type == 'email' && !preg_match( '/^' . CONTACT_EMAIL_PATTERN . '$/', $entry->contact_email ) ) || ( $type == 'sms' && !preg_match( '/^' . CONTACT_PHONE_PATTERN . '$/', $entry->contact_phone ) ) ) {
	$result = 'Sender contact is invalid.';
} else if( !$message ) {
	$result = 'Message is required.';
} else {
	$m = 'Sending complete.';
	if( $type == 'email' ) {
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->SMTPAuth   = MAIL_OUTBOUNT_SMTP_AUTH;
		$mail->SMTPSecure = MAIL_OUTBOUND_SMTP_SECURE;
		$mail->Host       = MAIL_OUTBOUND_HOST;
		$mail->Port       = MAIL_OUTBOUND_PORT;
		$mail->Username   = MAIL_OUTBOUND_ID;
		$mail->Password   = MAIL_OUTBOUND_PASSWORD;
		$mail->SetFrom( $sender, '' );
		$mail->AddReplyTo( $sender, '' );
		$mail->Subject    = '=?UTF-8?B?'.base64_encode( $entry->post_title ) . '?=';
		$mail->AltBody    = $message;
		$mail->MsgHTML( nl2br( $message ) );
		$mail->AddAddress( $entry->contact_email, '' );
		$mail->Send();
		$result = $mail->ErrorInfo ? $mail->ErrorInfo : $m;
	} else if( $type == 'sms' ) {
		$guest_no = urlencode( SMS_OUTBOUND_ID );
		$guest_key = urlencode( SMS_OUTBOUND_PASSWORD );
		$message = urlencode( $message );
		$snoopy = new Snoopy;
		$cmd = "SendSms";
		$method = "GET";
		$url = SMS_OUTBOUND_HOST."?cmd=$cmd&method=$method&guest_no=$guest_no&guest_key=$guest_key&tran_phone=$entry->contact_phone&tran_callback=$sender&tran_date=$date&tran_msg=$message";
		$snoopy->fetchtext($url);
		$result = $snoopy->results == 'OK' ? $m : $snoopy->results;
	}
	if( current_user_can( DEVEL_CAPABILITY ) ){
		$object = $mail ? $mail : $snoopy;
		print_r( $object );
	}
}
header('Content-Type: text/html; charset=utf-8');
$result_raw = esc_attr( $result );
$result_i18n = esc_attr( __( $result, 'candlelights' ) );
echo <<<EOT
<script>
window.parent._check_contact('$result_raw', '$result_i18n');
</script>
EOT;
exit;
?>
