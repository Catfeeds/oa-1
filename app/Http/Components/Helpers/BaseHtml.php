<?php

namespace App\Http\Components\Helpers;

/**
 * 封装前端
 * Class BaseHtml
 * @package App\Http\Components\Helpers
 */
class BaseHtml
{
    public static function tooltip($title, $url, $class = 'cog', $options = [])
    {
        $opt = '';
        $target = '_self';
        foreach($options as $k => $v) {
            if ($k == 'target') {
                $target = $v;
                continue;
            }
            $opt .= sprintf('%s="%s"', $k, $v);
        }
        return "<a href='{$url}' target='{$target}'> <i class='fa fa-{$class}' data-toggle='tooltip' data-placement='top' title='{$title}' data-original-title='{$title}' {$opt}></i> </a>";
    }

    public static function prompt($data = [])
    {
        $confs = config('prompts');

        $metric = [];
        foreach ($data as $v) {
            if (isset($confs[$v])) {
                $metric[$v] = $confs[$v];
            }
        }
        $metric = json_encode($metric);
        $html = <<<HTML
<div class="ibox-tools">
    <a class="metrics-helper-btn btn btn-white btn-xs" data-metric='{$metric}'>
        <i class="fa fa-question-circle"></i>
    </a>
</div>
HTML;

        return $html;
    }

    public static function thTollTip($thText, $title = '', $options = [], $placement = "top")
    {
        $opt = '';
        foreach ($options as $k => $v) {
            $opt .= sprintf('%s="%s"', $k, $v);
        }
        $title = $title ? 'title="' . trans('app.' . $title) . '"' : '';

        return '<th data-toggle="tooltip" data-toggle="popover" data-placement="' . $placement . '"  ' . $title . $opt . ' >' . trans('app.' . $thText) . '</th>';
    }

    public static function toggleModel($title, $url, $targetDiv , $tip)
    {
        return '<a class="show-modal" data-href="'.$url.'"  data-toggle="modal" data-target="'.$targetDiv.'" title="'.$tip.'"> '.$title.'</a>';
    }


    public static function about($type)
    {
        $confs = config('abouts');

        if (!isset($confs[$type])) {
            return '';
        }

        $title = $confs[$type];

        return <<<HTML
<a class="btn btn-xs btn-white" data-toggle="tooltip"  data-placement="right" title="{$title}"><i class="fa fa-info-circle"></i> 关于 </a>
HTML;
    }

}