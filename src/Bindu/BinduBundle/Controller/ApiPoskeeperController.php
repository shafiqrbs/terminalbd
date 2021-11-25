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
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class ApiPoskeeperController extends Controller
{

    public function appsAction(Request $request)
    {

        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        if ($request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            $data =$this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->getAndroidAppModules();
        }else{
            return new Response('Unauthorized access.', 401);
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }


    public function checkApiValidation($request)
    {
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $unique = $request->headers->get('X-API-SECRET');
        $setup = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('uniqueCode' => $unique,'status'=> 1));
        if (!empty($setup) and $request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            return $setup;
        }
        return "invalid";
    }

    public function dashboardAction(Request $request)
    {

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{
            $entity = $this->checkApiValidation($request);
            $deviceId = $request->headers->get('X-DEVICE-ID');
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
            $data['device'] = $deviceId;
            $purchaseCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->androidDevicePurchaseOverview($entity,$data);
            $salesCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->androidDeviceSalesOverview($entity,$data);
            $expenditureOverview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->androidDeviceExpenditureOverview($entity,$data);
            $data = array(
                'globalOption'              => $entity->getId(),
                'expenditureOverview'       => $expenditureOverview ,
                'totalSales'                => $salesCashOverview['total'] ,
                'salesReceive'              => $salesCashOverview['salesReceive'] ,
                'salesVoucher'              => $salesCashOverview['voucher'] ,
                'purchaseTotal'             => $purchaseCashOverview['total'] ,
                'purchasePayment'           => $purchaseCashOverview['payment'] ,
            );

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function loginAction(Request $request)
    {

        $formData = $request->request->all();
        $mobile = $formData['mobile'];
        $data = array();
        if( $this->checkApiValidation($request) == 'invalid') {
            return new Response('Unauthorized access.', 401);
        }else {
            $entity = $this->checkApiValidation($request);
            $user = $this->getDoctrine()->getRepository('UserBundle:User')->getAndroidOTP($entity,$mobile);
            if($user){
                $login = $this->getDoctrine()->getRepository("UserBundle:User")->find($user);
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.change_password', new \Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent($login, $login->getAppPassword()));
                $data = array('status'=>'valid','otp' => $login->getAppPassword());
            }else{
                $data = array('status'=>'in-valid','otp' => '');
            }
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    public function storeBuildAction(Request $request)
    {

        $formData = $request->request->all();
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $deviceId = $formData['deviceId'];
        $mobile = $formData['mobile'];
        $entity = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('mobile' => $mobile));
        $userExist = $this->getDoctrine()->getRepository('UserBundle:User')->checkExistingUser($mobile);
        if (empty($formData) and $request->headers->get('X-API-KEY') != $key and $request->headers->get('X-API-VALUE') != $value) {
            return new Response('Unauthorized access.', 401);
        }elseif (!empty($entity) and !empty($userExist)  and  $request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            $data = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getSetupInformation($entity);
            $device = $this->getDoctrine()->getRepository('SettingToolBundle:AndroidDeviceSetup')->insert($entity,$deviceId);
            /* @var $entity GlobalOption */
            if($device) {
                $mobile = empty($entity->getHotline()) ? $entity->getMobile() : $entity->getHotline();$address = $entity->getMedicineConfig()->getAddress();
                $vatPercentage = $entity->getMedicineConfig()->getVatPercentage();
                $vatRegNo = $entity->getVatPercent();
                $vatEnable = $entity->isVatEnable();
                $footerMessage = $entity->getPrintMessage();
                if($footerMessage){
                    $printFooter = $footerMessage;
                }else{
                    $printFooter = "Thanks For Visiting our pharmacy";
                }
                $data = array(
                    'setupId' => $entity->getId(),
                    'deviceId' => $device,
                    'uniqueCode' => $entity->getUniqueCode(),
                    'name' => $entity->getName(),
                    'mobile' => $mobile,
                    'email' => $entity->getEmail(),
                    'symbol' => $entity->getCurrency() ? $entity->getCurrency()->getSymbol() : '',
                    'country' => $entity->getCurrency() ? $entity->getCountry()->getName() : '',
                    'currency' => $entity->getCurrency() ? $entity->getCurrency()->getCurrency():'',
                    'address' => $entity->getAddress(),
                    'posPrint' => $printFooter,
                    'main_app' => $entity->getMainApp()->getId(),
                    'main_app_name' => $entity->getMainApp()->getSlug(),
                    'appsManual' => $entity->getMainApp()->getApplicationManual(),
                    'website' => $entity->getDomain(),
                    'vatRegNo' => $vatRegNo,
                    'vatPercentage' => $vatPercentage,
                    'vatEnable' => $vatEnable,
                );
            }
        }elseif ($formData and $request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            $entity = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->insertSetupInformation($formData);
            $this->getDoctrine()->getRepository('UserBundle:User')->androidUserCreate($entity,$formData);
            $device = $this->getDoctrine()->getRepository('SettingToolBundle:AndroidDeviceSetup')->insert($entity,$deviceId);
            if($device) {
                $mobile = empty($entity->getHotline()) ? $entity->getMobile() : $entity->getHotline();$address = $entity->getMedicineConfig()->getAddress();
                $vatPercentage = $entity->getMedicineConfig()->getVatPercentage();
                $vatRegNo = $entity->getVatPercent();
                $vatEnable = $entity->isVatEnable();
                $footerMessage = $entity->getPrintMessage();
                if($footerMessage){
                    $printFooter = $footerMessage;
                }else{
                    $printFooter = "Thanks For Visiting our pharmacy";
                }
                $data = array(
                    'setupId' => $entity->getId(),
                    'deviceId' => $device,
                    'uniqueCode' => $entity->getUniqueCode(),
                    'name' => $entity->getName(),
                    'mobile' => $mobile,
                    'email' => $entity->getEmail(),
                    'symbol' => $entity->getCurrency() ? $entity->getCurrency()->getSymbol() : '',
                    'country' => $entity->getCurrency() ? $entity->getCountry()->getName() : '',
                    'currency' => $entity->getCurrency() ? $entity->getCurrency()->getCurrency():'',
                    'address' => $entity->getAddress(),
                    'posPrint' => $printFooter,
                    'main_app' => $entity->getMainApp()->getId(),
                    'main_app_name' => $entity->getMainApp()->getSlug(),
                    'appsManual' => $entity->getMainApp()->getApplicationManual(),
                    'website' => $entity->getDomain(),
                    'vatRegNo' => $vatRegNo,
                    'vatPercentage' => $vatPercentage,
                    'vatEnable' => $vatEnable,
                );
            }
        }else{
            return new Response('Unauthorized access.', 401);
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    public function multiUserAction(Request $request)
    {

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $formData = $request->request->all();
            $entity = $this->checkApiValidation($request);
            $setup = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->updateSetupInformation($entity,$formData);
             $data = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getSetupInformation($setup);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function createUserAction(Request $request)
    {

        $formData = $request->request->all();
        $username = $formData['username'];
        $userExist = $this->getDoctrine()->getRepository('UserBundle:User')->checkExistingUser($username);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }elseif(empty($userExist)){

            $entity = $this->checkApiValidation($request);
            $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->updateSetupInformation($entity,$formData);
            $userId = $this->getDoctrine()->getRepository('UserBundle:User')->androidUserCreate($entity,$formData);
            $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($userId);
            $data = array(
                'username' => $user->getUsername(),
                'name' => $user->getName(),
                'status' => 'success'
            );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }else{
            return new Response('Unauthorized access.', 401);
        }
    }

    public function systemUsersAction(Request $request)
    {

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('UserBundle:User')->getUsers($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }



    public function checkDuplicateUserAction(Request $request)
    {
        $username = $request->request->get('username');
        $user = $this->getDoctrine()->getManager()->getRepository("UserBundle:User")
            ->checkExistingUser($username);
        if ($user == false) {
            $data = array(
                'status' => 'valid'
            );
        }else {
            $data = array(
                'status' => 'invalid'
            );
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;


    }

    public function editUserAction(Request $request)
    {
        $username = $request->request->get('user_id');
        $user = $this->getDoctrine()->getManager()->getRepository("UserBundle:User")
            ->findOneBy(array('id' => $username));
        $data = array(
            'user_id' => $username,
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'role' => $user->getAndroidRole()
        );
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    public function updateUserAction(Request $request)
    {

        $formData = $request->request->all();
        $username = $formData['user_id'];
        $userExist = $this->getDoctrine()->getRepository('UserBundle:User')->find($username);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }elseif($userExist){

            $this->getDoctrine()->getRepository('UserBundle:User')->androidUserUpdate($userExist,$formData);
            $data = array('status' => 'success');
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;

        }else{

            return new Response('Unauthorized access.', 401);
        }
    }

    public function forgetPasswordAction(Request $request)
    {

        $username = $request->request->get('username');
        $user = $this->getDoctrine()->getManager()->getRepository("UserBundle:User")
            ->checkLoginUser($username);
        if (empty($user)) {
            return new Response('Unauthorized access.', 401);
        }else{
            $data = array(
                'licenseKey' => $user->getGlobalOption()->getUniqueCode(),
                'username' => $user->getUsername(),
                'name' => $user->getName(),
                'status' => 'success'
            );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function resetPasswordAction(Request $request)
    {

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{
            $entity = $this->checkApiValidation($request);
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $user = $this->getDoctrine()->getManager()->getRepository("UserBundle:User")
                ->findOneBy(array('username' => $username,'enabled' => 1));
            if(empty($user)){
                return new Response('Unauthorized access.', 401);
            }

            $user->setPlainPassword($password);
            $this->get('fos_user.user_manager')->updateUser($user);
            $data = array(
                'username' => $user->getUsername(),
                'name' => $user->getProfile()->getName(),
                'status' => 'success'
            );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function medicineGlobalAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $_REQUEST;
            $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->getApiDims($data);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function StockItemAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getApiStock($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function purchaseShortListAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchaseItem')->getApiPurchaseShortListSearch($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function medicineNameAction(Request $request)
    {
        $process = isset($_REQUEST['process']) ? $_REQUEST['process'] : '';
        $keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            if($process == 'brand' and !empty($keyword)){
                $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->getApiMedicine($keyword);
            }elseif($process == 'generic' and !empty($keyword)){
                $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->getApiMedicineGeneric($keyword);
            }elseif($process == 'strength'){
                $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->getApiMedicineStrength($keyword);
            }elseif($process == 'form'){
                $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->getApiMedicineForm($keyword);
            }elseif($process == 'manufacture' and !empty($keyword)){
                $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->getApiMedicineManufacture($keyword);
            }else{
                return new Response('Unauthorized access.', 401);
            }

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }


    public function customerAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->getApiCustomer($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function unitAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $data = $this->getDoctrine()->getRepository('SettingToolBundle:ProductUnit')->getApiUnit();
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
            $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticularType')->getApiCategory($entity);

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function vendorAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {
            return new Response('Unauthorized access.', 401);
        }else{

            /* @var $entity GlobalOption */
            $keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->getApiVendor($entity,$keyword);

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function createStockAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();
            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->insertAndroidStock($entity,$data);
            $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getApiStock($entity);
            $array = array(
                'stockId'=> $stock->getId(),
                'stock' => $data
            );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($array));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function stockEditAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        if( $this->checkApiValidation($request) == 'invalid') {
            return new Response('Unauthorized access.', 401);
        }else{

            /* @var $entity GlobalOption */
            $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getStockEdit($entity,$id);

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function updateStockAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();
            $entity = $this->checkApiValidation($request);
            $stockId = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateAndroidStock($stockId,$data);
            $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getStockEdit($entity,$stockId);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function insertAndroidStockMeasurementAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();
            $stockId = isset($_REQUEST['stockId']) ? $_REQUEST['stockId'] : '';
            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineMeasurement')->insertAndroidStockMeasurement($stockId,$data);
            $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineMeasurement')->getApiMeasurement($stockId);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function deleteAndroidStockMeasurementAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $em = $this->getDoctrine()->getManager();
            $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
            $entity = $em->getRepository('MedicineBundle:MedicineMeasurement')->find($id);
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MedicineStock entity.');
            }
            $em->remove($entity);
            $em->flush();
            $stockId = isset($_REQUEST['stockId']) ? $_REQUEST['stockId'] : '';
            $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineMeasurement')->getApiMeasurement($stockId);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function insertPurchaseShortItemAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();
            $stockId = isset($_REQUEST['stockId']) ? $_REQUEST['stockId'] : '';
            /* @var $entity GlobalOption */
            $id = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchaseItem')->insertApiPurchaseShortList($stockId);
            $data = array('id' => $id);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function deletePurchaseShortItemAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $stockId = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
            $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchaseItem')->find($stockId);
            $em->remove($entity);
            $em->flush();
            $data = array('status' => 'success');
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function insertGlobalToLocalAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $option = $this->checkApiValidation($request);
            $stockId = isset($_REQUEST['medicineId']) ? $_REQUEST['medicineId'] : '';
            /* @var $entity GlobalOption */
            $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->insertApiGlobalToLocal($option,$stockId);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function transactionMethodAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $result = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findAll();
            $data = array();

            /* @var $row TransactionMethod */

            foreach($result as $key => $row) {
                $data[$key]['item_id']              = (int) $row->getId();
                $data[$key]['name']                 = $row->getName();
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function paymentCardAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $result = $this->getDoctrine()->getRepository('SettingToolBundle:PaymentCard')->findAll();
            $data = array();

            /* @var $row TransactionMethod */

            foreach($result as $key => $row) {
                $data[$key]['item_id']              = (int) $row->getId();
                $data[$key]['name']                 = $row->getName();
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function apiBankAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountBank')->findBy(array('globalOption'=>$entity));

            $data = array();

            /* @var $row AccountBank */

            if($result){
                foreach($result as $key => $row) {
                    $data[$key]['global_id']            = (int) $entity->getId();
                    $data[$key]['item_id']              = (int) $row->getId();
                    $data[$key]['name']                 = $row->getName();
                    $data[$key]['service_charge']       = $row->getServiceCharge();
                }
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function apiMobileAction(Request $request)
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
                    $data[$key]['item_id'] = (int)$row->getId();
                    $data[$key]['name'] = $row->getName();
                    $data[$key]['service_charge'] = $row->getServiceCharge();
                }
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function expenditureCategoryAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory')->getApiCategory($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function templateCustomizationAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);

            /* @var $template TemplateCustomize */

            $baseurl = "";
            $template = $entity->getTemplateCustomize();
            if($template->getAndroidLogo()){
                $dir = $template->getUploadDir();
                $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/'.$dir.'/'.$template->getAndroidLogo();
            }

            $data = array(
                'globalOption'                      => $entity->getId(),
                'androidLogo'                       => $baseurl,
                'androidHeaderBg'                   => $template->getAndroidHeaderBg() ,
                'androidMenuBg'                     => $template->getAndroidMenuBg() ,
                'androidMenuBgHover'                => $template->getAndroidMenuBgHover() ,
                'androidAnchorColor'                => $template->getAndroidAnchorColor() ,
                'androidAnchorHoverColor'           => $template->getAndroidAnchorHoverColor() ,
           );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function discountCouponAction(Request $request)
    {

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('RestaurantBundle:Coupon')->getApiDiscountCoupon($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function tokenNoAction(Request $request)
    {

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getApiRestaurantToken($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function apiPingAction(Request $request){

        $data = $request->request->all();
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode(array('message'=>$data['message'], 'ipAddress'=>$ipAddress)));
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    public function apiResponseAction(Request $request, $data)
    {

    }

    public function apiSalesAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);


        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{


            $jsonInput = '[
            {
            "invoiceId":"051900113","subTotal":"1200","discount":"200","discountType":"Flat","discountCalculation":"10","vat":"0","total":"1000","receive":"800","due":"200","customerId":"223","customerName":"Jacky","customerMobile":"01828148148","addresss":"Dhaka","transactionMethod":"cash","bankAccount":"","mobileBankAccount":"","paymentMobile":"","paymentCard":"","paymentCardNo":"","transactionId":"","salesBy":"","created":"","createdBy":"","slipNo":"","tokenNo":"","discountCoupon":"","remark":""
            }
        ]';

        $jsonInputItem = '[
            {"salesId":"051900113","stockId":"7087","unitPrice":"120","quantity":"2","subTotal":"240"},
            {"salesId":"051900113","stockId":"1295","unitPrice":"120","quantity":"2","subTotal":"240"},
            {"salesId":"051900113","stockId":"7088","unitPrice":"120","quantity":"2","subTotal":"240"},
            {"salesId":"051900113","stockId":"7420","unitPrice":"120","quantity":"2","subTotal":"240"}
        ]';



            $data = $request->request->all();

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $deviceId = $request->headers->get('X-DEVICE-ID');

           // $data = array('item' => $jsonInput,'itemCount'=> 1,'subItem'=> $jsonInputItem,'subItemCount'=> 4);


            if($entity->getMainApp()->getSlug() == 'miss'){
                $this->getDoctrine()->getRepository('MedicineBundle:MedicineAndroidProcess')->insertAndroidProcess($entity,$deviceId,'sales',$data);
            }elseif($entity->getMainApp()->getSlug() == 'business'){
                $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantAndroidProcess')->insertAndroidProcess($entity,$deviceId,'sales',$data);
            }
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array('message'=>"success", 'ipAddress'=>$ipAddress)));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function apiPurchaseAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);


        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->getPurchaseList($entity);
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function apiAddPurchaseAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);


        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $deviceId = $request->headers->get('X-DEVICE-ID');

            $jsonInput = '[
            {
            "invoiceId":"051900113","subTotal":"1200","discount":"200","discountType":"Flat","discountCalculation":"10","total":"1000","receive":"800","due":"200","vendorId":"223","created":"","createdBy":"","invoiceMode":"","process":""
            }
        ]';

            $jsonInputItem = '[
            {"purchaseId":"051900113","stockId":"10717","purchasePrice":"25","quantity":"2","subTotal":"50","measurement_id":1,"unit":"Pcs"}
          
        ]';

            $data = $request->request->all();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineAndroidProcess')->insertAndroidProcess($entity,$deviceId,'purchase',$data);
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array('message'=>"success", 'ipAddress'=>$ipAddress)));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function apiExpenseAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $deviceId = $request->headers->get('X-DEVICE-ID');
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $request->request->all();
            $this->getDoctrine()->getRepository('AccountingBundle:ExpenseAndroidProcess')->insertAndroidProcess($entity,$ipAddress,$deviceId,'expenditure',$data);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array('message'=>"success", 'ipAddress'=>$ipAddress)));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function apiInvoiceTokenAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $request->request->all();
            $data = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->insertApiInvoiceToken($entity,$data);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode("success"));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

}
