<?php

namespace Appstore\Bundle\DmsBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * DmsConfigRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DmsConfigRepository extends EntityRepository
{


    public function reset(GlobalOption $option)
    {

        $em = $this->_em;
        $config = $option->getDmsConfig()->getId();

        $DoctorInvoice = $em->createQuery('DELETE DmsBundle:DmsInvoice e WHERE e.dmsConfig = '.$config);
        $DoctorInvoice->execute();

        $hmsPurchase = $em->createQuery('DELETE DmsBundle:DmsPurchase e WHERE e.dmsConfig = '.$config);
        $hmsPurchase->execute();

        $hmsPurchase = $em->createQuery('DELETE DmsBundle:DmsParticular e WHERE e.dmsConfig = '.$config);
        $hmsPurchase->execute();

        $hmsPurchase = $em->createQuery('DELETE DmsBundle:DmsService e WHERE e.dmsConfig = '.$config);
        $hmsPurchase->execute();

    }
}
