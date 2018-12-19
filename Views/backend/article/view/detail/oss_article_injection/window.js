//{block name="backend/article/view/detail/window" append}
Ext.define('Shopware.apps.Article.view.detail.oss_article_injection.Window', {
    override: 'Shopware.apps.Article.view.detail.Window',

    /**
     * Creates the description field set for the main form panel.
     * Contains the keywords, short and long description.
     * @return Ext.form.FieldSet
     */
    createBaseTab: function() {
        var me = this,
            detailContainer = me.callParent(arguments);

        me.detailForm.insert(4,
            Ext.create('Ext.form.FieldSet', {
                layout: 'anchor',
                cls: Ext.baseCSSPrefix + 'article-description-field-set',
                defaults: {
                    labelWidth: 155,
                    anchor: '100%',
                    translatable: true,
                    xtype: 'textarea'
                },
                title: '{s name=ossTitle}Article Injection{/s}',
                collapsible: true,
                collapsed: false,
                items: [
                    Ext.create('Ext.form.FieldSet', {
                        layout: 'anchor',
                        cls: Ext.baseCSSPrefix + 'article-description-field-set',
                        title: '{s name=ossTitle/shortcodes}Shortcodes Helper{/s}',
                        collapsible: true,
                        collapsed: true,
                        items: [
                            {
                                xtype: 'container',
                                cls: "ossArticleAlert is--rounded warning",
                                html: '<div class="ossArticleAlert--icon"><i class="icon--element icon--warning"></i></div><div class="ossArticleAlert--content">[warning text="Warning message on red background color." icon="warning" color="warning" /warning]</div>'
                            },
                            {
                                xtype: 'container',
                                cls: "ossArticleAlert is--rounded info",
                                html: '<div class="ossArticleAlert--icon"><i class="icon--element icon--info"></i></div><div class="ossArticleAlert--content">[warning text="Info message on blue background color." icon="info" color="info" /warning]</div>'
                            }
                        ]
                    }),
                    {
                        xtype: 'textfield',
                        fieldLabel: '{s name=ossTitle/title}Article Injection Title{/s}',
                        translatable: false,
                        name: '__attribute_oss_additional'
                    }
                ]
            })
        );

        return detailContainer;
    },

    onStoresLoaded: function() {
        var me = this;

        me.callParent(arguments);
        console.log(me.article);

        Ext.Ajax.request({
            url: '{url controller=AttributeData action=loadData}',
            params: {
                _foreignKey: me.article.get('mainDetailId'),
                _table: 's_articles_attributes'
            },
            success: function(responseData, request) {
                var response = Ext.JSON.decode(responseData.responseText);

                me.detailForm.loadRecord(response);
            }
        });
    }

});
//{/block}