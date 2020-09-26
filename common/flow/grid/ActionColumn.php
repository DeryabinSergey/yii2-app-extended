<?php

namespace common\flow\grid;

use Yii;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/**
 * ActionColumn for bootstrap 4 icons
 *
 * @inheritdoc
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ActionColumn extends \yii\grid\ActionColumn
{
    /**
     * Initializes the default button with fontawesome 5 icons rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        $this->initDefaultButton('view', 'fa-search-plus');
        $this->initDefaultButton('update', 'fa-edit');
        $this->initDefaultButton('delete', 'fa-trash', [
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                switch ($name) {
                    case 'view':
                        $title = Yii::t('yii', 'View');
                        break;
                    case 'update':
                        $title = Yii::t('yii', 'Update');
                        break;
                    case 'delete':
                        $title = Yii::t('yii', 'Delete');
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);
                $icon = Html::tag('span', '', ['class' => "fas $iconName"]);
                return Html::a($icon, $url, $options);
            };
        }
    }
}
