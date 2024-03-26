<?php
class Email {
    public static function send($to, $message, $cc, $from = null) {
        $headers = "From: $from\r\n";
        $headers .= "Reply-To: $from\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
        
        // Send the email
        @mail($to, "Error in backend", $message, $headers);
    }
}