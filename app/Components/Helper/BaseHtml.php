<?php

namespace App\Components\Helper;

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
        foreach ($options as $k => $v) {
            $opt .= sprintf('%s="%s"', $k, $v);
        }
        return "<a href='{$url}'> <i class='fa fa-{$class}' data-toggle='tooltip' data-placement='top' title='{$title}' data-original-title='{$title}' {$opt}></i> </a>";
    }

    public static function prompt($type)
    {
        $confs = config('prompts');

        if (!isset($confs[$type])) {
            return '';
        }

        $metric = $confs[$type];
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