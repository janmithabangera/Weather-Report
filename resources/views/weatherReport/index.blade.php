<html>

<head>
    <title>Weather Report</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script type="text/javascript">
        $(document).ready(function() {

            $('ul li a').click(function() {
                $('li a').removeClass("active");
                $(this).addClass("active");
            });

            $("#showCurrentWeather").click(function() {
                $("#currentWeather").show();
                $('#next24Hours').hide();
                $('#next7days').hide();
            });

            $("#showNext24Hours").click(function() {
                $("#next24Hours").show();
                $('#currentWeather').hide();
                $('#next7days').hide();
            });

            $("#showNext7Days").click(function() {
                $("#next7days").show();
                $('#currentWeather').hide();
                $('#next24Hours').hide();
            });

        });
    </script>
</head>

<body>
    <div class="container">
        @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ $message }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="row">
            <form method="post" action="{{route('weatherReport.results')}}" enctype="multipart/form-data" accept-charset="UTF-8">
                {{ csrf_field() }}
                <div class=" col-6">
                    <h1 for="exampleInputLocation" class="form-label">Enter Location</h1>
                    <input type="text" class="form-control" id="location" name="location" placeholder="Location" value="{{ old('location', request()->input('location'))}}" aria-describedby="location">
                </div>
            </form>
        </div>

        <div class="row col-6">
            @if(isset($currentWeather))
            <div id="currentWeather">
                <p> Current Weather: {{$currentWeather['weather'][0]['main']}}</p>
                <p> Weather description: {{$currentWeather['weather'][0]['description']}} </p>
                <p> Current Temp: {{$currentWeather['main']['temp']}} &degC</p>
                <p> Feels like: {{$currentWeather['main']['feels_like']}}&degC</p>
                <p> Humidity: {{$currentWeather['main']['humidity']}}%</p>
            </div>
            @endif

            @if(isset($futureWeatherForecast['next24Hours']))
            <div id="next24Hours" style="display:none;" class="next24Hours ">
                <table class="table table-responsive table-bordered">
                    <thead>
                        <tr class="table-active">
                            <th>Date and Time</th>
                            <th>Weather</th>
                            <th>Temp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($futureWeatherForecast['next24Hours'] as $data)
                        <tr>
                            <th scope="row">{{$data['hour']}}</th>
                            <td>{{$data['weather']}}</td>
                            <td colspan="2">{{$data['temperature']}}&degC</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            @if(isset($futureWeatherForecast['next7Days']))
            <div id="next7days" style="display:none;" class="next7days ">
                <table class="table table-responsive table-bordered ">
                    <thead>
                        <tr class="table-active">
                            <th>Date</th>
                            <th>Weather</th>
                            <th>Temp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($futureWeatherForecast['next7Days'] as $data)
                        <tr>
                            <th scope="row">{{$data['date']}}</th>
                            <td>{{$data['weather']}}</td>
                            <td colspan="2">{{$data['temperature']}}&degC</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <div class="row">
            <nav>
                <ul>
                    <li><a class="menu active" href="javascript:void(0)" id="showCurrentWeather">Current Weather </a></li>
                    <li><a class="menu" href="javascript:void(0)" id="showNext24Hours">Next 24 hours </a></li>
                    <li><a class="menu" href="javascript:void(0)" id="showNext7Days">Next 7 Days</a></li>
                </ul>
            </nav>
        </div>
    </div>
</body>

</html>