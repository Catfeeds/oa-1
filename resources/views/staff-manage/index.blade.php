@extends('attendance.side-nav')

@section('title', $title)

@section('page-head')
    @parent

@endsection

@push('css')
<style type="text/css">
    .first-col-div p {
        font-size: 50px;
        font-weight: 800;
        color: #1bb394;
    }
    .first-col-div span {
        font-size: 25px;
        font-weight: 400;
    }
    #first-col .col-lg-6 {
        padding-left: 0;
    }
    #first-col .ibox {
        margin-bottom: 15px;
    }
    .wrapper > div {
        padding: 10px;
    }
</style>
@endpush

@section('content')

    @include('flash::message')
    <div class="wrapper wrapper-content">
        <div class="col-lg-3" id="first-col">
            <div class="row clear-fix">
                <div class="col-lg-6">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>待入职</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="first-col-div text-center">
                                <div>
                                    <canvas id="doughnutChart-entry" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>试用期</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="first-col-div text-center">
                                <div>
                                    <canvas id="doughnutChart-try" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>正式员工</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="first-col-div text-center">
                                <div>
                                    <canvas id="doughnutChart-regular" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>日程管理</h5>
                </div>
                <div class="ibox-content">
                    <div style="height: 500px; padding: 0 20px">
                        <div class="row" style="height: 30%">
                            <h1 id="day-info-title"></h1>
                            <h4 id="day-info-subtitle"></h4>
                        </div>
                        <div class="row" style="height: 70%">
                            <h4>今日</h4>
                            <div id="day-info-content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>工作日历</h5>
                </div>
                <div class="ibox-content">
                    @include('widget.calendar-user', ['clickRead' => 1])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/plugins/chartJs/Chart.min.js') }}"></script>
<script type="text/javascript">
    function ctxFont() {
        Chart.pluginService.register({
            beforeDraw: function (chart) {
                if (chart.config.options.elements.center) {
                    //Get ctx from string
                    var ctx = chart.chart.ctx;

                    //Get options from the center object in options
                    var centerConfig = chart.config.options.elements.center;
                    var fontStyle = centerConfig.fontStyle || 'Arial';
                    var txt = centerConfig.text;
                    var color = centerConfig.color || '#000';
                    var sidePadding = centerConfig.sidePadding || 20;
                    var sidePaddingCalculated = (sidePadding/100) * (chart.innerRadius * 2);
                    //Start with a base font of 30px
                    ctx.font = "35px " + fontStyle;

                    //Get the width of the string and also the width of the element minus 10 to give it 5px side padding
                    var stringWidth = ctx.measureText(txt).width;
                    var elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;

                    // Find out how much the font can grow in width.
                    var widthRatio = elementWidth / stringWidth;
                    var newFontSize = Math.floor(30 * widthRatio);
                    var elementHeight = (chart.innerRadius * 2);

                    // Pick a new font size so it will not be larger than the height of label.
                    var fontSizeToUse = Math.min(newFontSize, elementHeight);

                    //Set font settings to draw it correctly.
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
                    var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
                    ctx.font = fontSizeToUse+"px " + fontStyle;
                    ctx.fillStyle = color;

                    //Draw text in center
                    ctx.fillText(txt, centerX, centerY);
                }
            }
        });
    }

    $(function () {
        var doughnutData = {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: ["#a3e1d4","#dedede"]
            }]
        };

        var doughnutOptions = {
            responsive: true,
            legend: {
                display: false
            },
            cutoutPercentage: 48,
            elements: {
                center: {
                    text: '',
                    color: '#686b6d', // Default is #000000
                    fontStyle: 'Arial', // Default is Arial
                    sidePadding: 20 // Defualt is 20 (as a percentage)
                }
            }
        };

        $("[id^=doughnutChart]").each(function (i, ele) {
            if ($(ele).attr('id') == "doughnutChart-entry") {
                var d1 = doughnutData;
                d1.labels = ['待入职', '剩余'];
                d1.datasets[0].data = [10, 300 - 10];

                var t1 = doughnutOptions;
                t1.elements.center.text = '10';

                new Chart(ele.getContext("2d"), {type: 'doughnut', data: d1, options:t1});
                ctxFont();
            }

            if ($(ele).attr('id') == "doughnutChart-try") {
                var d2 = $.extend(true, {}, doughnutData);
                d2.labels = ['试用期', '剩余'];
                d2.datasets[0].data = [34, 300 - 34];

                var t2 = $.extend(true, {}, doughnutOptions);
                t2.elements.center.text = '34';
                new Chart(ele.getContext("2d"), {type: 'doughnut', data: d2, options:t2});
            }

            if ($(ele).attr('id') == "doughnutChart-regular") {
                var d3 = $.extend(true, {}, doughnutData);
                d3.labels = ['正式员工', '剩余'];
                d3.datasets[0].data = [134, 300 - 134];

                var t3 = $.extend(true, {}, doughnutOptions);
                t3.elements.center.text = '134';
                new Chart(ele.getContext("2d"), {type: 'doughnut', data: d3, options:t3});
            }
        });
    });
</script>
@endpush