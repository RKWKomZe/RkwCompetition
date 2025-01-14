<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'RKW Competition',
    'description' => 'Register handling for \"RKW Competition\"',
    'category' => 'plugin',
    'author' => 'Maximilian Fäßler',
    'author_email' => 'maximilian@faesslerweb.de',
    'state' => 'alpha',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
            'core_extended' => '10.4.0-10.4.99',
            'postmaster' => '10.4.0-10.4.99',
            'fe_register' => '10.4.0-10.4.99',
            'typo3-hcaptcha' => '10.4.0-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
