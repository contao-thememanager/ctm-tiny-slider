<?php

namespace ContaoThemeManager\TinySlider\Generator;

use ContaoThemeManager\Core\Generator\ConfigGenerator;
use ContaoThemeManager\Core\StyleManager\StyleManagerXML;

/**
 * Generates css and xml for tiny slider
 *
 * @author Sebastian Zoglowek <https://github.com/zoglo>
 */
class TinySliderConfigGenerator extends ConfigGenerator
{
    public function generateOptions(array &$configVars, StyleManagerXML $xml): void
    {
        $newOptions = [];

        if (isset($configVars['tns-items-amount']))
        {
            $newOptions = explode(',', $configVars['tns-items-amount']);

            foreach ($newOptions as $key => $var)
            {
                if (!is_numeric($var))
                {
                    unset($newOptions[$key]);
                }
            }
        }

        $newOptions = array_merge($newOptions, ['1','2','3','4','5','6']);

        asort($newOptions);
        $configVars['tns-items-amount'] = implode(',', $newOptions);;
        $options = $this->getListOptions($configVars, 'tns-items-amount');

        $xml->addGroup('slider')
            ->addChild('items', $options);
        $xml->addGroup('sliderXS')
            ->addChild('items', $options);
        $xml->addGroup('sliderS')
            ->addChild('items', $options);
        $xml->addGroup('sliderM')
            ->addChild('items', $options);
        $xml->addGroup('sliderL')
            ->addChild('items', $options);
        $xml->addGroup('sliderXL')
            ->addChild('items', $options);
    }
}
