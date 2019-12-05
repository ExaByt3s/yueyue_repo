
define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var model = require('../model');
    var utility = require('../../common/utility');
    var mine_edit_view = require('./view');

    page_control.add_page([function(){
        return{
           title:'文字设置',
            route:{
                'mine/edit/:type' : 'mine/edit'
            },
            dom_not_cache: true,
            ignore_exist: true,
            transition_type : 'slide',
            page_init: function(appView, routeParams, EditObj) {
                var self = this;
                var mineModel = utility.user;
                var pageNavTitle = '';
                var nickname = '';
                var signature = '';
                var isNickname;

                switch (EditObj.type) {
                    case 'edit-nickname':
                        pageNavTitle = '编辑昵称';
                        nickname = EditObj.nickname || mineModel.get('nickname');
                        isNickname = true;
                        break;
                    case 'edit-phone':
                        pageNavTitle = '绑定手机号';
                        nickname = EditObj.phone || mineModel.get('phone');
                        isNickname = true;
                        break;
                    case 'edit-signature':
                        pageNavTitle = '编辑简介';
                        signature = EditObj.signature || mineModel.get('signature');
                        isNickname = false;
                        break;
                }

                self.edit_view = new mine_edit_view({
                    model: mineModel,
                    templateModel: {
                        pageNavTitle: pageNavTitle,
                        nickname: nickname,
                        signature: signature,
                        isNickname: isNickname
                    },
                    parentNode: appView.$el
                }).setEditObj(EditObj).render();
            },


            page_show:function(){
                //必需在page_show时设置焦点才有效
                this.edit_view._inputFocus();
            },


            page_before_remove: function() {
                this.edit_view.destroy();
            }

        }
    }]);
})