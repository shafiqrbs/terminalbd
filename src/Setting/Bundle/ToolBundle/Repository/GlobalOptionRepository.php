<?php

namespace Setting\Bundle\ToolBundle\Repository;

use Appstore\Bundle\AccountingBundle\Entity\AccountingConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\DmsBundle\Entity\DmsConfig;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsConfig;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\AppearanceBundle\Entity\Menu;
use Setting\Bundle\AppearanceBundle\Entity\MenuGrouping;
use Setting\Bundle\ContentBundle\Entity\ContactPage;
use Setting\Bundle\ContentBundle\Entity\HomePage;
use Setting\Bundle\ContentBundle\Entity\Page;
use Setting\Bundle\ToolBundle\Entity\AdsTool;
use Setting\Bundle\ToolBundle\Entity\FooterSetting;
use Setting\Bundle\ToolBundle\Entity\MobileIcon;
use Setting\Bundle\ToolBundle\Entity\SiteSetting;
use Setting\Bundle\AppearanceBundle\Entity\TemplateCustomize;

/**
 * GlobalOptionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GlobalOptionRepository extends EntityRepository
{

    function getList() {
       return  $this->createQueryBuilder('g')
           ->orderBy('g.updated', 'DESC')
           ->getQuery();
    }

    public function searchHandle($qb,$data)
    {

        if(!empty($data['location'])){
            $qb->andWhere("e.location= :location");
            $qb->setParameter('location', $data['location']);
        }
        if(!empty($data['syndicate'])){
            $qb->andWhere("e.syndicate = :syndicate");
            $qb->setParameter('syndicate', $data['syndicate']);
        }
        if(!empty($data['name'])){
            $qb->andWhere("e.slug LIKE :slug");
            $qb->setParameter('slug',  '%'.$data['name'].'%');
        }
        return $qb;
    }

    function findByApplicationDomain($slug) {

        $qb =  $this->createQueryBuilder('e');
        $qb->join('e.siteSetting', 'sitesetting');
        $qb->join('sitesetting.appModules', 'appmodules');
        $qb->orderBy('e.name', 'ASC');
        $qb->where("e.status = 1");
        $qb->andWhere("e.domain != :domain")->setParameter('domain', 'NULL');
        $qb->andWhere("appmodules.slug = :slug")->setParameter('slug', $slug);
        $result = $qb->getQuery();
        return $result;
    }

    function findByDomain($data = array()) {

        $qb =  $this->createQueryBuilder('e');
        $qb->orderBy('e.name', 'ASC');
        $qb->where("e.status = 1");
        $qb->andWhere("e.domain != :domain");
        $qb->setParameter('domain', 'NULL');
        $this->searchHandle($qb,$data);
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    function findBySubdomain($data = array()) {

        $syndicate = isset($data['syndicate'])  ? $data['syndicate']:'';
        $location = isset($data['location']) ? $data['location']:'';
        $name = isset($data['name']) ? $data['name']:'';
        $data = array('location'=> $location ,'syndicate'=> $syndicate,'name' => $name);

        $qb =  $this->createQueryBuilder('e');
        $qb->leftJoin('e.templateCustomize', 't');
        $qb->orderBy('e.name', 'ASC');
        $qb->where("e.status = 1");
        $qb->andWhere("e.subDomain != :subDomain");
        $qb->setParameter('subDomain', 'null');
        $qb->andWhere("t.logo != :logo");
        $qb->setParameter('logo', 'null');
        $this->searchHandle($qb,$data);
        $result = $qb->getQuery();
        return $result;
    }

    function getActiveDomainList($form = array()) {


        $location = !empty($form->get('location')->getData()) ? $form->get('location')->getData()->getId():'';
        $syndicate = !empty($form->get('syndicate')->getData()) ? $form->get('syndicate')->getData()->getId() :'';
        $name = $form->get('name')->getData();
        $data = array('location'=>$location,'syndicate' => $syndicate,'name' => $name);
        $qb  = $this->createQueryBuilder('e');
            $qb->where("e.status = :status");
            $qb->setParameter('status', 1);
            $this->searchHandle($qb,$data);
            $qb->orderBy('e.updated', 'DESC');
            $result = $qb->getQuery();
        return $result;


    }


    function urlSlug($str, $options = array()) {

        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

        $defaults = array(
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => array(),
            'transliterate' => false,
        );

        // Merge options
        $options = array_merge($defaults, $options);


        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);


        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        $slug = $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;

        $em = $this->_em;
        $entity = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug'=>$slug));
        if (empty($entity)){
            return $slug;
        }else{
            return $this->checkExistingSlug($slug);
        }



    }

    public function checkExistingSlug($slug)
    {

        $em = $this->_em;

        $slugNew = '';
        for ($i = 1; $i < 100; $i++){

            $new = $slug.'-'.$i;
            $existsSlug = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug'=>$new));

            if (empty($existsSlug) ){
                $slugNew = $slug.'-'.$i;
                break;
            }
        }

        if ($slugNew == ''){
            return $slug.'-'.md5(time());
        }else{
            return $slugNew;
        }


    }

    public function getUniqueId(){

        $passcode =substr(str_shuffle(str_repeat('0123456789',5)),0,4);
        $t = microtime(true);
        $micro = ($passcode + floor($t));
        return $micro;
    }


    public function createGlobalOption($mobile,$data,$user ='')
    {

        $syndicate = $data['Core_userbundle_user']['globalOption']['syndicate'];
        $location = $data['Core_userbundle_user']['globalOption']['location'];
        $name = $data['Core_userbundle_user']['globalOption']['name'];
        $em = $this->_em;

        $syndicate = $em->getRepository('SettingToolBundle:Syndicate')->findOneBy(array('id' => $syndicate));
        $location = $em->getRepository('SettingLocationBundle:Location')->findOneBy(array('id' => $location));
        $globalOption = new GlobalOption();
        if($user){
            $globalOption->setAgent($user);
            $globalOption->setStatus(true);
        }else{
            $globalOption->setStatus(false);
        }
        $globalOption->setName($name);
        $globalOption->setSlug($this->urlSlug($name));
        $globalOption->setMobile($mobile);
        $globalOption->setSyndicate($syndicate);
        $globalOption->setLocation($location);
        $globalOption->setUniqueCode($this->getUniqueId());
        $em->persist($globalOption);
        $em->flush($globalOption);
        return $globalOption;

    }


    public function systemConfigUpdate(GlobalOption $globalOption )
    {
        $accounting = $this->_em->getRepository('AccountingBundle:AccountingConfig')->findOneBy(array('globalOption'=>$globalOption));
        if(empty($accounting)){
            $config = new AccountingConfig();
            $config->setGlobalOption($globalOption);
            $this->_em->persist($config);
        }

        $inventory = $this->_em->getRepository('InventoryBundle:InventoryConfig')->findOneBy(array('globalOption'=>$globalOption));
        if(empty($inventory)){
            $config = new InventoryConfig();
            $config->setGlobalOption($globalOption);
            $this->_em->persist($config);
        }

        $commerce = $this->_em->getRepository('EcommerceBundle:EcommerceConfig')->findOneBy(array('globalOption'=>$globalOption));
        if(empty($commerce)){
            $config = new EcommerceConfig();
            $config->setGlobalOption($globalOption);
            $this->_em->persist($config);

        }

        $hospital = $this->_em->getRepository('HospitalBundle:HospitalConfig')->findOneBy(array('globalOption'=>$globalOption));
        if(empty($hospital)){
            $config = new HospitalConfig();
            $config->setGlobalOption($globalOption);
            $this->_em->persist($config);
        }

        $dms = $this->_em->getRepository('DmsBundle:DmsConfig')->findOneBy(array('globalOption'=>$globalOption));
        if(empty($dms)){
            $config = new DmsConfig();
            $config->setGlobalOption($globalOption);
            $this->_em->persist($config);
        }

        $restaurantConfig = $this->_em->getRepository('RestaurantBundle:RestaurantConfig')->findOneBy(array('globalOption'=>$globalOption));
        if(empty($restaurantConfig)){
            $config = new RestaurantConfig();
            $config->setGlobalOption($globalOption);
            $this->_em->persist($config);
        }

        $dpsConfig = $this->_em->getRepository('DoctorPrescriptionBundle:DpsConfig')->findOneBy(array('globalOption'=>$globalOption));
        if(empty($dpsConfig)){
            $config = new DpsConfig();
            $config->setGlobalOption($globalOption);
            $this->_em->persist($config);
        }

        $medicineConfig = $this->_em->getRepository('MedicineBundle:MedicineBrand')->findOneBy(array('globalOption'=>$globalOption));
        if(empty($medicineConfig)){
            $config = new MedicineConfig();
            $config->setGlobalOption($globalOption);
            $this->_em->persist($config);
        }

        $businessConfig = $this->_em->getRepository('BusinessBundle:BusinessConfig')->findOneBy(array('globalOption'=>$globalOption));
        if(empty($businessConfig)){
            $config = new BusinessConfig();
            $config->setGlobalOption($globalOption);
            $this->_em->persist($config);
        }
        $this->_em->flush();

    }

    /**
     * @param GlobalOption $entity
     */
    public function initialSetup($entity)
    {
        $em = $this->_em;

        /**
        @var GlobalOption $globalOption;
         */

        $globalOption = $entity->getGlobalOption();

        $theme       =  $em->getRepository('SettingToolBundle:Theme')->find(1);
        $settingEntity = new SiteSetting();
        $settingEntity->setGlobalOption($globalOption);
        $settingEntity->setUser($entity);
        $settingEntity->setTheme($theme);
        $modules[] = $em->getRepository('SettingToolBundle:Module')->findOneBy(array('slug' => 'page'));
        $modules[] = $em->getRepository('SettingToolBundle:Module')->findOneBy(array('slug' => 'contact'));
        $settingEntity->setModules($modules);
        $em->persist($settingEntity);

        $homePageEntity = new HomePage();
        $homePageEntity->setGlobalOption($globalOption);
        $homePageEntity->setUser($entity);
        $homePageEntity->setName('Home');
        $em->persist($homePageEntity);


        $templateEntity = new TemplateCustomize();
        $templateEntity->setGlobalOption($globalOption);
        $em->persist($templateEntity);

        $iconEntity = new MobileIcon();
        $iconEntity->setGlobalOption($globalOption);
        $em->persist($iconEntity);


        $contactEntity = new ContactPage();
        $contactEntity->setGlobalOption($globalOption);
        $contactEntity->setUser($entity);
        $em->persist($contactEntity);

        $footerEntity = new FooterSetting();
        $footerEntity->setGlobalOption($globalOption);
        $em->persist($footerEntity);

        $adsEntity = new AdsTool();
        $adsEntity->setGlobalOption($globalOption);
        $em->persist($adsEntity);

        $customer = new Customer();
        $customer->setGlobalOption($globalOption);
        $customer->setMobile($globalOption->getMobile(0));
        $customer->setName('Default');
        $em->persist($customer);

        $inventory = new InventoryConfig();
        $inventory->setGlobalOption($globalOption);
        $em->persist($inventory);


        $ecommerce = new EcommerceConfig();
        $ecommerce->setGlobalOption($globalOption);
        $em->persist($ecommerce);

        $module = $this->_em->getRepository('SettingToolBundle:Module')->findOneBy(array('slug' => 'page'));
        $about = new Page();
        $about->setGlobalOption($globalOption);
        $about->setUser($entity);
        $about->setModule($module);
        $about->setName('About us');
        $about->setMenu('About us');
        $em->persist($about);


        $menu = new Menu();
        $menu->setGlobalOption($globalOption);
        $menu->setPage($about);
        $menu->setModule($module);
        $menu->setMenu($about->getName());
        $menu->setSlug($about->getSlug());
        $em->persist($menu);

        $menu = new Menu();
        $menu->setGlobalOption($globalOption);
        $menu->setMenu('Contact us');
        $menu->setSlug('contact');
        $em->persist($menu);
        $em->flush();

        $this->defaultMenuSetup($globalOption);
        $this->initialMenuSetup($globalOption);

    }

    public function defaultMenuSetup($globalOption)
    {
        $menus = $this->_em->getRepository('SettingAppearanceBundle:MenuCustom')->findBy(array('status'=>1));
        $em = $this->_em;
        foreach( $menus as $custom){

            $menu = new Menu();
            $menu->setGlobalOption($globalOption);
            $menu->setMenuCustom($custom);
            $menu->setMenu($custom->getMenu());
            $menu->setSlug($custom->getSlug());
            $menu->setStatus(0);
            $em->persist($menu);
        }

        $em->flush();

    }

    public function initialMenuSetup($globalOption)
    {

        $menus = $this->_em->getRepository('SettingAppearanceBundle:Menu')->findBy(array('globalOption'=>$globalOption,'status'=>1));
        $em = $this->_em;
        foreach( $menus as $menu){

            $menuGrouping = new MenuGrouping();
            $menuGrouping->setGlobalOption($globalOption);
            $menuGrouping->setMenu($menu);
            $menuGrouping->setMenuGroup($em->getRepository('SettingAppearanceBundle:MenuGroup')->find(1));
            $em->persist($menuGrouping);

        }
        foreach( $menus as $menu){

            $menuGrouping = new MenuGrouping();
            $menuGrouping->setGlobalOption($globalOption);
            $menuGrouping->setMenu($menu);
            $menuGrouping->setMenuGroup($em->getRepository('SettingAppearanceBundle:MenuGroup')->find(6));
            $em->persist($menuGrouping);

        }

        $em->flush();
    }

    public function optionUpdate($entity,$data,$mobile='')
    {
        $em = $this->_em;
       // $entity->setStatus(true);
        if(isset($data['domain']) and $data['domain'] != '') {

            $entity->setDomain($this->removeDomainPrefix($data['domain']));
        }

        if(isset($data['subDomain']) and $data['subDomain'] != '') {
            $arr = explode("/",$this->remove_http($data['subDomain']));
            $entity->setSubDomain(end($arr));
        }

        if(isset($data['callBackEmail']) and $data['callBackEmail'] != '') {
            $entity->setCallBackEmail($data['callBackEmail']);
        }
        if(isset($data['callBackContent']) and $data['callBackContent'] != '') {
            $entity->setCallBackContent($data['callBackContent']);
        }

        if(isset($data['leaveEmail']) and $data['leaveEmail'] != '') {
            $entity->setLeaveEmail($data['leaveEmail']);
        }
        if(isset($data['leaveContent']) and $data['leaveContent'] != '') {
            $entity->setLeaveContent($data['leaveContent']);
        }

        if(isset($mobile)  and $mobile != $entity->getMobile() ) {
            $entity->setMobile($mobile);
        }

        if(isset($data['email']) and $data['email'] != '') {
            $entity->setEmail($data['email']);
        }

        $primaryNumber = isset($data['primaryNumber']) ? 1:0 ;
        $entity->setPrimaryNumber($primaryNumber);

        $callBackNotify = isset($data['callBackNotify']) ? 1:0 ;
        $entity->setCallBackNotify($callBackNotify);

        $customizeDesign = isset($data['customizeDesign']) ? 1:0 ;
        $entity->setCustomizeDesign($customizeDesign);

        $smsIntegration = isset($data['smsIntegration']) ? 1:0 ;
        $entity->setSmsIntegration($smsIntegration);

        $emailIntegration = isset($data['emailIntegration']) ? 1 : 0;
        $entity->setEmailIntegration($emailIntegration);

        $promotion = isset($data['promotion']) ? 1 : 0;
        $entity->setPromotion($promotion);

        $facebookPageUrl = isset($data['facebookPageUrl']) ? 1:0;
        $entity->setFacebookPageUrl($facebookPageUrl);

        if(isset($data['facebookPageUrl']) and $data['facebookPageUrl'] != '') {
            $arr = explode("/",$this->remove_http($data['facebookPageUrl']));
            $entity->setFacebookPageUrl(end($arr));
        }

        if(isset($data['twitterUrl']) and $data['twitterUrl'] != '') {
            $arr = explode("/",$this->remove_http($data['twitterUrl']));
            $entity->setTwitterUrl(end($arr));
        }

        if(isset($data['googlePlus']) and $data['googlePlus'] != '') {
            $arr = explode("/",$this->remove_http($data['googlePlus']));
            $entity->setGooglePlus(end($arr));

        }

        $facebookAds = isset($data['facebookAds']) ? 1 : 0;
        $entity->setFacebookAds($facebookAds);

        $facebookApps = isset($data['facebookApps'])? 1 : 0;
        $entity->setFacebookApps($facebookApps);

        $googleAds = isset($data['googleAds'])? 1 : 0;
        $entity->setGoogleAds($googleAds);
        $entity->setIsIntro(1);

        $em->persist($entity);
        $em->flush();

    }

    public function remove_http($url) {
        $disallowed = array('http://', 'https://');
        foreach($disallowed as $d) {
            if(strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }
        return $url;
    }

    public function removeDomainPrefix($input) {


        //$input = 'www.google.co.uk/';

        // in case scheme relative URI is passed, e.g., //www.google.com/
        $input = trim($input, '/');

        // If scheme not included, prepend it
        if (!preg_match('#^http(s)?://#', $input)) {
            $input = 'http://' . $input;
        }

        $urlParts = parse_url($input);

        // remove www
        $domain = preg_replace('/^www\./', '', $urlParts['host']);

        return $domain;

    }



    public function getGlobalOptionGroup()
    {

        $em = $this->_em;
        $connection = $em->getConnection();
        $statement = $connection->prepare("
        SELECT GROUP_CONCAT(GlobalOption.id) AS groupId,GROUP_CONCAT(GlobalOption.name) as name, GROUP_CONCAT(GlobalOption.subDomain) as subDomain,
	    GlobalOption.syndicate_id,
	    Syndicate.`name` as syndicateName
        FROM GlobalOption INNER JOIN Syndicate ON GlobalOption.syndicate_id = Syndicate.id
        WHERE GlobalOption.status=1
        GROUP BY syndicate_id");
        $statement->bindValue('id', 123);
        $statement->execute();
        $results = $statement->fetchAll();

        $globalOptions = $this->getIndexedById($results);
        $grouped = array();

        foreach ($globalOptions as $globalOption) {
            /**
             * @var GlobalOption $globalOption
             */
            if(null != $globalOption->getParent()) {
                $grouped[$globalOptions[$globalOption->getParent()->getId()]->getName()][$globalOption->getId()] = $globalOption;
            }
        }

        return $grouped;
    }

    /**
     * @param globalOption[] $results
     * @return globalOption[]
     */
    protected function getIndexedById($results)
    {
        $globalOptions = array();

        foreach ($results as $globalOption) {
            $globalOptions[$globalOption->getId()] = $globalOption;
        }
        return $globalOptions;
    }

    public function getModuleListing($entity,$activeModule='')
    {
        $em = $this->_em;
        $data ="";
        if(!empty($entity->getSiteSetting())) {
            $modules = $entity->getSiteSetting()->getModules();
            if (!empty($modules)) {
                $data .= '<div class="tm-accordion tm-style1">';
                 foreach ($modules as $module):
                    if (!$module->isIsSingle() AND $activeModule != $module->getModuleClass() ){

                    $contents = $em->getRepository('SettingContentBundle:' . $module->getModuleClass())->findUserModuleContent($entity);
                    if(!empty($contents)){

                            $data .= '<div class="accordion-title">';
                            $data .= '<h3><a href="javascript:">' . $module->getName() . '</a></h3>';
                            $data .= '</div>';
                            $data .= '<div class="accordion-container ">';
                            $data .= '<aside class="widget tm-list-style1 hover-black">';
                            $data .= '<ul>';
                            foreach ($contents as $content):
                            $data .= '<li><a  href="/institute/'.$entity->getSlug().'/'.$module->getMenuSlug().'/'.$content->getSlug().'">' .$content->getName() . '</a></li>';
                            endforeach;
                            $data .= '</ul>';
                            $data .= '</aside>';
                            $data .= '</div>';
                        }
                }
                endforeach;
                $data .= '</div>';
            }
        }
        return $data;

    }

    public function getSyndicateModuleListing($entity)
    {
        $em = $this->_em;
        $data ="";
        if(!empty($entity->getSiteSetting())) {
            $data .= '<div class="tm-tabs tm-tabs4">';
            $data .= '<ul class="tm-filter tabs tm-style1">';
            $syndicateModules = $entity->getSiteSetting()->getSyndicateModules();

            if (!empty($syndicateModules)) {
                foreach ($syndicateModules as $synModule):
                    $data .= '<li><a href="#tab-' . $synModule->getName() . '" class="selected" >' . $synModule->getName() . '</a></li>';
                endforeach;
            }
            $data .= '</ul>';
            $data .= '<div class="tab-container">';
            if (!empty($syndicateModules)) {
                foreach ($syndicateModules as $synModule):
                    $data .= '<div class="tm-accordion tm-style1">';
                    $contents = $em->getRepository('SettingContentBundle:' . $synModule->getModuleClass())->findUserModuleContent($entity);
                    if (!empty($contents)) {
                        $data .= '<aside class="widget tm-list-style1 hover-black">';
                        $data .= '<ul>';
                        foreach ($contents as $content):
                            $data .= '<li><a  href="/institute/'.$entity->getSlug().'/'.$synModule->getMenuSlug().'/'.$content->getSlug().'">' .$content->getName() . '</a></li>';
                        endforeach;
                        $data .= '</ul>';
                        $data .= '</aside>';
                    }
                    $data .= '</div>';
                endforeach;
            }
            $data .= '</div>';
            $data .= '</div>';
        }
        return $data;

    }

    public function getHomeModuleListing($entity)
    {
        $em = $this->_em;
        $data ="";
        if( $entity && !empty($entity->getUser()->getHomePage())) {
            $data .= '<div class="tm-tabs tm-tabs4">';
            $data .= '<ul class="tm-filter tabs tm-style1">';
            $syndicateModules = $entity->getUser()->getHomePage()->getModules();

                if (!empty($syndicateModules)) {
                    foreach ($syndicateModules as $synModule):
                        $data .= '<li><a href="#tab-' . $synModule->getName() . '" class="selected" >' . $synModule->getName() . '</a></li>';
                    endforeach;
                }

            $data .= '</ul>';
            $data .= '<div class="tab-container">';
            if (!empty($syndicateModules)) {
                foreach ($syndicateModules as $synModule):
                    $data .= '<div class="tab-content" id="tab-' . $synModule->getName() . '" style="display: block;">';
                    $data .= '<div class="tm-accordion tm-style3">';
                    $contents = $em->getRepository('SettingContentBundle:' . $synModule->getModuleClass())->findFeatureContent($entity);
                    if (!empty($contents)) {
                        foreach ($contents as $content):
                            $data .= '<div class="accordion-title">';
                            $data .= '<h3>' . $content->getName() . '</h3>';
                            $data .= '</div>';
                            $data .= '<div class="accordion-container" style="display: none;">';
                            $data .= '<p>' . $this->limit_words($entity->getSlug(), $synModule->getMenuSlug(), $content) . '</p>';
                            $data .= '</div> <div class="clear"></div>';
                        endforeach;
                    }
                    $data .= '</div>';
                    $data .= '</div>';
                endforeach;
            }
            $data .= '</div>';
            $data .= '</div>';
        }
        return $data;

    }

    private function limit_words($domainSlug,$moduleSlug,$content,$limit = 30){

        $words = explode(" ", $content->getContent());
        $returnWords = implode(" ",array_splice($words,0,$limit));
        $link ='<a class="tm-btn darkblue small small-link-right" href="/institute/'.$domainSlug.'/'.$moduleSlug.'/'.$content->getSlug().'"><i  class="fa fa-chain-broken"></i>&nbsp;&nbsp;&nbsp;Details</a>';
        if(count($words) > $limit ){
            $content = $returnWords.'...'.$link;
        }else{
            $content = $returnWords;
        }
        return $content;
    }


    public function getSyndicateAdmissionListing($entity)
    {
        $em = $this->_em;
        if($entity && !empty($entity->getUser()->getHomePage())) {

            $syndicateModules = $entity->getUser()->getHomePage()->getSyndicateModules();
            if (!empty($syndicateModules)) {
                foreach ($syndicateModules as $synModule):
                    if ($synModule->getName() == 'Admission') {
                        return $contents = $em->getRepository('SettingContentBundle:Admission')->findFeatureContent($entity);
                    }
                endforeach;
            }
        }
    }


    public function getNext($entity){
        $db = $this->getNextPrevious($entity);
        return $db->andWhere($db->expr()->gt('g.id',$entity->getId()))->getQuery()->getOneOrNullResult();
    }

    public function getPrevious($entity){
        $db = $this->getNextPrevious($entity);
        return $db->andWhere($db->expr()->lt('g.id',$entity->getId()))->getQuery()->getOneOrNullResult();
    }

    private function getNextPrevious(GlobalOption $entity)
    {

        /**
         * @var GlobalOption $entity
         */
        $em = $this->_em;
        $db = $em->createQueryBuilder();
        $db->select('g');
        $db->from('SettingToolBundle:GlobalOption','g');
        $db->where($db->expr()->andX(
         $db->expr()->eq('g.status',1),
         $db->expr()->eq('g.syndicate',$entity->getSyndicate()->getId())
        ));
        $db->setMaxResults(1);
        return $db;

    }

    public function getRelatedLocationVendor(GlobalOption $entity )
    {
        /**
         * @var GlobalOption $entity
         */
        $entityName = $entity->getSyndicate()->getEntityName();
        $entityClass = 'get'.$entityName;
        if ($entityName) {
            $location = $entity->getUser()->$entityClass()->getLocation();
            if ($location) {
                $locationId = $location->getId();
                $em = $this->_em;
                $db = $em->createQueryBuilder();
                $db->select('g');
                $db->from('SettingToolBundle:GlobalOption', 'g');
                $db->innerJoin('g.user', 'u');
                $db->innerJoin('u.' . strtolower($entityName), 'e');
                $db->where($db->expr()->andX(
                    $db->expr()->eq('g.status', 1),
                    $db->expr()->notIn('g.id', $entity->getId()),
                    $db->expr()->eq('e.location', $locationId),
                    $db->expr()->eq('g.syndicate', $entity->getSyndicate()->getId())
                ));
                return $db->setMaxResults(4)->getQuery()->getResult();
            }
        }
    }

    public function getInstituteList($syndicate,$level)
    {
        /**
         * @var Syndicate $syndicate
         */
        $syndicate = $this->_em->getRepository('SettingToolBundle:Syndicate')->find($syndicate);
        //$level = $this->_em->getRepository('SettingToolBundle:InstituteLevel')->findOneBy(array('slug'=>$level));
        $entityName = $syndicate->getEntityName();
        if ($entityName) {
                $em = $this->_em;
                $db = $em->createQueryBuilder();
                $db->select('g');
                $db->from('SettingToolBundle:GlobalOption', 'g');
                $db->innerJoin('g.user', 'u');
                $db->innerJoin('u.' . strtolower($entityName), 'e');
                $db->where($db->expr()->andX(
                    $db->expr()->eq('g.status', 1)
                    //$db->expr()->eq('e.location',8)
                ));
                return $db->getQuery()->getResult();

        }
    }

    public function getAppmoduleArray(GlobalOption $globalOption)
    {
        $modules = $globalOption->getSiteSetting()->getAppModules();
        /* @var GlobalOption $globalOption */
        $menuName =array();
        if (!empty($globalOption->getSiteSetting()) and !empty($modules)) {
            foreach ($globalOption->getSiteSetting()->getAppModules() as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $menuName[] = $mod->getModuleClass();
                }

            }
        }

        return $menuName;
    }



}
