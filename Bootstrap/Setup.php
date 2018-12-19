<?php

namespace ShopwarePlugins\OssArticleInjection\Bootstrap;

use Shopware\Bundle\AttributeBundle\Service\TypeMapping;

class Setup
{
    /**
     * @var \Shopware_Plugins_Backend_OssArticleInjection_Bootstrap
     */
    protected $bootstrap = null;

    function __construct(\Shopware_Plugins_Backend_OssArticleInjection_Bootstrap $bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    /**
     * Run the install steps
     */
    public function install()
    {
        $this->createAttributes();
        $this->bootstrap->subscribeEvent( 'Enlight_Controller_Front_StartDispatch', 'onStartFrontDispatch' );
        $this->bootstrap->registerController('Backend', 'OssArticleInjection');
    }

    /**
     * create additional attributes in s_user_attributes and re-generate attribute models
     */
    private function createAttributes()
    {
        $regeneration = false;

        if( !$this->checkIfColumnExist('s_articles_attributes','oss_additional') ) {
            $regeneration = true;
            if (!empty(Shopware()->Container()->get('shopware_attribute.crud_service'))) {
                $service = Shopware()->Container()->get('shopware_attribute.crud_service');
                $service->update('s_articles_attributes', 'oss_additional', TypeMapping::TYPE_STRING);
            } else {
                Shopware()->Models()->addAttribute(
                    's_articles_attributes',
                    'oss',
                    'additional',
                    TypeMapping::TYPE_STRING,
                    true,
                    null
                );
            }
        }

        if($regeneration)
            $this->getEntityManager()->generateAttributeModels([
                's_articles_attributes'
            ]);
    }

    /**
     * @param $tableName
     * @param $columnName
     * @return bool
     */
    private function checkIfColumnExist($tableName, $columnName)
    {
        $sql = 'SHOW COLUMNS FROM ' . $tableName;
        $columns = \Shopware()->Db()->fetchAll($sql);

        foreach ($columns as $column) {
            if ($column['Field'] == $columnName){
                return true;
            }
        }
        return false;
    }

    /**
     * @return \Shopware\Components\Model\ModelManager
     */
    protected function getEntityManager()
    {
        return Shopware()->Models();
    }
}
