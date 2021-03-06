<?php    
// Headers Required
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/Database.php';
include_once '../../models/Games.php';
include_once '../../models/Users.php';

$database = new Database();
$db = $database->getConnection();
$game = new Games($db);
$user = new Users($db);

if (
    isset($_GET['api_key']) && 
    isset($_GET['game_id'])
) {
    $user_id = $user->fetch_id($_GET['api_key']);

    if ($game->verify($user_id, $_GET['game_id'])) {
        if ($game->populate_data($_GET['game_id'])) {
            $data = $game->data_array();
            echo json_encode($data);
            return;
        } else {
            http_response_code();
            echo json_encode(array("message" => "Unable to Get Game Data", "issue" => "Internal Server Error"));
            return;
        }
    } else {
        http_response_code(401); 
        echo json_encode(array("message" => "Unable to Get Game Data", "issue" => "Invalid API key and Game Id pair"));
        return;
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to Get Game Data", "issue" => "Missing Parameters", "required_params" => "api_key, game_id"));
    return;
}