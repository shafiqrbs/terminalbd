<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Divi\AjaxLoginBundle\DiviAjaxLoginBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new Sylius\Bundle\TranslationBundle\SyliusTranslationBundle(),
            new Sylius\Bundle\MoneyBundle\SyliusMoneyBundle(),
            new Sylius\Bundle\OrderBundle\SyliusOrderBundle(),
            new Sylius\Bundle\CartBundle\SyliusCartBundle(),

            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),

            new FOS\UserBundle\FOSUserBundle(),
	        new FOS\JsRoutingBundle\FOSJsRoutingBundle(),

            new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),

            new Knp\Bundle\TimeBundle\KnpTimeBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),

            new Core\UserBundle\UserBundle(),
            new Slik\DompdfBundle\SlikDompdfBundle(),
            new Setting\Bundle\LocationBundle\SettingLocationBundle(),
            new Setting\Bundle\MediaBundle\SettingMediaBundle(),
            new Setting\Bundle\AppearanceBundle\SettingAppearanceBundle(),
            new Setting\Bundle\ContentBundle\SettingContentBundle(),
            new Setting\Bundle\ToolBundle\SettingToolBundle(),
            new Syndicate\Bundle\ComponentBundle\SyndicateComponentBundle(),
            new Gregwar\ImageBundle\GregwarImageBundle(),
            new Product\Bundle\ProductBundle\ProductProductBundle(),
            new Frontend\FrontentBundle\FrontendBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new FOS\CommentBundle\FOSCommentBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new Setting\Bundle\AdvertismentBundle\SettingAdvertismentBundle(),
            new EightPoints\Bundle\GuzzleBundle\GuzzleBundle(),
            new Bindu\BinduBundle\BinduBundle(),
            new Sg\DatatablesBundle\SgDatatablesBundle(),
            new Hackzilla\BarcodeBundle\HackzillaBarcodeBundle(),
            new Appstore\Bundle\EcommerceBundle\EcommerceBundle(),
            new Appstore\Bundle\InventoryBundle\InventoryBundle(),
            new Appstore\Bundle\BillingBundle\AppstoreBillingBundle(),
            new Appstore\Bundle\DomainUserBundle\DomainUserBundle(),
           /* new SunCat\MobileDetectBundle\MobileDetectBundle(),*/
            new Appstore\Bundle\AccountingBundle\AccountingBundle(),
            new Appstore\Bundle\ConfigBundle\ConfigBundle(),
            new Appstore\Bundle\ImsBundle\ImsBundle(),
            new Xiidea\Bundle\DomainBundle\XiideaDomainBundle(),
            new Appstore\Bundle\CustomerBundle\CustomerBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

}
