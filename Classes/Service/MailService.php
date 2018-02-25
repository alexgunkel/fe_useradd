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
use TYPO3\CMS\Fluid\View\StandaloneView;

class MailService
{
    /**
     * @var object|StandaloneView
     */
    private $view;

    private const TEMPLATE = [
        ValidationMail::STATUS_VALIDATE => 'Email/AllowUser',
        ValidationMail::STATUS_INFORM   => 'Email/SendUserInfo',
    ];

    public function __construct(StandaloneView $standaloneView = null)
    {
        if (null === $standaloneView) {
            $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
            $standaloneView->setLayoutRootPaths(
                array(GeneralUtility::getFileAbsFileName('EXT:fe_useradd/Resources/Private/Layouts'))
            );
            $standaloneView->setPartialRootPaths(
                array(GeneralUtility::getFileAbsFileName('EXT:fe_useradd/Resources/Private/Partials'))
            );
            $standaloneView->setTemplateRootPaths(
                array(GeneralUtility::getFileAbsFileName('EXT:fe_useradd/Resources/Private/Templates'))
            );
        }

        $standaloneView->setFormat('txt');
        $this->view = $standaloneView;
    }

    public function sendMailTo(ValidationMail $mail, string $address): void
    {
        /** @var MailMessage $message */
        $message = GeneralUtility::makeInstance(MailMessage::class);
        $message->setSubject($mail->getSubject());
        $message->setTo($address);
        $message->setBody($this->renderEmailBody($mail));

        /** @var LogManager $logManager */
        $logManager = GeneralUtility::makeInstance(LogManager::class);
        $logger = $logManager->getLogger(__CLASS__);
        $int = $message->send();
        $logger->debug("Sent mail to $address and received return value $int. Body: $mail. Sent? " . $message->isSent());
    }

    private function renderEmailBody(ValidationMail $mail): string
    {

        $this->view->setTemplate(self::TEMPLATE[$mail->getStatus()]);
        $this->view->assignMultiple(
            [
                'user' => $mail->getFeUser(),
                'link' => $mail->getValidationLink(),
            ]
        );

        return $this->view->render();
    }
}