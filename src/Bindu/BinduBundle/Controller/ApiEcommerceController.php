<?php

namespace Bindu\BinduBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\DomainUserBundle\Entity\CustomerAddress;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use Gregwar\Image\Image;
use Setting\Bundle\ContentBundle\Entity\Page;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiEcommerceController extends Controller
{

    public function paginate($entities)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10  /*limit per page*/
        );
        return $pagination;
    }

    public function resizeFilter($pathToImage, $width = 520, $height = 520)
    {
        $path = '/' . Image::open(__DIR__.'/../../../../../web/' . $pathToImage)->cropResize($width, $height, 'transparent', 'top', 'left')->guess();
        return $_SERVER['HTTP_HOST'].$path;
    }

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

    public function portalStoreAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $result = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('isPortalStore'=>1));
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $data = array();
            if($result) {

                /* @var $entity GlobalOption */
                foreach ($result as $key => $entity) {

                    $address = '';
                    $vatRegNo = '';
                    $vatPercentage = '';
                    $vatEnable = '';
                    $productColumn = $entity->getEcommerceConfig()->getMobileProductColumn();
                    $productFeatureColumn = $entity->getEcommerceConfig()->getMobileFeatureColumn();
                    $currency = $entity->getEcommerceConfig()->getCurrency();
                    $address = $entity->getEcommerceConfig()->getAddress();
                    $preOrder = $entity->getEcommerceConfig()->getIsPreorder();
                    $cartProcess = $entity->getEcommerceConfig()->getCartProcess();
                    $shippingCharge = $entity->getEcommerceConfig()->getShippingCharge();
                    $cashOnDelivery = $entity->getEcommerceConfig()->isCashOnDelivery();
                    $pickupLocation = $entity->getEcommerceConfig()->getPickupLocation();
                    $relatedProductMode = $entity->getEcommerceConfig()->getRelatedProductMode();
                    $productMode = $entity->getEcommerceConfig()->getProductMode();

                    $logo = $entity->getTemplateCustomize()->getLogo();
                    $bgImage = $entity->getTemplateCustomize()->getBgImage();
                    $mobile = empty($entity->getHotline()) ? $entity->getMobile() : $entity->getHotline();
                    $data[$key]['name'] = $entity->getName();
                    $data[$key]['license'] = (int)$entity->getMobile();
                    $data[$key]['activeKey'] = $entity->getUniqueCode();
                    $data[$key]['setupId'] = $entity->getId();
                    $data[$key]['description'] = $entity->getDescription();
                    $data[$key]['mobile'] = $mobile;
                    $data[$key]['address'] = $address;
                    $data[$key]['email'] = $entity->getEmail();
                    $data[$key]['website'] = $entity->getDomain();
                    $data[$key]['locationName'] =  $entity->getLocation()->getName();
                    $data[$key]['vatRegNo'] = $vatRegNo;
                    $data[$key]['vatPercentage'] = $vatPercentage;
                    $data[$key]['productColumn'] = $productColumn;
                    $data[$key]['productFeatureColumn'] = $productFeatureColumn;
                    $data[$key]['currency'] = $currency;
                    $data[$key]['preOrder'] = $preOrder;
                    $data[$key]['cartProcess'] = $cartProcess;
                    $data[$key]['shippingCharge'] = $shippingCharge;
                    $data[$key]['cashOnDelivery'] = $cashOnDelivery;
                    $data[$key]['pickupLocation'] = $pickupLocation;
                    $data[$key]['vatEnable'] = $vatEnable;
                    $data[$key]['logo']      = empty($logo) ? '' : $_SERVER['HTTP_HOST']."/{$entity->getTemplateCustomize()->getUploadDir()}/{$logo}";
                    $data[$key]['backgroundImage']      = empty($bgImage) ? '' :  $_SERVER['HTTP_HOST']."/{$entity->getTemplateCustomize()->getUploadDir()}/{$bgImage}";
                    $data[$key]['productMode']      = empty($productMode) ? 'grid' : $productMode;
                    $data[$key]['relatedProductMode']      =  $relatedProductMode;
                }
            }
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    function hex6ToHex8($hex6) {

        return str_replace("#","0xFF",$hex6);

       /* // Convert the 6-digit HEX color code to RGB values
        $r = hexdec(substr($hex6, 0, 2));
        $g = hexdec(substr($hex6, 2, 2));
        $b = hexdec(substr($hex6, 4, 2));

        // Calculate the alpha value as fully opaque (255)
        $alpha = str_pad(dechex(255), 2, '0', STR_PAD_LEFT);

        // Convert the RGB values and alpha value to an 8-digit HEX color code
        $hex8 = $alpha . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
        return $hex8;*/

    }

    public function setupAction(Request $request)
    {

        $formData = $request->request->all();
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $uniqueCode = $formData['activeKey'];
        $mobile = $formData['license'];
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

            $productColumn = $entity->getEcommerceConfig()->getMobileProductColumn();
            $productFeatureColumn = $entity->getEcommerceConfig()->getMobileFeatureColumn();
            $currency = $entity->getEcommerceConfig()->getCurrency();
            $address = $entity->getEcommerceConfig()->getAddress();
            $preOrder = $entity->getEcommerceConfig()->getIsPreorder();
            $cartProcess = $entity->getEcommerceConfig()->getCartProcess();
            $shippingCharge = $entity->getEcommerceConfig()->getShippingCharge();
            $cashOnDelivery = $entity->getEcommerceConfig()->isCashOnDelivery();
            $pickupLocation = $entity->getEcommerceConfig()->getPickupLocation();
            $path = $entity->getEcommerceConfig()->getWebPath();
            $mobile = empty($entity->getHotline()) ? $entity->getMobile() : $entity->getHotline();

            $androidHeaderBg = (string) trim($entity->getTemplateCustomize()->getAndroidHeaderBg());
            $appPrimaryColor = (string) trim($entity->getTemplateCustomize()->getAppPrimaryColor());
            $appSecondaryColor =(string) trim($entity->getTemplateCustomize()->getAppSecondaryColor());
            $appBarColor = (string) trim($entity->getTemplateCustomize()->getAppBarColor());
            $appTextTitle = (string) trim($entity->getTemplateCustomize()->getAppTextTitle());
            $appTextColor = (string) trim($entity->getTemplateCustomize()->getAppTextColor());
            $appCartColor = (string) trim($entity->getTemplateCustomize()->getAppCartColor());
            $appMoreColor = (string) trim($entity->getTemplateCustomize()->getAppMoreColor());
            $appBorderColor =(string) trim( $entity->getTemplateCustomize()->getAppBorderColor());
            $appPositiveColor =(string) trim( $entity->getTemplateCustomize()->getAppPositiveColor());
            $appNegativeColor = (string) trim($entity->getTemplateCustomize()->getAppNegativeColor());
            $appIconColor = (string) trim($entity->getTemplateCustomize()->getAndroidIconColor());
            //$appAnchorColor = (string) trim($entity->getTemplateCustomize()->getAndroidAnchorColor());
            $appDiscountColor = (string) trim($entity->getTemplateCustomize()->getAndroidAnchorColor());
            $appAnchorHoverColor = (string) trim($entity->getTemplateCustomize()->getAndroidAnchorHoverColor());


            $data = array(
                    'setupId' => $entity->getId(),
                    'uniqueCode' => $entity->getUniqueCode(),
                    'name' => $entity->getName(),
                    'mobile' => $mobile,
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
                    'productColumn' => $productColumn,
                    'productFeatureColumn' => $productFeatureColumn,
                    'currency' => $currency,
                    'preOrder' => $preOrder,
                    'cartProcess' => $cartProcess,
                    'shippingCharge' => $shippingCharge,
                    'cashOnDelivery' => $cashOnDelivery,
                    'pickupLocation' => $pickupLocation,
                    'vatEnable' => $vatEnable,
                     'appHeaderBg' => empty($androidHeaderBg) ? '' : $this->hex6ToHex8($androidHeaderBg),
                     'appPrimaryColor' => empty($androidHeaderBg) ? '' : $this->hex6ToHex8($appPrimaryColor),
                     'appSecondaryColor' => empty($appSecondaryColor) ? '' : $this->hex6ToHex8($appSecondaryColor),
                     'appBarColor' => empty($appBarColor)?'':$this->hex6ToHex8($appBarColor) ,
                     'appTextTitle' => empty($appTextTitle)?'':$this->hex6ToHex8($appTextTitle) ,
                     'appTextColor' => empty($appTextColor)?'':$this->hex6ToHex8($appTextColor) ,
                     'appCartColor' => empty($appCartColor)?'':$this->hex6ToHex8($appCartColor) ,
                     'appMoreColor' => empty($appMoreColor)?'':$this->hex6ToHex8($appMoreColor) ,
                     'appBorderColor' => empty($appBorderColor)?'':$this->hex6ToHex8($appBorderColor) ,
                     'appPositiveColor' => empty($appPositiveColor)?'':$this->hex6ToHex8($appPositiveColor) ,
                     'appNegativeColor' => empty($appNegativeColor)?'':$this->hex6ToHex8($appNegativeColor) ,
                     'appDiscountColor' => empty($appDiscountColor)?'':$this->hex6ToHex8($appDiscountColor) ,
                     'appIconColor' => empty($appIconColor)?'':$this->hex6ToHex8($appIconColor) ,
                     'logo'      =>  $_SERVER['HTTP_HOST']."/{$path}"
                );
            }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }


    public function configurationAction(Request $request)
    {

        $formData = $request->request->all();
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $uniqueCode = $formData['activeKey'];
        $mobile = $formData['license'];
        $data = array();
        $entity = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('uniqueCode' => $uniqueCode,'mobile' => $mobile,'status'=>1));
        if (empty($entity) and $request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            return new Response('Unauthorized access.', 401);
        }else{

            /* @var $option GlobalOption */

            $address = '';
            $vatRegNo = '';
            $vatPercentage = '';
            $vatEnable = '';

            $entity = $option->getEcommerceConfig();

            $productColumn = $entity->getEcommerceConfig()->getMobileProductColumn();
            $productFeatureColumn = $entity->getEcommerceConfig()->getMobileFeatureColumn();
            $currency = $entity->getEcommerceConfig()->getCurrency();
            $address = $entity->getEcommerceConfig()->getAddress();
            $preOrder = $entity->getEcommerceConfig()->getIsPreorder();
            $cartProcess = $entity->getEcommerceConfig()->getCartProcess();
            $shippingCharge = $entity->getEcommerceConfig()->getShippingCharge();
            $cashOnDelivery = $entity->getEcommerceConfig()->isCashOnDelivery();
            $pickupLocation = $entity->getEcommerceConfig()->getPickupLocation();
            $path = $entity->getEcommerceConfig()->getWebPath();
            $mobile = empty($entity->getHotline()) ? $entity->getMobile() : $entity->getHotline();

            $data = array(
                'setupId' => $entity->getId(),
                'uniqueCode' => $entity->getUniqueCode(),
                'name' => $entity->getName(),
                'mobile' => $mobile,
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
                'productColumn' => $productColumn,
                'productFeatureColumn' => $productFeatureColumn,
                'currency' => $currency,
                'preOrder' => $preOrder,
                'cartProcess' => $cartProcess,
                'shippingCharge' => $shippingCharge,
                'cashOnDelivery' => $cashOnDelivery,
                'pickupLocation' => $pickupLocation,
                'vatEnable' => $vatEnable,
                'logo'      =>  $_SERVER['HTTP_HOST']."/{$path}"
            );
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    public function menuAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $data = array();
            $entity = $this->checkApiValidation($request);
            $data['discount'] = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiDiscount($entity);
            $data['category'] = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllCategory($entity);
            $data['brand'] = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllBrand($entity);
            $data['promotion'] = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiPromotion($entity);
            $data['tag'] = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiTag($entity);
            $data['page'] = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->getPages($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function pageMenuAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */


            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->getPages($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function pageAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $id = $_REQUEST['id'];
            $option = $this->checkApiValidation($request);
            $entity = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$option,'id'=>$id));

            /* @var $entity Page */

            $path = $_SERVER['HTTP_HOST'].'/'.$entity->getWebPath();
            $data = array(
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'menu' => $entity->getMenu(),
                'shortDescription' => $entity->getShortDescription(),
                'description' => $entity->getContent(),
                'image'      => empty($entity->getPath()) ? '' : $path
            );
            if($entity->getPageMetas()){
                foreach ($entity->getPageMetas() as $key => $sub ){
                    $data['specification'][$key]['metaId'] = (integer)$sub->getId();
                    $data['specification'][$key]['label'] = (string)$sub->getMetaKey();
                    $data['specification'][$key]['value'] = (string)$sub->getMetavalue();
                }

            }else{
                $data['specification'] = array();
            }

            if($entity->getPhotoGallery() and $entity->getPhotoGallery()->getGalleryImages()){
                foreach ($entity->getPhotoGallery()->getGalleryImages() as $key => $sub ){
                    $data['gallery'][$key]['imageId'] = (integer)$sub->getId();
                    if($sub->getPath()){
                        $path = $sub->getWebPath($option->getId(),$entity->getPhotoGallery()->getId());
                        $data['gallery'][$key]['imagePath'] =  $_SERVER['HTTP_HOST'].'/'.$path;
                    }else{
                        $data['gallery'][$key]['imagePath'] = "";
                    }
                }
            }else{
                $data['gallery'] = array();
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
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

            $feature = $_REQUEST['feature_id'];
            $module = $_REQUEST['module'];
            $entity = $this->checkApiValidation($request);
            if($entity and $feature and $module){
                $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getFeatureWidgetProduct($entity,$feature,$module);
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
            $search = $_REQUEST;
            $entity = $this->checkApiValidation($request);
            $result = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiProduct($entity,$search);
         /*   $data = array();
            if($result){
                foreach($result as $key => $row) {
                    $data[$key]['product_id']               = (int) $row['id'];
                    $data[$key]['item_id']                  = (int) rand(time(),10);
                    $data[$key]['name']                     = $row['name'];
                    $data[$key]['nameBn']                   = $row['nameBn'];
                    $data[$key]['quantity']                 = $row['quantity'];
                    $data[$key]['price']                    = $row['price'];
                    $data[$key]['discountPrice']            = $row['discountPrice'];
                    $data[$key]['categoryId']               = $row['categoryId'];
                    $data[$key]['category']                 = $row['categoryName'];
                    $data[$key]['categoryBn']               = $row['categoryNameBn'];
                    $data[$key]['brandId']                  = $row['brandId'];
                    $data[$key]['brand']                    = $row['brandName'];
                    $data[$key]['brandBn']                  = $row['brandNameBn'];
                    $data[$key]['discountId']               = $row['discountId'];
                    $data[$key]['discount']                 = $row['discountName'];
                    $data[$key]['discountBn']               = $row['discountNameBn'];
                    $data[$key]['discountType']             = $row['discountType'];
                    $data[$key]['discountAmount']           = $row['discountAmount'];
                    $data[$key]['promotionId']              = $row['promotionId'];
                    $data[$key]['promotion']                = $row['promotionName'];
                    $data[$key]['promotionBn']              = $row['promotionNameBn'];
                    $data[$key]['tag']                      = $row['tags'];
                    $data[$key]['tagBn']                    = $row['tagsBn'];
                    $data[$key]['colors']                   = $row['colors'];
                    $data[$key]['colorsBn']                 = $row['colorsBn'];
                    $data[$key]['country']                  = $row['country'];
                    $data[$key]['shortDescription']         = $row['shortContent'];
                    $data[$key]['shortDescriptionBn']       = $row['shortContentBn'];
                    $data[$key]['tagBn']                    = $row['tagNameBn'];
                    $data[$key]['unitName']                 = $row['unitName'];
                    $data[$key]['itemAssurance']                 = $row['itemAssurance'];
                    $data[$key]['warningLabel']                 = $row['warningLabel'];
                    $data[$key]['isFeatureBrand']           = ($row['isFeatureBrand']) ? 1 : 0;
                    $data[$key]['isFeatureCategory']        = ($row['isFeatureCategory']) ? 1 : 0;
                    $data[$key]['quantityApplicable']       = ($row['quantityApplicable']) ? 1 : 0;
                    $data[$key]['maxQuantity']              = ($row['maxQuantity']) ? $row['maxQuantity']:'';
                    if($row['path']){
                        $data[$key]['imagePath']            =  "http://".$_SERVER['HTTP_HOST']."/uploads/domain/{$entity->getId()}/ecommerce/product/{$row['path']}";
                    }else{
                        $data[$key]['imagePath']            = "";
                    }
                }
            }*/
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($result));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function productKeywordAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $keyword = $_REQUEST['search'];
            $entity = $this->checkApiValidation($request);
            $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->searchAndroidStock($keyword,$entity->getEcommerceConfig());
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($entities));
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

            $itemId = $_REQUEST['id'];
            $entity = $this->checkApiValidation($request);
            $productDetails = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiProductDetails($entity,$itemId);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($productDetails));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function relatedProductAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $itemId = $_REQUEST['category'];
            $entity = $this->checkApiValidation($request);
            $search = array('category' => $itemId);
            $relatedProduct = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiRelatedProduct($entity,$search);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($relatedProduct));
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
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiFeatureCategory($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function featureProductsAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiFeatureCategory($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function allFeatureProductsAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $module = $_REQUEST['module'];
            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getFeatureWidgetProductAll($entity,$module);
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
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllCategory($entity);
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
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiFeatureBrand($entity);
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



    public function orderCreateAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $userId = 1;
            $attachFile = '';
            $jsonUser = '[
                {
                "userId":"1538","address":"Dhaka","mobile":"01828148148","location":"Mirpur"
                }
            ]';

            $jsonOrder = '[
                {
                "id":"051900113","userId":"2","subTotal":"1200","discount":"200","vat":"0","total":"1000","shippingCharge":"100","couponCode":"434234234","timePeriod":"10.30 AM","deliveryDate":"2021-06-01","transactionMethod":"cash","cashOnDelivery":"1","discountCoupon":"123443","receiveAccount":"","paymentMobile":"","paymentCard":"","paymentCardNo":"","transactionId":"","comment":""
                }
            ]';

            $jsonOrderItem = '[
            {"id":"051900113","itemId":"051900113","orderId":"051900113","name":"ItemName","price":"120","quantity":"2","size":"XL","color":"Red","quantity":"2","subTotal":"240","url":"http://www.terminalbd.com/uploads/domain/4/ecommerce/product/Teer semolina suji.jpg"},
            {"id":"051900114","itemId":"051900114","orderId":"051900113","name":"ItemName","price":"120","quantity":"2","size":"XL","color":"Red","quantity":"2","subTotal":"240","url":"http://www.terminalbd.com/uploads/domain/4/ecommerce/product/Teer semolina suji.jpg"}
            ]';

            $data = $request->request->all();

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);

            $order = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->insertAndroidOrder($entity,$data);
            if($order){
                $returnData = array('orderId'=>$order->getId(),'invoice'=>$order->getInvoice(),'status'=>'success');
            }else{
                $returnData = array('orderId'=>'','invoice'=>'','status'=>'failed');
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }


    public function orderUploadAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);

            $data = $request->request->all();
            $order = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->insertNewCustomerOrder($entity,$data);
            if($order){
                $returnData = array('orderId'=>$order->getId(),'invoice'=>$order->getInvoice(),'status'=>'success');
            }else{
                $returnData = array('orderId'=>'','invoice'=>'','status'=>'failed');
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderListAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{


            $data = $_REQUEST;

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $returnData = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->getApiOrders($entity,$data);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function processOrderAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{


            $data = $_REQUEST;

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $returnData = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->getApiProcessOrders($entity,$data);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderDetailsAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{


            $order = $_REQUEST['id'];

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $returnData = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->getApiOrderDetails($order);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderDeleteAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{


            $order = $_REQUEST['id'];
            $user = $_REQUEST['user_id'];

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $remove = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->findOneBy(array('globalOption'=>$entity,'createdBy' => $user ,'id' => $order));
            $em = $this->getDoctrine()->getManager();
            if($remove){
                $em->remove($remove);
                $em->flush();
                $msg = "success";
            }else{
                $msg = 'Failed';
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($msg));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderTimePeriodAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $config = $entity->getEcommerceConfig();
            $returnData = $this->getDoctrine()->getRepository('EcommerceBundle:TimePeriod')->getApiTimePeriod($config);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderDeliveryLocationAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $config = $entity->getEcommerceConfig();
            $returnData = $this->getDoctrine()->getRepository('EcommerceBundle:DeliveryLocation')->getApiDeliveryLocation($config);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function testimonialAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $data = "";

            $option = $this->checkApiValidation($request);
            $testimonial = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($option,3);
            $data = array();
            /* @var $entity Page */
            foreach ($testimonial as $key => $entity){

                $data[$key]['id'] = $entity->getId();
                $data[$key]['name'] = $entity->getName();
                $data[$key]['designation'] = $entity->getDesignation();
                $data[$key]['content'] = $entity->getContent();
                $data[$key]['organization'] = $entity->getOrganization();
                if($entity->getWebPath()){
                    $path = $this->resizeFilter($entity->getWebPath());
                    $data[$key]['imagePath']            =  $path;
                }else{
                    $data[$key]['imagePath']            = "";
                }
            }

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
            if($user){
                $returnData['user_id'] = (int) $user->getId();
                $returnData['username'] = $user->getUsername();
                $returnData['name'] = $user->getProfile()->getName();
                $returnData['address'] = $user->getProfile()->getAddress();
                $returnData['email'] = $user->getProfile()->getEmail();
                $returnData['phone'] = $user->getProfile()->getAdditionalPhone();
                $returnData['password'] = $a;
                $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy(array('user' => $user->getId()));
                $addreses = $customer->getCustomerAddresses();
                $returnData['address'] = array();
                if($addreses) {
                    /* @var $address CustomerAddress */
                    foreach ($addreses as $address) {
                        $returnData['address'][$address->getId()]['id'] = (integer)$address->getId();
                        $returnData['address'][$address->getId()]['name'] = (string)$address->getName();
                        $returnData['address'][$address->getId()]['mobile'] = (string)$address->getMobile();
                        $returnData['address'][$address->getId()]['address'] = (string)$address->getAddress();
                        $returnData['address'][$address->getId()]['mode'] = (string)$address->getMode();
                    }
                }else{
                    $returnData['address'] = array();
                }
                $returnData['msg'] = "valid";
            }else{
                $returnData['msg'] = "invalid";
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;

        }

    }

    public function customerAddressAction(Request $request)
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
                $customer = $this->getDoctrine()->getRepository("DomainUserBundle:Customer")->findOneBy(array('globalOption'=>$user->getGlobalOption(),'user'=>$user->getId()));
                if(empty($customer)){
                    $customer = new Customer();
                    $customer->setUser($user->getId());
                    $customer->setGlobalOption($user->getGlobalOption());
                    $customer->setName($user->getProfile()->getName());
                    $customer->setMobile($data['mobile']);
                    $customer->setAddress($data['address']);
                    $em->persist($customer);
                    $em->flush();

                    $customerAddress = new CustomerAddress();
                    $customerAddress->setCustomer($customer);
                    $customerAddress->setName($data['name']);
                    $customerAddress->setMode('Home');
                    $customerAddress->setAddress($data['address']);
                    $customerAddress->setMobile($data['mobile']);
                    $em->persist($customerAddress);
                    $em->flush();

                }else{

                    $customerAddress = new CustomerAddress();
                    $customerAddress->setCustomer($customer);
                    $customerAddress->setName($data['name']);
                    $customerAddress->setMode($data['mode']);
                    $customerAddress->setAddress($data['address']);
                    $customerAddress->setMobile($data['mobile']);
                    $em->persist($customerAddress);
                    $em->flush();
                    $returnData['address_id'] = $customerAddress->getId();
                }
                $returnData['msg'] = "valid";
            }
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
            $user = $em->getRepository('UserBundle:User')->findOneBy(array('username'=> $mobile));
            $existEmail = $em->getRepository('UserBundle:User')->findOneBy(array('email'=> $email));

            /* @var $user User */

            if(empty($user) and !empty($email) and empty($existEmail)){

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
                $profile->setAdditionalPhone($mobile);
                $profile->setAddress($address);
                $em->persist($profile);
                $em->flush();

                $customerId = isset($data['customerId']) ? $data['customerId'] : '';
                $gender = isset($data['gender']) ? $data['gender'] : '';

                $customer = new Customer();
                $customer->setUser($user->getId());
                $customer->setGlobalOption($user->getGlobalOption());
                $customer->setName($profile->getName());
                $customer->setMobile($profile->getMobile());
                $customer->setAddress($profile->getAddress());
                if($customerId){
                    $customer->getCustomerId($customerId);
                }
                if($gender){
                    $customer->setGender($gender);
                }
                $em->persist($customer);
                $em->flush();

                $customerAddress = new CustomerAddress();
                $customerAddress->setCustomer($customer);
                $customerAddress->setMode('Home');
                $customerAddress->setAddress($customer->getAddress());
                $customerAddress->setMobile($customer->getMobile());
                $em->persist($customerAddress);
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
            $returnData['name'] = $user->getProfile()->getName();
            $returnData['address'] = $user->getProfile()->getAddress();
            $returnData['phone'] = $user->getProfile()->getAdditionalPhone();
            $returnData['location'] = empty($user->getProfile()->getDeliveryLocation()) ? '' : $user->getProfile()->getDeliveryLocation()->getName();
            $returnData['msg'] = "valid";
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function userUpdateProfileAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();

            /* @var $entity GlobalOption */

            $setup = $this->checkApiValidation($request);
            $user = $data['user_id'];
            $name = $data['name'];
            $mobile = isset($data['phone']) ? $data['phone'] :'';
            $email = isset($data['email']) ? $data['email'] :'';
            $address = $data['address'];
            $location = isset($data['location']) ? $data['location'] : '';
            $em = $this->getDoctrine()->getManager();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($mobile);
            $user = $em->getRepository('UserBundle:User')->find($user);

            /* @var $user User */

            if(($user) and $user->getProfile()){
                $profile = $user->getProfile();
                $profile->setName($name);
                $profile->setAdditionalPhone($mobile);
                $profile->setAddress($address);
                $profile->setEmail($email);
                if($location){
                    $loc = $em->getRepository('EcommerceBundle:DeliveryLocation')->find($location);
                    $profile->setDeliveryLocation($loc);
                }
                $em->persist($profile);
                $em->flush();
            }else{
                $profile = new Profile();
                $profile->setUser($user);
                $profile->setName($name);
                $profile->setMobile($user->getUsername());
                $profile->setAdditionalPhone($mobile);
                $profile->setAddress($address);
                $profile->setEmail($email);
                if($location){
                    $loc = $em->getRepository('EcommerceBundle:DeliveryLocation')->find($location);
                    $profile->setDeliveryLocation($loc);
                }
                $em->persist($profile);
                $em->flush();
            }

            $returnData['user_id'] = (int) $user->getId();
            $returnData['username'] = $user->getUsername();
            $returnData['name'] = $user->getProfile()->getName();
            $returnData['address'] = $user->getProfile()->getAddress();
            $returnData['email'] = $user->getProfile()->getEmail();
            $returnData['phone'] = $user->getProfile()->getAdditionalPhone();
            $returnData['location'] = empty($user->getProfile()->getDeliveryLocation()) ? '' : $user->getProfile()->getDeliveryLocation()->getName();
            $returnData['msg'] = "valid";
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function mobileAccountAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->findBy(array('globalOption'=>$entity));
            $data = array();

            /* @var $row AccountMobileBank */

            if($result) {
                foreach ($result as $key => $row) {
                    $data[$key]['global_id'] = (int)$entity->getId();
                    $data[$key]['id'] = (int)$row->getId();
                    $data[$key]['name'] = $row->getName();
                }
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }


}
