<?php

namespace Appstore\Bundle\HospitalBundle\Repository;
use Doctrine\ORM\EntityRepository;


/**
 * PathologyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoiceRepository extends EntityRepository
{
    public function invoiceLists($user , $data)
    {
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $invoice = isset($data['invoice'])? $data['invoice'] :'';
        $service = isset($data['service'])? $data['service'] :'';

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.hospitalConfig = :hospital')->setParameter('hospital', $hospital) ;
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("e.invoice", "'%$invoice%'"  ));
        }
        if(!empty($service)){
            $qb->andWhere("e.service = :service");
            $qb->setParameter('service', $service);
        }
        $qb->orderBy('e.updated','DESC');
        $qb->getQuery();
        return  $qb;
    }
}
