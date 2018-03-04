<?php
    /* @var $this yii\web\View */
    /* @var $module yii\base\Module */
    /* @var $dataProvider yii\data\ActiveDataProvider */

    use asb\yii2\common_2_170212\base\ModulesManager;
    use asb\yii2\common_2_170212\assets\CommonAsset;

    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\grid\GridView;
    use yii\grid\ActionColumn;
    use yii\grid\SerialColumn;


    $commonAssets = CommonAsset::register($this);
    
    $editboxIdPrefix = $this->context->editboxIdPrefix;

    $tc = $this->context->tcModule;

    $title = Yii::t($tc, 'Parameters');
    $this->title = Yii::t($tc, 'Adminer') . ' - ' . $title; 
    if (!empty($module->uniqueId)) {
        $title .= ' ' . Yii::t($tc, 'for module') . " '{$module->uniqueId}'";
    }

    $paramsValuesModel = $this->context->module->model('ParamsValues');

    $modulesList = ModulesManager::modulesNamesList();

?>
<div class="admin-params-index">
    <h3><?= Html::encode($title) ?></h3>

    <div class="modules-list">
        <?= Html::beginForm([''], 'get') ?>
            <div class="col-md-8">
                <?= Html::dropDownList('muid', $module->uniqueId, $modulesList, [
                        'id' => 'module-uid',
                        'prompt' => '- ' . Yii::t($tc, 'application') . ' -',
                        'class' => 'form-control select-module-uid',
                        'onchange' => "this.form.submit()",
                     ]) ?>
            </div>
            <div class="col-md-4">
                <?= Html::checkbox('scalar', $dataProvider->isScalar, $options = [
                        'id' => 'scalar',
                        'label' => Yii::t($tc, 'show scalar parameters only'),
                        'onchange' => "this.form.submit()",
                    ]); ?>
            </div>
        <?= Html::endForm() ?>
    </div>
    <br style="clear:both" />

    <?php if (empty($module->params)): ?>
        <div class="text-center media">
            <b><?php echo Yii::t($tc, '(no parameters for module)') ?></b>
        </div>
    <?php else: ?>
        <div class="params-list">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => null, // no $searchModel
                'emptyText' => Yii::t($tc, 'no scalar parameters found'),
                'options' => [
                    'class' => 'params-list-grid',
                ],
                'layout' => "{items}\n{summary}", // no pager, summary at bottom
              //'summary' => Yii::t($tc, 'Total') . ": {totalCount}", // useful if no SerialColumn
                'summary' => false, // no summary

                'columns' => [
                    [
                        'class' => SerialColumn::className(),
                        'header' => 'N',
                    ],
                    [
                        'attribute' => 'param',
                        'header' => false, // disable sort link
                    ],
                    [
                        'attribute' => 'comment',
                        'headerOptions' => [
                            'class' => 'col-md-4',
                        ],
                    ],
                    'type',
                    [
                        'attribute' => 'value',
                        'headerOptions' => [
                            'class' => 'col-md-4',
                        ],
                        'contentOptions' => function ($model, $key, $index, $column) use ($editboxIdPrefix) {
                            return ['id' => "$editboxIdPrefix-$key"];
                        },
                        'content' => function ($model, $key, $index, $column) {
                            if (is_scalar($model->value)) {
                                if ($model->type === 'boolean') {
                                    return $model->value ? 'true' : 'false';
                                } else {
                                    return $model->value;
                                }
                            } else {
                                return var_export($model->value, true);
                            }
                        },
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'header' => Yii::t($tc, 'Actions'),
                        'template' => '{update} {delete}',
                        'buttonOptions' => [
                            'data-method' => 'post',
                        ],
                        'visibleButtons' => [
                            'update' => function ($model, $key, $index) use ($module) {
                                return is_scalar($model->value)
                                    && $model->canUserEditParam($module);
                            },
                            'delete' => function ($model, $key, $index) use ($module) {
                              //return is_scalar($model->value) && $paramsValuesModel::hasSavedParam($module, $key);
                                return is_scalar($model->value)
                                    && $model->canUserRemoveParam($module);
                            },
                        ],
                        'buttons' => [
                            'update' => function($url, $model, $key) use($module, $tc) {
                                $title = Yii::t($tc, 'Edit');
                                $options = [
                                    'class' => 'edit-button',
                                    'title' => $title,
                                    'aria-label' => $title,
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                    'data-paramname' => $key,
                                ];
                                $url = Url::toRoute(['edit',
                                    'muid' => $module->uniqueId,
                                    'param' => urlencode(htmlspecialchars($key)),
                                ]);
                                return Html::a("<span class='glyphicon glyphicon-pencil'></span>", $url, $options);
                            },
                            'delete' => function($url, $model, $key) use($module, $tc) {
                                $title = Yii::t($tc, 'Restore default value');
                                $options = [
                                    'title' => $title,
                                    'aria-label' => $title,
                                    'data-confirm' => Yii::t($tc, "Are you sure to restore default value for param '{param}'?", ['param' => $key]), //+id
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                ];
                                $url = Url::toRoute(['remove',
                                    'muid' => $module->uniqueId,
                                    'param' => urlencode(htmlspecialchars($key)),
                                ]);
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    <?php endif ?>
</div>

<?php
    $waitImg = Html::img("{$commonAssets->baseUrl}/img/wait-smaller.gif");
    $this->registerJs("
        jQuery('.edit-button').bind('click', function() {
            var boxId = '#' + '{$editboxIdPrefix}-' + jQuery(this).attr('data-paramname');
            jQuery(boxId).html('{$waitImg}');
            jQuery(boxId).load(this.href, {});
            return false;
        });
    ");
?>
