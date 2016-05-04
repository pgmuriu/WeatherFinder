
<?php
	
/*
*index.php 1.0.0 4 May 2016
*
*Copyright (c) Paul Muriu.
*University of Leeds, Leeds, UK. LS2 9JT
*All rights reserved.
*
*This work is Licensed under the Academic Free Licence version 3.0
*For more details, please see http://opensource.org/licenses/AFL-3.0
*/

/**
*This script uses the OpenWeatherMap API (see openweathermap.org) to get the weather.
*The openweathermap API provides the weather data in JSON format.
*This script obtains the JSON weather data for the city entered by the user, converts it to an array, extracts the weather components, and
*defines the text to display to the user.
*
*@author Paul Muriu: gy15pgm@leeds.ac.uk
*@version 1.0.0 4 May 2016
*/

/**
*Global variables will be accessed from anywhere within the script.
*Initialise the two variables with an empty string.
*/
$weather = "";
$error = "";

//Check if the user has entered a city i.e. the GET array has a city/variable/key
if(array_key_exists('city', $_GET)){
		
	//Get the file headers of the specified url
	$file_headers = @get_headers("http://api.openweathermap.org/data/2.5/weather?q=".urlencode($_GET['city'])."&appid=efb920efc5c1699f8bc4aa6fa3db4f27");
		
	//Check if city/page/url exists
	if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
			
		//Display an error if city does not exist
		$error = "That city could not be found.";
		
	//Proceed with processing only if the city exists
	} else{
			
		//Get the contents of the specified url. City entered by user completes the url
		//Encode part of url entered by user just in case it contains spaces and other unusual characters
		$urlContents = file_get_contents("http://api.openweathermap.org/data/2.5/weather?q=".urlencode($_GET['city'])."&appid=efb920efc5c1699f8bc4aa6fa3db4f27");
			
		//Process the JSON data into an array so we can extract the data
		//The 'true' flag returns the data as an associative array
		$weatherArray = json_decode($urlContents, true);
			
		//Check if the weather has been obtained before attempting to extract its components
		//A code of 200 is returned if the city entered by user exists
		if($weatherArray['cod'] == 200){
			
			$weatherMainDescription = $weatherArray['weather'][0]['description']; //Extract the weather description from the array
				
			$tempInCelsius = intval($weatherArray['main']['temp'] - 273); //Extract temperature, convert to celsius and return as whole number
				
			$humidity = intval($weatherArray['main']['humidity']);
				
			$windSpeed = $weatherArray['wind']['speed'];
				
			$latitude = $weatherArray['coord']['lat'];
				
			$longitude = $weatherArray['coord']['lon'];
				
			//Define the string to be displayed to the user
			$weather = "The weather in ".$_GET['city']." is currently ".$weatherMainDescription.". The temperature is ".$tempInCelsius."&deg;C. 
				The humidity is ".$humidity."%. The wind speed is ".$windSpeed."m/s. The latitude is ".$latitude." and the longitude is ".$longitude.".";
					
		} else {
				
			//If the weather has not been obtained, define the error to display
			$error = "Could not find the city - please try again.";
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

	<title>Know the Weather</title>
	
	<!-- The file that will style the web page -->
	<link rel="stylesheet" href="style.css" type="text/css">

</head>
<body>
	<div class="container">
		<div class="innerpage">
			<div class = "info">
			
				<!-- Name of the web application -->
				<h1>Weather Finder</h1>
				
				<form>
					<div><label for="city">Enter the name of a city to get the current weather.</label></div>
					<br>
					
					<!-- Text box where the user enters the city name -->
					<div><input type="text" name="city" id="city" class="textbox" placeholder="Eg. Leeds, New York" value="<?php 
						
						//If the user enters a city name, retain it in the input box
						if(array_key_exists('city', $_GET)){
														
							echo $_GET['city']; 
								
						} ?>">
					</div>
					<br>	
					<div><button type="submit" id="myButton">Submit</button></div>
					<br>
				</form>
			</div>
			
			<div id="weather"><?php
			
				if($weather){
					
					//If the weather has been obtained, display the following message
					echo '<div class="success">City found, see map below for the weather details</div>';
					
				} else if($error){
					
					//If the weather has not been obtained, display the error
					echo '<div class="error">'.$error.'</div>';
				}
					
			?></div>
			
			<div id="map"></div>
			<script>
			
				/**
				*This script sets up the map, places the marker, and attaches weather information to the info window.
				*It sets up the map and processes the weather information based on the city entered by the user. 
				*It also applies styles to the map to make it more attractive to the user.
				*
				*@author Paul Muriu: gy15pgm@leeds.ac.uk
				*@version 1.0.0 4 May 2016
				*/
				var city = "<p><b><?php echo $_GET['city'] ?> </b></p>"; //Assign city name entered by user to a javascript variable as a string
				var myCity = city.toUpperCase(); //Convert city name entered by user to upper case
				var descTitle = "<b>General Weather Description: </b>"; //Assign a string to a javascript variable
				var weatherMainDescription = "<?php echo $weatherMainDescription ?>"; //Assign contents of a php variable to a javascript variable
				var tempTitle = "<br><b>Temperature: </b>";
				var tempInCelsius = "<?php echo $tempInCelsius ?> &deg;C";
				var humidityTitle = "<br><b>Humidity: </b>";
				var humidity = "<?php echo $humidity ?> %";
				var speedTitle = "<br><b>Wind Speed: </b>";
				var windSpeed = "<?php echo $windSpeed ?> m/s";
				var latitudeTitle = "<br><b>Latitude: </b>";
				var latitude = "<?php echo $latitude ?>";
				var longitudeTitle = "<br><b>Longitude: </b>";
				var longitude = "<?php echo $longitude ?>";
				
				//Define the text to appear in the info window by concatenating strings	
				var infotext = "<div class='infotext'>" + myCity.concat(descTitle, weatherMainDescription, tempTitle, tempInCelsius, humidityTitle, humidity, speedTitle, windSpeed, latitudeTitle, latitude, longitudeTitle, longitude) + "</div>";
											
				/**
				*This function is used to set up the map.
				*It centres the map on the city entered by the user.
				*It applies custom styles to the map
				*It displays a marker on the city entered by the user and displays weather information using an info window
				*/
				function initMap() {
					//The object variable refers to the latitude and longitude values for the city entered by user
					//The latitude and longitude values are obtained fron the OpenWeatherMap API
					var myLatLng = {lat: parseFloat(latitude), lng: parseFloat(longitude)};
									  
					//Create an array of styles to use to style the map.
					//Specify features and elements to define the styles.
					var styleArray = [
						{
						  stylers: [
							{ hue: "#00ffe6" },
							{ saturation: -20 }
						  ]
						},{
						  featureType: "road",
						  elementType: "geometry",
						  stylers: [
							{ lightness: 100 },
							{ visibility: "simplified" }
						  ]
						},{
						  featureType: "road",
						  elementType: "labels",
						  stylers: [
							{ visibility: "off" }
						  ]
						}
					  ];
					
					//Create a map object and specify the DOM element for display.
					var map = new google.maps.Map(document.getElementById('map'), {
					center: myLatLng, //Center the map on city entered by user based on its coordinates
					styles: styleArray, //Apply the map style array to the map.
					zoom: 8
					});
					
					var marker = new google.maps.Marker({
						position: myLatLng, //Marker to display on selected city based on coordinates
					});
					
					marker.setMap(map); //Attach marker to the map
					
					//Create an info window and attach to it the text defined earlier
					var infowindow = new google.maps.InfoWindow({content: infotext});
					
					infowindow.open(map, marker); //The info window opens at the marker location
				}
			 
			</script>
			
			<!-- A key is needed to be able to use the google map -->
			<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbiPSPxzUK5MCmoPWllZV1P_Dk4HKULO8&callback=initMap"
			async defer></script>
		</div>		
	</div>
		
	<!-- Bring in jQuery into the file -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	   
</body>
</html>