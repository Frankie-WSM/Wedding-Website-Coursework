<html>
<head>
<link rel="stylesheet" href="wedding-style.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<div class="container sticky-top">
	<div id="header">
		<image class ="ringImg" src="Ring.PNG">
		<image class ="cakeImg" src="Cake.PNG">
		<p id="headerText">Wedding Venue Search</p>
	</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#searchButton").click(function() {
                var startDate = $("#startDate").val();
				var endDate = $("#endDate").val();
				var partySize = $("#partySize").val();
				var grade = $("#grade").val();
				if (endDate < startDate) {
					let insertedHtml = "<p class='errorMsg'>From date cannot be before To date. Please try again.</p>";
					$("#serverResponse").html(insertedHtml);
				} else {
					if (partySize < 1) {
						let insertedHtml = "<p class='errorMsg'>Party size cannot be less than a person. Please try again.</p>";
						$("#serverResponse").html(insertedHtml);
					} else if (partySize > 1000) {
						let insertedHtml = "<p class='errorMsg'>Sorry, no venues accomodate more than 1000 guests. Please try again.</p>";
						$("#serverResponse").html(insertedHtml);
					} else {
		
						$.ajax({
							url: "venue-request.php",
							type: "GET",
							data: {startDate:startDate, endDate:endDate, partySize:partySize, grade:grade},
							success: function (responseData) {
								let len = responseData.length;
								
								if (len == 0) {
									let insertedHtml = "<p class='errorMsg'>Unfortunately no venues matching these criteria were found.</p>";
									$("#serverResponse").html(insertedHtml);
								} else {
								
									let insertedHtml = "<div class='card-holder w-300'><div class='wrapper'>";
									let gridCount = 1;
									
									for (let i = 0; i < len; i++) {
										let name = responseData[i].name;
										let capacity = responseData[i].capacity;
										let weekend_price = responseData[i].weekend_price;
										let weekday_price = responseData[i].weekday_price;
										let licensedNum = responseData[i].licensed;
										let cost = responseData[i].cost;
										let bookings = responseData[i].bookings;
										let freeDates = responseData[i].freeDates;
										
										var strDates = [];
										for (let j = 0; j < freeDates.length; j++) {
											strDates.push(dayCostString(freeDates[j], partySize, cost, weekday_price, weekend_price));
										}
										
										if (licensedNum == "1") {
											var licensed = "Licensed";
										} else {
											var licensed = "Un-licensed";
										}
										
										let partySizeInt = parseInt(partySize);
										let costInt = parseInt(cost);
										let weekdayPriceInt = parseInt(weekday_price);
										let weekendPriceInt = parseInt(weekend_price);	
								
										
										insertedHtml +=
										"<div class='card mb-3'>" +
										"<img class='card-img-top' src='" + name + ".jpg' alt='Card image cap'>" +
										"<div class='card-body'>" +
										"<h5 class='card-title'>" + name + "</h5>" +
										"<p class='card-text'>This venue is " + licensed + " and accomodates up to " + capacity + " people. It has been booked " + bookings + " times before.</p>" +
										"<p class='card-text'>The catering cost for your selected grade is £" + cost + " per person. The booking cost for weekdays is £" + weekday_price + " and for weekends is £" + weekend_price + " .</p>" +
										"<p class='card-text'>The available dates for this venue in your date range are:</p>";
										for (let k = 0; k < strDates.length; k++) {
											insertedHtml += "<p class='date-text'>" + strDates[k] + "</p>";
										}
										insertedHtml +=
										"</div>" +
										"</div>";					
					
									}
									insertedHtml += "</div></div>";
									
									$("#serverResponse").html(insertedHtml);
								}
							},
							error: function (xhr, status, error) {
								console.log(xhr.status + ': ' + xhr.statusText);
							},
							dataType: "json"
						});
					}
				}	
            });
        });
		
		function dayCostString(date, partySize, cost, weekday_price, weekend_price) {
			//getting the date in string form
			let dateParts = date.split('-');
			let year = parseInt(dateParts[0]);
			let month = parseInt(dateParts[1]) - 1;
			let day = parseInt(dateParts[2]);
			let d = new Date(year, month, day);
			let dayNum = d.getDay()
			let dayArr = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
			let monthArr = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
			var monthStr = monthArr[month];
			var dayName = dayArr[dayNum];
			var dayStr = (dayName + " " + day.toString() + " " + monthStr + " " + year.toString());
			//getting the cost for the day
			let partySizeInt = parseInt(partySize);
			let costInt = parseInt(cost);
			let weekdayPriceInt = parseInt(weekday_price);
			let weekendPriceInt = parseInt(weekend_price);
							
			if (dayNum == 0 || dayNum == 6) {
				var yourCost = (partySizeInt * costInt) + weekendPriceInt;
			} else {
				var yourCost = (partySizeInt * costInt) + weekdayPriceInt;
			}
			
			var finalStr = (dayStr + "<br>Your price on this day: £" + yourCost);
			return (finalStr);
		}
		
    </script>
	
</head>
<body>
 
 <div class="container">
 <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
	<button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="3" aria-label="Slide 4"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="4" aria-label="Slide 5"></button>
	<button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="5" aria-label="Slide 6"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="Ashby Castle.jpg" class="d-block w-100">
      <div class="carousel-caption d-none d-md-block">
        <p class="imageTitle">Ashby Castle</p>
        <p class="imageText">Built in 1092, ruined on your wedding day.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="Haslegrave Hotel.jpg" class="d-block w-100">
      <div class="carousel-caption d-none d-md-block">
        <p class="imageTitle">Haslegrave Hotel</p>
        <p class="imageText">The priest is an AI.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="Forest Inn.jpg" class="d-block w-100">
      <div class="carousel-caption d-none d-md-block">
        <p class="imageTitle">Forest Inn</p>
        <p class="imageText">(Not actually in a forest)</p>
      </div>
    </div>
	<div class="carousel-item">
      <img src="Divorce.jpeg" class="d-block w-100">
      <div class="carousel-caption d-none d-md-block">
        <p class="imageTitle">Whoops!</p>
        <p class="imageText">How did this get here???</p>
      </div>
    </div>
	<div class="carousel-item">
      <img src="Sea View Tavern.jpg" class="d-block w-100">
      <div class="carousel-caption d-none d-md-block">
        <p class="imageTitle">Seaview Hotel</p>
        <p class="imageText">First dance must be to a shanty.</p>
      </div>
    </div>
	<div class="carousel-item">
      <img src="Fawlty Towers.jpg" class="d-block w-100">
      <div class="carousel-caption d-none d-md-block">
        <p class="imageTitle">Fawlty Towers</p>
        <p class="imageText">I know nothing. Que?</p>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
</div>

<p id="guide">Please enter two dates to search between. If you have a specific day please enter it for both start and end date.</p>

<div class="container" id="inputContainer">
<div class="row g-2">
  <div class="col-md">
    <div class="form-floating">
      <input type="number" class="form-control" id="partySize" min="1" max="1000" required >
      <label class="inputLabel" for="floatingInputGrid">Party Size:</label>
    </div>
  </div>
  <div class="col-md">
    <div class="form-floating">
      <select class="form-select" id="grade" required >
        <option selected>Select from list</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
		<option value="4">4</option>
        <option value="5">5</option>
      </select>
      <label class="inputLabel" for="floatingSelectGrid">Catering Grade:</label>
    </div>
  </div>
</div>
<div class="row g-2">
  <div class="col-md">
    <div class="form-floating">
      <input type="date" class="form-control" id="startDate" min="" required >
      <label class="inputLabel" for="floatingInputGrid">From Date:</label>
    </div>
  </div>
  <div class="col-md">
    <div class="form-floating">
      <input type="date" class="form-control" id="endDate" min="" required >
      <label class="inputLabel" for="floatingInputGrid">To Date:</label>
    </div>
  </div>
</div>
</div>

<div id="button">
	<button id="searchButton" class="btn btn-outline-dark">Show venues</button>
</div>
  
<div class="container" id="cardContainer">
	<div id="serverResponse">
	</div>
</div>

<script>
	var inpToday = new Date();
	var inpDay = inpToday.getDate();
	var inpMonth = inpToday.getMonth()+1; 
	var inpYear = inpToday.getFullYear();
	if (inpDay < 10) {
		inpDay = "0" + day
	} 
	if (inpMonth < 10) {
		inpMonth = "0" + inpMonth
	} 

	inpToday = inpYear + "-" + inpMonth + "-" + inpDay;
	document.getElementById("startDate").setAttribute("min", inpToday);
	document.getElementById("endDate").setAttribute("min", inpToday);
</script>

</body>
</html>