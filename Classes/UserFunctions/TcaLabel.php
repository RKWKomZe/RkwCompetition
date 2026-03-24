<?php
namespace RKW\RkwCompetition\UserFunctions;

use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Class TcaLabel
 * @package RKW\RkwCompetition\UserFunctions
 */
class TcaLabel
{
    /**
     * @param array $parameters
     * @return void
     */
    public function getRegisterLabel(array &$parameters)
    {
        $record = BackendUtility::getRecord($parameters['table'], $parameters['row']['uid']);
        if ($record) {
            $lastName = $record['last_name'] ?? '';
            $firstName = $record['first_name'] ?? '';
            $crdate = $record['crdate'] ?? 0;
            $formattedDate = $crdate ? date('d.m.Y', $crdate) : '-';

            $parameters['title'] = sprintf('%s, %s (%s)', $lastName, $firstName, $formattedDate);
        }
    }
}
