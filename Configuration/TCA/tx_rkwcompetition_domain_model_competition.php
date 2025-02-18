<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition',
        'label' => 'title',
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
        'searchFields' => 'title, description,jury_access_end,record_removal_date,jury_add_data,link_jury_declaration_confident,link_cond_participation,link_privacy',
        'iconfile' => 'EXT:rkw_competition/Resources/Public/Icons/tx_rkwcompetition_domain_model_competition.gif'
    ],
    'types' => [
        '1' => ['showitem' =>
            'title, --palette--;;startEnd, --palette--;;userInfo, sectors, link_cond_participation, link_privacy, description, 
            
            --div--;LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.tab_jury,
            jury_access_end, link_jury_declaration_confident, group_for_jury, jury_member_candidate, jury_member_confirmed, jury_add_data, 
            
             --div--;LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.tab_admin,
            admin_member, 
            
            --div--;LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.tab_register,
            register,
            
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, 
            sys_language_uid, l10n_parent, l10n_diffsource, 
            
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, 
            hidden, starttime, endtime
        '],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
        'dataFields' => [
            'showitem' => 'hidden, sys_language_uid, l10n_parent, l10n_diffsource',
        ],
        'startEnd' => [
            'showitem' => 'register_start, register_end, record_removal_date',
        ],
        'userInfo' => [
            'showitem' => 'group_for_user, allow_team_participation ',
        ],
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
                'foreign_table' => 'tx_rkwcompetition_domain_model_competition',
                'foreign_table_where' => 'AND {#tx_rkwcompetition_domain_model_competition}.{#pid}=###CURRENT_PID### AND {#tx_rkwcompetition_domain_model_competition}.{#sys_language_uid} IN (-1,0)',
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
        'crdate' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim, required',
                'default' => '',
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim, required',
                'fieldControl'  => [
                    'fullScreenRichtext' => [
                        'disabled' => false,
                    ],
                ],
                'enableRichtext' => true,
            ]
        ],
        'register_start' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.register_start',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 7,
                'eval' => 'date, required',
                'default' => time()
            ],
        ],
        'register_end' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.register_end',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 7,
                'eval' => 'date, required',
                'default' => time()
            ],
        ],
        'jury_access_end' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.jury_access_end',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 7,
                'eval' => 'date, required',
                'default' => time()
            ],
        ],
        'record_removal_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.record_removal_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 7,
                'eval' => 'date, required',
                'default' => time()
            ],
        ],
        'jury_add_data' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.jury_add_data',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim, required',
                'fieldControl'  => [
                    'fullScreenRichtext' => [
                        'disabled' => false,
                    ],
                ],
                'enableRichtext' => true,
            ],
        ],
        'link_jury_declaration_confident' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.link_jury_declaration_confident',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'eval' => 'required',
            ],
        ],
        'allow_team_participation' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.allow_team_participation',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ]
                ],
                'default' => 0,
            ]
        ],
        'reminder_incomplete_mail_tstamp' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.reminder_incomplete_mail_tstamp',
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
        'reminder_cleanup_mail_tstamp' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.reminder_cleanup_mail_tstamp',
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
        'reminder_jury_mail_tstamp' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.reminder_jury_mail_tstamp',
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
        'closing_day_mail_tstamp' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.closing_day_mail_tstamp',
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
        'link_cond_participation' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.link_cond_participation',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'eval' => 'required',
            ]
        ],
        'link_privacy' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.link_privacy',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'eval' => 'required',
            ]
        ],
        'own_cloud_folder_link' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.own_cloud_folder_link',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
                'default' => '',
            ]
        ],
        'admin_member' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.admin_member',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'be_users',
                'default' => 0,
                'size' => 10,
                'autoSizeMax' => 30,
                'minitems' => 1,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],

        ],
        'jury_member_candidate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.jury_member_candidate',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim, required',
                'default' => '',
            ],
        ],
        'jury_member_confirmed' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.jury_member_confirmed',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_rkwcompetition_domain_model_juryreference',
                'default' => 0,
                'size' => 10,
                'autoSizeMax' => 30,
                'minitems' => 0,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],
        ],
        'group_for_jury' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.group_for_jury',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_groups',
                'default' => 0,
                'minitems' => 1,
                'maxitems' => 1,
            ],

        ],
        'group_for_user' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.group_for_user',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_groups',
                'default' => 0,
                'minitems' => 1,
                'maxitems' => 1,
            ],

        ],
        'sectors' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.sectors',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_rkwcompetition_domain_model_sector',
                'default' => 0,
                'minitems' => 1,
                'maxitems' => 9999,
            ],

        ],
        'register' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_competition.register',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_rkwcompetition_domain_model_register',
                'foreign_field' => 'competition',
                'default' => 0,
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],

        ],
    
    ],
];
