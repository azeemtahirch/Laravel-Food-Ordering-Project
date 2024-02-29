<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;


class VerifyOtpMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $phone;
    private $verification_code;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($phone,$verification_code)
    {
        $this->phone = $phone;
        $this->verification_code = $verification_code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $twilio = new Client($twilio_sid, $token);
        $verification = $twilio->verify->v2->services($twilio_verify_sid)
        ->verificationChecks
        ->create(['code' => $this->verification_code, 'to' =>  $this->phone]);
        if ($verification->valid) {
            User::where('phone', $this->phone)->update(['isVerified' => true]);
            $phoneNumber =  $this->phone;
            return response()->json([
                'message' =>'Phone number verified',
                'phone' => $phoneNumber,
                ],200);
                               }

    }
}
