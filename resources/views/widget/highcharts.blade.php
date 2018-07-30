{{-- http://api.highcharts.com/highcharts --}}
@push('scripts')

<script src="{{ asset('js/plugins/highcharts/highcharts.js') }}"></script>
<script>
    $(function() {
        Highcharts.setOptions({
            credits: false,
            //title: false,
            /*
            tooltip: {
                shared: true,
                crosshairs: true
            },
            legend: {
                align: 'right',
                verticalAlign: 'top',
                y: 20,
                floating: true,
                borderWidth: 0
            },
            yAxis: {
                title: false,
            },
            plotOptions: {
                series: {
                    // 透明度
                    fillOpacity: 0.5
                },
                area: {
                    marker: {
                        enabled: false,
                        symbol: 'circle',
                        radius: 2,
                        states: {
                            hover: {
                                enabled: true
                            }
                        }
                    }
                }
            },
            */
            colors: ["#39c7aa", "#636368", "#f8ac59", "#b5b8cf", "#30a8f2",  "#f8ac59", "#79d2c0", "#dedede", "#e4f0fb", "#cce7e2", "#bababa", "#54cdb4", "#d3d3d3", "#96d3c6", "#79d2c0", "#b3b3b3", "#1ab394", "#aaeeee"],
        });
    });
</script>

@endpush
