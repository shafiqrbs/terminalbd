<?php

namespace Setting\Bundle\ToolBundle\Repository;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\AppearanceBundle\Entity\Menu;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * SiteSettingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SiteSettingRepository extends EntityRepository
{

    public function globalOptionSetting($globalOption)
    {
        $em = $this->_em;

        $reEntity = $em->getRepository('SettingToolBundle:SiteSetting')->findOneBy(array('globalOption'=>$globalOption));
        if(empty($reEntity) && $globalOption){
            $entity = New SiteSetting();
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush();

        }

    }

    public function moduleUpdate(GlobalOption $globalOption,$data)
    {
        $em = $this->_em;
        $entity = $globalOption->getSiteSetting();
        if(!empty($data['module'])){

            $modules = array();
            foreach($data['module'] as $sid ){
                $modules[] = $em->getRepository('SettingToolBundle:Module')->find($sid);
            }
            if (!empty($modules)) {
                $entity->setModules($modules);
                $this->removeModuleMenu($globalOption);
                $this->addModuleMenu($globalOption, $modules,'module');
            }

        }
        if(!empty($data['syndicateModule'])){
            $synarr = array();
            foreach($data['syndicateModule'] as $sid ){
                $synarr[] = $em->getRepository('SettingToolBundle:SyndicateModule')->find($sid);
            }
            if (!empty($synarr)) {
                $entity->setSyndicateModules($synarr);
            }

        }
        if(!empty($data['appModule'])){
            $synarr = array();
            foreach($data['appModule'] as $sid ){
                $synarr[] = $em->getRepository('SettingToolBundle:AppModule')->find($sid);
            }
            if (!empty($synarr)) {
                $entity->setAppModules($synarr);
            }
        }

        $em->persist($entity);
        $em->flush();

    }

    public function removeModuleMenu($globalOption)
    {
            $em = $this->_em;
            $remove = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('siteSetting' => $globalOption->getSiteSetting()));
            if(!empty($remove)){
                $em->remove($remove);
            }
            $em->flush();

    }

    public function addModuleMenu($globalOption,$modules,$type)
    {
        $em = $this->_em;

        foreach( $modules as $module ) {
            $entity = new Menu();
            if($type == 'module'){
                $entity->setModule($module);
            }else {
                $entity->setSyndicateModule($module);
            }
            $slug = $module->getSlug();
            $entity->setGlobalOption($globalOption);
            $entity->setMenu($module->getMenu());
            $entity->setSlug($slug);
            $entity->setSiteSetting($globalOption->getSiteSetting());
            $em->persist($entity);

        }
        $em->flush();

    }


    public  function insertSettingMenu($user){

        $em = $this->_em;
        $globalOption = $user->getGlobalOption();
        $reEntity = $em->getRepository('SettingToolBundle:SiteSetting')->findOneBy(array('globalOption'=>$globalOption));

        if($reEntity){
            $syndicates = $reEntity->getSyndicates();
            foreach($syndicates as $syndicate ){
                $id = $syndicate->getId();
                $syndicate = $em->getRepository('SettingContentBundle:Syndicate')->find($id);

                if(!empty($entity)){

                    $menu = $syndicate->getMenu();
                    $menuSlug = $syndicate->getSlug();
                    $entity = New Menu();
                    $entity->setGlobalOption($globalOption);
                    $entity->setSyndicate($syndicate);
                    $entity->setMenu($menu);
                    $entity->setSlug($menuSlug);
                    $entity->setSiteSetting($reEntity->getId());
                    $em->persist($entity);

                }
            }

            $em->flush();
        }

    }

    public  function updateSettingMenu(GlobalOption $reEntity)
    {

        if($reEntity){

            $modules = $reEntity->getSiteSetting()->getModules();
            if(!empty($modules)){
                $this->createModuleMenu($reEntity->getSiteSetting());
            }
            $syndicateModules = $reEntity->getSiteSetting()->getSyndicateModules();
            if(!empty($syndicateModules)){
                $this->createSyndicateModuleMenu($reEntity->getSiteSetting());
            }

        }

    }

    public function setUpdateSettingSyndicate($entity,$syndicates){

        $em = $this->_em;
        if(!empty($entity)){
            $synarr = array();
            foreach($syndicates as $sid ){
                $synarr[] = $em->getRepository('SettingToolBundle:Syndicate')->findOneBy(array('id'=>$sid));
            }
            if (!empty($synarr)) {

                $entity->setSyndicates($synarr);
                $em->persist($entity);
                $em->flush();
            }
        }

    }

    public function createSyndicateMenu($reEntity)
    {

        $em = $this->_em;

        $syndicates = $reEntity->getSyndicates();

        $checkEntity = $em->getRepository('SettingAppearanceBundle:Menu')->findBy(array('siteSetting'=>$reEntity));

        $insData=array();
        $dbData=array();

        foreach($syndicates as $module ){
            $insData[] = $module->getId();
        }
        foreach($checkEntity as $module ){

            if($module->getSyndicate()){

                $dbData[] = $module->getSyndicate()->getId();
            }
        }
        $removeData = array_diff($dbData,$insData);
        if(!empty($removeData)){
            $this->removeExistingMenu('syndicate',$removeData,$reEntity);
        }
        $insertData = array_diff($insData,$dbData);

        foreach($insertData as $syndicate ){


            $synEntity = $em->getRepository('SettingToolBundle:Syndicate')->find($syndicate);
            if(!empty($synEntity)){

                $menu = $synEntity->getName();
                $menuSlug = $synEntity->getSlug();

                $entity = New Menu();
                $entity->setSyndicate($synEntity);
                $entity->setMenu($menu);
                $entity->setMenuSlug($menuSlug);
                $entity->setSiteSetting($reEntity);
                $em->persist($entity);

            }
        }

        $em->flush();

    }
    public function createModuleMenu($reEntity)
    {

        $em = $this->_em;

        $modules = $reEntity->getModules();
        $checkEntity = $em->getRepository('SettingAppearanceBundle:Menu')->findBy(array('siteSetting'=>$reEntity));

        $insData=array();
        $dbData=array();

        foreach($modules as $module ){
            $insData[] = $module->getId();
        }
        foreach($checkEntity as $module ){

            if($module->getModule()){

                $dbData[] = $module->getModule()->getId();
            }
        }
        $removeData = array_diff($dbData,$insData);
        if(!empty($removeData)){
            $this->removeExistingMenu('module',$removeData,$reEntity);
        }
        $insertData = array_diff($insData,$dbData);

        foreach($insertData as $module ){

            $modEntity = $em->getRepository('SettingToolBundle:Module')->find($module);
            if(!empty($modEntity)){

                $menu = $modEntity->getMenu();
                $entity = New Menu();
                $entity->setModule($modEntity);
                $entity->setMenu($menu);
                $entity->setSlug($modEntity->getSlug());
                $entity->setGlobalOption($reEntity->getUser()->getGlobalOption());
                $entity->setSiteSetting($reEntity);
                $em->persist($entity);

            }
        }

        $em->flush();

    }
    public function createSyndicateModuleMenu($reEntity){

        $em = $this->_em;

        $syndicateModule = $reEntity->getSyndicateModules();

        $checkEntity = $em->getRepository('SettingAppearanceBundle:Menu')->findBy(array('siteSetting'=>$reEntity));

        $insData=array();
        $dbData=array();

        foreach($syndicateModule as $module ){
            $insData[] = $module->getId();
        }
        foreach($checkEntity as $module ){

            if($module->getSyndicateModule()){

                $dbData[] = $module->getSyndicateModule()->getId();
            }
        }
        $removeData = array_diff($dbData,$insData);
        if(!empty($removeData)){
            $this->removeExistingMenu('syndicateModule',$removeData,$reEntity);
        }
        $insertData = array_diff($insData,$dbData);

        foreach($insertData as $syndicateModule ){

            $synEntity = $em->getRepository('SettingToolBundle:SyndicateModule')->find($syndicateModule);
            if(!empty($synEntity)){

                $entity = New Menu();
                $entity->setSyndicateModule($synEntity);
                $entity->setMenu($synEntity->getName());
                $entity->setSlug($synEntity->getSlug());
                $entity->setGlobalOption($reEntity->getUser()->getGlobalOption());
                $entity->setSiteSetting($reEntity);
                $em->persist($entity);

            }
        }

        $em->flush();

    }

    /*
     * This function use menu remove that are related syndicate.
     * **/

    public function removeExistingMenu($field,$removeData,$reEntity){

        $em =$this->_em;

        foreach($removeData as $data){
            $menuEntity = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array($field => $data ,'siteSetting'=> $reEntity ));
            if(!empty($menuEntity)){

                $id = $menuEntity->getId();

                /*
                * This function use for delete menu only set by syndicate.
                * Menu grouping remove syndicate related.
                * **/

                $groupingEntity = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('menu' => $id));
                if(!$groupingEntity){
                    foreach($groupingEntity as $remove){
                        $groupingEntity = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->find($remove);
                        if (!empty($groupingEntity)) {
                            $em->remove($groupingEntity);
                        }
                    }

                }

                $em->remove($menuEntity);
            }
            $em->flush();

        }
    }
}
