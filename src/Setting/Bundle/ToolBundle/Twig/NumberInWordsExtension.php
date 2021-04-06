<?php
namespace Setting\Bundle\ToolBundle\Twig;

use Symfony\Component\Form\AbstractExtension;

class NumberInWordsExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('price', [$this, 'formatPrice']),
        ];
    }


    public function formatPrice($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
    {
        $price = number_format($number, $decimals, $decPoint, $thousandsSep);
        $price = '$'.$price;

        return $price;
    }

}
