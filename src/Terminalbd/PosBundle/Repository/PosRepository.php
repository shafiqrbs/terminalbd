<?php
namespace Terminalbd\PosBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\Vendor;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Terminalbd\PosBundle\Entity\Pos;
use Terminalbd\PosBundle\Entity\PosItem;

/**
 * VendorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PosRepository extends EntityRepository
{

    public function insert(User $user){

        $em = $this->_em;
        $find = $this->findOneBy(array('createdBy' => $user , 'process' => 'Created'));
        if(empty($find)){
            $config = $user->getGlobalOption()->getInventoryConfig();
            $entity = new Pos();
            $entity ->setCreatedBy($user);
            $entity ->setTerminal($user->getGlobalOption());
            if($config->getVatEnable() == 1){
                $vatPercentage = $config->getVatPercentage();
                $entity->setVatPercent($vatPercentage);
            }
            $em->persist($entity);
            $em->flush();
            return $entity;
        }
        return $find;
    }


    public function reset($user)
    {


        $em = $this->_em;

        /* @var $entity Pos */
        $entity = $this->findOneBy(array('createdBy' => $user , 'process' => 'Created'));
        $entity->setSalesBy(null);
        $entity->setTransactionMethod(null);
        $entity->setSubTotal(0);
        $entity->setInvoice(null);
        $entity->setVat(0);
        $entity->setMode(null);
        $entity->setSd(0);
        $entity->setDue(0);
        $entity->setCustomer(null);
        $entity->setTotal(0);
        $entity->setPayment(0);
        $entity->setReturnAmount(0);
        $entity->setDeliveryCharge(0);
        $entity->setReceive(0);
        $entity->setDiscount(0);
        $entity->setDiscountCalculation(0);
        $entity->setDiscountType(null);
        $entity->setAccountBank(null);
        $entity->setAccountMobileBank(null);
        $entity->setTransactionId(null);
        $entity->setSalesBy(null);
        $em->persist($entity);
        $em->flush();
        return $entity;

    }
    public function update($user,$cart)
    {

        $em = $this->_em;
        $config = $user->getGlobalOption()->getInventoryConfig();

        /* @var $entity Pos */

        $entity = $this->findOneBy(array('createdBy' => $user , 'process' => 'Created'));
        $subTotal = $cart->total();
        $entity->setSubTotal($cart->total());
        $discountCal = $entity->getDiscountCalculation();
        $discountType = $entity->getDiscountType();
        if($discountType == 'flat' and $discountCal > 0){
            $entity->setDiscount($discountCal);
        }elseif($discountType == 'percent' and $discountCal > 0){
            $discount = ($subTotal * $discountCal)/100;
            $entity->setDiscount($discount);
        }
        $entity->setDiscountType($discountType);
        if($entity->getVatPercent() > 0){
            $vatPercentage = $entity->getVatPercent();
            $total = ($entity->getSubTotal() - $entity->getDiscount());
            $vat = (($total * $vatPercentage)/100);
            $entity->setVat($vat);
        }
        if($entity->getSdPercent() > 0){
            $sdPercentage = $entity->getSdPercent();
            $total = ($entity->getSubTotal() - $entity->getDiscount());
            $vat = (($total * $sdPercentage)/100);
            $entity->setSd($vat);
        }
        $total = ($entity->getSubTotal() - $entity->getDiscount() + $entity->getVat() + $entity->getSd()+ $entity->getDeliveryCharge());
        $entity->setTotal($total);
        $em->persist($entity);
        $em->flush();
        return $entity;

    }

    public function insertHold($user,$cart)
    {
        $em = $this->_em;
        /* @var $entity Pos */

        $pos = $this->findOneBy(array('createdBy' => $user , 'process' => 'Created'));
        foreach ($cart->contents as $product){
            $entity = new PosItem();
            $entity->setPos($pos);
            $entity->setItemId($product['id']);
            $entity->setUnit($product['unit']);
            $entity->setQuantity($product['quantity']);
            $entity->setSubTotal($product['subTotal']);
            $em->persist($entity);
            $em->flush();
        }
    }

    public function getLastId($inventory)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(e.id)');
        $qb->from('InventoryBundle:Vendor','e');
        $qb->where("e.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $count = $qb->getQuery()->getSingleScalarResult();
        if($count > 0 ){
            return $count+1;
        }else{
            return 1;
        }

    }

    public function getApiVendor(GlobalOption $entity)
    {

        $config = $entity->getInventoryConfig()->getId();
        $qb = $this->createQueryBuilder('s');
        $qb->where('s.inventoryConfig = :config')->setParameter('config', $config) ;
        $qb->orderBy('s.companyName','ASC');
        $result = $qb->getQuery()->getResult();

        $data = array();

        /* @var $row Vendor */

        foreach($result as $key => $row) {
            $data[$key]['vendor_id']    = (int) $row->getId();
            $data[$key]['name']           = $row->getCompanyName();
        }

        return $data;
    }


    public function searchAutoComplete($q, InventoryConfig $inventory)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.inventoryConfig', 'ic');
        $query->select('e.companyName as id');
        $query->addSelect('e.companyName as text');
        $query->where($query->expr()->like("e.companyName", "'$q%'"  ));
        $query->andWhere("ic.id = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->groupBy('e.id');
        $query->orderBy('e.companyName', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

}
