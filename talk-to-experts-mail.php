<?php
session_start();
ini_set('upload_max_filesize', '40000M');
ini_set('post_max_size', '40000M');
ini_set('max_input_time', 300000);
ini_set('max_execution_time', '-1');

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

/**
 * Build a tidy, email-safe HTML table
 */
function render_email(array $data): string {
    // Inline styles for max email client compatibility
    $wrap = "width:100%;max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #eaeaea;border-radius:6px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;";
    $head = "background:#111111;color:#ffffff;padding:18px 20px;font-size:18px;font-weight:bold;";
    $tbl  = "width:100%;border-collapse:collapse;";
    $th   = "text-align:left;background:#f7f7f7;width:38%;padding:10px 12px;border-bottom:1px solid #eee;font-weight:600;color:#222;";
    $td   = "padding:10px 12px;border-bottom:1px solid #eee;color:#333;";
    $foot = "padding:14px 20px;font-size:12px;color:#666;background:#fafafa;";
    $p    = "margin:0;line-height:1.5;";

    // Safe values
    $v = fn($k) => htmlspecialchars($data[$k] ?? '', ENT_QUOTES, 'UTF-8');

    ob_start(); ?>
    <!doctype html>
    <html>
      <head>
        <meta charset="utf-8">
        <meta name="color-scheme" content="light only">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Talk to Experts Request</title>
      </head>
      <body style="margin:0;background:#f3f4f6;padding:20px;">
        <div style="<?= $wrap ?>">
          <div style="<?= $head ?>">New Talk to Experts Request</div>

          <table role="presentation" style="<?= $tbl ?>">
            <tr>
              <th style="<?= $th ?>">Full Name</th>
              <td style="<?= $td ?>"><?= $v('full_name') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Company Name</th>
              <td style="<?= $td ?>"><?= $v('company_name') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Designation</th>
              <td style="<?= $td ?>"><?= $v('designation') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Email</th>
              <td style="<?= $td ?>"><?= $v('email') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Phone</th>
              <td style="<?= $td ?>"><?= $v('phone') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Location</th>
              <td style="<?= $td ?>"><?= $v('location') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Services Needed</th>
              <td style="<?= $td ?>"><?= $v('services') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Other Service</th>
              <td style="<?= $td ?>"><?= $v('other_service') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Project Location</th>
              <td style="<?= $td ?>"><?= $v('project_location') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Contact Method</th>
              <td style="<?= $td ?>"><?= $v('contact_method') ?></td>
            </tr>
          </table>

          <div style="<?= $foot ?>">
            <p style="<?= $p ?>">This email was generated from the Talk to Experts form.</p>
          </div>
        </div>
      </body>
    </html>
    <?php
    return ob_get_clean();
}

if ($_POST) {
    // Process services array into a comma-separated string if it exists
    $servicesString = '';
    if (isset($_POST['services']) && is_array($_POST['services'])) {
        $servicesString = implode(', ', $_POST['services']);
    } elseif (isset($_POST['services'])) {
        $servicesString = $_POST['services']; // Just in case it comes as string somehow
    }

    // Collect fields from your form
    $data = [
        'full_name'      => $_POST['full_name']      ?? '',
        'company_name'   => $_POST['company_name']   ?? '',
        'designation'    => $_POST['designation']    ?? '',
        'email'          => $_POST['email']          ?? '',
        'phone'          => $_POST['phone']          ?? '',
        'location'       => $_POST['location']       ?? '',
        'services'       => $servicesString,
        'other_service'  => $_POST['other_service']  ?? '',
        'project_location' => $_POST['project_location'] ?? '',
        'contact_method' => $_POST['contact_method'] ?? '',
    ];

    $body = render_email($data);

    $subject = "New Talk to Experts Request • " . ($data['full_name'] ?: 'Website');
    $to      = "marketing@precise3dm.com";     // receiver
    $from    = "precise3dmdre@gmail.com";      // sender (Gmail you SMTP with)

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->CharSet = 'UTF-8';

    // SMTP (like your sample)
    $mail->SMTPDebug  = false;
    $mail->isSMTP();
    $mail->Host       = "smtp.gmail.com";
    $mail->SMTPAuth   = true;
    $mail->Username   = "precise3dmdre@gmail.com";   // <-- your Gmail
    $mail->Password   = "nctdxqoxvqhflyaj";    // <-- use Gmail App Password
    $mail->SMTPSecure = "ssl";
    $mail->Port       = 465;

    // Headers
    $mail->setFrom($from, 'Website Talk to Experts Form');
    $mail->addAddress($to);
    if (!empty($data['email'])) {
        $mail->addReplyTo($data['email'], $data['full_name'] ?: $data['email']);
    }

    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    try {
        $mail->send();
        $_SESSION['status'] = "success";
        echo "<script>
                alert('✅ Your request has been sent successfully!');
                window.location.href='talk-to-experts.html';
              </script>";
    } catch (Exception $e) {
        $_SESSION['status'] = "failure";
        echo "<script>
                alert('❌ Message could not be sent. Please try again later.');
                window.location.href='talk-to-experts.html';
              </script>";
    }
}

exit;
