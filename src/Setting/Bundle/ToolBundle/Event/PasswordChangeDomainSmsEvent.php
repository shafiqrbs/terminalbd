<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\EventDispatcher\Event;


class PasswordChangeDomainSmsEvent extends Event
{

    /** @var \Setting\Bundle\ToolBundle\Repository\GlobalOption  */

    protected $option;

    protected $password;

    public function __construct(GlobalOption $option, $password)
    {
        $this->option = $option;
        $this->password = $password;

    }

    /**
     * @return User
     */
    public function getOption()
    {
        return $this->option;
    }

    public function getPassword()
    {
        return $this->password;
    }


}