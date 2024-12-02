<?php
declare(strict_types = 1);

return [
    \RKW\RkwCompetition\Domain\Model\BackendUser::class => [
        'tableName' => 'be_users',
    ],
    \RKW\RkwCompetition\Domain\Model\FrontendUser::class => [
        'tableName' => 'fe_users',
    ],
    \RKW\RkwCompetition\Domain\Model\File::class => [
        'tableName' => 'sys_file',
    ],
    \RKW\RkwCompetition\Domain\Model\FileReference::class => [
        'tableName' => 'sys_file_reference',
        'properties' => [
            'file' => [
                'fieldName' => 'uid_local'
            ],
        ],
    ],
];
