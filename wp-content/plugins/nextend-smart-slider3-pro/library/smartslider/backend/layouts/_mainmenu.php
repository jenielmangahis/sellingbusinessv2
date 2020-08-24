<?php
$cmd = N2Request::getVar("nextendcontroller", "sliders");
/**
 * @see Nav
 */

$views = array();
$views[] = N2Html::tag('a', array(
    'href'  => $this->appType->router->createUrl("settings/default"),
    'class' => 'n2-h4 n2-uc ' . ($cmd == "settings" ? "n2-active" : "")
), n2_('Settings'));



$views[] = N2Html::link(n2_('Help'), $this->appType->router->createUrl("help/index"), array(
    'class' => 'n2-h4 n2-uc ' . ($cmd == "help" ? "n2-active" : "")
));

N2Html::nav(array(
    'logoUrl'      => $this->appType->router->createUrl("sliders/index"),
    'logoImageUrl' => $this->appType->app->getLogo(),
    'views'        => $views,
    'actions'      => $this->getFragmentValue('actions')
));