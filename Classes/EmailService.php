<?php
/**
 * 11.12.2023
 * 13:37
 * Prepared by Buğra Şıkel @bugraskl
 * https://www.bugra.work/
 */

namespace EmailService;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class EmailService
{
    public function sendMail($title, $mailContent, $reciver, $file = NULL)
    {
        $baslik = $title;
        $icerik = $mailContent;
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAutoTLS = false;
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.sendgrid.net';
        $mail->Port = 465;
        $mail->SMTPSecure = 'ssl';
        $mail->Username = 'apikey';
        $mail->Password = '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';
        $mail->SetFrom('noreply@pratikhesap.com', $title);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $baslik;
        $content = $icerik;
        if (isset($file)) {
            $mail->addAttachment($file);
        }
        $mail->MsgHTML($content);
        $adress = $reciver;
        $mail->AddAddress($adress);
        if ($mail->Send()) {

        } else {
            echo $mail->ErrorInfo;
        }
    }
}
