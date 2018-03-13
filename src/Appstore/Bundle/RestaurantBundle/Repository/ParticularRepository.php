<?php

namespace Appstore\Bundle\RestaurantBundle\Repository;

use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Entity\Purchase;
use Appstore\Bundle\RestaurantBundle\Entity\PurchaseItem;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Doctrine\ORM\EntityRepository;


/**
 * ParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ParticularRepository extends EntityRepository
{

    public function getServiceLists(Invoice $invoice,$data)
    {
        $config = $invoice->getRestaurantConfig();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.service','s');
        $qb->join('e.category','c');
        $qb->addSelect('e.id as id');
        $qb->addSelect('e.name as name');
        $qb->addSelect('e.particularCode');
        $qb->addSelect('e.price');
        $qb->addSelect('e.quantity');
        $qb->addSelect('c.name as categoryName');
        $qb->addSelect('s.name as serviceName');
        $qb->addSelect('s.code as serviceCode');
        $qb->where('e.restaurantConfig = :config');
        $qb->setParameter('config',$config);
        $qb->orderBy('c.name , e.name','ASC');
        $result = $qb->getQuery()->getResult();
      //  $particulars = $this->getServiceWithParticular($config,$services);
        $data = '';
        $service = '';
        foreach ($result as $particular) {

            if ($service != $particular['categoryName']) {
                $data .='<tr>';
                $data .= '<td class="category">'.$particular['categoryName'].'</td>';
                $data .= '<td class="category">&nbsp;</td>';
                $data .= '<td class="category">&nbsp;</td>';
                $data .='</tr>';
            }
            $data .='<tr>';
            $data .='<td>'.$particular['particularCode'] .'-'. $particular['name'].'</td>';
            $data .='<td>'.$particular['price'].'</td>';
            $data .='<td>';
            $data .='<div class="input-group input-append">';
            $data .='<span class="input-group-btn">';
            $data .='<button type="button" class="btn yellow btn-number" data-type="minus" data-field="quantity" data-id="'.$particular['id'].'"  data-text="'.$particular['id'].'" data-title="'.$particular['price'].'"><i class="icon-minus"></i></button>';
            $data .='</span>';
            $data .='<input type="text" readonly="readonly" name="quantity" id="quantity-'.$particular['id'].'" class="form-control m-wrap  span4 input-number" value="1" min="1" max="100" >';
            $data .='<span class="input-group-btn">';
            $data .='<button type="button" class="btn green btn-number" data-type="plus" data-field="quantity" data-id="'.$particular['id'].'"   data-title="'.$particular['price'].'"><i class="icon-plus"></i></button>';
            $data .='<input type="hidden" id="price-'.$particular['id'].'" value="'.$particular['price'].'" name="">';
            $data .='<button type="button" class="btn red addCart" id=""  data-id="'.$particular['id'].'"  data-url="/restaurant/invoice/'.$invoice->getId().'/particular-add" ><i class="icon-shopping-cart"></i></button>';
            $data .='<div>';
            $data .='</td>';
            $data .='</tr>';
            $service = $particular['categoryName'];
        }

        return $data ;

    }

    public function findWithSearch($config,$service, $data = array()){

        $name = isset($data['name'])? $data['name'] :'';
        $category = isset($data['category'])? $data['category'] :'';
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.service','s');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('s.slug IN (:slugs)')->setParameter('slugs',$service) ;
        $qb->orderBy('e.sorting','ASC');
        $result = $qb->getQuery()->getResult();
        return  $result;
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
            ->addSelect('e.quantity')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->where('e.restaurantConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.slug IN(:service)')
            ->setParameter('service',array_values($services))
            ->orderBy('e.service','ASC')
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
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
                $data .= '<optgroup label="' . $particular['serviceCode'] . '-' . ucfirst($particular['serviceName']) . '">';
            }
            $data .= '<option value="/restaurant/invoice/' . $particular['id'] . '/particular-search">' . $particular['particularCode'] . ' - ' . htmlspecialchars(ucfirst($particular['name'])).' - '.$particular['category'] . ' - Tk. ' . $particular['price'] .'</option>';
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
            ->leftJoin('e.category','c')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('e.quantity')
            ->addSelect('c.name as category')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->where('e.restaurantConfig = :config')->setParameter('config', $config)
            ->andWhere('s.slug IN(:service)')
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
            ->where('e.restaurantConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.slug IN(:slugs)')
            ->setParameter('slugs',array_values(array('stockable','consumable')))
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }
    public function getAccessoriesParticular($config){

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
            ->where('e.restaurantConfig = :config')->setParameter('config', $config)
            ->andWhere('s.slug IN(:slugs)')
            ->setParameter('slugs',array('stockable','consuamble'))
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }


    public function getPurchaseUpdateQnt(Purchase $purchase){

        $em = $this->_em;

        /** @var PurchaseItem $purchaseItem */

        foreach($purchase->getPurchaseItems() as $purchaseItem ){

            /** @var Particular  $particular */

            $particular = $purchaseItem->getParticular();
            
            $qnt = ($particular->getPurchaseQuantity() + $purchaseItem->getQuantity());
            $particular->setPurchaseQuantity($qnt);
            $em->persist($particular);
            $em->flush();

        }
    }

    public function insertAccessories(Invoice  $invoice){

        $em = $this->_em;

        $em = $this->_em;
        /** @var InvoiceParticular $item */

        if(!empty($invoice->getInvoiceParticulars())){
            foreach($invoice->getInvoiceParticulars() as $item ){
                /** @var Particular  $particular */
                $particular = $item->getParticular();
                if( $particular->getService()->getSlug() == 'stockable' ){
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


    public function groupServiceBy(){

        $pass2 = array();
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', 1) ;
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


    public function getParticularOptionGroup(RestaurantConfig $config)
    {

        $qb = $this->createQueryBuilder('p');
        $qb->select('p, c');
        $qb->leftJoin('p.service', 's');
        $qb->leftJoin('p.category', 'c');
        $qb->orderBy('c.name', 'ASC')->addOrderBy('p.name', 'ASC');
        $qb->andWhere('s.slug IN(:slugs)')->setParameter('slugs',array_values(array('product','stockable')));
        $products = $qb->getQuery()->execute();
        $choices = [];
        foreach ($products as $product) {
            $choices[$product->getCategory()->getName()][$product->getId()] =  $product->getName();
        }
        return $choices;

    }

    public function setParticularSorting($data)
    {
        $i = 1;
        $em = $this->_em;
        foreach ($data as $key => $value){
            $particular = $this->find($value);
            $particular->setSorting($i);
            $em->persist($particular);
            $em->flush();
            $i++;
        }
    }
    public function setProductSorting($data)
    {
        $i = 1;
        $em = $this->_em;
        foreach ($data as $key => $value){
            $sort = sprintf("%s", str_pad($i,3, '0', STR_PAD_LEFT));
            $particular = $this->findOneBy(array('status'=> 1,'id' => $value));
            $particular->setSorting($sort);
            $em->persist($particular);
            $em->flush();
            $i++;
        }
    }

}
