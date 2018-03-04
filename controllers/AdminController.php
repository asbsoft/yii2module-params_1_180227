<?php

namespace asb\yii2\modules\params_1_180227\controllers;

use asb\yii2\modules\params_1_180227\Module;
use asb\yii2\modules\params_1_180227\models\ParamsAccessControl;

use asb\yii2\common_2_170212\controllers\BaseAdminController;

use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use Yii;

/**
 * @author ASB <ab2014box@gmail.com>
 */
class AdminController extends BaseAdminController
{
    /** Prefix of id for HTML-element contains edit form */
    public $editboxIdPrefix = 'param-editbox';
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = ArrayHelper::merge($behaviors, [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'remove' => ['post'],
                ],
            ],
            'param-access' => [
                'class' => ParamsAccessControl::className(),
                'tc' => $this->tcModule,
                'actions' => ['edit', 'remove'],
            ],
        ]);
        return $behaviors;
    }

    /**
     * Lists all NewsTagitem models.
     * @param string $muid module unique id
     * @param boolean $scalar show scalar parameters only
     * @return mixed
     */
    public function actionIndex($muid = '', $scalar = false)
    {
        $module = $this->module->getModuleByUniqueId($muid);
        $dataProvider = $this->module->model('ParamsDataProvider', [$module, $scalar, [
            'pagination' => false,
            'sort' => [
                'attributes' => ['param'],
                'defaultOrder' => ['param' => SORT_ASC],
            ],
        ]]);
        return $this->render('index', compact('module', 'dataProvider'));
    }

    /**
     * @param string $muid module unique id
     * @param string $param parameter name
     * @return mixed
     */
    public function actionRemove($muid, $param)
    {
        $param = urldecode($param);
        $model = $this->module->model('ParamsValues');
        if ($model->removeParam($muid, $param)) {
            Yii::$app->session->setFlash('success'
              , Yii::t($this->tcModule, "Default value restored successful for parameter '{param}'", ['param' => $param]));
        } else {
            Yii::$app->session->setFlash('error', $model->getFirstError('param'));
        }
        return $this->redirect(['index', 'muid' => $muid, 'scalar' => true]);
    }

    /**
     * @param string $muid module unique id
     * @param string $param parameter name
     * @return mixed
     */
    public function actionEdit($muid, $param)
    {
        if (Yii::$app->request->isGet) { // if reload when browsers' URL-line contains URL from PJAX
            return $this->redirect(['index', 'muid' => $muid, 'scalar' => true]);
        }
        
        $param = urldecode($param);
        $module = Module::getModuleByUniqueId($muid);
        if (!$module) {
            $msg = Yii::t($this->tcModule, "Can't find module '{muid}'", ['muid' => $muid]);
            Yii::error($msg, __METHOD__);
            return $msg;
        }

        $valuesModel = $this->module->model('ParamsValues');
        $model = $valuesModel::find()
            ->where(['param' => $param])
            ->andWhere(['module_uid' => $muid])
            ->one();
        if (!$model) { // no saved value in db - empty model
            $model = $valuesModel;
            $model->value = $module->params[$param]; // default value of parameter
            $model->type = gettype($model->value);
        }
        
        $post = Yii::$app->request->post();
        $loaded = $model->load($post);
        $model->module_uid = $muid;
        $model->param = $param;
        $model->value = $model->correctValue($model->value, $model->type); // can set $model->errors

        if ($loaded && !$model->hasErrors() && $model->save()) {
          //return $model->value; // OK but not render [Remove] link
            return $this->redirect(['index', 'muid' => $muid, 'scalar' => true]);
        }

        return $this->renderAjax('edit', compact('model'));
    }

}
