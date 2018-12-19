<?php

class Shopware_Controllers_Backend_OssArticleInjection extends Shopware_Controllers_Backend_ExtJs {
    public function saveAction() {
        $someValidation = true;

        $articleDetailId = $this->Request()->getParams()['_foreignKey'];
        if( !empty($articleDetailId) && $someValidation) {
            Shopware()->Db()->update(
                's_articles_attributes',
                [
                    'oss_additional' => $this->Request()->getParams()['__attribute_oss_additional'],
                ],
                'articledetailsID=' . $articleDetailId
            );

            $this->View()->assign( [
                'success' => true,
                'message'  =>  $this->Request()->getParams()['__attribute_oss_additional']
            ] );
        } else {
            $this->View()->assign(
                [
                    'success' => false,
                    'message'   =>'Something goes wrong..?'
                ]
            );
        }
    }
}