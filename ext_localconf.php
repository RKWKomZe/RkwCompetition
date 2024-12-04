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
            Register::class => 'edit, update, deleteQuestion, delete'
        ],
        // non-cacheable actions
        [
            Participant::class => 'list, show',
            Upload::class => 'edit, update, delete',
            Register::class => 'edit, update, deleteQuestion, delete'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'RKW.RkwCompetition',
        'Jury',
        [
            Jury::class => 'list, show'
        ],
        // non-cacheable actions
        [
            Jury::class => 'list, show'
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


    //=================================================================
    // Register Signal-Slots
    //=================================================================
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
        'confirmRegistrationUser'
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
        'confirmRegistrationAdmin'
    );

//    $signalSlotDispatcher->connect(
//        RKW\RkwCompetition\Controller\RegisterController::class,
//        \RKW\RkwCompetition\Controller\RegisterController::SIGNAL_AFTER_RESERVATION_UPDATE_USER,
//        RKW\RkwCompetition\Service\RkwMailService::class,
//        'updateReservationUser'
//    );
//
//    $signalSlotDispatcher->connect(
//        RKW\RkwCompetition\Controller\RegisterController::class,
//        \RKW\RkwCompetition\Controller\RegisterController::SIGNAL_AFTER_RESERVATION_UPDATE_ADMIN,
//        RKW\RkwCompetition\Service\RkwMailService::class,
//        'updateReservationAdmin'
//    );
//
//    $signalSlotDispatcher->connect(
//        RKW\RkwCompetition\Controller\RegisterController::class,
//        \RKW\RkwCompetition\Controller\RegisterController::SIGNAL_AFTER_RESERVATION_DELETE_USER,
//        RKW\RkwCompetition\Service\RkwMailService::class,
//        'deleteReservationUser'
//    );
//
//    $signalSlotDispatcher->connect(
//        RKW\RkwCompetition\Controller\RegisterController::class,
//        \RKW\RkwCompetition\Controller\RegisterController::SIGNAL_AFTER_RESERVATION_DELETE_ADMIN,
//        RKW\RkwCompetition\Service\RkwMailService::class,
//        'deleteReservationAdmin'
//    );
    
    
})();
