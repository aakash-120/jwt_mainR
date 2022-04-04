<?php

use Phalcon\Mvc\Controller;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class SignupController extends Controller
{
    public function indexAction()
    {
        echo "sigup controller index";
        $this->view->role = Roles::find();
    }

    public function signupAction()
    {


  
        
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password =  $this->request->getPost('password');
        $role = $this->request->getPost('role');
        // echo $name;
        // echo $email;
        // echo $password;
        // echo $role;


        $key = "example_key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000,
            "role" => $_POST['role']
        );
        $jwt = JWT::encode($payload, $key, 'HS256');


        $user = new Users();
        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        $user->role = $role;
        $user->token =  $jwt;
//         echo "name";
// echo  $user->name."<br>";
// echo  $user->email."<br>";
// echo  $user->password."<br>";
// echo  $user->role."<br>";
// echo  $user->token."<br>";
//         die();
        $user->save();
        $this->response->redirect('/signup');

    }
}
