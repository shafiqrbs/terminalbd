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
        if(isset($file['uploadFile'])){
            $img = $file['uploadFile'];
            $fileName = $img->getClientOriginalName();
            $imgName =  uniqid(). '.' .$fileName;
            $img->move($entity->getUploadDir(), $imgName);
            $entity->setPath($imgName);
        }
        $em->persist($entity);
        $em->flush();
    }


    public function insertNewCustomerOrder(User $user,$cart, $data = '',$files = '')
    {

        $em = $this->_em;
        $couponCode     = empty($data['couponCode']) ? '' : $data['couponCode'];
        $comment        = empty($data['comment']) ? '' : $data['comment'];
        $name           = empty($data['customerName']) ? '' : $data['customerName'];
        $phone          = empty($data['customerMobile']) ? '' : $data['customerMobile'];
        $location       = empty($data['deliveryLocation']) ? '' : $data['deliveryLocation'];
        $address        = empty($data['deliveryAddress']) ? '' : $data['deliveryAddress'];
        $deliveryDate   = empty($data['deliveryDate']) ? '' : $data['deliveryDate'];
        $timePeriod     = empty($data['timePeriod']) ? '' : $data['timePeriod'];
        $accountMobile  = empty($data['accountMobile']) ? '' : $data['accountMobile'];
        $paymentMobile  = empty($data['paymentMobile']) ? '' : $data['paymentMobile'];
        $transactionId  = empty($data['transactionId']) ? '' : $data['transactionId'];
        $grandDiscount  = empty($data['grandDiscount']) ? '' : $data['grandDiscount'];
        $shippingCharge = empty($data['shippingCharge']) ? '' : $data['shippingCharge'];
        $order = new Order();
        $globalOption = $user->getGlobalOption();
        $order->setGlobalOption($globalOption);
        $customer = $this->getDomainCustomer($user, $globalOption);
        $order->setCustomer($customer);
        $order->setCustomerName($name);
        if($phone){
            $order->setCustomerMobile($user);
        }else{
            $order->setCustomerMobile($phone);
        }
        $order->setAddress($address);
        if($location){
            $loc = $em->getRepository('EcommerceBundle:DeliveryLocation')->find($location);
            $order->setLocation($loc);
        }
        if($timePeriod){
             $period = $em->getRepository('EcommerceBundle:TimePeriod')->find($timePeriod);
            $order->setTimePeriod($period);
        }
        if(empty($deliveryDate)){
            $order->setDeliveryDate(new \DateTime("now"));
        }else{
            $date =new \DateTime($deliveryDate);
            $order->setDeliveryDate($date);
        }
        if($accountMobile){
            $account = $em->getRepository('AccountingBundle:AccountMobileBank')->find($accountMobile);
            $order->setAccountMobileBank($account);
            $order->setPaymentMobile($paymentMobile);
            $order->setTransaction($transactionId);
            $order->setCashOnDelivery(false);
        }else{
            $order->setCashOnDelivery(true);
        }
        $order->setEcommerceConfig($globalOption->getEcommerceConfig());
        $order->setShippingCharge($shippingCharge);
        $vat = $this->getCulculationVat($globalOption, $cart->total());
        $order->setVat($vat);
        $order->setComment($comment);
        $order->setCreatedBy($user);
        $order->setTotalAmount($cart->total());
        $order->setItem($cart->total_items());
        $grandTotal = $cart->total() + $order->getShippingCharge() + $vat;
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
            $orderItem->setSubTotal($row['subtotal']);
            $em->persist($orderItem);
            $em->flush();
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
        $grandTotal = $totalAmount + $order->getShippingCharge() + $vat - $order->getDiscountAmount();
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
            $order->setReturnAmount($order->getPaidAmount()  - $grandTotal);
            $order->setDueAmount(0);
        }elseif($totalAmount < $grandTotal ){
            $order->setReturnAmount(0);
            $due = (int)$grandTotal - ((int) $order->getPaidAmount());
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

    public function insertAndroidOrder(GlobalOption $option , $data= array())
    {
        $jsonUser = json_decode($data['jsonUser'],true);
        $userJson = $jsonUser[0];
        $jsonOrder = json_decode($data['jsonOrder'],true);
        $orderJson = $jsonOrder[0];
        $em = $this->_em;

        $userId         = empty($userJson['userId']) ? '' : $userJson['userId'];
        $user = $em->getRepository('UserBundle:User')->find($userId);
        $address        = empty($userJson['address']) ? '' : $userJson['address'];
        $mobile         = empty($userJson['mobile']) ? '' : $userJson['mobile'];
        $location       = empty($userJson['location']) ? '' : $userJson['location'];

        $orderId        = empty($orderJson['id']) ? '' : $orderJson['id'];
        $couponCode     = empty($orderJson['couponCode']) ? '' : $orderJson['couponCode'];
        $comment        = empty($orderJson['comment']) ? '' : $orderJson['comment'];
        $deliveryDate   = empty($orderJson['deliveryDate']) ? '' : $orderJson['deliveryDate'];
        $timePeriod     = empty($orderJson['timePeriod']) ? '' : $orderJson['timePeriod'];
        $accountMobile  = empty($orderJson['bankAccount']) ? '' : $orderJson['bankAccount'];
        $paymentMobile  = empty($orderJson['paymentMobile']) ? '' : $orderJson['paymentMobile'];
        $transactionId  = empty($orderJson['transactionId']) ? '' : $orderJson['transactionId'];
        $subTotal       = empty($orderJson['subTotal']) ? '' : $orderJson['subTotal'];
        $total          = empty($orderJson['total']) ? '' : $orderJson['total'];
        $shippingCharge = empty($orderJson['shippingCharge']) ? '' : $orderJson['shippingCharge'];
        $find = $this->findOneBy(array('globalOption' => $option,'orderId'=> $orderId));
        if(empty($find)){
            $order = new Order();
            $order->setGlobalOption($option);
            $order->setCreatedBy($user);
            $order->setAddress($address);
            $order->setCustomerMobile($mobile);
            $order->setOrderId($orderId);
            if($location){
                $loc = $em->getRepository('EcommerceBundle:DeliveryLocation')->find($location);
                $order->setLocation($loc);
            }
            if($timePeriod){
                $period = $em->getRepository('EcommerceBundle:TimePeriod')->find($timePeriod);
                $order->setTimePeriod($period);
            }
            if(empty($deliveryDate)){
                $order->setDeliveryDate(new \DateTime("now"));
            }else{
                $date =new \DateTime($deliveryDate);
                $order->setDeliveryDate($date);
            }
            if($accountMobile){
                $account = $em->getRepository('AccountingBundle:AccountMobileBank')->find($accountMobile);
                $order->setAccountMobileBank($account);
                $order->setPaymentMobile($paymentMobile);
                $order->setTransaction($transactionId);
                $order->setCashOnDelivery(false);
            }else{
                $order->setCashOnDelivery(true);
            }
            $order->setEcommerceConfig($option->getEcommerceConfig());
            $order->setShippingCharge($shippingCharge);
            $vat = $this->getCulculationVat($option, $total);
            $order->setVat($vat);
            $order->setComment($comment);
            $order->setSubTotal($subTotal);
            $order->setTotalAmount($total);
            $order->setTotal($total);
            $grandTotal = $total + $shippingCharge + $vat;
            if (!empty($couponCode)) {
                $coupon = $this->_em->getRepository('EcommerceBundle:Coupon')->getValidCouponCode($option,$couponCode);
                if (!empty($coupon)){
                    $couponAmount = $this->getCalculatorCouponAmount($order->getTotalAmount(), $coupon);
                    $order->setGrandTotalAmount($grandTotal - $couponAmount);
                    $order->setTotal($grandTotal - $couponAmount);
                    $order->setCoupon($coupon);
                    $order->setCouponAmount($couponAmount);
                }
            }else{
                $order->setGrandTotalAmount($grandTotal);
                $order->setTotal($grandTotal);
            }
            $em->persist($order);
            $em->flush();
            $this->insertJsonOrderItem($order,$data);
            return $order;
        }
        return false;


    }

    public function insertJsonOrderItem(Order $order,$data)
    {

        $em = $this->_em;
        $orderItem = json_decode($data['jsonOrderItem'],true);
        foreach ($orderItem as $row){
            $find = $em->getRepository('EcommerceBundle:OrderItem')->findOneBy(array('order' => $order,'orderItemId'=>$row['id']));
            if(empty($find)){
                $item = $em->getRepository('EcommerceBundle:Item')->find($row['itemId']);
                $orderItem = new OrderItem();
                $orderItem->setOrder($order);
                $orderItem->setOrderItemId($row['id']);
                $orderItem->setOrderId($row['orderId']);
                if($item){
                    $orderItem->setItem($item);
                }
                $orderItem->setPrice($row['price']);
                $orderItem->setQuantity($row['quantity']);
                $orderItem->setItemName($row['name']);
                $orderItem->setSize($row['size']);
                $orderItem->setColor($row['color']);
                $orderItem->setImagePath($row['url']);
                $orderItem->setSubTotal($row['price'] * $row['quantity']);
                $em->persist($orderItem);
                $em->flush();
            }

        }

    }

    public function getApiOrders($option, $arr)
    {
        $user = $arr['user'];
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.location','l');
        $qb->leftJoin('e.timePeriod','tp');
        $qb->leftJoin('e.orderItems','subProduct');
        $qb->select('e.id as id','e.created as created','e.total as total','e.subTotal as subTotal','e.invoice as invoice',
            'e.process as process','e.shippingCharge as shippingCharge','e.cashOnDelivery as cashOnDelivery','e.deliveryDate as deliveryDate');
        $qb->addSelect("l.name as location");
        $qb->addSelect("tp.name as timePeriod");
        $qb->where("e.globalOption = :option")->setParameter('option', $option->getId());
        $qb->andWhere("e.createdBy = :user")->setParameter('user', $user);
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }



    public function getApiOrderDetails($order)
    {

        //$user = $arr['user'];
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.location','l');
        $qb->leftJoin('e.timePeriod','tp');
        $qb->leftJoin('e.orderItems','subProduct');
        $qb->select('e.id as id','e.created as created','e.total as total','e.subTotal as subTotal','e.invoice as invoice',
            'e.process as process','e.shippingCharge as shippingCharge','e.cashOnDelivery as cashOnDelivery','e.deliveryDate as deliveryDate');
        $qb->addSelect("l.name as location");
        $qb->addSelect("tp.name as timePeriod");
        $qb->addSelect("GROUP_CONCAT(CONCAT(subProduct.id,'*#*',subProduct.itemName,'*#*',subProduct.price,'*#*', subProduct.quantity,'*#*', subProduct.size,'*#*', subProduct.color,'*#*', subProduct.imagePath)) as orderItems");
        $qb->where("e.id = :id")->setParameter('id', 69);
        $row = $qb->getQuery()->getOneOrNullResult();
        $data = array();
        $data['order_id'] = (int)$row['id'];
        $data['created'] = $row['created'];
        $data['total'] = $row['total'];
        $data['subTotal'] = $row['subTotal'];
        $data['invoice'] = $row['invoice'];
        $data['timePeriod'] = $row['timePeriod'];
        $data['location'] = $row['location'];
        $data['process'] = $row['process'];
        $data['deliveryDate'] = $row['deliveryDate'];
        $data['shippingCharge'] = $row['shippingCharge'];
        $data['cashOnDelivery'] = $row['cashOnDelivery'];
        $orderItems = explode(',', $row['orderItems']);
        if (!empty($row['orderItems'])) {
            for ($i = 0; count($orderItems) > $i; $i++) {
                $subs = explode("*#*", $orderItems[$i]);
                $data['orderItem'][$i]['subItemId'] = (integer)$subs[0];
                $data['orderItem'][$i]['name'] = (string)$subs[1];
                $data['orderItem'][$i]['price'] = (integer)$subs[2];
                $data['orderItem'][$i]['quantity'] = (integer)$subs[3];
                $data['orderItem'][$i]['size'] = (string)$subs[4];
                $data['orderItem'][$i]['color'] = (string)$subs[5];
                $data['orderItem'][$i]['imagePath'] = (string)$subs[6];

            }
        } else {
            $data['orderItem'] = array();
        }
        return $data;

    }




}
