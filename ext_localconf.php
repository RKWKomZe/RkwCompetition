<?php
defined('TYPO3_MODE') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'RKW.RkwCompetition',
        'Competition',
        [
            Competition::class => 'show'
        ],
        // non-cacheable actions
        [
            Competition::class => 'show'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'RKW.RkwCompetition',
        'Register',
        [
            Register::class => 'new, create, optIn'
        ],
        // non-cacheable actions
        [
            Register::class => 'new, create, optIn'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'RKW.RkwCompetition',
        'Participant',
        [
            Participant::class => 'list, show',
            Upload::class => 'edit, update, delete',
            Register::class => 'edit, update, deleteQuestion, delete, submitQuestion, submit'
        ],
        // non-cacheable actions
        [
            Participant::class => 'list, show',
            Upload::class => 'edit, update, delete',
            Register::class => 'edit, update, deleteQuestion, delete, submitQuestion, submit'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'RKW.RkwCompetition',
        'Jury',
        [
            Jury::class => 'show, edit, update, delete',
        ],
        // non-cacheable actions
        [
            Jury::class => 'show, edit, update, delete'
        ]
    );

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'rkw_competition-plugin-register',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:rkw_competition/Resources/Public/Icons/user_plugin_register.svg']
    );
    $iconRegistry->registerIcon(
        'rkw_competition-plugin-edit',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:rkw_competition/Resources/Public/Icons/user_plugin_edit.svg']
    );
    $iconRegistry->registerIcon(
        'rkw_competition-plugin-jury',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:rkw_competition/Resources/Public/Icons/user_plugin_jury.svg']
    );



    $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['de']['EXT:hcaptcha/Resources/Private/Language/locallang.xlf'][] = 'EXT:rkw_competition/Resources/Private/Language/Overrides/de.locallang_hcaptcha.xlf';


    //=================================================================
    // Register Signal-Slots
    //=================================================================

    // @toDo: The FeRegister still works with SignalSlots.
    // For all other stuff: https://docs.typo3.org/m/typo3/reference-coreapi/10.4/en-us/ApiOverview/Events/EventDispatcher/Index.html#eventdispatcher

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher
     */
    $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);

    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('fe_register')) {
        $signalSlotDispatcher->connect(
            Madj2k\FeRegister\Registration\AbstractRegistration::class,
            \Madj2k\FeRegister\Registration\AbstractRegistration::SIGNAL_AFTER_CREATING_OPTIN . 'RkwCompetition',
            RKW\RkwCompetition\Service\RkwMailService::class,
            'optInRequest'
        );

//        $signalSlotDispatcher->connect(
//            Madj2k\FeRegister\Registration\AbstractRegistration::class,
//            \Madj2k\FeRegister\Registration\AbstractRegistration::SIGNAL_AFTER_REGISTRATION_ENDED,
//            RKW\RkwCompetition\Controller\RegisterController::class,
//            'removeAllOfUserSignalSlot'
//        );
    }

    $signalSlotDispatcher->connect(
        RKW\RkwCompetition\Controller\RegisterController::class,
        \RKW\RkwCompetition\Controller\RegisterController::SIGNAL_AFTER_REGISTER_CREATED_USER,
        RKW\RkwCompetition\Service\RkwMailService::class,
        'confirmRegisterUser'
    );

    $signalSlotDispatcher->connect(
        RKW\RkwCompetition\Controller\RegisterController::class,
        \RKW\RkwCompetition\Controller\RegisterController::SIGNAL_AFTER_REGISTER_CREATED_USER,
        RKW\RkwCompetition\Service\RkwMailService::class,
        'uploadDocumentsUser'
    );

    $signalSlotDispatcher->connect(
        RKW\RkwCompetition\Controller\RegisterController::class,
        \RKW\RkwCompetition\Controller\RegisterController::SIGNAL_AFTER_REGISTER_CREATED_ADMIN,
        RKW\RkwCompetition\Service\RkwMailService::class,
        'confirmRegisterAdmin'
    );

    
})();
