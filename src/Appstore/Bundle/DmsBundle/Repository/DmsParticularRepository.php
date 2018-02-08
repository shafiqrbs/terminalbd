<?php

namespace Appstore\Bundle\DmsBundle\Repository;
use Appstore\Bundle\DmsBundle\Entity\DmsConfig;
use Appstore\Bundle\DmsBundle\Entity\DmsPurchase;
use Appstore\Bundle\DmsBundle\Entity\DmsPurchaseItem;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoice;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoiceParticular;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoiceTransaction;
use Appstore\Bundle\DmsBundle\Entity\DmsParticular;
use Doctrine\ORM\EntityRepository;


/**
 * DmsParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DmsParticularRepository extends EntityRepository
{

    public function getServiceLists(DmsConfig $config)
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
            ->orderBy('s.sorting','ASC')
            ->orderBy('e.name','ASC')
            ->getQuery()->getResult();
            return  $qb;
    }

    public function getServices($config,$services){


        $particulars = $this->getServiceWithParticular($config,$services);

        $data = '';
        $service = '';
        foreach ($particulars as $particular) {
            if ($service != $particular['serviceName']) {
                if ($service != '') {
                    $data .= '</optgroup>';
                }
                $data .= '<optgroup label="' .ucfirst($particular['serviceName']) . '">';
            }
            if ($particular['serviceCode'] != '04'){
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

    public function getMedicineParticular($config){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->leftJoin('e.unit','u')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('e.status')
            ->addSelect('e.salesQuantity')
            ->addSelect('e.minQuantity')
            ->addSelect('e.openingQuantity')
            ->addSelect('u.name as unit')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->addSelect('e.purchasePrice')
            ->addSelect('e.purchaseQuantity')
            ->where('e.dmsConfig = :config')->setParameter('config', $config)
            ->andWhere('s.slug IN(:process)')
            ->setParameter('process',array_values(array('accessories')))
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }
    public function getAccessoriesParticular($hospital){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->leftJoin('e.unit','u')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('e.status')
            ->addSelect('e.salesQuantity')
            ->addSelect('e.minQuantity')
            ->addSelect('e.openingQuantity')
            ->addSelect('u.name as unit')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->addSelect('e.purchasePrice')
            ->addSelect('e.purchaseQuantity')
            ->where('e.dmsConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.id IN(:process)')
            ->setParameter('process',array_values(array(8)))
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }

    public function findDmsExistingCustomer($hospital, $mobile,$data)
    {
    }

    public function getPurchaseUpdateQnt(DmsPurchase $purchase){

        $em = $this->_em;

        /** @var DmsPurchaseItem $purchaseItem */

        foreach($purchase->getPurchaseItems() as $purchaseItem ){

            /** @var DmsParticular  $particular */

            $particular = $purchaseItem->getDmsParticular();
            
            $qnt = ($particular->getPurchaseQuantity() + $purchaseItem->getQuantity());
            $particular->setPurchaseQuantity($qnt);
            $em->persist($particular);
            $em->flush();

        }
    }

    public function insertAccessories(DmsInvoice $invoice){

        $em = $this->_em;

        $em = $this->_em;

        /** @var DmsInvoiceParticular $item */

        if(!empty($invoice->getInvoiceParticulars())){
            foreach($invoice->getInvoiceParticulars() as $item ){
                /** @var DmsParticular  $particular */
                $particular = $item->getDmsParticular();
                if( $particular->getService()->getId() == 4 ){
                    $qnt = ($particular->getSalesQuantity() + $item->getQuantity());
                    $particular->setSalesQuantity($qnt);
                    $em->persist($particular);
                    $em->flush();
                }
            }
        }
    }

    public function getSalesUpdateQnt(DmsInvoice $invoice){

        $em = $this->_em;

        /** @var InvoiceParticular $item */

        foreach($invoice->getInvoiceParticulars() as $item ){

            /** @var Particular  $particular */

            $particular = $item->getParticular();
            if( $particular->getService()->getId() == 4 ){

                $qnt = ($particular->getSalesQuantity() + $item->getQuantity());
                $particular->setSalesQuantity($qnt);
                $em->persist($particular);
                $em->flush();
            }
        }
    }

    public function admittedPatientAccessories(InvoiceTransaction $transaction){

        $em = $this->_em;

        /** @var InvoiceParticular $item */
        if(!empty($transaction->getAdmissionPatientParticulars())){

            foreach($transaction->getAdmissionPatientParticulars() as $item ){

                /** @var Particular  $particular */

                $particular = $item->getParticular();
                if( $particular->getService()->getId() == 4 ){
                    $qnt = ($particular->getSalesQuantity() + $item->getQuantity());
                    $particular->setSalesQuantity($qnt);
                    $em->persist($particular);
                    $em->flush();
                }
            }
        }

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
