<?php

namespace Appstore\Bundle\EcommerceBundle\Repository;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\EcommerceBundle\Entity\Coupon;
use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\OrderItem;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * OnlineOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderRepository extends EntityRepository
{

    protected function handleSearchBetween($qb,$data)
    {

            $invoice = isset($data['invoice'])  ? $data['invoice'] : '';
            $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
            $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
            $name =    isset($data['name'])? $data['name'] :'';
            $mobile =    isset($data['mobile'])? $data['mobile'] :'';
            $processHead =    isset($data['processHead'])? $data['processHead'] :'';
            if($name){
                $qb->andWhere($qb->expr()->like("e.customerName", "'%$name%'" ));
            }
            if($mobile){
                $qb->andWhere($qb->expr()->like("e.customerMobile", "'%$mobile%'" ));
            }
            if (!empty($startDate) and !empty($endDate) ) {
                $compareTo = new \DateTime($startDate);
                $startDate =  $compareTo->format('Y-m-d 00:00:00');
                $qb->andWhere("e.created >= :startDate")->setParameter('startDate', $startDate);
            }
            if (!empty($startDate) and !empty($endDate) ) {
                $compareTo = new \DateTime($endDate);
                $endDate =  $compareTo->format('Y-m-d 23:59:59');
                $qb->andWhere("e.created <= :endDate")->setParameter('endDate', $endDate);
            }
            if (!empty($invoice)) {
                $qb->andWhere("e.invoice = :invoice")->setParameter('invoice', $invoice);
            }
            if (!empty($processHead)) {
                $qb->andWhere("e.processHead = :process")->setParameter('process', $processHead);
            }

    }

    public function findWithSearch($option, $data)
    {
        if (!empty($data['sortBy'])) {

            $sortBy = explode('=?=', $data['sortBy']);
            $sort = $sortBy[0];
            $order = $sortBy[1];
        }
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :option")->setParameter('option', $option);
        $qb->andWhere("e.process != :head")->setParameter('head', "Delete");
        if (empty($data['sortBy'])){
            $qb->orderBy('e.updated', 'DESC');
        }else{
            $qb->orderBy($sort ,$order);
        }
        $res = $qb->getQuery();
        return  $res;

    }


    public function insertOrder(GlobalOption $globalOption)
    {
        $em = $this->_em;
        $order = new Order();
        $user = $em->getRepository('UserBundle:User')->find(30);
        $order->setCreatedBy($user);
        $order->setEcommerceConfig($globalOption->getEcommerceConfig());
        $em->persist($order);
        $em->flush();
        return $order;
    }

    /**
     * @param $datetime
     * @param $entity
     * @return int|mixed
     */
    /*public function getLastCode($datetime, $entity)
    {
        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');


        $qb = $this->_em->getRepository('EcommerceBundle:Order')->createQueryBuilder('s');

        $qb
            ->select('MAX(s.code)')
            ->where('s.globalOption = :option')
            ->andWhere('s.updated >= :today_startdatetime')
            ->andWhere('s.updated <= :today_enddatetime')
            ->setParameter('option', $entity)
            ->setParameter('today_startdatetime', $today_startdatetime)
            ->setParameter('today_enddatetime', $today_enddatetime);
        $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }*/



    public function fileUploader(Order $entity, $file = '')
    {
        $em = $this->_em;
        if(isset($file['prescriptionFile'])){
            $img = $file['prescriptionFile'];
            $fileName = $img->getClientOriginalName();
            $imgName =  uniqid(). '.' .$fileName;
            $img->move($entity->getUploadDir(), $imgName);
            $entity->setPath($imgName);
        }
        $em->persist($entity);
        $em->flush();
    }


    public function insertNewCustomerOrder(User $user,$shop, $cart, $couponCode ='',$files = '')
    {

        $em = $this->_em;

        $order = new Order();
        $globalOption = $this->_em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('uniqueCode' => $shop));
        $order->setGlobalOption($globalOption);
        $customer = $this->getDomainCustomer($user, $globalOption);
        $order->setCustomer($customer);
        if($user->getProfile()->getLocation()){
            $order->setLocation($user->getProfile()->getLocation());
        }
        $address = $user->getProfile()->getAddress().'-'.$user->getProfile()->getPostalCode();
        $order->setAddress($address);

        $order->setEcommerceConfig($globalOption->getEcommerceConfig());
        $order->setShippingCharge($globalOption->getEcommerceConfig()->getShippingCharge());
        $order->setDeliveryDate(new \DateTime("now"));
        $vat = $this->getCulculationVat($globalOption, $cart->total());
        $order->setVat($vat);
        $order->setCreatedBy($user);
        $order->setTotalAmount($cart->total());
        $order->setItem($cart->total_items());
        $grandTotal = $cart->total() + $globalOption->getEcommerceConfig()->getShippingCharge() + $vat;
        if (!empty($couponCode)) {
            $coupon = $this->_em->getRepository('EcommerceBundle:Coupon')->getValidCouponCode($globalOption,$couponCode);
            if (!empty($coupon)){
                $couponAmount = $this->getCalculatorCouponAmount($order->getTotalAmount(), $coupon);
                $order->setGrandTotalAmount($grandTotal - $couponAmount);
                $order->setCoupon($coupon);
                $order->setCouponAmount($couponAmount);
            }
        }else{
            $order->setGrandTotalAmount($grandTotal);
        }
        $em->persist($order);
        $em->flush();
        $this->insertOrderItem($order,$cart);
        $this->fileUploader($order,$files);
        return $order;

    }

    public function getDomainCustomer($user,GlobalOption $globalOption)
    {

        $customer = $this->_em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption,'mobile' => $user->getUsername()));
        if(!empty($customer)){

            return $customer;

        }else{

            $em = $this->_em;
            $entity = new Customer();
            $entity->setGlobalOption($globalOption);
            $entity->setMobile($user->getUsername());
            $entity->setEmail($user->getEmail());
            $entity->setAddress($user->getProfile()->getAddress());
            $entity->setName($user->getProfile()->getName());
            $entity->setCustomerType('online');
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }
    }

    public function insertOrderItem(Order $order,$cart)
    {

        $em = $this->_em;

        $domainType = $order->getGlobalOption()->getDomainType();

        if($domainType == 'ecoomerce'){
            foreach ($cart->contents() as $row){

                $goodsItem = $em->getRepository('EcommerceBundle:ItemSub')->find($row['id']);
                if(!empty($goodsItem)) {
                    $salesPrice = empty($goodsItem->getDiscountPrice()) ? $goodsItem->getSalesPrice() : $goodsItem->getDiscountPrice();
                    $orderItem = new OrderItem();
                    $orderItem->setOrder($order);
                    $orderItem->setItem($goodsItem->getItem());
                    $orderItem->setItemSub($goodsItem);
                    $orderItem->setPrice($salesPrice);
                    $orderItem->setQuantity($row['quantity']);
                    $orderItem->setSubTotal($row['quantity'] * $salesPrice);
                    if (!empty($row['colorId'])){
                        $orderItem->setColor($em->getRepository('SettingToolBundle:ProductColor')->find($row['colorId']));
                    }
                    $em->persist($orderItem);
                    $em->flush();

                }

            }

        }elseif($domainType == 'medicine'){
            foreach ($cart->contents() as $row){
                $item = $em->getRepository('EcommerceBundle:Item')->find($row['id']);
                $orderItem = new OrderItem();
                $orderItem->setOrder($order);
                if($item){
                    $orderItem->setItem($item);
                }
                $orderItem->setPrice($row['price']);
                $orderItem->setQuantity($row['quantity']);
                $orderItem->setUnitName($row['productUnit']);
                $orderItem->setItemName($row['name']);
                $orderItem->setBrandName($row['brand']);
                $orderItem->setCategoryName($row['category']);
                $orderItem->setUnitName($row['productUnit']);
                $orderItem->setSubTotal($row['subtotal']);
                $em->persist($orderItem);
                $em->flush();
            }

        }


    }

    public function getCulculationVat(GlobalOption $globalOption,$total)
    {
        /* @var EcommerceConfig $config */
        $totalVat = 0;
        $config = $globalOption->getEcommerceConfig();
        if($config->isVatEnable() == 1 and $config->getVat() > 0 ){
            $vat = $config->getVat();
            $totalVat = round(($total  * $vat )/100);
        }
        return $totalVat;


    }

    public function getCalculatorCouponAmount( $grandTotal = 0, Coupon $coupon)
    {
        if ($coupon->getPercentage() == 1 ){
            $percentage = round(($grandTotal  * $coupon->getAmount() )/100);
            if($percentage >= $coupon->getAmountLimit()){
                $couponAmount = $coupon->getAmountLimit();
            }else{
                $couponAmount = $percentage;
            }
        }else{
            $couponAmount = $coupon->getAmount();
        }
        return $couponAmount;
    }


    public function updateOrder(Order $order)
    {
        $em = $this->_em;
        $orderItem = $em->getRepository('EcommerceBundle:OrderItem')->getItemOverview($order);
        $totalAmount = $orderItem['totalAmount'];
        $totalItem = $orderItem['totalQuantity'];
        $order->setTotalAmount($totalAmount);
        $order->setItem($totalItem);
        $vat = $this->getCulculationVat($order->getGlobalOption(),$totalAmount);
        $grandTotal = $totalAmount + $order->getShippingCharge() + $vat;
        $order->setVat($vat);
        $order->setGrandTotalAmount($grandTotal);
        if (!empty($order->getCoupon())) {
            $couponAmount = $this->getCalculatorCouponAmount($totalAmount, $order->getCoupon());
            $order->setGrandTotalAmount($grandTotal - $couponAmount);
            $order->setCouponAmount($couponAmount);
        }else{
            $order->setGrandTotalAmount($grandTotal);
        }

        if($order->getPaidAmount() > $grandTotal ){
            $order->setReturnAmount(($order->getPaidAmount() + $order->getDiscountAmount()) - $grandTotal);
            $order->setDueAmount(0);
        }elseif($totalAmount < $grandTotal ){
            $order->setReturnAmount(0);
            $due = (int)$grandTotal - ((int) $order->getPaidAmount() + $order->getDiscountAmount());
            $order->setDueAmount($due);
        }
        $em->flush();
    }

    public function updateOrderPayment(Order $entity)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('EcommerceBundle:OrderPayment','e')
            ->select('sum(e.amount) as totalAmount')
            ->where('e.order = :order')
            ->andWhere('e.status = :status')
            ->setParameter('order', $entity ->getId())
            ->setParameter('status', 1)
            ->getQuery()->getSingleResult();

        $entity->setPaidAmount(floatval($total['totalAmount']));
        $due = $entity->getGrandTotalAmount() - $entity->getPaidAmount();
        $entity->setDueAmount($due);
        $em->persist($entity);
        $em->flush();
    }

    public function insertAndroidOrder(GlobalOption $option,$data= array())
    {

    }



}
