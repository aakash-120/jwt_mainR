<?php

namespace App\Listeners;

use Phalcon\Events\Event;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class notificationListeners
{

    public function beforeSend(Event $event, $values, $settings)
    {

        //setting object me dafult value hai database ki , tgas bhi hai database ke
        // values me form se get kri hui values hai

        // die();

        echo "<pre>";
        print_r($values);
        echo "</pre>";

        echo "after jdon decode setting look like <br>";
        print_r(json_decode(json_encode($settings[0])));
        //  die();
        if ($settings[0]->Title_Optimization == "with_tags") {
            $values->name = $values->name . " " . $values->tags;
        }
        if ($values->price == '') {
            $values->price = $settings[0]->Default_price;
        }
        if ($values->stock == '') {
            $values->stock = $settings[0]->Default_Stock;
        }

        echo "after doing changes<br>";
        echo "<pre>";
        echo "my values <br>";
        print_r($values);
        echo "</pre>";

        // die();
        return $values;
    }

    public function afterSend(Event $event, $values, $settings)
    {

        echo "before changes <pre>";
        print_r($values);
        echo "</pre>";

        if ($values->czipcode == '') {
            $values->czipcode = $settings[0]->Default_Zipcode;
        }
        echo "after doing changes<br>";
        echo "<pre>";
        print_r($values);
        echo "</pre>";
        // die();
        return $values;
    }

    // public function beforeHandleRequest(Event $event, \Phalcon\Mvc\Application $application)
    // {
    //     $aclFile = APP_PATH . '/security/acl.cache';
    //     if (true == is_file($aclFile)) {
    //         $acl = unserialize(
    //             file_get_contents($aclFile)
    //         );

    //         $role = $application->request->get("role");
    //         $controller = $application->router->getControllerName();
    //         $action = $application->router->getActionName();
    //         if (!$role || true !== $acl->isAllowed($role, $controller, $action)) {
    //             echo "Access denied :(";
    //             die();
    //         } else {
    //             // echo "we don't find any acl list try after somtiome";
    //         }
    //     }
    // }


    // public function beforeHandleRequest(Event $event, \Phalcon\Mvc\Application $application)
    // {
    //     //yha par hme url poora dena padega chahe wo index ki file hi ho
    //     $aclfile = APP_PATH . '/security/acl.cache';

    //     if (is_file($aclfile) == true) {
    //         $acl = unserialize(
    //             file_get_contents($aclfile)
    //         );

    //         $bearer = $application->request->get('bearer');
    //         $controller = $application->router->getControllerName();
    //         $action = $application->router->getActionName();
    //         if ($bearer) {
    //             try {
    //                 $parser = new Parser();
    //                 $tokenObject = $parser->parse($bearer);
    //                 $now = new \DateTimeImmutable();
    //                 $expire = $now->getTimestamp();
    //                 // $expire=$now->modify('+1 day')->getTimestamp();
    //                 $validator = new Validator($tokenObject, 100);
    //                 $validator->validateExpiration($expire);
    //                 $claims = $tokenObject->getClaims()->getPayload();
    //                 $role = $claims['sub'];
    //                 // echo $role;
    //                 // die;
    //                 $controller = $application->router->getControllerName();
    //                 $action = $application->router->getActionName();
    //                 echo $controller;
    //                 echo "after controller<br>";
    //                 echo $action;
                
    //                 if (!$role || true !== $acl->isAllowed($role, $controller, $action)) {
    //                     echo "access denied";
    //                     die();
    //                 }
    //             } catch (\Exception $e) {
    //                 $e->getMessage();
    //             }
    //         } else {
    //             echo "token not provided";
    //             die;
    //         }
    //     } else {
    //         echo "No ACL";
    //         die();
    //     }
    // }


    public function beforeHandleRequest(Event $event, \Phalcon\Mvc\Application $application)
    {
        $aclfile = APP_PATH . '/security/acl.cache';
        if (is_file($aclfile) == true) {
            $acl = unserialize(
                file_get_contents($aclfile)
            );

            $bearer = $application->request->get('bearer');
            if ($bearer) {
                try {
                    $key = "example_key";
                    $decoded = JWT::decode($bearer, new Key($key, 'HS256'));
                    $role = $decoded->role;
                    $controller = $application->router->getControllerName();
                    $action = $application->router->getActionName();
                    if (!$role || true !== $acl->isAllowed($role, $controller, $action)) {
                        echo "access denied";
                        die();
                    }
                } catch (\Exception $e) {
                    $e->getMessage();
                    die;
                }
            } else {
                echo "token not provided";
                die;
            }
        } else {
            echo "No ACL";
            die();
        }
    }
}
