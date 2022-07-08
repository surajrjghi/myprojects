<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	
// enter your RCA ID here
define('RCA_ID', 'ex4mp13-ex4mp13-ex4mp13-ex4mp13');

$qs = http_build_query([
'start_date'=>date('Y-m-d'),
'end_date'=>date('Y-m-d'),
'api_key'=>'DEMO_KEY'
]);

$data = file_get_contents("https://api.nasa.gov/neo/rest/v1/feed?$qs");
$objNeos = json_decode($data);

$potentiallyHazardousAsteroidCount = 0;
$arrMissDistance = [];
foreach ($objNeos->near_earth_objects as $d=>$arrNeos) {
foreach ($arrNeos as $neo) {
//keep count of all potentially hazardous asteroids
if ($neo->is_potentially_hazardous_asteroid === true) {
$potentiallyHazardousAsteroidCount++;
}
//find the closest asteroids
$arrMissDistance[] = (int) $neo->close_approach_data[0]->miss_distance->miles;
}
}

$payload = [
'id'=>RCA_ID,
'pha'=>$potentiallyHazardousAsteroidCount,
'closest'=>min($arrMissDistance)
];

$ch = curl_init('https://results.rapidspike.com/rca/');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

echo "RCA API response - $result", PHP_EOL;




}

