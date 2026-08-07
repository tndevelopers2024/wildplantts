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
	include "wildplant-contact.phtml";
	return ob_get_contents();
}

if ($_POST) {
	// Form one
	$data['name'] = isset($_POST['name']) ? $_POST['name'] : '';
	$data['company'] = isset($_POST['company']) ? $_POST['company'] : '';
	$data['address'] = isset($_POST['address']) ? $_POST['address'] : '';
	$data['GST'] = isset($_POST['GST']) ? $_POST['GST'] : '';
	$data['email'] = isset($_POST['email']) ? $_POST['email'] : '';
	$data['phone'] = isset($_POST['phone']) ? $_POST['phone'] : '';
	$data['city'] = isset($_POST['city']) ? $_POST['city'] : '';
	$data['state'] = isset($_POST['state']) ? $_POST['state'] : '';


	// Form two
	$data['sqft'] = isset($_POST['sqft']) ? $_POST['sqft'] : '';
	$data['sqmt'] = isset($_POST['sqmt']) ? $_POST['sqmt'] : '';
	$data['typearea'] = isset($_POST['typearea']) ? $_POST['typearea'] : '';
	$data['Location'] = isset($_POST['Location']) ? $_POST['Location'] : '';

	// Form Three
	$data['yes'] = isset($_POST['yes']) ? $_POST['yes'] : '';
	$data['modeling'] = isset($_POST['modeling']) ? $_POST['modeling'] : '';
	$data['deliverables'] = isset($_POST['deliverables']) ? $_POST['deliverables'] : '';
	$data['LOD'] = isset($_POST['LOD']) ? $_POST['LOD'] : '';


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
header("Location: success.html");
exit;
?>