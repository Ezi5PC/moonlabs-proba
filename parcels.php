<?php 
require_once __DIR__ . '/connect.php';

    function decideWhich(){
        if($_SERVER["REQUEST_METHOD"] === "GET"){
            if(isset($_GET['parcel_number'])){
                getParcel($_GET['parcel_number']);
            }else{
                echo '{"notice": {"text": "Parcel not found"}';
            }
        }
        else if($_SERVER["REQUEST_METHOD" === "POST"]){
            insertParcel();
        }
        else{
            echo '{"notice": {"text": "Method not allowed"}';
        }
    }

    function getParcel($parcel_number){
        $db = new Connect;
        $parcels = array();
        $data = $db->prepare('SELECT * FROM parcels WHERE parcel_number = :parcel_number');
        $data->execute(array(':parcel_number' => $parcel_number));
        while ($OutputData = $data->fetch(PDO::FETCH_ASSOC)) {
            $id = $OutputData['user_id'];
            $user = $db->prepare('SELECT * FROM users WHERE id = :id');
            $user->execute(array(':id' => $id));
            $user = $user->fetch(PDO::FETCH_ASSOC);
            $outputUser = array(
                'id' => $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email_address' => $user['email_address'],
                'phone_number' => $user['phone_number']
            );
            $parcels[$OutputData['parcel_number']] = array(
                'id' => $OutputData['id'],
                'parcel_number' => $OutputData['parcel_number'],
                'size' => $OutputData['size'],
                'user' => $outputUser
            );
        }
        header("Content-Type: application/json");
        echo json_encode($parcels);
    }
    function insertParcel() {
        $db = new Connect;
        if($_POST['size'] === 'S' || $_POST['size'] === 'M' || $_POST['size'] === 'L' || $_POST['size'] === 'XL'){
            $size = $_POST['size'];
        }else{
            echo '{"notice": {"text": "Size must be S, M, L or XL"}';
            return;
        }
        $userid = $_POST['userid'];
        $parcel_number = generateRandomHex();
        $isParcelNumberUnique = $db->prepare('SELECT * FROM parcels WHERE parcel_number = :parcel_number');
        $isParcelNumberUnique->execute(array(':parcel_number' => $parcel_number));
        while($isParcelNumberUnique->fetch(PDO::FETCH_ASSOC)){
            $parcel_number = generateRandomHex();
            $isParcelNumberUnique->execute(array(':parcel_number' => $parcel_number));
        }
        $id = $db->prepare('SELECT MAX(id) FROM parcels');
        $id->execute();
        $id = $id->fetch(PDO::FETCH_ASSOC);
        $parcelid = $id['MAX(id)'] + 1;
        $data = $db->prepare('INSERT INTO parcels(id, parcel_number, size, user_id) VALUES(:id, :parcel_number, :size, :user_id)');
        $data->execute(array(':id' => $parcelid, ':parcel_number' => $parcel_number, ':size' => $size, ':user_id' => $userid));
        echo '{"notice": {"text": "Parcel Added"}';
    }

    function generateRandomHex(){
        $char = '0123456789ABCDEF';
        $result = '';
        for($i = 0; $i < 10; $i++){
            $result .= $char[rand(0, strlen($char) - 1)];
        }
        return $result;
    }

    decideWhich();
?>