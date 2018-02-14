<?php
/**
 * Extension Configuration Script.
 *
 * Application Common
 */
if(!defined('TYPO3_MODE')){
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'AlexGunkel.' . $_EXTKEY,
    'fe_useradd',
    [
        'User' => 'addUser, submitUser, allowUser, activateUser, setPassword, existingUserError',
    ],
    [
        'User' => 'addUser, submitUser, allowUser, activateUser, setPassword',
    ]
);
