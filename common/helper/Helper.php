<?php

namespace common\helper;

use yii\helpers\ArrayHelper;

class Helper
{
    static function printf($model)
    {
        echo "<pre>";
        var_dump($model);
        echo "</pre>";
        exit;
    }

    static function toFloat($number)
    {
        return str_replace(',', '', $number);
    }

    static function formatCUR($num, $symbol = '', $decimal = 2)
    {
        return number_format($num, $decimal, '.', ',') . $symbol;
    }

    static function makeUpperString($str)
    {
        return strtoupper(static::toLower($str));
    }

    static function firstError($model)
    {
        $modelErrs = $model->getFirstErrors();
        foreach ($modelErrs as $err) {
            return $err;
        }
        return "No error founded";
    }

    static function dateFormat($time, $timestamp = false)
    {
        if (is_numeric($time)) {
            return date('d-m-Y', $time) . ($timestamp ? ' <small class="text-muted">' . date('H:i:s') . '</small>' : '');
        }
        return $time;
    }

    static function countries()
    {
        return ArrayHelper::map(\Yii::$app->params['countries'], 'code', function ($item) {
            return $item['code'] . ' - ' . $item['name'];
        });
    }

    static function countryName($code)
    {
        return ArrayHelper::getValue(self::countries(), $code, '--');
    }

    static function toLower($str)
    {
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }

    static function defaultImage($name = 'product')
    {
        return ArrayHelper::getValue([
            'product' => '/theme/images/default.jpg'
        ], $name);
    }

    static function makeCodeIncrement($lastID, $country = "VN", $prefix = '#CC')
    {
        $defaultCode = $prefix . $country . "0000000";
        $maxLen = strlen($lastID);
        return substr_replace($defaultCode, $lastID, -$maxLen);
    }

    static function isEmpty($val)
    {
        if (!$val || $val === '' || $val === null || empty($val)) {
            return true;
        }
        return false;
    }
}