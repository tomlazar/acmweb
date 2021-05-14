<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . "/include/mysql.php";

if (!loggedin()) {
    require_once "login.php";
    exit();
}

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

$event_id = $input["id"];
$event_datetime = $input["edate"];
$event_publish_datetime = $input["pdate"];
$event_title = $input["name"];
$event_description = $input["description"];

if(strlen($input["name"]) > 64 || strlen($input["description"]) > 1024) {
    echo json_response(400, "Data is too long");
    return;
}

$mysql->query("USE muhostin_acm");

$stmt = $mysql->prepare("UPDATE events SET date = ?, publish_date = ?, title = ?, description = ? WHERE eventid = ?");
$success = $stmt->bind_param("ssssi", date("Y-m-d H:i:s",strtotime($event_datetime)), date("Y-m-d H:i:s",strtotime($event_publish_datetime)), $event_title, $event_description, $event_id);

if (!$success){
    echo json_response(400, "Error updating database. Please check the data.");
    return;
}

$success = $stmt->execute();
if (!$success){
    echo json_response(400, "Error updating database. Please check the data.");
    return;
}

$stmt->close();
echo json_response(200, "Success");

?>