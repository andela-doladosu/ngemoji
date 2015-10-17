<?php

require_once 'vendor/autoload.php';

use Dara\Origins\User;
use Dara\Origins\Emoji;
use Slim\Slim;

date_default_timezone_set('Africa/Lagos');

$app = new Slim();

$authCheck = function ($route) use ($app) {
   
    $user = new User();
    $username = $app->request->params('username');
    $password = $app->request->params('password');
    $userToken = $app->request->headers['token']; 

    $userInfo = $user->where('users', 'username', $username);
    
    $storedToken =  $userInfo['token'];
    $now = date('Y-m-d H:i:s', time());
    
    if ($now > $userInfo['token_expiry']) {
        $app->halt(401, json_encode(["Message" => "Expired token. Please login again"]));
    }

    if ($userToken != $storedToken || !$userInfo['logged_in']) {
        $app->halt(401, json_encode(["Message" => "You are not allowed to access this route!"]));
    }  
};

$ownerCheck = function ($route) use ($app) {

    $username = $app->request->params('username');
    $emojiId = $route->getParams()['id'];

    $user = new User();
    $check = $user->checkEmojiOwnership($username, $emojiId);
    
    if (!$check) {
        $app->halt(309, json_encode(["Message" => "You are not allowed to modify this emoji!"]));
    }    
};

$emojiExists = function ($route) use ($app) {
    
    $emojiId = $route->getParams()['id'];
    $find = Emoji::find($emojiId);
    $check = $find->resultRows[0];

    if (!$check) {
        $app->halt(301, json_encode(["Message" => "That emoji does not exist!"]));
    }  
};


$app->post('/auth/login', function () use ($app) {
    
    $username = $app->request->params('username');
    $password = $app->request->params('password');
    
    $user = new User();
    $login = $user->login($username, $password);
    echo json_encode($login);

});

$app->post('/auth/register', function () use ($app) {

    $user = new User();
    $username = $app->request->params('username');
    $password = $app->request->params('password');
    $register = $user->register($username, $password);

    echo json_encode($register);

});

$app->get('/auth/logout', $authCheck, function () use ($app) {

    $user = new User();
    $username = $app->request->params('username');
    $password = $app->request->params('password');
    $logout = $user->logout($username, $password);

    echo json_encode($logout);

});

$app->get('/emojis', function () {
    echo json_encode(Emoji::getAll());
});

$app->post('/emojis', $authCheck, function () use ($app) {
   
    $user = new User();
    $emoji = new Emoji();
    $emoji->name = $app->request->params('name');
    $emoji->category = $app->request->params('category');
    $emoji->char = $app->request->params('char');
    $emoji->keywords = implode(', ', $app->request->params('keywords'));
    $emoji->created_by = $user->getUserId($app->request->params('username'));
    
    echo json_encode($emoji->save());
});


$app->get('/emojis/:id', function ($id) {
    $emoji = Emoji::find($id);
    echo json_encode($emoji->resultRows);
});

$app->put('/emojis/:id', $authCheck, $emojiExists, $ownerCheck, function ($id) use ($app) {
    $emoji = new Emoji();
    $put = $emoji->put($id, $app->request->params());
    echo json_encode($put);    
});

$app->patch('/emojis/:id', $authCheck, $emojiExists, $ownerCheck, function ($id) use ($app) {
    $emoji = new Emoji();
    $patch = $emoji->put($id, $app->request->params());
    echo json_encode($patch);  
});

$app->delete('/emojis/:id', $authCheck, $emojiExists, $ownerCheck, function ($id) use ($app) {
    $delete = Emoji::destroy($id);
    var_dump($delete);
});

$app->run();

