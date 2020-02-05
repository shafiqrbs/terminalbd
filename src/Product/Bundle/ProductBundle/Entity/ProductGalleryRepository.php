<?php

namespace Product\Bundle\ProductBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductGalleryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductGalleryRepository extends EntityRepository
{
    public function insertProductGallery($reEntity,$data)
    {

        $em = $this->_em;
        $i=0;
        $user = $reEntity->getUser();
        foreach ($data as $key => $value) {

            $entity = new ProductGallery();

            if(strpos($key,'tmpname')){

                $imageName = $user.'/products/'.nl2br(htmlentities(stripslashes($value)));
                $entity->setPath($imageName);
                $entity->setProduct($reEntity);
                $em->persist($entity);

            }
            $i++;

        }
        $em->flush();

        if(!empty($data['imageId'])){
            $this->removeImage($data['imageId']);
        }
    }

    public function removeImage($posts)
    {
        $em = $this->_em;
        foreach ($posts as $post ){
           $entity = $em->getRepository('ProductProductBundle:ProductGallery')->find($post);
           $em->remove($entity);
        }
        $em->flush();


    }

    public function insertDuplicateProductGallery($reEntity,$oldEntity)
    {
        $em = $this->_em;
        $productGalleries = $oldEntity->getProductGalleries();


        foreach($productGalleries as $row ){

            $entity = New ProductGallery();

            $entity->setProduct($reEntity);
            $entity->setPath($row->getPath());
            $em->persist($entity);

        }
        $em->flush();

    }



}
