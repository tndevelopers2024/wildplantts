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
        'name' => isset($_POST['name']) ? trim($_POST['name']) : '',
        'company' => isset($_POST['company']) ? trim($_POST['company']) : '',
        'designation' => isset($_POST['designation']) ? trim($_POST['designation']) : '',
        'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
        'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
        'location' => isset($_POST['location']) ? trim($_POST['location']) : '',
        'product' => isset($_POST['product']) ? trim($_POST['product']) : ''
    );
    
    // Basic validation
    if (empty($data['name']) || empty($data['email']) || empty($data['phone'])) {
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
        $mail->SMTPSecure = 'ssl'; 
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('precise3dmdre@gmail.com', 'WildplantTS - Demo Request');
        $mail->addAddress('marketing@precise3dm.com', 'Marketing Admin'); 
        $mail->addReplyTo($data['email'], $data['name']);

        // Set email format to HTML
        $mail->isHTML(true);
        $mail->Subject = 'New Demo Booking Request - ' . $data['company'];

        // Load the HTML template
        ob_start();
        include 'sotia-contact.phtml';
        $mail_body = ob_start() ? ob_get_clean() : '';
        if (!$mail_body) {
             ob_end_clean();
        }

        $mail->Body    = $mail_body;
        $mail->AltBody = "New Demo Request from: {$data['name']} | Company: {$data['company']} | Product: {$data['product']} | Email: {$data['email']} | Phone: {$data['phone']}";

        $mail->send();
        echo "<script>window.location.href='sotia-form.html?status=success';</script>";
        exit();
    } catch (Exception $e) {
        echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}'); window.history.back();</script>";
        exit();
    }
} else {
    // Not a POST request
    header("Location: sotia-form.html");
    exit();
}
?>
