@extends('layouts.main')

@section('title_menu', 'Tempratures')

@section('content')
    <div id="chart">
        {{-- chart akan dirender --}}
    </div>
@endsection

@push('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>

    <script>
        let chart; // variabel chart untuk menampung data chart

        async function requestData() {
            let baseUrl = '{{ url('') }}'; // http://127.0.0.1:8000/
            let endpoint = 'api/v1/temperatures';

            // http://127.0.0.1:8000/api/v1/temperatures
            const result = await fetch(`${baseUrl}/${endpoint}`);

            if (result.ok) {
                const data = await result.json();
                const temperatures = data.data;
                const firstTemperature = temperatures[0];

                const date = firstTemperature.created_at;
                const value = Number(firstTemperature.value);

                console.log(date, value);

                const point = [
                    new Date(date).getTime(),
                    value
                ];

                const series = chart.series[0],
                    shift = series.data.length > 20;

                chart.series[0].addPoint(point, true, shift);

                setTimeout(requestData, 1000);
            }
        }

        window.addEventListener('load', function() {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'chart', // id dari div tempat chart di render
                    defaultSeriesType: 'spline',
                    events: {
                        load: requestData
                    }
                },
                title: {
                    text: 'Monitoring Temperature'
                },
                xAxis: {
                    type: 'datetime',
                    tickPixelInterval: 150,
                    maxZoom: 20 * 1000
                },
                yAxis: {
                    minPadding: 0.2,
                    maxPadding: 0.2,
                    title: {
                        text: 'Temperature (°C)',
                        margin: 80
                    }
                },
                series: [{
                    name: 'Temperature',
                    data: []
                }]
            });
        });
    </script>
@endpush
