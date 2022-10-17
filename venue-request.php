<?php

$username = "coa123wuser";
$password = "grt64dkh!@2FD";
$servername = "sci-mysql";
$dbname = "coa123wdb";

$startDate = trim($_GET["startDate"]);
$endDate = trim($_GET["endDate"]);
$partySize = trim($_GET["partySize"]);
$grade = trim($_GET["grade"]);
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT `venue_id`, `booking_date` FROM `venue_booking` WHERE `booking_date` BETWEEN '$startDate' AND '$endDate';"; 
$result = mysqli_query($conn, $sql);
$takenDatesArr = array();

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$takenDatesArr[] = $row;
}

$dayAmount = abs(strtotime($startDate) - strtotime($endDate));
$venueArr = array();

for ($i = 1; $i < 11; $i++) {
	$freeDateArr = array();
	$allDates = all_dates_array($startDate, $endDate);
	foreach ($takenDatesArr as &$booking) {
		if ($booking["venue_id"] == $i) {
			$allDates = array_diff($allDates, array($booking['booking_date']));
		}
	}
	
	if (!empty ($allDates)) {
		$venueSql="SELECT DISTINCT `name`, `capacity`, `weekend_price`, `weekday_price`, `licensed`, `cost`, COUNT(`booking_date`) AS `bookings` FROM `venue` INNER JOIN `catering` ON venue.venue_id = catering.venue_id INNER JOIN `venue_booking` ON venue.venue_id = venue_booking.venue_id WHERE venue_booking.venue_id = $i AND `capacity` >= $partySize AND `grade` = $grade GROUP BY `name`, `capacity`, `weekend_price`, `weekday_price`, `licensed`, `cost`;";
		$venueInfo = mysqli_query($conn, $venueSql);
		if (!$venueInfo) {
			printf("Error: %s\n", mysqli_error($conn));
			exit();
		}	
		while ($row = mysqli_fetch_array($venueInfo, MYSQLI_ASSOC)) {
	
			foreach($allDates as $freeDate) {
				$freeDateArr[] = $freeDate;
			}
			$row["freeDates"] = $freeDateArr;
			$venueArr[] = $row;
		}
		
	}
}

echo json_encode($venueArr);

function all_dates_array($fromDate, $toDate) {
	$allDates = [];
	
	$intFromDate = mktime(1, 0, 0, substr($fromDate, 5, 2), substr($fromDate, 8, 2), substr($fromDate, 0, 4));
    $intToDate = mktime(1, 0, 0, substr($toDate, 5, 2), substr($toDate, 8, 2), substr($toDate, 0, 4));
	
	if ($intToDate >= $intFromDate) {
        array_push($allDates, date('Y-m-d', $intFromDate)); // first entry
        while ($intFromDate < $intToDate) {
            $intFromDate += 86400; // add 24 hours
            array_push($allDates, date('Y-m-d', $intFromDate));
        }
    }
    return $allDates;
}
?>