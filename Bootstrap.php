<?php

use ShopwarePlugins\OssArticleInjection\Bootstrap\Setup;

/**
 * Class Shopware_Plugins_Backend_OssArticleInjection_Bootstrap
 *
 * @category    Shopware
 * @package     Shopware_Plugins
 * @subpackage  OssArticleInjection
 * @copyright   Copyright(c) 2018, Odessite
 * @version $Id$
 */
class Shopware_Plugins_Backend_OssArticleInjection_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    private $pluginInfo = [];
    protected $setupService = null;

    /**
     * @return array
     */
    public function getCapabilities(){
        return array(
            'install'   =>  true,
            'update'   =>  true,
            'enable'   =>  true
        );
    }

    /**
     * @return array|mixed
     */
    public function getPluginDetails(){
        if( empty($this->pluginInfo) )
            $this->pluginInfo = json_decode( file_get_contents(__DIR__ . '/plugin.json'), true );

        return $this->pluginInfo;
    }

    /**
     * Meta info about plugin
     * @return array
     * @throws Exception
     */
    public function getInfo(){
        $this->getPluginDetails();

        if( $this->pluginInfo )
            return array(
                'version'   =>  $this->getVersion(),
                'copyright' =>  $this->pluginInfo['copyright'],
                'label' =>  $this->getLabel(),
                'source' => $this->getSource(),
                'author'    =>  $this->pluginInfo['author'],
                'supplier'    =>  $this->pluginInfo['author'],
                'description'    =>  file_get_contents( __DIR__ . '/description.html' ),
                'support' => $this->pluginInfo['author'],
                'link'  =>  $this->pluginInfo['link']
            );
        else
            throw new Exception('The plugin has an invalid version file.');
    }

    /**
     * Returns the current version of the plugin.
     * @return string
     */
    public function getVersion(){
        $info = $this->getPluginDetails();

        return $info['currentVersion'];
    }

    /**
     * Get (nice) name for plugin manager list
     * @return string
     */
    public function getLabel(){
        $info = $this->getPluginDetails();

        return $info['label']['en'];
    }

    /**
     * Standard plugin install method to register all required components.
     *
     * @throws \Exception
     * @return array
     */
    public function install()
    {
        try {
            if ( !$this->assertMinimumVersion('4.3.3') ) {
                throw new Exception('This plugin requires Shopware 4.3.3 or a later version.');
            }
            $this->getSetupService()->install();
        } catch (Exception $e) {
            $this->uninstall();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'invalidateCache' => $this->getInvalidateCacheArray()
            ];
        }

        return ['success' => true, 'invalidateCache' => $this->getInvalidateCacheArray()];
    }

    /**
     * Register Plugin namespace in autoloader
     */
    public function afterInit()
    {
        $this->Application()->Loader()->registerNamespace( 'ShopwarePlugins\OssArticleInjection', $this->Path() );
    }

    public function onStartFrontDispatch()
    {
        $container =  Shopware()->Container();
        $subscribers = [
            new \ShopwarePlugins\OssArticleInjection\Subscriber\Backend($this, $container)
        ];


        foreach ($subscribers as $subscriber ) {
            $this->get('events')->addSubscriber($subscriber);
        }
    }

    /**
     * @return Setup
     */
    private function getSetupService()
    {
        if (!$this->setupService) {
            $this->setupService =  new Setup($this);
        }

        return $this->setupService;
    }

    /**
     * @return array
     */
    private function getInvalidateCacheArray()
    {
        return array( 'backend', 'template', 'theme' );
    }
}