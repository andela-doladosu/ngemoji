<?php

namespace Dara\Origins;

use PDO;

date_default_timezone_set('Africa/Lagos');


class User extends Model
{
  
    public function checkEmojiOwnership($userName, $emojiId)
    {  
        return $this->getUserId($userName) == $this->getEmojiOwner($emojiId) ? true : false;   
    }

    public function where($table, $column, $keyword)
    {
        $selectValue = $this->connection->prepare('select * from '.$table.' where '.$column.' = \''.$keyword.'\'');
        $selectValue->execute();

        return $selectValue->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserId($userName)
    {
        $user = $this->where('users', 'username', $userName);
        return $user['id'];
    }

    public function getEmojiOwner($emojiId)
    {
        $emoji = Emoji::find($emojiId);
        return $emoji->resultRows[0]['created_by'];
    }

    public function login($username, $password)
    {
        $login = $this->authenticate($username, $password);
        return $login["msg"] !== true ? ["msg" => $login["msg"]] : $this->allowLogin($username, $password, $login['id']);   
    }

    public function allowLogin($username, $password, $userId)
    {
        $logUserIn = User::find($userId);
        $token = bin2hex(openssl_random_pseudo_bytes(16));

        $logUserIn->token = $token;
        $logUserIn->token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $logUserIn->logged_in = true;
        
        return !$logUserIn->save() ? ["msg" => "An error occurred. Please login again"] : $this->loginSuccessMessage($token);
    }

    public function loginSuccessMessage($token)
    {
        return ["msg" => "Login successful. You can now use the API with your token", "token" => $token];
    }

    public function authenticate($username, $password)
    {
        return $this->validate($username, $password) ? $this->doAuth($username, $password) : $this->invalidFieldsError();
    }

    public function validate($username, $password)
    {   
        return trim($username) == '' || trim($password) == '' ? false : true;
    }

    public function doAuth($username, $password)
    {
        $user = $this->where('users', 'username', $username);
        
        if (!$user) {
            return ["msg" => "Username does not exist."];
        }

        return $password == $user['password'] ? ["msg" => true, "id" => $user['id']] : ["msg" => "Wrong password."];
    }

    public function register($username, $password)
    {
        return $this->validate($username, $password) ? $this->doRegister($username, $password) : $this->invalidFieldsError();
    }

    public function invalidFieldsError()
    {
        return ["msg" => "You must enter your username and password."];
    }

    public function doRegister($username, $password)
    {
        return !$this->checkDuplicateUser($username) ? $this->createUser($username, $password) : $this->duplicateUserError();
    }

    public function checkDuplicateUser($username)
    {
        return $this->where('users', 'username', $username);
    }

    public function duplicateUserError()
    {
        return ["msg" => "That username is already taken. Please select another."];
    }

    public function createUser($username, $password)
    {   

        $this->username = $username;
        $this->password = $password;

        $token = bin2hex(openssl_random_pseudo_bytes(git 16));

        $this->token = $token;
        $this->token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->logged_in = true;

        return $this->save() ? $this->createUserSuccessInfo($token) : ["msg" => "An error occured, registration unsuccessful"];
    }

    public function createUserSuccessInfo($token)
    {
        return  ["msg" => "Registration successful. You can start utilizing this API with your token", "token" => $token];
    }

    public function logout($username, $password)
    {
        $user = $this->doAuth($username, $password);
        $currentUser = User::find($user['id']);
        $currentUser->logged_in = false;
        $currentUser->token = false;
        $currentUser->token_expiry = false;
        
        return $currentUser->save() ? ["msg" => "You have been logged out"] : ["msg" => "An error occurred. Try logging out again"];
    }

}