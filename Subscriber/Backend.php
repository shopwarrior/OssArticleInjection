<?php

namespace ShopwarePlugins\OssArticleInjection\Subscriber;

use Doctrine\ORM\Tools\Export\ExportException;
use Shopware_Plugins_Backend_OssArticleInjection_Bootstrap  as Bootstrap;

class Backend implements \Enlight\Event\SubscriberInterface
{
    /**
     * Database connection which used for each database operation in this class.
     * Injected over the class constructor
     *
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    /**
     * @var Bootstrap
     */
    private $bootstrap = null;
    
    function __construct(Bootstrap $bootstrap)
    {
        $this->bootstrap = $bootstrap;
        $this->db = \Shopware()->Db();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
//            Inject js for backend
            'Enlight_Controller_Action_PostDispatch_Backend_Index' => 'onIndexEvent',
            'Enlight_Controller_Action_PostDispatch_Backend_Article' => 'onArticleEvent',

//            Fetch attributes data
            'Shopware_Modules_Articles_GetArticleById_FilterSQL' => 'modifyQuery',
            'Shopware_Modules_Articles_sGetArticlesByCategory_FilterSql' => 'modifyQuery',
            'Shopware_Modules_Articles_GetPromotionById_FilterSql' => 'modifyQuery',
        ];
     }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     * @return mixed
     */
    public function modifyQuery(\Enlight_Controller_ActionEventArgs $args)
    {
        $args->setReturn(str_replace('attr20,', "attr20, oss_additional, \n", $args->getReturn()));
        return $args->getReturn();
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     * @return bool
     */
    public function onIndexEvent($args)
    {
        $action = $args->getSubject();
        $request = $action->Request();
        $response = $action->Response();
        $view = $action->View();

        if (!$request->isDispatched()
            || $response->isException()
            || $request->getActionName() != 'index'
            || !$view->hasTemplate()
        ) {
            return;
        }

        // Add template directory
        $args->getSubject()->View()->addTemplateDir(
            $this->bootstrap->Path() . 'Views/'
        );
        $view->extendsTemplate('backend/index/oss_article_injection/header.tpl');

        return true;
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     * @return bool
     */
    public function onArticleEvent(\Enlight_Controller_ActionEventArgs $args)
    {
        $request = $args->getRequest();
        $view = $args->getSubject()->View();

        if ($request->getActionName() == 'load') {
            // Add template directory
            $view->addTemplateDir(
                $this->bootstrap->Path() . 'Views/'
            );

            $view->extendsTemplate(
                'backend/article/model/oss_article_injection/attribute.js'
            );

            $view->extendsTemplate(
                'backend/article/controller/detail/oss_article_injection/base.js'
            );

            $view->extendsTemplate(
                'backend/article/view/detail/oss_article_injection/window.js'
            );
        }

        return true;
    }
}