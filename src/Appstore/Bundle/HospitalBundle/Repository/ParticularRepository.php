<?php

namespace Appstore\Bundle\HospitalBundle\Repository;
use Appstore\Bundle\HospitalBundle\Entity\AdmissionPatientParticular;
use Appstore\Bundle\HospitalBundle\Entity\HmsPurchase;
use Appstore\Bundle\HospitalBundle\Entity\HmsPurchaseItem;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Doctrine\ORM\EntityRepository;


/**
 * PathologyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ParticularRepository extends EntityRepository
{
    public function findWithSearch($config,$service, $data){

        $name = isset($data['name'])? $data['name'] :'';
        $category = isset($data['category'])? $data['category'] :'';
        $department = isset($data['department'])? $data['department'] :'';

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.hospitalConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.service = :service')->setParameter('service', $service) ;
        $qb->andWhere('e.isDelete is null');
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

    public function getFindWithParticular($hospital,$services){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.mobile')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->where('e.hospitalConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.id IN(:service)')
            ->setParameter('service',array_values($services))
            ->orderBy('e.service','ASC')
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
        return  $qb;
    }

    public function getServices($hospital,$services){


        $particulars = $this->getServiceWithParticular($hospital,$services);

        $data = '';
        $service = '';
        foreach ($particulars as $particular) {
            if ($service != $particular['serviceName']) {
                if ($service != '') {
                    $data .= '</optgroup>';
                }
                $data .= '<optgroup label="' . $particular['serviceCode'] . '-' . ucfirst($particular['serviceName']) . '">';
            }
            if ($particular['serviceCode'] != '04'){
                $data .= '<option value="/hms/invoice/' . $particular['id'] . '/particular-search">' . $particular['particularCode'] . ' - ' . htmlspecialchars(ucfirst($particular['name'])) . ' - Tk. ' . $particular['price'] . ' to ' . $particular['minimumPrice'] . '</option>';
            }else{
                $data .= '<option value="/hms/invoice/' . $particular['id'] . '/particular-search">' . $particular['particularCode'] . ' - ' . htmlspecialchars(ucfirst($particular['name'])) . ' - Tk. ' . $particular['price'].'</option>';
            }
            $service = $particular['serviceName'];
        }
        if ($service != '') {
            $data .= '</optgroup>';
        }
        return $data ;

    }


    public function getServiceWithParticular($hospital,$services){

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
            ->where('e.hospitalConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.id IN(:service)')
            ->setParameter('service',array_values($services))
            ->orderBy('e.service','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }

    public function getMedicineParticular($hospital){

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
            ->where('e.hospitalConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.id IN(:process)')
            ->setParameter('process',array_values(array(4)))
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
            ->where('e.hospitalConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.id IN(:process)')
            ->setParameter('process',array_values(array(8)))
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }

    public function findHmsExistingCustomer($hospital, $mobile,$data)
    {
        $em = $this->_em;

        $name = $data['referredDoctor']['name'];
        $department = $data['referredDoctor']['department'];
        $location = $data['referredDoctor']['location'];
        $address = $data['referredDoctor']['address'];
        $entity = $em->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $hospital ,'service' => 6 ,'mobile' => $mobile));
        if($entity){

            return $entity;

        }else{

            $entity = new Particular();
            if(!empty($location)){
                $location = $em->getRepository('SettingLocationBundle:Location')->find($location);
                $entity->setLocation($location);
            }
            if(!empty($department)){
                $department = $em->getRepository('HospitalBundle:HmsCategory')->find($department);
                $entity->setDepartment($department);
            }
            $entity->setService($em->getRepository('HospitalBundle:Service')->find(6));
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setAddress($address);
            $entity->setHospitalConfig($hospital);
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }

    }

    public function getPurchaseUpdateQnt(HmsPurchase $purchase){

        $em = $this->_em;

        /** @var HmsPurchaseItem $purchaseItem */

        foreach($purchase->getPurchaseItems() as $purchaseItem ){

            /** @var Particular  $particular */

            $particular = $purchaseItem->getParticular();
            
            $qnt = ($particular->getPurchaseQuantity() + $purchaseItem->getQuantity());
            $particular->setPurchaseQuantity($qnt);
            $em->persist($particular);
            $em->flush();

        }
    }

    public function insertAccessories(Invoice $invoice){

        $em = $this->_em;

        $em = $this->_em;
        /** @var InvoiceParticular $item */
        if(!empty($invoice->getInvoiceParticulars())){
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
    }

    public function getSalesUpdateQnt(Invoice $invoice){

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
        $qb->where('e.hospitalConfig = :config')->setParameter('config', 1) ;
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
