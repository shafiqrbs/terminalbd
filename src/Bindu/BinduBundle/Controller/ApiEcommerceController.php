<?php

namespace Bindu\BinduBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
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

}
