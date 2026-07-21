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
	include "3d-bim-scanning-form.phtml";
	return ob_get_contents();
}

if ($_POST) {
    $data['name'] = isset($_POST['name']) ? $_POST['name'] : '';
    $data['email'] = isset($_POST['email']) ? $_POST['email'] : '';
    $data['phone'] = isset($_POST['phone']) ? $_POST['phone'] : '';
    $data['company'] = isset($_POST['company']) ? $_POST['company'] : '';
    $data['project'] = isset($_POST['project']) ? $_POST['project'] : '';
	$data['upload_link'] = '';

	/*Upload Function*/
	if(isset($_FILES['uploadscan']) && $_FILES['uploadscan']['size'] > 0)
	{
	   

	  $uploadfile_name=$_FILES["uploadscan"]["name"];

	  $filename   = uniqid() . "-" . time();

	  $extension  = pathinfo( $_FILES["uploadscan"]["name"], PATHINFO_EXTENSION );

	  $filename = $filename.".".$extension;

	  $folder="Documents";

  		if (!is_dir($folder)) {
    		mkdir($folder, 0777, true);
		}

	  move_uploaded_file($_FILES["uploadscan"]["tmp_name"], $folder."/".$filename);

	  $data['upload_link'] = "https://wildplantts.com/"."/".$folder."/".$filename;
	}


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