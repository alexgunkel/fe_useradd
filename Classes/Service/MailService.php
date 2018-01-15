<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 15.01.18
 * Time: 23:51
 */

namespace AlexGunkel\FeUseradd\Service;


use AlexGunkel\FeUseradd\Domain\Model\ValidationMail;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MailService
{
    public function sendMailTo(ValidationMail $mail, string $address): void
    {
        /** @var MailMessage $message */
        $message = GeneralUtility::makeInstance(MailMessage::class);
        $message->setTo($address);
        $message->setBody((string) $mail);

        /** @var LogManager $logManager */
        $logManager = GeneralUtility::makeInstance(LogManager::class);
        $logger = $logManager->getLogger(__CLASS__);
        $int = $message->send();
        $logger->debug("Sent mail to $address and received return value $int. Body: $mail. Sent? " . $message->isSent());
    }
}