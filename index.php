<?php
/*
Plugin Name: TaskPlugin
Plugin URI:
Description: Task plugin for work
Author: Alessandro
Author URI:
Version: 0.1
*/

add_action("admin_menu","addMenu");
function addMenu()
{
	add_menu_page("Task","Task Options",4,"task-options","taskMenu");
}


add_shortcode('external_data','callback_function_name');

function callback_function_name(){

	$url = 'https://api.jsonbin.io/b/601fd8aa81c79e442992afc9/14'; //Getting the json from this url



	$arguments = array(
		'method' => 'GET',


	);

	$response = wp_remote_get($url,$arguments); //storing the json data from the url and storing it to $response

	if (is_wp_error($response)) {
		$error_message = $response->get_error_message();
		return "Something went wrong: $error_message";


	}
	
	$results = json_decode(wp_remote_retrieve_body($response)); //decoding the json from the response variable




	$stars = ""; //string to store the rating and convert it into stars
	$images =[]; //array to store the logo's images
	$brand =[]; //array to store the brand id
	$bonus =[]; //array to store the bonus
	$play =[];//array to store the url's for the play button
	$terms =[]; //array to store the terms and conditions
	
		
	

	for ($i=0; $i < 4 ; $i++) { //Loop that runs for 4 times because in total there are 4 rows that we wish to display from the json
		array_push($images,$results->toplists->id[$i]->logo);  //storing the images into the array that we have initilized at the top
		array_push($brand, $results->toplists->id[$i]->brand); //storing the brand into the array
		array_push($bonus, $results->toplists->id[$i]->info->bonus); //storing the bonus into the array
		array_push($play,$results->toplists->id[$i]->play_url);//storing the play url links into the array
		array_push($terms,$results->toplists->id[$i]->terms_and_conditions); //storing the terms and conditions into the array
	}

	
	$color = "style='background-color : Gold';"; //defining gold color to be used for the headers of the table
	$casinoHeader = 'Casino'; //First Header
	$bonusHeader = 'Bonus'; //Second Header
	$featuresHeader ='Features';//Third Header
	$playHeader ='Play';//Fourth Header
	$html = '';
	$html .= '<table>'; //This is the part where I have created the table structure
	$html .= '<tr>';
	$html .= "<td ".$color.">".$casinoHeader."</td>"; //Displaying the headers with the gold color that was defined at the top
	$html .= "<td ".$color.">".$bonusHeader."</td>";
	$html .= "<td ".$color.">".$featuresHeader."</td>";
	$html .= "<td ".$color.">".$playHeader."</td>";
	$html .= '</tr>';
	$html .= '<tr>';
	for ($i=0; $i < 4 ; $i++) { //This loop will display all the four rows with the data from the array's and from the json
	//First we have displayed all the images from the array and then underneath the images I have displayed the Review Href with the brand id as the href
		$html .=  '<td>'  .'<img src="'.$images[$i]. '" height="200%" width="100%" />'. '<br>' . '<a href="'.$brand[$i].'">Review</a>' .'</td>';
		for($y=0;$y<$results->toplists->id[$i]->info->rating;$y++){ //This is the loop for the rating so we convert the number from the rating to actual stars
    $stars .= "★"; //Depending on the rating number a star is added to the varaible $stars
    
	}


		$html .=  '<td>' .$stars. '<br>'  .$bonus[$i]  . '</td>'; //Here we are displaying the actual stars and also the bonus text from the bonus array
		$stars = null; //Then we  are resetting the variable starts so each time that the starts are shown, the string gets empty because if not the previous value of the starts will be added to the current one.

			//Displaying the features by getting the the features array from the json and displaying the 3 features each time and showing them inside a list
			$html .=  '<td>' . '<ul>' . '<li>' . $results->toplists->id[$i]->info->features[0]. '</li>'.'<li>' .$results->toplists->id[$i]->info->features[1] . '</li>' . '<li>' . $results->toplists->id[$i]->info->features[2] . '</li>' . '</ul>'.  '</td>';
		
		//Play button link(This is not working as for some reason the link when you click it does direct you directly to that url but it is adding that url with my website url). Also here we are displaying the terms and conditions below the play now button
		$html .=  '<td>' ."<button onclick=\"location.href='.$play[$i].'\">Play Now</button>" . '<br>' .$terms[$i].'</td>';

		$html .= '<tr>';

		
			
		

	
		
	}
	



	$html .='<tr>';
	
	
	
	$html .= '</table>'; //Closing table

	return $html;
}
