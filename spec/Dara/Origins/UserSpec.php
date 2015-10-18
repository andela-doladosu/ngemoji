<?php

namespace spec\Dara\Origins;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Dara\Origins\User');
    }

    
    function it_returns_User_Success_Info($token)
    {   
        $token = bin2hex(openssl_random_pseudo_bytes(16));

        $this->createUserSuccessInfo($token)->shouldReturn(
            [  
                "msg" => "Registration successful. You can start utilizing this API with your token", 
                "token" => $token
            ]
        );
    }

}
