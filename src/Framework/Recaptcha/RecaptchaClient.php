<?php

namespace Framework\Recaptcha;

class RecaptchaClient
{
    protected $recaptcha;

    protected $recaptchaKey;

    public function __construct($recaptcha, $recaptchaKey)
    {
        $this->recaptcha = $recaptcha;
        $this->recaptchaKey = $recaptchaKey;
    }

    public function verified($expectedHostname, $expectedAction, $response, $ipAddress)
    {
        $resp = $this->recaptcha->setExpectedHostname($expectedHostname)
            ->setExpectedAction($expectedAction)
            ->verify($response, $ipAddress);

        return $resp->isSuccess();
    }

    public function getRecaptchaKey()
    {
        return $this->recaptchaKey;
    }
}
