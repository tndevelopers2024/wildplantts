<?php
session_start();
ini_set('upload_max_filesize', '40000M');
ini_set('post_max_size', '40000M');
ini_set('max_input_time', 300000);
ini_set('max_execution_time', '-1');

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
function render_email($email, $data)
{
	ob_start();
	include "contact-form.phtml";
	return ob_get_contents();
}

if ($_POST) {
    $data['email'] = isset($_POST['email']) ? $_POST['email'] : '';
    $data['username'] = isset($_POST['username']) ? $_POST['username'] : '';
    $data['phone'] = isset($_POST['phone']) ? $_POST['phone'] : '';
    $data['companyname'] = isset($_POST['companyname']) ? $_POST['companyname'] : '';
    $data['ephone'] = isset($_POST['ephone']) ? $_POST['ephone'] : '';
    $data['message'] = isset($_POST['message']) ? $_POST['message'] : '';

	$body = render_email('email', $data);

	$subject = "You have a message from your client from contact us";
    $to = "marketing@precise3dm.com";
    //$to = "prasannakanthan@gmail.com";
	$from = "precise3dmdre@gmail.com";

	$mail = new PHPMailer\PHPMailer\PHPMailer(); 

	$mail->SMTPDebug = false;
	$mail->isSMTP();
	$mail->Host = "smtp.gmail.com";
	$mail->SMTPAuth = true;
    $mail->Username = "precise3dmdre@gmail.com";                 
	$mail->Password = "nctdxqoxvqhflyaj";    
	$mail->SMTPSecure = "ssl";
	$mail->Port = 465;
	$mail->From = $from;
	$mail->FromName = "contact us details";

	$mail->addAddress($to); 
	$mail->isHTML(true);

	$mail->Subject = $subject;
	$mail->Body = $body;


	try {
		$mail->send();
		echo "sent";
		$_SESSION['status'] = "success";
	} catch (Exception $e) {
		print_r(error_get_last());
		echo "Error: Message not accepted";
		$_SESSION['status'] = "failure";
	}

}
// header("Location: index.html");
exit;
?>