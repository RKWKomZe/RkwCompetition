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

use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\Domain\Model\Register;
use RKW\RkwCompetition\Domain\Model\Sector;
use RKW\RkwCompetition\Domain\Repository\RegisterRepository;
use RKW\RkwCompetition\Utility\RegisterUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Class RegisterNumberViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class RegisterNumberViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('register', Register::class, 'The register object', false, null);
        $this->registerArgument('competition', Competition::class, 'The competition object', false, null);
        $this->registerArgument('sector', Sector::class, 'The sector object', false, null);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string|int
     * @throws Exception
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        /** @var Register|null $register */
        $register = $arguments['register'] ?? null;
        /** @var Competition|null $competition */
        $competition = $arguments['competition'] ?? null;
        /** @var Sector|null $sector */
        $sector = $arguments['sector'] ?? null;

        if ($sector instanceof Sector) {
            if ($competition instanceof Competition) {
                $registerRepository = GeneralUtility::makeInstance(RegisterRepository::class);
                return $registerRepository->findAdminApprovedByCompetition($competition, $sector)->count();
            }
            return 0;
        }

        if ($register instanceof Register) {
            return RegisterUtility::getRegistrationNumber($register, $competition);
        }

        if ($competition instanceof Competition) {
            $registerRepository = GeneralUtility::makeInstance(RegisterRepository::class);
            return $registerRepository->findAdminApprovedByCompetition($competition)->count();
        }

        return '-';
    }
}
