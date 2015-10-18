<?php

namespace spec\Dara\Origins;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EmojiSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Dara\Origins\Emoji');
    }
}
