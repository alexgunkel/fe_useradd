<?php
/**
 * Meta configuration for LogBook-PHP Wrapper
 */
$EM_CONF[$_EXTKEY] = array (
    'title' => 'FE Useradd',
    'description' => 'TYPO3-Extension that allows fe_user-registration via form',
    'category' => 'plugin',
    'version' => '0.0.2',
    'state' => 'alpha',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearcacheonload' => 1,
    'author' => 'Alexander Gunkel',
    'author_email' => 'alexandergunkel@gmail.com',
    'author_company' => '',
    'constraints' =>
        array (
            'depends' =>
                array (
                    'typo3' => '7.6.00-8.7.99',
                ),
            'conflicts' =>
                array (
                ),
            'suggests' =>
                array (
                ),
        ),
);
