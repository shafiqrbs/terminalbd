<?php

namespace Bindu\BinduBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Appstore\Bundle\EcommerceBundle\Entity\Item;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use Setting\Bundle\AppearanceBundle\Entity\TemplateCustomize;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiEcommerceController extends Controller
{

    public function checkApiValidation($request)
    {

        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $unique = $request->headers->get('X-API-SECRET');
        $setup = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('uniqueCode' => $unique,'status'=>1));
        if (!empty($setup) and $request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            return $setup;
        }
        return "invalid";
    }

    public function checkApiWithoutSecretValidation($request)
    {

        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        if ($request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            return "valid";
        }
        return "invalid";
    }

    public function setupAction(Request $request)
    {

        $formData = $request->request->all();
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $uniqueCode = $formData['uniqueCode'];
        $mobile = $formData['mobile'];
        $data = array();
        $entity = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('uniqueCode' => $uniqueCode,'mobile' => $mobile,'status'=>1));
        if (empty($entity) and $request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
                return new Response('Unauthorized access.', 401);
        }else{

            /* @var $entity GlobalOption */

                $address = '';
                $vatRegNo = '';
                $vatPercentage = '';
                $vatEnable = '';
                if ($entity->getMainApp()->getSlug() == "miss") {
                    $address = $entity->getMedicineConfig()->getAddress();
                    $vatPercentage = $entity->getMedicineConfig()->getVatPercentage();
                    $vatRegNo = $entity->getMedicineConfig()->getVatRegNo();
                    $vatEnable = $entity->getMedicineConfig()->isVatEnable();
                } elseif ($entity->getMainApp()->getSlug() == "business") {
                    $address = $entity->getBusinessConfig()->getAddress();
                    $vatPercentage = $entity->getBusinessConfig()->getVatPercentage();
                    $vatRegNo = $entity->getBusinessConfig()->getVatRegNo();
                    $vatEnable = $entity->getBusinessConfig()->getVatEnable();
                } elseif ($entity->getMainApp()->getSlug() == "restaurant") {
                    $address = $entity->getRestaurantConfig()->getAddress();
                    $vatPercentage = $entity->getRestaurantConfig()->getVatPercentage();
                    $vatRegNo = $entity->getRestaurantConfig()->getVatRegNo();
                    $vatEnable = $entity->getRestaurantConfig()->getVatEnable();
                } elseif ($entity->getMainApp()->getSlug() == "inventory") {
                    $address = $entity->getInventoryConfig()->getAddress();
                    $vatPercentage = $entity->getInventoryConfig()->getVatPercentage();
                    $vatRegNo = $entity->getInventoryConfig()->getVatRegNo();
                    $vatEnable = $entity->getInventoryConfig()->getVatEnable();
                }
                $data = array(
                    'setupId' => $entity->getId(),
                    'uniqueCode' => $entity->getUniqueCode(),
                    'name' => $entity->getName(),
                    'mobile' => $entity->getMobile(),
                    'email' => $entity->getEmail(),
                    'locationId' => $entity->getLocation()->getId(),
                    'address' => $address,
                    'locationName' => $entity->getLocation()->getName(),
                    'main_app' => $entity->getMainApp()->getId(),
                    'main_app_name' => $entity->getMainApp()->getSlug(),
                    'appsManual' => $entity->getMainApp()->getApplicationManual(),
                    'website' => $entity->getDomain(),
                    'vatRegNo' => $vatRegNo,
                    'vatPercentage' => $vatPercentage,
                    'vatEnable' => $vatEnable,
                );
            }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    public function homeFeatureSliderAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $data = "";

            $entity = $this->checkApiValidation($request);
            $feature = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->getFeatureWidget($entity,"Home");
            if($feature){
                $data = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->getFeatureSlider($feature);
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function featureProductAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $data = array();

            $menu = $_REQUEST['menu'];
            $entity = $this->checkApiValidation($request);
            $feature = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->getFeatureWidget($entity,$menu);
            if($feature){
                $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getFeatureWidgetProduct($feature);
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }


    public function productAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $data = $_REQUEST;
            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiProduct($entity,$data);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function productDetailsAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $item = $_REQUEST['id'];


            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiProductDetails($entity,$item);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }


    public function categoryAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureCategory')->getApiFeature($entity,10);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function allCategoryAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllCategory($entity,10);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function brandAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureBrand')->getApiFeature($entity,10);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function allBrandAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllBrand($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function promotionAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiPromotion($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function tagAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiTag($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function discountAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiDiscount($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function userLoginAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiWithoutSecretValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();

            /* @var $entity GlobalOption */

            $intlMobile =$data['mobile'];
            $em = $this->getDoctrine()->getManager();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
            $user = $em->getRepository('UserBundle:User')->findOneBy(array('username'=> $mobile,'userGroup'=> 'customer','enabled'=>1));
            /* @var $user User */
            if(empty($user)){
                $data['msg'] = "invalid";
            }else{
                $a = mt_rand(1000,9999);
                $user->setPlainPassword($a);
                $this->get('fos_user.user_manager')->updateUser($user);
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.change_password', new \Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent($user,$a));
            }

            $returnData['user_id'] = (int) $user->getId();
            $returnData['username'] = $user->getUsername();
            $returnData['password'] = $a;
            $returnData['msg'] = "valid";

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function userRegisterAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();

            /* @var $entity GlobalOption */

            $setup = $this->checkApiValidation($request);
            $name = $data['name'];
            $mobile = $data['mobile'];
            $email = $data['email'];
            $address = $data['address'];
            $em = $this->getDoctrine()->getManager();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($mobile);
            $user = $em->getRepository('UserBundle:User')->findOneBy(array('username'=> $mobile,'userGroup'=> 'customer','enabled'=> 1));

            /* @var $user User */

            if(empty($user)){

                $user = new User();
                $a = mt_rand(1000,9999);
                $user->setPlainPassword($a);
                $user->setEnabled(true);
                $user->setUsername($mobile);
                if(empty($data['email'])){
                    $user->setEmail($mobile.'@gmail.com');
                }else{
                    $user->setEmail($email);
                }
                $user->setRoles(array('ROLE_CUSTOMER'));
                $user->setUserGroup('customer');
                $user->setGlobalOption($setup);
                $user->setEnabled(1);
                $em->persist($user);
                $em->flush();

                $profile = new Profile();
                $profile->setUser($user);
                $profile->setName($name);
                $profile->setMobile($mobile);
                $profile->setAddress($address);
                $em->persist($profile);
                $em->flush();
                $dispatcher = $this->container->get('event_dispatcher');
           $dispatcher->dispatch('setting_tool.post.customer_signup_msg', new \Setting\Bundle\ToolBundle\Event\CustomerSignup($user,$setup));

            }else{

                $a = mt_rand(1000,9999);
                $user->setPlainPassword($a);
                $this->get('fos_user.user_manager')->updateUser($user);
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.change_password', new \Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent($user,$a));

            }

            $returnData['user_id'] = (int) $user->getId();
            $returnData['username'] = $user->getUsername();
            $returnData['password'] = $a;
            $returnData['msg'] = "valid";
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $userId = 1;
            $attachFile = '';
            $jsonOrder = '[
            {
            "subTotal":"1200","discount":"200","vat":"0","total":"1000","shippingCharge":"100","transactionMethod":"cash","discountCoupon":"123443","bankAccount":"","mobileBankAccount":"","paymentMobile":"","paymentCard":"","paymentCardNo":"","transactionId":"","remark":""
            }
        ]';

            $jsonOrderItem = '[
            {"itemId":"051900113","name":"ItemName","unitPrice":"120","quantity":"2","subTotal":"240"},
            {"itemId":"051900113","name":"ItemName","unitPrice":"120","quantity":"2","subTotal":"240"},
            {"itemId":"051900113","name":"ItemName","unitPrice":"120","quantity":"2","subTotal":"240"},
            {"itemId":"051900113","name":"ItemName","unitPrice":"120","quantity":"2","subTotal":"240"},
          
        ]';



            $data = $request->request->all();

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $orderData = array('userId'=> $userId,'order'=>  $data['jsonOrder'] ,'orderItem'=> $data['jsonOrderItem'],'attachFile' => $data['attachFile']);

            $this->getDoctrine()->getRepository('EcommerceBundle:Order')->insertAndroidOrder($entity,$data);

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }


}
