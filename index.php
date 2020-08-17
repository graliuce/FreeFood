<?php include_once("config.php"); ?>

<?php
	$unix_time_offset = 21600;
	
	$all_listings = db_query("SELECT * FROM free_food_listings");
	while ($listing = mysqli_fetch_assoc($all_listings)) {
		$id = $listing['id'];
		if (time() - strtotime($listing['timestamp']) - $unix_time_offset > $listing['duration']) {
			db_query("DELETE FROM free_food_listings WHERE id = '$id'");
		} else {
			$top = $listing['location_y'];
			$left = $listing['location_x'];
			echo "<div style='width: 2vw; position: absolute; top: $top"."px; left: $left"."px; display: block;' onclick='openListingInfo($id)'><img src='static/location_marker.png' style='width: 100%;'>Listing $id</div>";
		}
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_POST['new_listing'])) {
			$category = clean_input($_POST['category']);
			$food = clean_input($_POST['food']);
			$remaining = clean_input($_POST['remaining']);
			$interested = 0;
			$room = clean_input($_POST['room']);
			$location_x = clean_input($_POST['location_x']);
			$location_y = clean_input($_POST['location_y']);
			$duration = clean_input($_POST['duration']) * 60;
			$comments = clean_input($_POST['comments']);

			db_query("INSERT INTO free_food_listings (category, food, remaining, interested, room, location_x, location_y, duration, comments)
					VALUES ('$category', '$food', '$remaining', '$interested', '$room', '$location_x', '$location_y', '$duration', '$comments')");
		} elseif (isset($_POST['interest'])) {
			$id = $_POST['id'];
			db_query("UPDATE free_food_listings SET interested = interested + 1 WHERE id = '$id'");
		} elseif (isset($_POST['filters'])) {
			// MAKE THIS REMOVE ALL EXISTING MARKERS
			$categories = $_POST['categories'];
			
			while ($listing = mysqli_fetch_assoc($all_listings)) {
				$id = $listing['id'];
				$category = $listing['category'];
				if (time() - strtotime($listing['timestamp']) - $unix_time_offset > $listing['duration']) {
					db_query("DELETE FROM free_food_listings WHERE id = '$id'");
				} elseif (in_array($category, $categories)) {
					$top = $listing['location_y'] * 45;
					$left = $listing['location_x'] * 80;
					echo "<div style='width: 5vw; position: absolute; top: $top"."vw; left: $left"."vw; display: block;' onclick='openListingInfo($id)'><img src='images/location_marker.png' style='width: 100%;'>Listing $id</div>";
				}
			}
		}
	}
?>

<html>

<head>
<title>Home</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="CssCopy2.css">
<script>
	function openListingInfo(id) {
		document.getElementById("listing_info").style.display = "block";

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				document.getElementById("listing_info_content").innerHTML = this.responseText;
			}
		};
		xmlhttp.open("GET", "listing_info.php?id=" + id, true);
		xmlhttp.send();
	}

	window.onclick = function(event) {
		if (event.target == document.getElementById("listing_info")) {
			document.getElementById("listing_info").style.display = "none";
		}
	}
	
	function addListing() {
		document.getElementById("myListing").style.display = "block";
	}

	function addListing2() {
		document.getElementById("myListing2").style.display = "block";
	}

	function addListing3() {
		document.getElementById("myListing3").style.display = "block";
	}

	function addListing4() {
		document.getElementById("myListing4").style.display = "block";
	}

	function addListing5() {
		document.getElementById("myListing5").style.display = "block";
	}

	
	function closeForm(element, event) {
		if (event.target === element) {
			element.style.display = "none";
		}
	}
	
	function setCoordinates(element, event) {
		var clickedCoordinates = document.getElementById("click_map").getBoundingClientRect();
		var mapCoordinates = document.getElementById("map").getBoundingClientRect();
		var xProportion = (event.clientX - clickedCoordinates.left) / (clickedCoordinates.right - clickedCoordinates.left);
		var yProportion = (event.clientY - clickedCoordinates.top) / (clickedCoordinates.bottom - clickedCoordinates.top);
		document.getElementById("location_x").value = xProportion * (mapCoordinates.right - mapCoordinates.left) + mapCoordinates.left;
		document.getElementById("location_y").value = yProportion * (mapCoordinates.bottom - mapCoordinates.top) + mapCoordinates.top;
	}

	function filter() {
		document.getElementById("filter").style.display = "block";
	}

	function applyFilter() {
		document.getElementById("filter").style.display = "none";
	}
	
	function mapKey() {
		var m = document.getElementById("mapKey");
		if (m.style.display === "none") {
			m.style.display = "block";
			document.getElementById("key-button").innerHTML="Hide Map Key";
		} else {
			m.style.display = "none";
			document.getElementById("key-button").innerHTML="View Map Key";
		}
	}
</script>
</head>

<body>

<div id="listing_info" class="background">
	<div id="listing_info_content"></div>
</div>

<div class="header">
	<img class="tiger" src="static/tiger.png"> TigerChow</img>
	<p class="slogan">Find Free Food</p>
</div>

<div class="buttons">
    <button onclick="addListing()" type="button" class = "button">Add New Listing</button>
    <button onclick="filter()" type="button" class = "button">Filter Listings</button>
</div>

<div class="map">
	<img id="map" src="static/CampusMap.jpg" style="width: 100%"/>
</div>

<div class="contact">
	<p> Questions or concerns? Email student@princeton.edu </p>
</div>


<div class="background" id="filter" onclick="closeForm(this, event)">
    <div class="popup_form_thin">
        <h1class="popup_title">Filter Listings<h1>
        <form action="">
            <input type="checkbox" id="food1">
            <label for="food1">Brunch</label><br>
            <input type="checkbox" id="food2">
            <label for="food2">Dinner</label><br>
            <input type="checkbox" id="food3">
            <label for="food3">Dessert</label><br>
            <input type="checkbox" id="food4">
            <label for="food5">Vegetarian</label><br>
            <input type="checkbox" id="food5">
            <label for="food6">Vegan</label><br>
        </form>
        <button onclick="applyFilter()" type="button" class="button">Apply</button>
    </div>
</div>

<div class="background" id="myListing" onclick="closeForm(this, event)">
    <div class="popup_form">
    	<div class="popup_header">
			<span class="close">&times;</span>
			<p>Post New Food Listing</p>
		</div>
            <form action="" method="post">
                <p class="popup_label">Food Description:</p>
                <input class="popup_input" type="text" name="food description" placeholder="ex: chocolate chip cookies" required/>
                <p class="popup_label">Organization/Event:</p>
                <input class="popup_input" type="text" name="organization/event" placeholder="ex: Women in Computer Science">
                <p class="popup_label">Location (Building and Room):</p>
                <input class="popup_input" type="text" name="location" placeholder="ex: Frist A01" required/>
                <p class="popup_label">Quantity Remaining:</p>
                <input class="popup_input" type="text" name="quantity remaining" placeholder = "ex: 25 cookies" required/>
                <p class="popup_label">Additional Information:</p>
                <input class="popup_input" type="text" name="additional info"/>
                <button onclick="addListing2()" type="button" class = "next_
                button">Next</button>
                </div>
            </form>
    </div>
</div>
<div class="background" id="myListing2" onclick="closeForm(this, event)">
    <div class="popup_form">
    	<div class="popup_header">
			<span class="close">&times;</span>
			<h2>Post New Food Listing</h2>
		</div>
            <form action="" method="post">
                    <form action="">
                        <input type="checkbox" id="food1">
                        <label for="food1">Brunch</label><br>
                        <input type="checkbox" id="food2">
                        <label for="food2">Dinner</label><br>
                        <input type="checkbox" id="food3">
                        <label for="food3">Dessert</label><br>
                        <input type="checkbox" id="food4">
                        <label for="food5">Vegetarian</label><br>
                        <input type="checkbox" id="food5">
                        <label for="food6">Vegan</label><br>
                    </form>
                <div class="popup_submit">
                    <input type="submit" name="new_listing" class="popup_submit" value="Next"/>
                </div>
                <button onclick="addListing()" type="button" class = "back_button">Back</button>
                <button onclick="addListing3()" type="button" class = "next_button">Next</button>
            </form>
    </div>
</div>
<div class="background" id="myListing3" onclick="closeForm(this, event)">
    <div class="popup_form">
    	<div class="popup_header">
			<span class="close">&times;</span>
			<p>Take a Vertical Picture of Food (Opt.)</p>
		</div>
            <form action="" method="post">
                <img src="static/cameraApp.png">
                <button onclick="addListing2()" type="button" class = "back_button">Back</button>
                <button onclick="addListing4()" type="button" class = "next_button">Next</button>
            </form>
    </div>
</div>
<div class="background" id="myListing4" onclick="closeForm(this, event)">
    <div class="popup_form">
    	<div class="popup_header">
			<span class="close">&times;</span>
			<h2>Select Approximate Location on Map</h2>
		</div>
                <form action="" method="post">
                <img src="static/CampusMap.jpg">
            </form>
    </div>
</div>
<div class="background" id="myListing5" onclick="closeForm(this, event)">
    <div class="popup_form">
    	<div class="popup_header">
			<span class="close">&times;</span>
			<h2>Select Approximate Location on Map</h2>
		</div>
            <form action="" method="post">
                <img src="static/PUCampusMap.jpg">
                <div class="popup_submit">
                    <input type="submit" name="new_listing" class="popup_submit" value="Post Listing"/>
                </div>
            </form>
    </div>
</div>
</body>
</html>


