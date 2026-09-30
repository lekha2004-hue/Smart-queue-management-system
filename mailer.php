<?php
// Paths linking to your manual PHPMailer files setup
require __DIR__ . '/libs/PHPMailer/Exception.php';
require __DIR__ . '/libs/PHPMailer/PHPMailer.php';
require __DIR__ . '/libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * 1. Alerts the user currently being called to the counter.
 */
function sendQueueEmail($recipientEmail, $recipientName, $tokenNumber, $counterDepartment) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                       
        $mail->SMTPAuth   = true;                                   
        // FIXED: Make sure to replace these with your actual operational Gmail configuration details
        $mail->Username   = 'lekhashreeh@gmail.com';       
        $mail->Password   = 'gznk kqrp fbfh pjac';    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
        $mail->Port       = 587;                                    

        $mail->setFrom('lekhashreeh@gmail.com', 'Smart Queue System');
        $mail->addAddress($recipientEmail, $recipientName);         

        $mail->isHTML(true);                                        
        $mail->Subject = "Your Token #{$tokenNumber} is Now Active!";
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 25px; border: 1px solid #e2e8f0; border-radius: 8px; max-width: 500px;'>
                <h2 style='color: #2c3e50; margin-top: 0;'>🔔 Counter Service Alert</h2>
                <p>Hello <strong>" . htmlspecialchars($recipientName) . "</strong>,</p>
                <div style='background-color: #ebf5fb; border-left: 5px solid #3498db; padding: 15px; margin: 20px 0;'>
                    <p style='margin: 0; font-size: 18px; color: #2980b9; font-weight: bold;'>
                        Your token #{$tokenNumber} is now being served!
                    </p>
                    <p style='margin: 5px 0 0 0; font-size: 14px; color: #4a5568;'>
                        Please proceed immediately to: <strong>" . htmlspecialchars($counterDepartment) . "</strong>
                    </p>
                </div>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer Call Error: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * 2. Alerts waiting users when their position advances in line.
 */
// FIXED: Added $counterDepartment to the parameters list so it is accessible inside the HTML body below
function sendPositionUpdateEmail($recipientEmail, $recipientName, $tokenNumber, $currentPosition, $counterDepartment) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                       
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'lekhashreeh@gmail.com';       
        $mail->Password   = 'gznk kqrp fbfh pjac';    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
        $mail->Port       = 587;                                    

        $mail->setFrom('lekhashreeh@gmail.com', 'Smart Queue System');
        $mail->addAddress($recipientEmail, $recipientName);         

        $mail->isHTML(true);                                        
        $mail->Subject = "Proceed to Counter: Your Token #{$tokenNumber} is Next!";
        
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 25px; border: 1px solid #e2e8f0; border-radius: 8px; max-width: 500px; margin: 0 auto; background-color: #ffffff;'>
                <h2 style='color: #2c3e50; margin-top: 0; border-bottom: 1px solid #edf2f7; padding-bottom: 10px;'>🚀 Counter Entry Alert</h2>
                <p style='color: #4a5568; font-size: 16px;'>Hello <strong>" . htmlspecialchars($recipientName) . "</strong>,</p>
                
                <div style='background-color: #e8f8f5; border-left: 5px solid #2ecc71; padding: 15px; margin: 20px 0; border-radius: 0 4px 4px 0;'>
                    <p style='margin: 0; font-size: 20px; color: #27ae60; font-weight: bold;'>
                        Your token #{$tokenNumber} is up! You can go in now. (Queue Position: #{$currentPosition})
                    </p>
                    <p style='margin: 8px 0 0 0; font-size: 15px; color: #4a5568;'>
                        Please proceed immediately to: <strong>" . htmlspecialchars($counterDepartment) . "</strong>
                    </p>
                </div>
                
                <p style='color: #7f8c8d; font-size: 12px; margin-top: 25px; text-align: center; border-top: 1px solid #edf2f7; padding-top: 15px; margin-bottom: 0;'>
                    This is an automated system message. Please do not reply directly to this email.
                </p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer Position Update Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>