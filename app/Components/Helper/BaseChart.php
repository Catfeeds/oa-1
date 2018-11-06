<?php
/**
 * Created by PhpStorm.
 * User: caiwenpi
 * Date: 2017/7/13
 * Time: 17:45
 */

namespace App\Components\Helper;


class BaseChart
{
    public static function chartHTML($id = 'container')
    {
        $html = <<<HTML
<div class="ibox-content">
    <div id="{$id}" class="ibox-container">
        <div class="ajax-mask"><div class="loading"></div></div>
    </div>
</div>
HTML;

        return $html;
    }

    public static function tableHTML($headers = [], $isFoot = true)
    {
        $footTd = '';
        foreach ($headers as $k => $v) {
            $footTd .= sprintf('<td data-key="%s">--</td>', $k);
        }

        $footer = '';
        if ($isFoot) {
            $footer = sprintf('<tfoot><tr>%s</tr></tfoot>', $footTd);
        }

        $html = <<<HTML
<div class="ibox-content">
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
            </thead>
            {$footer}
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="ajax-mask"><div class="loading"></div></div>
</div>
HTML;

        return $html;
    }

    public static function tableJS($url, $columns = [], $id = 'base_content', $chartId = 'container', $sort = true, $sortIndex = 0)
    {
        $columns = \GuzzleHttp\json_encode($columns);
        $order = $sort ? \GuzzleHttp\json_encode([[$sortIndex, 'desc']]) : \GuzzleHttp\json_encode([]);

        $js = <<<JS
    var base{$id} = $('#{$id}');
    var foot{$id} = base{$id}.find('.table tfoot');
    var summary;
    foot{$id}.hide();
    $.getJSON('{$url}', function(res) {
        var data = [];
        var columns = {$columns};

        if (res.hasOwnProperty('data')) {
            data = res.data
        }
        
        if (res.hasOwnProperty('columns')) {
            columns = res.columns
        }

        base{$id}.find('.table').DataTable({
            destroy: true,    // 解决重复生成问题
            bLengthChange: false,
            paging: false,
            info: false,
            searching: false,
            fixedHeader: true,
            order: {$order},
            data: data,
            columns: columns,
            columnDefs: [
                { "width": "100px", "targets": 0 }
              ],
            pageLength: 25,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                {extend: 'copy'},
                {extend: 'csv'},
                {extend: 'excel'}
            ]
        });

        if(res.hasOwnProperty('summary')) {
             var data = res.summary;
             summary = data;
            foot{$id}.find('td').each(function () {
            var k = $(this).attr('data-key');
            if (data.hasOwnProperty(k)) {
                $(this).html("<strong>"+ data[k] +"</strong>");
            }
            })
            foot{$id}.show();
        }
        
        var c = Object.create(Charts{$chartId});
        c.run(res)

        base{$id}.find('.ajax-mask').remove();
    }); 

    var Charts{$chartId} = {
        run: function() {
            console.log('run')
        }
    }
JS;

        return $js;
    }

    public static function chartJS($options = [], $id = 'container')
    {
        $default = [
            'chart' => [
                'type' => 'column',
            ],
            'title' => false,
            'xAxis' => [
                'crosshair' => true,
            ],
            'yAxis' => [
                'min' => 0,
                'title' => false,
            ],
            'plotOptions' => [
                'column' => [
                    'pointPadding' => 0.2,
                    'borderWidth' => 0,
                ],
            ],
            'tooltip' => [
                'shared' => true,
            ],
            'series' => [],
        ];

        $optionsJS = \GuzzleHttp\json_encode(array_merge($default, $options));
        $js = <<<JS
        var Charts{$id} = {
    options: {$optionsJS},
    run: function(res) {
        if(!res.hasOwnProperty('chart')) {
            return
        }
        var chart = res.chart
        
        if(chart.hasOwnProperty('xAxis')) {
            this.options.xAxis.categories = chart.xAxis
        }
        if(chart.hasOwnProperty('series')) {
            this.options.series = chart.series
        }
        
        Highcharts.chart('{$id}', this.options)
    }
        }
JS;

        return $js;
    }

    public static function modalTableJS($columns, $baseId = 'base_content', $modalId = 'detail_modal', $sortIndex = 0) {
        $columns = \GuzzleHttp\json_encode($columns);
        $js = <<<JS
            var firstTime = true
            $('#{$baseId} .ibox-content .table').on('click', 'td', function(event) {
                var u = $(this).find('a').attr('data-url');
                if (typeof u === "undefined") {
                    return
                }

                var columns = {$columns}
                var m = $('#{$modalId}')

                if (!firstTime) {
                    m.find('.table').DataTable().clear().draw()
                    m.find('.ibox-content').append('<div class="ajax-mask"><div class="loading"></div></div>');
                }

                m.find('tfoot').hide()
                $.getJSON(u, function(res) {
                    m.find('.table').DataTable({
                        destroy: true,    // 解决重复生成问题
                        bLengthChange: false,
                        paging: false,
                        info: false,
                        searching: false,
                        order: [[ {$sortIndex}, "desc" ]],
                        data: res.data,
                        columns: columns,
                    });

                    if(res.hasOwnProperty('summary')) {
                        var data = res.summary
                        m.find('tfoot td').each(function () {
                            var k = $(this).attr('data-key');
                            if (data.hasOwnProperty(k)) {
                                $(this).html("<strong>"+ data[k] +"</strong>");
                            }
                        })

                        m.find('tfoot').show()
                    }

                    firstTime = false

                    m.find('.ajax-mask').remove()
                })

                m.modal('show')
            });
JS;

        return $js;
    }


    public static function nativeDataTable($id) {
        $js = <<<JS
          $('#{$id}').dataTable({
                language: {
                    url: '{{ asset('js/plugins/dataTables/i18n/Chinese.json') }}'
                },
                bLengthChange: false,
                paging: 30,
                info: false,
                searching: false,
                fixedHeader: true,
                "order": [[1, "asc"]],
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    {extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel'}
                ]
            });

JS;

        return $js;

    }

}