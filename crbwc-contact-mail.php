<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and collect POST data
    $data = array(
        'fullName' => isset($_POST['fullName']) ? trim($_POST['fullName']) : '',
        'companyName' => isset($_POST['companyName']) ? trim($_POST['companyName']) : '',
        'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
        'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
        'location' => isset($_POST['location']) ? trim($_POST['location']) : '',
        'siteType' => isset($_POST['siteType']) ? trim($_POST['siteType']) : '',
        'siteLocation' => isset($_POST['siteLocation']) ? trim($_POST['siteLocation']) : '',
        'siteArea' => isset($_POST['siteArea']) ? trim($_POST['siteArea']) : '',
        'multiFloors' => isset($_POST['multiFloors']) ? trim($_POST['multiFloors']) : '',
        'need3D' => isset($_POST['need3D']) ? trim($_POST['need3D']) : ''
    );
    
    // Basic validation
    if (empty($data['fullName']) || empty($data['email']) || empty($data['phone'])) {
        echo "<script>alert('Please fill out the required fields.'); window.history.back();</script>";
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'precise3dmdre@gmail.com';
        $mail->Password   = 'nctdxqoxvqhflyaj'; 
        $mail->SMTPSecure = 'ssl'; // Use PHPMailer::ENCRYPTION_SMTPS in newer versions, but ssl is standard here
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('precise3dmdre@gmail.com', 'WildplantTS - Website Request');
        $mail->addAddress('marketing@precise3dm.com', 'Marketing Admin'); 
        $mail->addReplyTo($data['email'], $data['fullName']);

        // Set email format to HTML
        $mail->isHTML(true);
        $mail->Subject = 'New 3D Scanning Quote Request - ' . $data['companyName'];

        // Load the HTML template
        ob_start();
        include 'crbwc-contact.phtml';
        $mail_body = ob_get_clean();

        $mail->Body    = $mail_body;
        $mail->AltBody = "New Quote Request from: {$data['fullName']} | Company: {$data['companyName']} | Email: {$data['email']} | Phone: {$data['phone']}";

        $mail->send();
        echo "<script>alert('✅ Your request has been sent successfully!'); window.location.href='get-a-quote-3d-scanning.html';</script>";
        exit();
    } catch (Exception $e) {
        echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}'); window.history.back();</script>";
        exit();
    }
} else {
    // Not a POST request
    header("Location: get-a-quote-3d-scanning.html");
    exit();
}
?>
