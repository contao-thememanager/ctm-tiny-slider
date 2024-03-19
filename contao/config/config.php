<?php
/*
 * This file is part of Contao ThemeManager Tiny Slider.
 *
 * (c) https://www.oveleon.de/
 */

use ContaoThemeManager\TinySlider\Generator\TinySliderConfigGenerator;

// Add SCSS sources
$GLOBALS['TC_SOURCES']['configFiles'][] = 'bundles/contaothememanagertinyslider/framework/scss/_config.scss';
$GLOBALS['TC_SOURCES']['files'][]       = 'bundles/contaothememanagertinyslider/framework/scss/_slider.scss';

$GLOBALS['CTM_HOOKS']['onCreateCustomXmlConfig'][] = [TinySliderConfigGenerator::class, 'generateOptions'];
