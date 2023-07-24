<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;
use Config;
use Exception;


class WeatherReportController extends Controller
{
    public function index(Request $request)
    {
        if (isset($request->location)) {
            $currentWeather = $this->getCurrentWeather($request);
            $futureWeatherForecast = $this->getWeatherForecast($request);

            if (isset($currentWeather['errorMessage']) || (isset($futureWeatherForecast['errorMessage']))) {

                $message = $currentWeather['errorMessage'] ?? $futureWeatherForecast['errorMessage'];
                return redirect()->route('weatherReport.index')->with('error', $message);
            }

            return view('weatherReport.index')->with([
                'currentWeather' => $currentWeather,
                'futureWeatherForecast' => $futureWeatherForecast, "location" => $request->location
            ]);
        } else {
            return view('weatherReport.index');
        }
    }

    private function getCurrentWeather(Request $request)
    {
        $location = $request->location;
        try {
            $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=" . $location . "&units=metric&APPID=" .  Config::get('constants.apiKey');
            $responseData = $this->getOpenWeatherMapApiResponse($apiUrl);
            if ($responseData['cod'] == 200) {
                return $responseData;
            } else {
                $responseData['errorMessage'] = $responseData['message'];
            }
        } catch (Exception $e) {
            $responseData['errorMessage'] = "Please try after sometime.";
        }
        return $responseData;
    }

    private function getWeatherForecast(Request $request)
    {
        $location = $request->location;
        try {
            $apiUrl = "https://api.openweathermap.org/data/2.5/forecast?q=" . $location . "&units=metric&APPID=" . Config::get('constants.apiKey');
            $responseData = $this->getOpenWeatherMapApiResponse($apiUrl);

            if ($responseData['cod'] == 200) {
                $next24HoursData = $next5DaysData = [];
                $previousDate = $currentDate = null;

                foreach ($responseData['list'] as $data) {

                    $dateTime = Carbon::createFromTimestamp($data['dt'] + ($responseData['city']['timezone']));
                    if ($currentDate == null) {
                        $currentDate = $dateTime->copy();
                        $nextDay = $dateTime->copy()->addDays(1);
                    }

                    if ($dateTime <= $nextDay) {
                        $next24Hours['hour'] = Carbon::parse($dateTime)->format('Y-m-d g:i A');
                        $next24Hours['weather'] = $data['weather'][0]['main'];
                        $next24Hours['temperature'] = $data['main']['temp'];
                        array_push($next24HoursData, $next24Hours);
                    }
                    if (($previousDate == null && $dateTime > $currentDate->copy()->endOfDay()) || ($dateTime == $previousDate)) {
                        $next5Days['date'] = Carbon::parse($dateTime)->format('D, M d');
                        $next5Days['weather'] = $data['weather'][0]['main'];
                        $next5Days['temperature'] = $data['main']['temp'];
                        array_push($next5DaysData, $next5Days);
                        $previousDate = $dateTime->copy()->addDays(1);
                    }
                }
                $dataToBeReturned['next24Hours'] = $next24HoursData;
                $dataToBeReturned['next5Days'] = $next5DaysData;
            } else {
                $dataToBeReturned['errorMessage'] = $responseData['message'];
            }
        } catch (Exception $e) {
            $dataToBeReturned['errorMessage'] = "Please try after sometime.";
        }
        return $dataToBeReturned;
    }

    private function getOpenWeatherMapApiResponse($apiUrl)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        curl_close($ch);

        $responseData = json_decode($response, true);
        return $responseData;
    }
}
