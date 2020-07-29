<?php

namespace Frontend\FrontentBundle\Controller;

use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\CustomerRegisterType;
use Core\UserBundle\Form\SignupType;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Setting\Bundle\ToolBundle\Entity\AppModule;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class WebOrderController extends Controller
{

    /**
     * @Secure(roles="ROLE_BUYER")
     */

    public function orderAction()
    {
        echo "Order";
        exit;
    }

}
