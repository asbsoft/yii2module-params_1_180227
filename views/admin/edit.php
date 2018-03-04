<?php
    /* @var $this yii\web\View */
    /* @var $model asb\yii2\modules\params_1_180227\models\ParamsValues */

    use asb\yii2\common_2_170212\assets\CommonAsset;

    use yii\widgets\Pjax;
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;


    $formId  = 'form-edit-param-' . $model->param;
    $btnId   = 'btn-sav-param-' . $model->param;

    $tc = $this->context->tcModule;

    $commonAssets = CommonAsset::register($this);
    $waitImg = Html::img("{$commonAssets->baseUrl}/img/wait-smaller.gif");

?>
<div class="paramvalue-editbox">
<?php Pjax::begin([
        'id' => "pjax-box-{$model->param}",
    ]); ?>
    <?php $form = ActiveForm::begin([
              'enableClientValidation' => false, // turn off JS-validation
              'options' => [
                  'data' => ['pjax' => true],
                  'id' => $formId,
              ],
        ]); ?>
        <?php if ($model->type === 'boolean'): ?>
            <?= $form->field($model, 'value', [
                    'options' => [
                        'class' => 'col-sm-9'
                    ],
                ])->checkbox(['label' => false]); ?>
        <?php else: ?>
            <?= $form->field($model, 'value', [
                    'options' => [
                        'class' => 'col-sm-9'
                    ],
                ])->textInput()->label(false); ?>
        <?php endif ?>

        <?= Html::submitButton(Yii::t($tc, 'Save'), [
                'id' => $btnId,
                'class' => 'btn col-sm-3',
                'onclick' => "jQuery('#{$btnId}').html('{$waitImg}')",
            ]) ?>
    <?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
</div>
