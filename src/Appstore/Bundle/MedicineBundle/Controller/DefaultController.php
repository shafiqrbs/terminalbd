<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        /* @var GlobalOption $globalOption */
        $globalOption = $this->getUser()->getGlobalOption();
        return $this->render('MedicineBundle:Default:index.html.twig', array(
            'globalOption' => $globalOption,
        ));

    }
}
