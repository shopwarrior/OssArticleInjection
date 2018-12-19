//{block name="backend/article/controller/detail" append}
Ext.define('Shopware.apps.Article.controller.detail.oss_article_injection.Base', {
    override: 'Shopware.apps.Article.controller.Detail',

    onSaveArticle: function(win, article, options) {
        var me = this;

        me.callParent([win, article, options]);

        var params = Ext.merge({}, me.getMainWindow().detailForm.getForm().getValues(), {
            _foreignKey: article.get('mainDetailId'),
            _table: 's_articles_attributes'
        });

        Ext.Ajax.request({
            method: 'POST',
            url: '{url controller=OssArticleInjection action=save}',
            params: params,
            success: function(response)
            {
                var operation = Ext.decode(response.responseText);
                if (operation.success)
                {
                    Shopware.Notification.createGrowlMessage(
                        '{s name=ossSuccess}Success{/s}', 'Looks like okay: ' + operation.message
                    );
                }
                else
                {
                    Shopware.Notification.createGrowlMessage(
                        '{s name=ossError}Some error occured{/s}', operation.message
                    );
                }
            }
        });
    }
});
//{/block}