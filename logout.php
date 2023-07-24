<?php
    function logout() {
        session_start();
        session_unset();
        session_destroy();
    }


    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        logout();
        echo json_encode(array("finished" => TRUE));
    }
?>