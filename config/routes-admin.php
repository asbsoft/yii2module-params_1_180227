<?php

return [
  //'<action:(edit|remove)>/<muid:[A-Za-z0-9/_\-]+>/<param:[A-Za-z0-9_%\-\+]+>' => 'admin/<action>',
  //'<action:(edit|remove)>/module(<muid:[\w/-]+>)/param(<param:[\w%\+-]+>)' => 'admin/<action>', // ?? 404 not found
    '<action:(edit|remove)>' => 'admin/<action>',
  //'<action:(index)>/scalar:<scalar:\d>/module(<muid:[\w/-]+>)' => 'admin/<action>',
  //'<action:(index)>/scalar=<scalar:\d>' => 'admin/<action>',
    '<action:(index)>' => 'admin/<action>',
    '?' => 'admin/index',
];
