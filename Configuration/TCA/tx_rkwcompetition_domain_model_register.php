<?php
return [
    'ctrl' => [
        'hideTable' => 1,
        'title' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register',
        'label' => 'last_name',
        'label_alt' => 'first_name',
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
        'searchFields' => 'salutation,first_name,last_name,institution,address,city,telephone,email,contribution_title,remark,conditions_of_participation,group_work_insurance,group_work_add_persons,competition,admin_approved,admin_approved_by,admin_approved,admin_refused,admin_refused_by,admin_refused_at,admin_refused_text,unique_id',
        'iconfile' => 'EXT:rkw_competition/Resources/Public/Icons/tx_rkwcompetition_domain_model_register.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'salutation, title, first_name, last_name, institution, address, zip, city, telephone, email, contribution_title, type_of_work, sector, remark, privacy, conditions_of_participation, is_group_work, group_work_insurance, group_work_add_persons, upload, competition, admin_approved_by, admin_approved, admin_refused_by, admin_refused_at, admin_refused_text, unique_id, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
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
                'foreign_table' => 'tx_rkwcompetition_domain_model_register',
                'foreign_table_where' => 'AND {#tx_rkwcompetition_domain_model_register}.{#pid}=###CURRENT_PID### AND {#tx_rkwcompetition_domain_model_register}.{#sys_language_uid} IN (-1,0)',
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

        'salutation' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.salutation',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.salutation.I.0', 0],
                    ['LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.salutation.I.1', 1],
                    ['LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.salutation.I.2', 2],
                    ['LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.salutation.I.3', 3],
                ],
                'default' => 99,
                'size' => 1,
                'maxitems' => 1,
            ],
        ],
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.title',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'first_name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.first_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'last_name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.last_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'institution' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.institution',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'address' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.address',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'zip' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.zip',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'city' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.city',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'telephone' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.telephone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'email' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,email'
            ],
        ],
        'contribution_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.contribution_title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        /*
         * // use simply "isGroupWork" for it?
        'type_of_work' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.type_of_work',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
                'default' => 0
            ]
        ],
        */
        'sector' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.sector',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'remark' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.remark',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'privacy' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.privacy',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 4,
                'eval' => 'time',
                'default' => time()
            ]
        ],
        'conditions_of_participation' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.conditions_of_participation',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'is_group_work' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.is_group_work',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
                'default' => 0
            ]
        ],
        'group_work_insurance' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.group_work_insurance',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
                'default' => 0
            ],
        ],
        'group_work_add_persons' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.group_work_add_persons',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'admin_approved' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.admin_approved',
            'config' => [
                'type' => 'check',
                'default' => 0
            ],
        ],
        'admin_approved_by' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.admin_approved_by',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'be_users',
                'foreign_table_where' => 'AND be_users.deleted = 0 AND be_users.disable = 0 AND be_users.email != "" ORDER BY be_users.username',
            ],
        ],
        'admin_approved_at' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.admin_approved_at',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 4,
                'eval' => 'time',
                'default' => time()
            ]
        ],
        'admin_refused' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.admin_refused',
            'config' => [
                'type' => 'check',
                'default' => 0
            ],
        ],
        'admin_refused_by' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.admin_refused_by',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'be_users',
                'foreign_table_where' => 'AND be_users.deleted = 0 AND be_users.disable = 0 AND be_users.email != "" ORDER BY be_users.username',
            ],
        ],
        'admin_refused_at' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.admin_refused_at',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 4,
                'eval' => 'time',
                'default' => time()
            ]
        ],
        'admin_refused_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.admin_refused_text',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'unique_id' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.unique_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'upload' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.upload',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_rkwcompetition_domain_model_upload',
                'minitems' => 1,
                'maxitems' => 1,
                'appearance' => [
                    'collapseAll' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],
        ],
        'frontend_user' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_db.xlf:tx_rkwcompetition_domain_model_register.frontend_user',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'foreign_table_where' => 'AND fe_users.deleted = 0  AND fe_users.disable = 0 AND fe_users.email <> "" ORDER BY fe_users.username',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'competition' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    
    ],
];
