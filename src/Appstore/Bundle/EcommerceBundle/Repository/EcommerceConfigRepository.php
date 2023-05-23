<?php

namespace Appstore\Bundle\EcommerceBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * EcommerceConfigRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EcommerceConfigRepository extends EntityRepository
{
    public function ecommerceReset(GlobalOption $option)
    {
        $em = $this->_em;
        $optionId = $option->getId();
        $ecommerceId = $option->getEcommerceConfig()->getId();

        $FeatureWitget = $em->createQuery('DELETE SettingAppearanceBundle:FeatureWidget e WHERE e.globalOption = '. $optionId);
        $FeatureWitget->execute();

        $Feature = $em->createQuery('DELETE SettingAppearanceBundle:Feature e WHERE e.globalOption = '. $optionId);
        $Feature->execute();

        $FeatureBrand = $em->createQuery('DELETE SettingAppearanceBundle:FeatureBrand e WHERE e.globalOption = '. $optionId);
        $FeatureBrand->execute();

        $FeatureCategory = $em->createQuery('DELETE SettingAppearanceBundle:FeatureCategory e WHERE e.globalOption = '. $optionId);
        $FeatureCategory->execute();

        $Order = $em->createQuery('DELETE EcommerceBundle:Order e WHERE e.globalOption = '. $optionId);
        $Order->execute();

        $OrderReturn = $em->createQuery('DELETE EcommerceBundle:OrderReturn e WHERE e.globalOption = '. $optionId);
        $OrderReturn->execute();

        $PreOrder = $em->createQuery('DELETE EcommerceBundle:PreOrder e WHERE e.globalOption = '. $optionId);
        $PreOrder->execute();

        $Promotion = $em->createQuery('DELETE EcommerceBundle:Promotion e WHERE e.ecommerceConfig = '. $ecommerceId);
        $Promotion->execute();

        $Promotion = $em->createQuery('DELETE EcommerceBundle:Discount e WHERE e.ecommerceConfig = '. $ecommerceId);
        $Promotion->execute();

    }

    public function ecommerceDelete(GlobalOption $option)
    {
        $em = $this->_em;
        $optionId = $option->getId();
        $ecommerceId = $option->getEcommerceConfig()->getId();

        $FeatureWitget = $em->createQuery('DELETE SettingAppearanceBundle:FeatureWidget e WHERE e.globalOption = '. $optionId);
        $FeatureWitget->execute();

        $Feature = $em->createQuery('DELETE SettingAppearanceBundle:Feature e WHERE e.globalOption = '. $optionId);
        $Feature->execute();

        $FeatureBrand = $em->createQuery('DELETE SettingAppearanceBundle:FeatureBrand e WHERE e.globalOption = '. $optionId);
        $FeatureBrand->execute();

        $FeatureCategory = $em->createQuery('DELETE SettingAppearanceBundle:FeatureCategory e WHERE e.globalOption = '. $optionId);
        $FeatureCategory->execute();

        $Order = $em->createQuery('DELETE EcommerceBundle:Order e WHERE e.globalOption = '. $optionId);
        $Order->execute();

        $OrderReturn = $em->createQuery('DELETE EcommerceBundle:OrderReturn e WHERE e.globalOption = '. $optionId);
        $OrderReturn->execute();

        $PreOrder = $em->createQuery('DELETE EcommerceBundle:PreOrder e WHERE e.globalOption = '. $optionId);
        $PreOrder->execute();

        $Item = $em->createQuery('DELETE EcommerceBundle:Item e WHERE e.ecommerceConfig = '. $ecommerceId);
        $Item->execute();

        $Promotion = $em->createQuery('DELETE EcommerceBundle:Promotion e WHERE e.ecommerceConfig = '. $ecommerceId);
        $Promotion->execute();

        $Promotion = $em->createQuery('DELETE EcommerceBundle:Discount e WHERE e.ecommerceConfig = '. $ecommerceId);
        $Promotion->execute();

    }
}
