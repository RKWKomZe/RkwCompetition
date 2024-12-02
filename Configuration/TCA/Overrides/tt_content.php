<?php
defined('TYPO3_MODE') || die();
call_user_func(
    function($extKey)
    {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'RkwCompetition',
            'Competition',
            'RKW Competition: Wettbewerb'
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'RkwCompetition',
            'Register',
            'RKW Competition: Anmeldung'
        );

//        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
//            'RkwCompetition',
//            'Edit',
//            'RKW Competition: Edit'
//        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'RkwCompetition',
            'Participant',
            'RKW Competition: Teilnehmer (Login-Bereich)'
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'RkwCompetition',
            'Jury',
            'RKW Competition: Jury (Login-Bereich)'
        );


        //=================================================================
        // Add Flexform
        //=================================================================
        $extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($extKey));
        $pluginName = strtolower('Competition');
        $pluginSignature = $extensionName . '_' . $pluginName;
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
            $pluginSignature,
            'FILE:EXT:' . $extKey . '/Configuration/FlexForms/Competition.xml'
        );


    },
    'rkw_competition'
);