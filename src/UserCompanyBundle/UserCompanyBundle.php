<?php

namespace UserCompanyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserCompanyBundle extends Bundle
{
     public function getParent()
    {
        return 'FOSUserBundle';
    }
}
