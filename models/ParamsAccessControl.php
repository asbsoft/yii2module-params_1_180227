<?php

namespace asb\yii2\modules\params_1_180227\models;

use asb\yii2\modules\params_1_180227\Module;

use asb\yii2\common_2_170212\behaviors\ParamsAccessBehaviour;

use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;
use Yii;

use Exception;

/**
 * @author ASB <ab2014box@gmail.com>
 */
class ParamsAccessControl extends ActionFilter
{
    public $tc = 'app';

    public $actions = [];

    public function beforeAction($action)
    {
        $actionId = $this->getActionId($action);

        if (in_array($actionId, $this->actions)) {
            $params = Yii::$app->request->queryParams; // don't get params when action render by runAction() from another template
            $moduleUid = $params['muid'];
            $module = Module::getModuleByUniqueId($moduleUid);
            $user = Yii::$app->user;
            $canEdit = false;
            try {
                $paramName = urldecode($params['param']);
                $roles = $module->rolesForParam($paramName);
                foreach ($roles as $role) {
                    if ($user->can($role)) {
                        $canEdit = true;
                        break;
                    }
                }
            } catch(Exception $ex) { // module don't have params behaviors
                $defaultRole = (new ParamsAccessBehaviour)->defaultRole;
                $canEdit = $user->can($defaultRole);
            }    
            if (!$canEdit) {
                $moduleName = $moduleUid === '' ? Yii::t($this->tc, 'application') : Yii::t($this->tc, 'module') . " '{$moduleUid}'";
                $msg = Yii::t($this->tc, "You can't edit parameter '{param}' of {module}"
                  , ['param' => $paramName, 'module' => $moduleName]);
                throw new ForbiddenHttpException($msg);
            }
        }
        return true;
    }

}
