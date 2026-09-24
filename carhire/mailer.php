<?php
/**
 * Simple email helper using PHP mail()
 * For production, consider PHPMailer with SMTP (Gmail, SendGrid, etc.)
 */

function sendEmail($to, $subject, $htmlBody, $fromName = 'DriveElite') {
    $fromEmail = 'noreply@driveelite.com';
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: {$fromName} <{$fromEmail}>\r\n";
    $headers .= "Reply-To: info@michael.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    return @mail($to, $subject, $htmlBody, $headers);
}

function sendBookingConfirmation($booking, $customer) {
    $subject = "Booking Confirmed - DriveElite #" . str_pad($booking['id'], 6, '0', STR_PAD_LEFT);

    $html = "
    <html>
    <body style='font-family: Arial, sans-serif; background: #f9fafb; padding: 20px;'>
        <div style='max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);'>
            <h1 style='color: #1a3a5c; margin-top: 0;'>✅ Booking Confirmed!</h1>
            <p>Hi <strong>{$customer['full_name']}</strong>,</p>
            <p>Thank you for choosing DriveElite. Your booking has been received and confirmed.</p>

            <div style='background: #f9fafb; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                <h3 style='color: #1a3a5c; margin-top: 0;'>Booking Details</h3>
                <p><strong>Booking ID:</strong> #" . str_pad($booking['id'], 6, '0', STR_PAD_LEFT) . "</p>
                <p><strong>Vehicle:</strong> {$booking['brand']} {$booking['model']}</p>
                <p><strong>Hire Date:</strong> {$booking['hire_date']}</p>
                <p><strong>Return Date:</strong> {$booking['return_date']}</p>
                <p style='font-size: 1.4em; color: #2563eb;'><strong>Total: $" . number_format($booking['total_amount'], 2) . "</strong></p>
            </div>

            <p>Our team will contact you within 24 hours to confirm pickup details.</p>
            <p>Questions? Call us at <a href='tel:+254746674121'>+254 746 674 121</a>.</p>

            <p style='color: #6b7280; font-size: 0.9em; margin-top: 30px;'>— The DriveElite Team</p>
        </div>
    </body>
    </html>";

    return sendEmail($customer['email'], $subject, $html);
}

function sendAdminNotification($booking, $customer) {
    $subject = "🚗 New Booking #" . str_pad($booking['id'], 6, '0', STR_PAD_LEFT);
    $html = "
    <h2>New Booking Received</h2>
    <p><strong>Customer:</strong> {$customer['full_name']}</p>
    <p><strong>Email:</strong> {$customer['email']}</p>
    <p><strong>Phone:</strong> {$customer['phone']}</p>
    <p><strong>Vehicle:</strong> {$booking['brand']} {$booking['model']}</p>
    <p><strong>Dates:</strong> {$booking['hire_date']} → {$booking['return_date']}</p>
    <p><strong>Total:</strong> $" . number_format($booking['total_amount'], 2) . "</p>
    <p><a href='https://yourdomain.com/admin/manage_bookings.php'>View in Admin Panel</a></p>
    ";

    return sendEmail('info@michael.com', $subject, $html);
}
?>