<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\ItemMetaAttribute;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Doctrine\ORM\EntityRepository;

/**
 * ItemMeatAttributeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemMeatAttributeRepository extends EntityRepository
{
    public function insertProductAttribute($reEntity,$data)
    {

        $em = $this->_em;
        $i=0;

        if(isset($data['attributeId'])){
            foreach ($data['attributeId'] as $value) {

                $metaAttribute = $this->_em->getRepository('InventoryBundle:ItemMetaAttribute')->findOneBy(array('purchaseVendorItem'=>$reEntity,'itemAttribute'=>$value));
                if(!empty($metaAttribute)){
                    $this->updateMetaAttribute($metaAttribute,$data['value'][$i]);
                }else{
                    $itemAttribute= $this->_em->getRepository('InventoryBundle:ItemAttribute')->find($value);
                    $entity = new ItemMetaAttribute();
                    $entity->setValue($data['value'][$i]);
                    $entity->setItemAttribute($itemAttribute);
                    $entity->setPurchaseVendorItem($reEntity);
                    $em->persist($entity);
                }
                $i++;
            }
            $em->flush();
        }


    }

    public function updateMetaAttribute($metaAttribute,$value)
    {
            $em = $this->_em;
            $metaAttribute->setValue($value);
            $em->flush();
    }

    public function insertCopyProductAttribute(PurchaseVendorItem $purchaseVendorItem , PurchaseVendorItem $item)
    {
        $em = $this->_em;
        $i=0;

        if(!empty($item->getItemMetaAttributes())){
            /* @var ItemMetaAttribute $attribute */
            foreach ($item->getItemMetaAttributes() as $attribute) {
                $entity = new ItemMetaAttribute();
                $entity->setValue($attribute->getValue());
                $entity->setItemAttribute($attribute);
                $entity->setPurchaseVendorItem($purchaseVendorItem);
                $em->persist($entity);
                $em->flush($entity);

            }

        }
    }

}
