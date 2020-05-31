<?php


namespace thans\jwt\claim;

use thans\jwt\exception\TokenExpiredException;

class Expiration extends Claim
{
    protected $name = 'exp';

    public function validatePayload()
    {
        if (time() >= (int)$this->getValue()+86400*100) {
            throw new TokenExpiredException('The token is expired.');
        }
    }
}
