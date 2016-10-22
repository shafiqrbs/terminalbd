<?php

namespace Setting\Bundle\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\AppearanceBundle\Entity\Menu;

/**
 * ContactPageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContactPageRepository extends EntityRepository
{
    public function globalOptionContact($user)
    {
        $em = $this->_em;

        $reEntity = $em->getRepository('SettingContentBundle:ContactPage')->findOneBy(array('user'=>$user));
        if(empty($reEntity) && $user){

            $entity = New ContactPage();
            $entity->setUser($user);
            $entity->setName("Contact us");
            $em->persist($entity);

            $entity = New Menu();
            $entity->setMenu('Contact us');
            $entity->setMenuSlug('contact');
            $entity->setUser($user);
            $entity->setDefaultMenu(true);
            $em->persist($entity);

            $em->flush();
        }


    }

    public function contactUpdate($globalOption,$data){


        $em = $this->_em;

        $entity = $em->getRepository('SettingContentBundle:ContactPage')->findOneBy(array('globalOption'=>$globalOption));

        if(isset($data['address1']) and $data['address1'] != '') {
            $entity->setAddress1($data['address1']);
        }

        if(isset($data['address2']) and $data['address2'] != '') {
            $entity->setAddress2($data['address2']);
        }

        if(isset($data['district']) and $data['district'] != '') {

            $district = $em->getRepository('SettingLocationBundle:Location')->find( $data['district']);
            $entity->setDistrict($district);
        }

        if(isset($data['thana']) and $data['thana'] != '') {

            $thana = $em->getRepository('SettingLocationBundle:Location')->find( $data['thana']);
            $entity->setThana($thana);
        }

        if(isset($data['postalCode']) and $data['postalCode'] != '') {
            $entity->setPostalCode($data['postalCode']);
        }
        if(isset($data['fax']) and $data['fax'] != '') {
            $entity->setFax($data['fax']);
        }
        if(isset($data['additionalPhone']) and $data['additionalPhone'] != '') {
            $entity->setAdditionalPhone($data['additionalPhone']);
        }
        if(isset($data['additionalEmail']) and $data['additionalEmail'] != '') {
            $entity->setAdditionalEmail($data['additionalEmail']);
        }
        if(isset($data['contactPerson']) and $data['contactPerson'] != '') {
            $entity->setContactPerson($data['contactPerson']);
        }
        if(isset($data['designation']) and $data['designation'] != '') {
            $entity->setDesignation($data['designation']);
        }
        if(isset($data['startHour']) and $data['startHour'] != '') {
            $entity->setStartHour($data['startHour']);
        }
        if(isset($data['endHour']) and $data['endHour'] != '') {
            $entity->setEndHour($data['endHour']);
        }
        if(isset($data['weeklyOffDay']) and $data['weeklyOffDay'] != '') {
            $entity->setWeeklyOffDay($data['weeklyOffDay']);
        }
        if(isset($data['email']) and $data['email'] != '') {
            $entity->setEmail($data['email']);
        }
        if(isset($data['content']) and $data['content'] != '') {
            $entity->setContent($data['content']);
        }

        $askForEmail = isset($data['askForEmail']) ? 1:0 ;
        $entity->setAskForEmail($askForEmail);

        $askForSms = isset($data['askForSms']) ? 1:0 ;
        $entity->setAskForSms($askForSms);

        $displayPhone = isset($data['displayPhone']) ? 1:0 ;
        $entity->setAskForEmail($displayPhone);

        $displayEmail = isset($data['displayEmail']) ? 1:0 ;
        $entity->setAskForSms($displayEmail);

        if(isset($data['address1']) and $data['address1'] != '') {

            $address1   = $data['address1'];
            $thana      = !empty($entity->getThana()) ? $entity->getThana()->getName() : '' ;
            $district   = !empty($entity->getDistrict()) ? $entity->getDistrict()->getName() : '' ;
            $address    = $address1.' '.$thana.' '.$district.' Bangladesh';
            $latLong = $this->getLatLong($address);
            $entity->setLatitude($latLong['latitude']);
            $entity->setLongitude($latLong['longitude']);

        }

        $em->persist($entity);
        $em->flush();


    }



    public function getLatLong($address){

        if(!empty($address)){
            //Formatted address
            $formattedAddr = str_replace(' ','+',$address);
            //Send request and receive json data by address
            $geocodeFromAddr = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddr.'&sensor=false&key=AIzaSyD-cXbJSbrIgV5FngnhXTI5LO9PFWaVH0A');
            $output = json_decode($geocodeFromAddr);
            //Get latitude and longitute from json data
            $data['latitude']  = $output->results[0]->geometry->location->lat;
            $data['longitude'] = $output->results[0]->geometry->location->lng;
            //Return latitude and longitude of the given address
            if(!empty($data)){
                return $data;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}
