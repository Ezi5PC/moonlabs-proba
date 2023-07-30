<?php
require_once __DIR__ . '/connect.php';
    function decide(){
        if($_SERVER["REQUEST_METHOD"] === "GET"){
            getUsers();
        }
        else if($_SERVER["REQUEST_METHOD" === "POST"]){
            insertUser();
        }
        else{
            echo '{"notice": {"text": "Method not allowed"}}';
        }
    }

    function getUsers() {
            $db = new Connect;
            $users = array();
            $data = $db->prepare('SELECT * FROM users ORDER BY id');
            $data->execute();
            while ($OutputData = $data->fetch(PDO::FETCH_ASSOC)) {
                $users[$OutputData['id']] = array(
                    'id' => $OutputData['id'],
                    'first_name' => $OutputData['first_name'],
                    'last_name' => $OutputData['last_name'],
                    'email_address' => $OutputData['email_address'],
                    'phone_number' => $OutputData['phone_number']
                );
            }
            header("Content-Type: application/json");
            echo json_encode($users);
        }

    function insertUser() {
            $db = new Connect;
            $id = $db->prepare('SELECT MAX(id) FROM users');
            $id->execute();
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email_address = $_POST['email_address'];
            isset($_POST['phone_number']) ? $phone_number = $_POST['phone_number'] : $phone_number = null;
            $data = $db->prepare('INSERT INTO users(id, first_name, last_name, email_address, phone_number) VALUES(:id, :first_name, :last_name, :email_address, :phone_number)');
            $data->execute(array('id' => $id, ':first_name' => $first_name, ':last_name' => $last_name, ':email_address' => $email_address, ':phone_number' => $phone_number));
            
        }

    decide();
?>