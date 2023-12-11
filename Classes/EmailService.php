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
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Username = 'apikey';
        $mail->Password = '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';
        $mail->SetFrom('noreply@rgsteknoloji.com.tr', $title);
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