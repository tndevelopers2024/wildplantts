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
        <title>New 3D Model Quote Request</title>
      </head>
      <body style="margin:0;background:#f3f4f6;padding:20px;">
        <div style="<?= $wrap ?>">
          <div style="<?= $head ?>">New 3D Model Quote Request</div>

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
              <th style="<?= $th ?>">Email</th>
              <td style="<?= $td ?>"><?= $v('email') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Phone</th>
              <td style="<?= $td ?>"><?= $v('phone') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Project Type</th>
              <td style="<?= $td ?>"><?= $v('project_type') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Total Site Area</th>
              <td style="<?= $td ?>"><?= $v('site_area') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Have Scan Data?</th>
              <td style="<?= $td ?>"><?= $v('have_scan_data') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Required LOD</th>
              <td style="<?= $td ?>"><?= $v('lod') ?></td>
            </tr>
            <tr>
              <th style="<?= $th ?>">Additional Requirements</th>
              <td style="<?= $td ?>"><?= nl2br($v('requirements')) ?></td>
            </tr>
          </table>

          <div style="<?= $foot ?>">
            <p style="<?= $p ?>">This email was generated from the Get a Quote 3D Model form.</p>
          </div>
        </div>
      </body>
    </html>
    <?php
    return ob_get_clean();
}

if ($_POST) {
    // Process LOD array into a comma-separated string if it exists
    $lodString = '';
    if (isset($_POST['lod']) && is_array($_POST['lod'])) {
        $lodString = implode(', ', $_POST['lod']);
    } elseif (isset($_POST['lod'])) {
        $lodString = $_POST['lod']; // Just in case it comes as string somehow
    }

    // Collect fields from your form
    $data = [
        'full_name'      => $_POST['full_name']      ?? '',
        'company_name'   => $_POST['company_name']   ?? '',
        'email'          => $_POST['email']          ?? '',
        'phone'          => $_POST['phone']          ?? '',
        'project_type'   => $_POST['project_type']   ?? '',
        'site_area'      => $_POST['site_area']      ?? '',
        'have_scan_data' => $_POST['have_scan_data'] ?? '',
        'lod'            => $lodString,
        'requirements'   => $_POST['requirements']   ?? '',
    ];

    $body = render_email($data);

    $subject = "New 3D Model Quote Request • " . ($data['full_name'] ?: 'Website');
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
    $mail->setFrom($from, 'Website Quote Form');
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
                window.location.href='get-a-quote-3d-model.html';
              </script>";
    } catch (Exception $e) {
        $_SESSION['status'] = "failure";
        echo "<script>
                alert('❌ Message could not be sent. Please try again later.');
                window.location.href='get-a-quote-3d-model.html';
              </script>";
    }
}

exit;
