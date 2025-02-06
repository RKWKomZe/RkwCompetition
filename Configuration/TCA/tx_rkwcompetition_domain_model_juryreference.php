<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_juryreference',
        'label' => 'email',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'remark,email',
        'iconfile' => 'EXT:rkw_competition/Resources/Public/Icons/tx_rkwcompetition_domain_model_juryreference.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'invitation_mail_tstamp, consented_at, email, invite_token, competition, guest_user, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_rkwcompetition_domain_model_juryreference',
                'foreign_table_where' => 'AND {#tx_rkwcompetition_domain_model_juryreference}.{#pid}=###CURRENT_PID### AND {#tx_rkwcompetition_domain_model_juryreference}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],

        'invitation_mail_tstamp' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_juryreference.invitation_mail_tstamp',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'readOnly' => 1,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'consented_at' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_juryreference.consented_at',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'readOnly' => 1,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'email' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_juryreference.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,email'
            ],
        ],
        'invite_token' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_juryreference.invite_token',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,email'
            ],
        ],
        'guest_user' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_juryreference.guest_user',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'foreign_table_where' => 'AND fe_users.disable = 0',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'competition' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_juryreference.competition',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_rkwcompetition_domain_model_competition',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],

    
    ],
];
