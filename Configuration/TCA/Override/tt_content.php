<?php
$extensionKey = 'fe_useradd';
$pluginName = 'ExamplePlugin';
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'AlexGunkel.' . $extensionKey,
    'fe_useradd',
    'Register User',
    null
);
