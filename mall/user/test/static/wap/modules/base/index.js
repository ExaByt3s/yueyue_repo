define('base/index', function(require, exports, module){ /**
 * Created by hudingwen on 15/8/25.
 * ������
 */

// ========= ģ������ ========= 
var $ = require('components/zepto/zepto.js');
var utility = require('common/utility/index');
var ua = require('common/ua/index');

// ========= Helps ===========

/**
 * �պ���
 * @return {[type]} [description]
 */

function noop(){}
/**
 * ��������
 * @param  {[type]} proto [description]
 * @return {[type]}       [description]
 */
function create_object( proto ) 
{
    var f;

    if ( Object.create ) 
    {
        return Object.create( proto );
    } 
    else 
    {
        f = function() {};
        f.prototype = proto;
        return new f();
    }
}

// ========= Yue������ =========
var YB = 
{
	/**
     * ʵ��������֮��ļ̳С�
     * @method inherits
     * @grammar Base.inherits( super ) => child
     * @grammar Base.inherits( super, protos ) => child
     * @grammar Base.inherits( super, protos, statics ) => child
     * @param  {Class} super ����
     * @param  {Object | Function} [protos] ������߶�����������а���constructor�����ཫ���ô�����ֵ��
     * @param  {Function} [protos.constructor] ���๹��������ָ���Ļ�����������ʱ��ֱ��ִ�и��๹�����ķ�����
     * @param  {Object} [statics] ��̬���Ի򷽷���
     * @return {Class} �������ࡣ
     * @example
     * function Person() {
     *     console.log( 'Super' );
     * }
     * Person.prototype.hello = function() {
     *     console.log( 'hello' );
     * };
     *
     * var Manager = Base.inherits( Person, {
     *     world: function() {
     *         console.log( 'World' );
     *     }
     * });
     *
     * // ��Ϊû��ָ��������������Ĺ���������ִ�С�
     * var instance = new Manager();    // => Super
     *
     * // �̳��Ӹ���ķ���
     * instance.hello();    // => hello
     * instance.world();    // => World
     *
     * // �����__super__����ָ����
     * console.log( Manager.__super__ === Person );    // => true
     */
    inherits: function( Super, protos, staticProtos ) 
    {
        var child;

        if ( typeof protos === 'function' ) 
        {
            child = protos;
            protos = null;
        } 
        else if ( protos && protos.hasOwnProperty('constructor') ) 
        {
            child = protos.constructor;
        } 
        else 
        {
            child = function() 
            {
                return Super.apply( this, arguments );
            };
        }

        // ���ƾ�̬����
        $.extend( true, child, Super, staticProtos || {} );

        /* jshint camelcase: false */

        // �������__super__����ָ���ࡣ
        child.__super__ = Super.prototype;

        // ����ԭ�ͣ����ԭ�ͷ��������ԡ�
        // ��ʱ��Object.createʵ�֡�
        child.prototype = createObject( Super.prototype );
        protos && $.extend( true, child.prototype, protos );

        return child;
    },

    /**
     * һ�������κ�����ķ���������������ֵ��Ĭ�ϵ�callback.
     * @method noop
     */
    noop: noop,
    /**
     * ���߼�ģ��
     * @type {[type]}
     */
    utility : utility,
    /**
     * ua ģ��
     * @type {[type]}
     */
    ua : ua
};

module.exports = YB; 
});