<?php

namespace RKW\RkwCompetition\ViewHelpers;
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Class IsMandatoryFieldViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class IsMandatoryFieldViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Initialize arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('fieldName', 'string', 'The field name');
        $this->registerArgument('mandatoryFields', 'string', 'List of mandatory fields from TypoScript');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string
     * @throws Exception
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $fieldName = $arguments['fieldName'];
        $mandatoryFields = $arguments['mandatoryFields'];

        $mandatoryFieldsArray = array_map('trim', explode(',', $mandatoryFields));

        if (!in_array($fieldName, $mandatoryFieldsArray)) {
            return true;
        }

        return false;
    }


}
