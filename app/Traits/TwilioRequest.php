<?php
namespace App\Traits;
use Twilio\Rest\Client;

trait TwilioRequest{



    protected function Otpsend($phone_number){

        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $twilio = new Client($twilio_sid, $token);
        $twilio->verify->v2->services($twilio_verify_sid)
            ->verifications
            ->create($phone_number, "sms");

    }

    protected function Otpverify($verification_code , $phone_number){

        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $twilio = new Client($twilio_sid, $token);
        $verification = $twilio->verify->v2->services($twilio_verify_sid)
        ->verificationChecks
        ->create(['code' => $verification_code, 'to' => $phone_number]);
        return  $verification;


    }
}
