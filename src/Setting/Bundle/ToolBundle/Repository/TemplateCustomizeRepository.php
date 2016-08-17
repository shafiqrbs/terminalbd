<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 10/9/15
 * Time: 8:05 AM
 */

namespace Setting\Bundle\ToolBundle\Repository;


use Doctrine\ORM\EntityRepository;

class TemplateCustomizeRepository extends EntityRepository {


    public function updateTemplateCustomize($entity , $data , $file){

        $em = $this->_em;

        $this->fileUploader($entity, $file);

        if(isset($data['logoDisplayWebsite']) and $data['logoDisplayWebsite'] != '') {
            $entity->setLogoDisplayWebsite($data['logoDisplayWebsite']);
        }
        if(isset($data['siteBgColor']) and $data['siteBgColor'] != '') {
            $entity->setSiteBgColor($data['siteBgColor']);
        }
        if(isset($data['headerBgColor']) and $data['headerBgColor'] != '') {
            $entity->setHeaderBgColor($data['headerBgColor']);
        }
        if(isset($data['menuBgColor']) and $data['menuBgColor'] != '') {
            $entity->setMenuBgColor($data['menuBgColor']);
        }
        if(isset($data['bodyColor']) and $data['bodyColor'] != '') {
            $entity->setBodyColor($data['bodyColor']);
        }
        if(isset($data['footerBgColor']) and $data['footerBgColor'] != '') {
            $entity->setFooterBgColor($data['footerBgColor']);
        }
        if(isset($data['footerTextColor']) and $data['footerTextColor'] != '') {
            $entity->setFooterTextColor($data['footerTextColor']);
        }

        $em->persist($entity);
        $em->flush();
    }

    public function fileUploader($entity, $file = '')
    {
        $em = $this->_em;
        if(isset($file['logo'])){
             $img = $file['logo'];
             $fileName = $img->getClientOriginalName();
             $imgName =  uniqid(). '.' .$fileName;
             $img->move($entity->getUploadDir(), $imgName);
             $entity->setLogo($imgName);
        }

        if(isset($file['bgImage'])){
             $img = $file['bgImage'];
             $fileName = $img->getClientOriginalName();
             $imgName =  uniqid(). '.' .$fileName;
             $img->move($entity->getUploadDir(), $imgName);
             $entity->setBgImage($imgName);
        }

        if(isset($file['headerBgImage'])){
             $img = $file['headerBgImage'];
             $fileName = $img->getClientOriginalName();
             $imgName =  uniqid(). '.' .$fileName;
             $img->move($entity->getUploadDir(), $imgName);
             $entity->setHeaderBgImage($imgName);
        }

        $em->persist($entity);
        $em->flush();
    }
} 