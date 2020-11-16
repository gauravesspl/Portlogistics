<?php

namespace App\Libraries;

use App\Repositories\SendEmail\SendEmailRepository;
use App\Services\SendEmailService;
use Carbon\Carbon;

class SendEmailLibrary
{

    protected $sendEmailRepository;
    define("ERRORMSG", "something went wrong");
    public function __construct(SendEmailRepository $sendEmailRepository, SendEmailService $sendEmailService)
    {
        $this->sendEmailRepository = $sendEmailRepository;
        $this->sendEmailService = $sendEmailService;
    }

    /**
     * @description Generate random token If token time is expired
     * @author Gaurav Agrawal
     * @return string token
     */
    public function generateRandomToken()
    {
        return uniqid();
    }

    /**
     * @description send email
     * @author Gaurav Agrawal
     * @param array ['user_id','name','email','template'] 
     * @return array ['status','message']
     */
    public function modify($email_data)
    {
        $checkSendEmail = $this->sendEmailRepository->checkSendEmail($email_data['user_id']);
        if ($checkSendEmail['isError']) {
            return ['status' => 'failed', 'message' => ERRORMSG];
        }
        $view = $this->sendEmailService->generateEmailTemplate($email_data['template']);
        $heading = $this->sendEmailService->generateEmailHeading($email_data['template']);
        if (!$checkSendEmail['isError'] && !empty($checkSendEmail['data'])) 
        {
           $diff = $this->calculateDifferenceSeconds($checkSendEmail['data']->updated_at);
            if ($diff <= config('constants.email_resend_time')) 
            { //If diff between now and last updated at <= email resend time
                return ['status' => 'success', 'message' => 'Please check your inbox'];
            } else 
            { //If diff between now and last updated at > email resend time
                if($diff >= config('constants.email_token_expiry_time')) 
                { //If token time is expired, generate a new token
                    $email_data['token'] = $this->generateRandomToken();
                    $updatestatus = $this->sendEmailRepository->updateSendEmail($email_data);
                    if (!$updatestatus) 
                    {
                        $errorMsg = 'something went wrong';
                        return ['status' => 'failed', 'message' =>$errorMsg];
                    }
                } else
                { //If token time is not expired, send the same token again
                    $email_data['token'] = $checkSendEmail['data']->token;
                }
            }
        }
        if (!$checkSendEmail['isError'] && empty($checkSendEmail['data'])) 
        {
            $email_data['token'] = $this->generateRandomToken();
            $status = $this->sendEmailRepository->saveSendEmail($email_data);
            if (!$status) 
            {
                return ['status' => 'failed', 'message' => 'something went wrong'];
            }
        }
        $email_data['view'] = $view;
        $email_data['heading'] = $heading;
        $sendEmail = $this->sendEmailService->sendEmail($email_data);
        if (empty($sendEmail)) {
            return ['message' => 'Mail Sent Sucssfully', 'status' => 'success'];
        } else {
            return ['message' => 'Mail Sent fail', 'status' => 'failed'];
        }
    }

    /**
     * @description difference in seconds from now when last Email sent
     * @author Gaurav Agrawal
     * @param datetime updated_at 
     * @return integer seconds
     */
    public function calculateDifferenceSeconds($updated_at)
    {
            return $updated_at->diffInSeconds(now());
    }
}
