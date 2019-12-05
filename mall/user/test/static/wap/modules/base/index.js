define('base/index', function(require, exports, module){ /**
 * Created by hudingwen on 15/8/25.
 * 基础类
 */

// ========= 模块引入 ========= 
var $ = require('components/zepto/zepto.js');
var utility = require('common/utility/index');
var ua = require('common/ua/index');

// ========= Helps ===========

/**
 * 空函数
 * @return {[type]} [description]
 */

function noop(){}
/**
 * 创建对象
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

// ========= Yue基础类 =========
var YB = 
{
	/**
     * 实现类与类之间的继承。
     * @method inherits
     * @grammar Base.inherits( super ) => child
     * @grammar Base.inherits( super, protos ) => child
     * @grammar Base.inherits( super, protos, statics ) => child
     * @param  {Class} super 父类
     * @param  {Object | Function} [protos] 子类或者对象。如果对象中包含constructor，子类将是用此属性值。
     * @param  {Function} [protos.constructor] 子类构造器，不指定的话将创建个临时的直接执行父类构造器的方法。
     * @param  {Object} [statics] 静态属性或方法。
     * @return {Class} 返回子类。
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
     * // 因为没有指定构造器，父类的构造器将会执行。
     * var instance = new Manager();    // => Super
     *
     * // 继承子父类的方法
     * instance.hello();    // => hello
     * instance.world();    // => World
     *
     * // 子类的__super__属性指向父类
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

        // 复制静态方法
        $.extend( true, child, Super, staticProtos || {} );

        /* jshint camelcase: false */

        // 让子类的__super__属性指向父类。
        child.__super__ = Super.prototype;

        // 构建原型，添加原型方法或属性。
        // 暂时用Object.create实现。
        child.prototype = createObject( Super.prototype );
        protos && $.extend( true, child.prototype, protos );

        return child;
    },

    /**
     * 一个不做任何事情的方法。可以用来赋值给默认的callback.
     * @method noop
     */
    noop: noop,
    /**
     * 工具集模块
     * @type {[type]}
     */
    utility : utility,
    /**
     * ua 模块
     * @type {[type]}
     */
    ua : ua
};

module.exports = YB; 
});