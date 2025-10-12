<?php
session_start();
require_once __DIR__.'/db.php';
require_once 'Auth.php';

$auth = new Auth($conn);

if($_SERVER['REQUEST_METHOD']==='POST') {
    if(isset($_POST['action'])) {
        try {
            if($_POST['action']=='register') {
                $auth->register($_POST,$_FILES);
                $_SESSION['register_success']="Registration successful! Redirecting to login...";
                $_SESSION['register_modal']=true;
            }
            if($_POST['action']=='login') {
                $role = $auth->login($_POST['email'],$_POST['password']);
                if($role==='buyer') header("Location: hero.php");
                else header("Location: agent/dashboard.php");
                exit;
            }
        } catch(Exception $e) {
            if($_POST['action']=='register') $_SESSION['register_msg']=$e->getMessage();
            if($_POST['action']=='login') $_SESSION['login_msg']=$e->getMessage();
            $_SESSION[$_POST['action'].'_modal']=true;
        }
        header("Location: ../index.php");
        exit;
    }
}
