<?php

return [
  //'<action:(edit|remove)>/<muid:[A-Za-z0-9/_\-]+>/<param:[A-Za-z0-9_%\-\+]+>' => 'admin/<action>',
    '<action:(edit|remove)>/<muid:\w+>/<param:\w+>' => 'admin/<action>',
  //'<action:(index)>/<muid:[A-Za-z0-9_\-]+>' => 'admin/<action>',
  //'<action:(index)>/<muid:\w+>' => 'admin/<action>', // not use
    '<action:(index)>' => 'admin/<action>',
    '' => 'admin/index',
];
