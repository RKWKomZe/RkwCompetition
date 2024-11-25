<?php


defined('TYPO3_MODE') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'RKW.RkwCompetition',
        'web',
        'list',
        '',
        [
            Backend::class => 'list, show',
        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:rkw_competition/Resources/Public/Icons/user_mod_list.svg',
            'labels' => 'LLL:EXT:rkw_competition/Resources/Private/Language/locallang_list.xlf',
        ]
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('rkw_competition_domain_model_register', 'EXT:rkw_competition/Resources/Private/Language/locallang_csh_tx_rkwcompetition_domain_model_register.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('rkw_competition_domain_model_register');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_rkwcompetition_domain_model_upload', 'EXT:rkw_competition/Resources/Private/Language/locallang_csh_tx_rkwcompetition_domain_model_upload.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_rkwcompetition_domain_model_upload');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_rkwcompetition_domain_model_competition', 'EXT:rkw_competition/Resources/Private/Language/locallang_csh_tx_rkwcompetition_domain_model_competition.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_rkwcompetition_domain_model_competition');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_rkwcompetition_domain_model_sector', 'EXT:rkw_competition/Resources/Private/Language/locallang_csh_tx_rkwcompetition_domain_model_sector.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_rkwcompetition_domain_model_sector');
})();
