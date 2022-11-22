<?php

declare(strict_types=1);

/*
 * This file is part of Contao ThemeManager Tiny Slider.
 *
 * (c) https://www.oveleon.de/
 */

namespace ContaoThemeManager\TinySlider\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use ContaoThemeManager\TinySlider\ContaoThemeManagerTinySlider;
use ContaoThemeManager\Core\ContaoThemeManagerCore;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoThemeManagerTinySlider::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                    ContaoThemeManagerCore::class
                ])->setReplace(['ctm-tiny-slider']),
        ];
    }
}
