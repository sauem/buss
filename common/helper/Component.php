<?php

namespace common\helper;

use kartik\money\MaskMoney;
use pendalf89\filemanager\widgets\FileInput;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

class Component
{
    static function btnView($url, $text = '', $options = [], $pjax = 0)
    {
        return Html::a("<i class='mdi mdi-eye'></i> $text", $url, array_merge($options, [
            'data-pjax' => "$pjax",
            'data-toggle' => 'tooltip',
            'title' => $text ? $text : 'Chi tiết',
            'class' => 'text-info action-icon'
        ]));
    }

    static function btnDelete($url, $text = '', $options = [], $pjax = 0)
    {
        return Html::a("<i class='mdi mdi-delete'></i> $text", $url, array_merge($options, [
            'data-pjax' => "$pjax",
            'data-request-method' => 'POST',
            'data-confirm-ok' => 'Xoá',
            'data-confirm-cancel' => 'Hủy',
            'data-confirm-title' => 'Xóa đối tượng',
            'data-confirm-message' => 'Bạn có muốn xóa đối tượng này?',
            'role' => 'modal-remote',
            'class' => 'text-danger action-icon'
        ]));
    }

    static function btnUpdate($url, $text = '', $options = [], $pjax = 0)
    {
        return Html::a("<i class='mdi mdi-square-edit-outline'></i> $text", $url, array_merge($options, [
            'data-pjax' => "$pjax",
            'data-toggle' => 'tooltip',
            'title' => $text ? $text : 'Cập nhật',
            'class' => 'text-warning action-icon'
        ]));
    }

    static function inputMoney($model, $form, $name, $prefix = '', $label = null)
    {
        return $form->field($model, $name)->widget(MaskMoney::className(), [
            'options' => [
                'placeholder' => 'Nhập số tiền...'
            ],
            'pluginOptions' => [
                'affixesStay' => false,
                'prefix' => $prefix,
                'allowNegative' => true,
                'allowZero' => true,
                'precision' => 0,
                'allowEmpty' => false
            ]
        ]);
    }

    static function filePicker($model, $form, $name)
    {
        return $form->field($model, $name)->widget(FileInput::className(), [
            'buttonTag' => 'button',
            'buttonName' => 'Browse',
            'buttonOptions' => ['class' => 'btn btn-default'],
            'options' => ['class' => 'form-control'],
            'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
            'thumb' => 'original',
            'imageContainer' => '.img',
            'pasteData' => FileInput::DATA_URL,
            'callbackBeforeInsert' => 'function(e, data) {
                console.log( data );
            }',
        ]);
    }

    static function dropDown($button)
    {
        $html = '<div class="btn-group dropdown">';
        $html .= '    <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-light ml-1 btn-sm" data-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-horizontal"></i></a>';
        $html .= '<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end">';
        foreach ($button as $btn) {
            $html .= $btn;
        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
}
