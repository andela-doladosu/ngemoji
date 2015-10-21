<?php

namespace Dara\Origins;

use PDO;

date_default_timezone_set('Africa/Lagos');


class User extends Model
{
    
    /**
     * Confirm that it's the creator of the emoji that wants to edit it
     * 
     * @param  string $userName 
     * @param  int $emojiId  
     * @return string           
     */
    public function checkEmojiOwnership($token, $emojiId)
    {  
        return $this->getUserId($token) == $this->getEmojiOwner($emojiId) ? true : false;   
    }


    /**
     * Return User ID
     * 
     * @param  string $userName 
     * @return int           
     */
    public function getUserId($token)
    {
        $user = $this->where('users', 'token', $token);
        return $user['id'];
    }


    /**
     * Get emoji owner
     * 
     * @param  string $emojiId 
     * @return string          
     */
    public function getEmojiOwner($emojiId)
    {
        $emoji = Emoji::find($emojiId);
        return $emoji->resultRows[0]['created_by'];
    }


    /**
     * Choose to Log user in
     * 
     * @param  string $username 
     * @param  string $password 
     * @return string           
     */
    public function login($username, $password)
    {
        $login = $this->authenticate($username, $password);
        return $login["msg"] !== true ? ["msg" => $login["msg"]] : $this->allowLogin($username, $password, $login['id']);   
    }


    /**
     * Log user in
     * 
     * @param  string $username 
     * @param  string $password 
     * @param  string $userId   
     * @return string           
     */
    public function allowLogin($username, $password, $userId)
    {
        $logUserIn = User::find($userId);
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $logUserIn->token = $token;
        $logUserIn->token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $logUserIn->logged_in = true;
              
        return !$logUserIn->save() ? ["msg" => "An error occurred. Please login again"] : $this->loginSuccessMessage($token);
    }


    /**
     * Return login confirmation
     * 
     * @param  string $token 
     * @return array        
     */
    public function loginSuccessMessage($token)
    {
        return ["msg" => "Login successful. You can now use the API with your token", "token" => $token];
    }


    /**
     * Authenticate user if validation is successful
     * 
     * @param  string $username 
     * @param  string $password 
     * @return null           
     */
    public function authenticate($username, $password)
    {
        return $this->validate($username, $password) ? $this->doAuth($username, $password) : $this->invalidFieldsError();
    }


    /**
     * Validate user input
     * 
     * @param  string $username 
     * @param  string $password 
     * @return bool           
     */
    public function validate($username, $password)
    {   
        return trim($username) == '' || trim($password) == '' ? false : true;
    }


    /**
     * Authenticate user
     * 
     * @param  string $username 
     * @param  string $password 
     * @return array           
     */
    public function doAuth($username, $password)
    {
        $user = $this->where('users', 'username', $username);
        
        if (!$user) {
            return ["msg" => "Username does not exist."];
        }

        return $password == $user['password'] ? ["msg" => true, "id" => $user['id']] : ["msg" => "Wrong password."];
    }


    /**
     * Register user if validation is successful
     * 
     * @param  string $username 
     * @param  string $password 
     * @return null           
     */
    public function register($username, $password)
    {
        return $this->validate($username, $password) ? $this->doRegister($username, $password) : $this->invalidFieldsError();
    }


    /**
     * Return invalid Fields Error
     * 
     * @return array 
     */
    public function invalidFieldsError()
    {
        return ["msg" => "You must enter your username and password."];
    }


    /**
     * Register a new user
     * 
     * @param  string $username 
     * @param  string $password 
     * @return null           
     */
    public function doRegister($username, $password)
    {
        return !$this->checkDuplicateUser($username) ? $this->createUser($username, $password) : $this->duplicateUserError();
    }


    /**
     * Check for duplicate user 
     * 
     * @param  string $username 
     * @return null           
     */
    public function checkDuplicateUser($username)
    {
        return $this->where('users', 'username', $username);
    }


    /**
     * Return duplicate User Error message
     * 
     * @return array 
     */
    public function duplicateUserError()
    {
        return ["msg" => "That username is already taken. Please select another."];
    }


    /**
     * Create new user 
     * 
     * @param  string $username 
     * @param  string $password 
     * @return array           
     */
    public function createUser($username, $password)
    {   

        $this->username = $username;
        $this->password = $password;

        $token = bin2hex(openssl_random_pseudo_bytes(16));

        $this->token = $token;
        $this->token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->logged_in = true;

        return $this->save() ? $this->createUserSuccessInfo($token) : ["msg" => "An error occured, registration unsuccessful"];
    }


    /**
     * Return registration confirmation
     * 
     * @param  string $token 
     * @return array        
     */
    public function createUserSuccessInfo($token)
    {
        return  ["msg" => "Registration successful. You can start utilizing this API with your token", "token" => $token];
    }


    /**
     * Log user out
     * 
     * @param  string $username 
     * @param  string $password 
     * @return array           
     */
    public function logout($token)
    {
        $user = $this->where('users', 'token', $token);
        $currentUser = User::find($user['id']);
        $currentUser->logged_in = false;
        $currentUser->token = false;
        $currentUser->token_expiry = false;
        
        return $currentUser->save() ? ["msg" => "You have been logged out"] : ["msg" => "An error occurred. Try logging out again"];
    }

}