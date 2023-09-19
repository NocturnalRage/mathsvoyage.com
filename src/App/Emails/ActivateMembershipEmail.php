<?php

namespace App\Emails;

class ActivateMembershipEmail extends EmailMessage
{
    protected function prepareEmail(array $mailInfo)
    {
        $activateLink = $mailInfo['requestScheme'].'://'.
          $mailInfo['serverName'].
          '/activate-free-membership?token='.
          $mailInfo['token'];
        $toEmail = $mailInfo['toEmail'];
        $subject = 'Activate Your Free MathsVoyage.com Membership';

        // Now, put together the body of the email
        $bodyText = 'Hi '.htmlspecialchars($mailInfo['givenName'], ENT_QUOTES, 'UTF-8').",\n".
                "\nPlease click on the following link to activate your free MathsVoyage.com membership. As soon as you do you'll have access to all our free membership features.".
                "\n\n$activateLink".
                "\n\nKind regards,\nJeff Plumb.";

        $bodyTextHtml = 'Hi '.htmlspecialchars($mailInfo['givenName'], ENT_QUOTES, 'UTF-8').",<br /><br />Please click on the following button to activate your free MathsVoyage.com membership. As soon as you do you'll have access to all our free membership features.";

        $bodyHtml = $this->emailTemplate->addEmailHeader('Activate Your Free MathsVoyage.com Membership');
        $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
        $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
        $bodyHtml .= $this->emailTemplate->addEmailButton('Activate Your Membership', $activateLink);
        $bodyHtml .= $this->emailTemplate->addEmailParagraph('You can also copy the following into your browser to activate your free membership:');
        $bodyHtml .= $this->emailTemplate->addEmailParagraph($activateLink);
        $bodyHtml .= $this->emailTemplate->addEmailParagraph('Regards,<br />Jeff Plumb.');
        $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
        $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('This email was generated from the MathsVoyage.com website.');

        $this->setToEmail($toEmail);
        $this->setSubject($subject);
        $this->setBodyText($bodyText);
        $this->setBodyHtml($bodyHtml);
    }
}
