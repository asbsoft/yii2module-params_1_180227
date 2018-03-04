<?php

use asb\yii2\modules\params_1_180227\models\ParamsModules;
use asb\yii2\modules\params_1_180227\models\ParamsValues;

use asb\yii2\common_2_170212\behaviors\ParamsAccessBehaviour;

return [
    'label' => 'Parameters manager', // default: only roleRoot can edit this parameter

    // nobody can edit this parameters
    'readonlyFloatParam' => 3.14159,
    'listSize' => 0,

    // parameters for roleAdmin
    'testStrParam' => 'This is demo parameter only',
    'testIntParam' => 2018,
    'testFloatParam' => 3.8e-4,

    'behaviors' => [ // readonly by default
        'params-access' => [
            'class' => ParamsAccessBehaviour::className(),
          //'defaultRole' => 'roleRoot',
            'readonlyParams' => [
                'readonlyFloatParam',
                'listSize',
            ],
            'roleParams' => [
                'roleAdmin' => ['testStrParam', 'testIntParam', 'testFloatParam'],
                'roleContentAuthor' => ['testStrParam'],
            ],
        ],
    ],

    // tables names
    ParamsModules::className() => [
        'tableName' => '{{%params_modules}}',
    ],
    ParamsValues::className() => [
        'tableName' => '{{%params_values}}',
    ],

];
