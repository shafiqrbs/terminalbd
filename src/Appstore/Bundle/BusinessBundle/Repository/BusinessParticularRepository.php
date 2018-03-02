<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceAccessories;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;

use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Doctrine\ORM\EntityRepository;


/**
 * BusinessParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessParticularRepository extends EntityRepository
{

    public function getServiceLists(BusinessConfig $config)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.service','service');
        $qb->where('e.dmsConfig = :config');
        $qb->setParameter('config',$config);
        $qb->andWhere('service.dentalService is null');
        $qb->orderBy('service.name , e.name','ASC');
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function searchAutoComplete(BusinessConfig $config,$q)
    {
        $query = $this->createQueryBuilder('e');
        $query->select('e.name as id');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->andWhere("e.dmsConfig = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();
    }

    public function findWithSearch($config,$service, $data){

        $name = isset($data['name'])? $data['name'] :'';
        $category = isset($data['category'])? $data['category'] :'';
        $department = isset($data['department'])? $data['department'] :'';

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.dmsConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.service = :service')->setParameter('service', $service) ;
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("e.name", "'%$name%'"  ));
        }
        if(!empty($category)){
            $qb->andWhere("e.category = :category");
            $qb->setParameter('category', $category);
        }
        if(!empty($department)){
            $qb->andWhere("e.department = :department");
            $qb->setParameter('department', $department);
        }
        $qb->orderBy('e.name','ASC');
        $qb->getQuery();
        return  $qb;
    }

    public function getFindWithParticular($config,$services){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->where('e.dmsConfig = :config')->setParameter('config', $config)
            ->andWhere('e.status = :status')->setParameter('status', 1)
            ->andWhere('s.serviceFormat is null')
            ->andWhere('s.slug IN(:slugs)')
            ->setParameter('slugs',array_values($services))
            ->orderBy('s.sorting','ASC')
            ->orderBy('e.name','ASC')
            ->getQuery()->getResult();
            return  $qb;
    }

    public function getFindDentalServiceParticular($config,$services){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->where('e.dmsConfig = :config')->setParameter('config', $config)
         /*   ->andWhere('e.status = :status')->setParameter('status', 1)*/
            ->andWhere('s.serviceFormat IN(:slugs)')
            ->setParameter('slugs',array_values($services))
            ->orderBy('s.sorting','ASC')
            ->orderBy('e.name','ASC')
            ->getQuery()->getResult();
        return  $qb;
    }


    public function getServices($config,$services){


        $particular = $this->getMedicineParticular($config,$services);
        $data = '';
        $service = '';
        $result = $particular->getArrayResult();
        foreach ($result as $particular) {
            if ($service != $particular['serviceName']) {
                if ($service != '') {
                    $data .= '</optgroup>';
                }
                $data .= '<optgroup label="' .ucfirst($particular['serviceName']) . '">';
            }
            if ($particular['serviceFormat'] != 'treatment'){
                $data .= '<option value="/dms/invoice/' . $particular['id'] . '/particular-search">' . $particular['particularCode'] . ' - ' . htmlspecialchars(ucfirst($particular['name'])) . ' - Tk. ' . $particular['minimumPrice'] .' - '.$particular['price'].'</option>';
            }else{
                $data .= '<option value="/dms/invoice/' . $particular['id'] . '/particular-search">' . $particular['particularCode'] . ' - ' . htmlspecialchars(ucfirst($particular['name'])) . ' - Tk. ' . $particular['minimumPrice'] .' - '.$particular['price'].'</option>';
            }
            $service = $particular['serviceName'];
        }
        if ($service != '') {
            $data .= '</optgroup>';
        }
        return $data ;

    }


    public function getServiceWithParticular($config,$services){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->where('e.dmsConfig = :config')->setParameter('config', $config)
            ->andWhere('s.slug IN(:slugs)')
            ->setParameter('slugs',array_values($services))
            ->orderBy('e.service','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }

    public function getMedicineParticular($config,$services,$data = array()){

        $qb = $this->createQueryBuilder('e');
            $qb->leftJoin('e.service','s');
            $qb->leftJoin('e.unit','u');
            $qb->select('e.id');
            $qb->addSelect('e.name');
            $qb->addSelect('e.particularCode');
            $qb->addSelect('e.price');
            $qb->addSelect('e.minimumPrice');
            $qb->addSelect('e.quantity');
            $qb->addSelect('e.status');
            $qb->addSelect('e.salesQuantity');
            $qb->addSelect('e.minQuantity');
            $qb->addSelect('e.openingQuantity');
            $qb->addSelect('u.name as unit');
            $qb->addSelect('s.serviceFormat as serviceFormat');
            $qb->addSelect('s.name as serviceName');
            $qb->addSelect('s.code as serviceCode');
            $qb->addSelect('e.purchasePrice');
            $qb->addSelect('e.purchaseQuantity');
            $qb->where('e.dmsConfig = :config')->setParameter('config', $config);
            $qb->andWhere('s.serviceFormat IN(:process)');
            $qb->setParameter('process',$services);
            if(!empty($data['particular'])) {
                $qb->andWhere('e.id =:particularId');
                $qb->setParameter('particularId', $data['particular']);
            }
            $qb->orderBy('e.name','ASC');
            $result = $qb->getQuery();
            return  $result;
    }

    public function getAccessoriesParticular($config,$services){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->leftJoin('e.unit','u')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.status')
            ->addSelect('e.salesQuantity')
            ->addSelect('e.openingQuantity')
            ->addSelect('u.name as unit')
            ->addSelect('e.purchaseQuantity')
            ->where('e.dmsConfig = :config')->setParameter('config', $config)
            ->andWhere('s.serviceFormat IN(:process)')
            ->setParameter('process',$services)
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }

    public function findBusinessExistingCustomer($hospital, $mobile,$data)
    {
    }

    public function getPurchaseUpdateQnt(BusinessPurchase $purchase){

        $em = $this->_em;

        /** @var BusinessPurchaseItem $purchaseItem */

        foreach($purchase->getPurchaseItems() as $purchaseItem ){

            /** @var BusinessParticular  $particular */

            $particular = $purchaseItem->getBusinessParticular();
            $qnt = ($particular->getPurchaseQuantity() + $purchaseItem->getQuantity());
            $particular->setPurchaseQuantity($qnt);
            $em->persist($particular);
            $em->flush();

        }
    }

    public function getSalesUpdateQnt(BusinessInvoiceAccessories  $accessories){

        $em = $this->_em;
        $particular = $accessories->getBusinessParticular();
        $qnt = $particular->getSalesQuantity() + $accessories->getQuantity();
        $particular->setSalesQuantity($qnt);
        $em->persist($particular);
        $em->flush();
    }

    public function groupServiceBy(){

        $pass2 = array();
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.dmsConfig = :config')->setParameter('config', 1) ;
        $qb->andWhere('e.service IN(:service)')
            ->setParameter('service',array_values(array(1,2,3,4)));
        $qb->orderBy('e.name','ASC');
        $data = $qb->getQuery()->getResult();
        foreach ($data as $parent => $children){

            foreach($children as $child => $none){
                $pass2[$parent][$child] = true;
                $pass2[$child][$parent] = true;
            }
        }

    }

}
