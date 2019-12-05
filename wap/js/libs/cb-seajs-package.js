/*! cache: 2014-07-07 10:36:18 */
/**
 * Sea.js 2.3.0 | seajs.org/LICENSE.md
 */
(function(global, undefined) {

// Avoid conflicting when `sea.js` is loaded multiple times
if (global.seajs) {
  return
}

var seajs = global.seajs = {
  // The current version of Sea.js being used
  version: "2.3.0"
}

var data = seajs.data = {}


/**
 * util-lang.js - The minimal language enhancement
 */

function isType(type) {
  return function(obj) {
    return {}.toString.call(obj) == "[object " + type + "]"
  }
}

var isObject = isType("Object")
var isString = isType("String")
var isArray = Array.isArray || isType("Array")
var isFunction = isType("Function")

var _cid = 0
function cid() {
  return _cid++
}


/**
 * util-events.js - The minimal events support
 */

var events = data.events = {}

// Bind event
seajs.on = function(name, callback) {
  var list = events[name] || (events[name] = [])
  list.push(callback)
  return seajs
}

// Remove event. If `callback` is undefined, remove all callbacks for the
// event. If `event` and `callback` are both undefined, remove all callbacks
// for all events
seajs.off = function(name, callback) {
  // Remove *all* events
  if (!(name || callback)) {
    events = data.events = {}
    return seajs
  }

  var list = events[name]
  if (list) {
    if (callback) {
      for (var i = list.length - 1; i >= 0; i--) {
        if (list[i] === callback) {
          list.splice(i, 1)
        }
      }
    }
    else {
      delete events[name]
    }
  }

  return seajs
}

// Emit event, firing all bound callbacks. Callbacks receive the same
// arguments as `emit` does, apart from the event name
var emit = seajs.emit = function(name, data) {
  var list = events[name], fn

  if (list) {
    // Copy callback lists to prevent modification
    list = list.slice()

    // Execute event callbacks, use index because it's the faster.
    for(var i = 0, len = list.length; i < len; i++) {
      list[i](data)
    }
  }

  return seajs
}


/**
 * util-path.js - The utilities for operating path such as id, uri
 */

var DIRNAME_RE = /[^?#]*\//

var DOT_RE = /\/\.\//g
var DOUBLE_DOT_RE = /\/[^/]+\/\.\.\//
var MULTI_SLASH_RE = /([^:/])\/+\//g

// Extract the directory portion of a path
// dirname("a/b/c.js?t=123#xx/zz") ==> "a/b/"
// ref: http://jsperf.com/regex-vs-split/2
function dirname(path) {
  return path.match(DIRNAME_RE)[0]
}

// Canonicalize a path
// realpath("http://test.com/a//./b/../c") ==> "http://test.com/a/c"
function realpath(path) {
  // /a/b/./c/./d ==> /a/b/c/d
  path = path.replace(DOT_RE, "/")

  /*
    @author wh1100717
    a//b/c ==> a/b/c
    a///b/////c ==> a/b/c
    DOUBLE_DOT_RE matches a/b/c//../d path correctly only if replace // with / first
  */
  path = path.replace(MULTI_SLASH_RE, "$1/")

  // a/b/c/../../d  ==>  a/b/../d  ==>  a/d
  while (path.match(DOUBLE_DOT_RE)) {
    path = path.replace(DOUBLE_DOT_RE, "/")
  }

  return path
}

// Normalize an id
// normalize("path/to/a") ==> "path/to/a.js"
// NOTICE: substring is faster than negative slice and RegExp
function normalize(path) {
  var last = path.length - 1
  var lastC = path.charAt(last)

  // If the uri ends with `#`, just return it without '#'
  if (lastC === "#") {
    return path.substring(0, last)
  }

  return (path.substring(last - 2) === ".js" ||
      path.indexOf("?") > 0 ||
      lastC === "/") ? path : path + ".js"
}


var PATHS_RE = /^([^/:]+)(\/.+)$/
var VARS_RE = /{([^{]+)}/g

function parseAlias(id) {
  var alias = data.alias
  return alias && isString(alias[id]) ? alias[id] : id
}

function parsePaths(id) {
  var paths = data.paths
  var m

  if (paths && (m = id.match(PATHS_RE)) && isString(paths[m[1]])) {
    id = paths[m[1]] + m[2]
  }

  return id
}

function parseVars(id) {
  var vars = data.vars

  if (vars && id.indexOf("{") > -1) {
    id = id.replace(VARS_RE, function(m, key) {
      return isString(vars[key]) ? vars[key] : m
    })
  }

  return id
}

function parseMap(uri) {
  var map = data.map
  var ret = uri

  if (map) {
    for (var i = 0, len = map.length; i < len; i++) {
      var rule = map[i]

      ret = isFunction(rule) ?
          (rule(uri) || uri) :
          uri.replace(rule[0], rule[1])

      // Only apply the first matched rule
      if (ret !== uri) break
    }
  }

  return ret
}


var ABSOLUTE_RE = /^\/\/.|:\//
var ROOT_DIR_RE = /^.*?\/\/.*?\//

function addBase(id, refUri) {
  var ret
  var first = id.charAt(0)

  // Absolute
  if (ABSOLUTE_RE.test(id)) {
    ret = id
  }
  // Relative
  else if (first === ".") {
    ret = realpath((refUri ? dirname(refUri) : data.cwd) + id)
  }
  // Root
  else if (first === "/") {
    var m = data.cwd.match(ROOT_DIR_RE)
    ret = m ? m[0] + id.substring(1) : id
  }
  // Top-level
  else {
    ret = data.base + id
  }

  // Add default protocol when uri begins with "//"
  if (ret.indexOf("//") === 0) {
    ret = location.protocol + ret
  }

  return ret
}

function id2Uri(id, refUri) {
  if (!id) return ""

  id = parseAlias(id)
  id = parsePaths(id)
  id = parseVars(id)
  id = normalize(id)

  var uri = addBase(id, refUri)
  uri = parseMap(uri)

  return uri
}


var doc = document
var cwd = (!location.href || location.href.indexOf('about:') === 0) ? '' : dirname(location.href)
var scripts = doc.scripts

// Recommend to add `seajsnode` id for the `sea.js` script element
var loaderScript = doc.getElementById("seajsnode") ||
    scripts[scripts.length - 1]

// When `sea.js` is inline, set loaderDir to current working directory
var loaderDir = dirname(getScriptAbsoluteSrc(loaderScript) || cwd)

function getScriptAbsoluteSrc(node) {
  return node.hasAttribute ? // non-IE6/7
      node.src :
    // see http://msdn.microsoft.com/en-us/library/ms536429(VS.85).aspx
      node.getAttribute("src", 4)
}


// For Developers
seajs.resolve = id2Uri


/**
 * util-request.js - The utilities for requesting script and style files
 * ref: tests/research/load-js-css/test.html
 */

var head = doc.head || doc.getElementsByTagName("head")[0] || doc.documentElement
var baseElement = head.getElementsByTagName("base")[0]

var currentlyAddingScript
var interactiveScript

function request(url, callback, charset) {
  var node = doc.createElement("script")

  if (charset) {
    var cs = isFunction(charset) ? charset(url) : charset
    if (cs) {
      node.charset = cs
    }
  }

  addOnload(node, callback, url)

  node.async = true
  node.src = url

  // For some cache cases in IE 6-8, the script executes IMMEDIATELY after
  // the end of the insert execution, so use `currentlyAddingScript` to
  // hold current node, for deriving url in `define` call
  currentlyAddingScript = node

  // ref: #185 & http://dev.jquery.com/ticket/2709
  baseElement ?
      head.insertBefore(node, baseElement) :
      head.appendChild(node)

  currentlyAddingScript = null
}

function addOnload(node, callback, url) {
  var supportOnload = "onload" in node

  if (supportOnload) {
    node.onload = onload
    node.onerror = function() {
      emit("error", { uri: url, node: node })
      onload()
    }
  }
  else {
    node.onreadystatechange = function() {
      if (/loaded|complete/.test(node.readyState)) {
        onload()
      }
    }
  }

  function onload() {
    // Ensure only run once and handle memory leak in IE
    node.onload = node.onerror = node.onreadystatechange = null

    // Remove the script to reduce memory leak
    if (!data.debug) {
      head.removeChild(node)
    }

    // Dereference the node
    node = null

    callback()
  }
}

function getCurrentScript() {
  if (currentlyAddingScript) {
    return currentlyAddingScript
  }

  // For IE6-9 browsers, the script onload event may not fire right
  // after the script is evaluated. Kris Zyp found that it
  // could query the script nodes and the one that is in "interactive"
  // mode indicates the current script
  // ref: http://goo.gl/JHfFW
  if (interactiveScript && interactiveScript.readyState === "interactive") {
    return interactiveScript
  }

  var scripts = head.getElementsByTagName("script")

  for (var i = scripts.length - 1; i >= 0; i--) {
    var script = scripts[i]
    if (script.readyState === "interactive") {
      interactiveScript = script
      return interactiveScript
    }
  }
}


// For Developers
seajs.request = request


/**
 * util-deps.js - The parser for dependencies
 * ref: tests/research/parse-dependencies/test.html
 */

var REQUIRE_RE = /"(?:\\"|[^"])*"|'(?:\\'|[^'])*'|\/\*[\S\s]*?\*\/|\/(?:\\\/|[^\/\r\n])+\/(?=[^\/])|\/\/.*|\.\s*require|(?:^|[^$])\brequire\s*\(\s*(["'])(.+?)\1\s*\)/g
var SLASH_RE = /\\\\/g

function parseDependencies(code) {
  var ret = []

  code.replace(SLASH_RE, "")
      .replace(REQUIRE_RE, function(m, m1, m2) {
        if (m2) {
          ret.push(m2)
        }
      })

  return ret
}


/**
 * module.js - The core of module loader
 */

var cachedMods = seajs.cache = {}
var anonymousMeta

var fetchingList = {}
var fetchedList = {}
var callbackList = {}

var STATUS = Module.STATUS = {
  // 1 - The `module.uri` is being fetched
  FETCHING: 1,
  // 2 - The meta data has been saved to cachedMods
  SAVED: 2,
  // 3 - The `module.dependencies` are being loaded
  LOADING: 3,
  // 4 - The module are ready to execute
  LOADED: 4,
  // 5 - The module is being executed
  EXECUTING: 5,
  // 6 - The `module.exports` is available
  EXECUTED: 6
}


function Module(uri, deps) {
  this.uri = uri
  this.dependencies = deps || []
  this.exports = null
  this.status = 0

  // Who depends on me
  this._waitings = {}

  // The number of unloaded dependencies
  this._remain = 0
}

// Resolve module.dependencies
Module.prototype.resolve = function() {
  var mod = this
  var ids = mod.dependencies
  var uris = []

  for (var i = 0, len = ids.length; i < len; i++) {
    uris[i] = Module.resolve(ids[i], mod.uri)
  }
  return uris
}

// Load module.dependencies and fire onload when all done
Module.prototype.load = function() {
  var mod = this

  // If the module is being loaded, just wait it onload call
  if (mod.status >= STATUS.LOADING) {
    return
  }

  mod.status = STATUS.LOADING

  // Emit `load` event for plugins such as combo plugin
  var uris = mod.resolve()
  emit("load", uris)

  var len = mod._remain = uris.length
  var m

  // Initialize modules and register waitings
  for (var i = 0; i < len; i++) {
    m = Module.get(uris[i])

    if (m.status < STATUS.LOADED) {
      // Maybe duplicate: When module has dupliate dependency, it should be it's count, not 1
      m._waitings[mod.uri] = (m._waitings[mod.uri] || 0) + 1
    }
    else {
      mod._remain--
    }
  }

  if (mod._remain === 0) {
    mod.onload()
    return
  }

  // Begin parallel loading
  var requestCache = {}

  for (i = 0; i < len; i++) {
    m = cachedMods[uris[i]]

    if (m.status < STATUS.FETCHING) {
      m.fetch(requestCache)
    }
    else if (m.status === STATUS.SAVED) {
      m.load()
    }
  }

  // Send all requests at last to avoid cache bug in IE6-9. Issues#808
  for (var requestUri in requestCache) {
    if (requestCache.hasOwnProperty(requestUri)) {
      requestCache[requestUri]()
    }
  }
}

// Call this method when module is loaded
Module.prototype.onload = function() {
  var mod = this
  mod.status = STATUS.LOADED

  if (mod.callback) {
    mod.callback()
  }

  // Notify waiting modules to fire onload
  var waitings = mod._waitings
  var uri, m

  for (uri in waitings) {
    if (waitings.hasOwnProperty(uri)) {
      m = cachedMods[uri]
      m._remain -= waitings[uri]
      if (m._remain === 0) {
        m.onload()
      }
    }
  }

  // Reduce memory taken
  delete mod._waitings
  delete mod._remain
}

// Fetch a module
Module.prototype.fetch = function(requestCache) {
  var mod = this
  var uri = mod.uri

  mod.status = STATUS.FETCHING

  // Emit `fetch` event for plugins such as combo plugin
  var emitData = { uri: uri }
  emit("fetch", emitData)
  var requestUri = emitData.requestUri || uri

  // Empty uri or a non-CMD module
  if (!requestUri || fetchedList[requestUri]) {
    mod.load()
    return
  }

  if (fetchingList[requestUri]) {
    callbackList[requestUri].push(mod)
    return
  }

  fetchingList[requestUri] = true
  callbackList[requestUri] = [mod]

  // Emit `request` event for plugins such as text plugin
  emit("request", emitData = {
    uri: uri,
    requestUri: requestUri,
    onRequest: onRequest,
    charset: data.charset
  })

  if (!emitData.requested) {
    requestCache ?
        requestCache[emitData.requestUri] = sendRequest :
        sendRequest()
  }

  function sendRequest() {
    seajs.request(emitData.requestUri, emitData.onRequest, emitData.charset)
  }

  function onRequest() {
    delete fetchingList[requestUri]
    fetchedList[requestUri] = true

    // Save meta data of anonymous module
    if (anonymousMeta) {
      Module.save(uri, anonymousMeta)
      anonymousMeta = null
    }

    // Call callbacks
    var m, mods = callbackList[requestUri]
    delete callbackList[requestUri]
    while ((m = mods.shift())) m.load()
  }
}

// Execute a module
Module.prototype.exec = function () {
  var mod = this

  // When module is executed, DO NOT execute it again. When module
  // is being executed, just return `module.exports` too, for avoiding
  // circularly calling
  if (mod.status >= STATUS.EXECUTING) {
    return mod.exports
  }

  mod.status = STATUS.EXECUTING

  // Create require
  var uri = mod.uri

  function require(id) {
    return Module.get(require.resolve(id)).exec()
  }

  require.resolve = function(id) {
    return Module.resolve(id, uri)
  }

  require.async = function(ids, callback) {
    Module.use(ids, callback, uri + "_async_" + cid())
    return require
  }

  // Exec factory
  var factory = mod.factory

  var exports = isFunction(factory) ?
      factory(require, mod.exports = {}, mod) :
      factory

  if (exports === undefined) {
    exports = mod.exports
  }

  // Reduce memory leak
  delete mod.factory

  mod.exports = exports
  mod.status = STATUS.EXECUTED

  // Emit `exec` event
  emit("exec", mod)

  return exports
}

// Resolve id to uri
Module.resolve = function(id, refUri) {
  // Emit `resolve` event for plugins such as text plugin
  var emitData = { id: id, refUri: refUri }
  emit("resolve", emitData)

  return emitData.uri || seajs.resolve(emitData.id, refUri)
}

// Define a module
Module.define = function (id, deps, factory) {
  var argsLen = arguments.length

  // define(factory)
  if (argsLen === 1) {
    factory = id
    id = undefined
  }
  else if (argsLen === 2) {
    factory = deps

    // define(deps, factory)
    if (isArray(id)) {
      deps = id
      id = undefined
    }
    // define(id, factory)
    else {
      deps = undefined
    }
  }

  // Parse dependencies according to the module factory code
  if (!isArray(deps) && isFunction(factory)) {
    deps = parseDependencies(factory.toString())
  }

  var meta = {
    id: id,
    uri: Module.resolve(id),
    deps: deps,
    factory: factory
  }

  // Try to derive uri in IE6-9 for anonymous modules
  if (!meta.uri && doc.attachEvent) {
    var script = getCurrentScript()

    if (script) {
      meta.uri = script.src
    }

    // NOTE: If the id-deriving methods above is failed, then falls back
    // to use onload event to get the uri
  }

  // Emit `define` event, used in nocache plugin, seajs node version etc
  emit("define", meta)

  meta.uri ? Module.save(meta.uri, meta) :
      // Save information for "saving" work in the script onload event
      anonymousMeta = meta
}

// Save meta data to cachedMods
Module.save = function(uri, meta) {
  var mod = Module.get(uri)

  // Do NOT override already saved modules
  if (mod.status < STATUS.SAVED) {
    mod.id = meta.id || uri
    mod.dependencies = meta.deps || []
    mod.factory = meta.factory
    mod.status = STATUS.SAVED

    emit("save", mod)
  }
}

// Get an existed module or create a new one
Module.get = function(uri, deps) {
  return cachedMods[uri] || (cachedMods[uri] = new Module(uri, deps))
}

// Use function is equal to load a anonymous module
Module.use = function (ids, callback, uri) {
  var mod = Module.get(uri, isArray(ids) ? ids : [ids])

  mod.callback = function() {
    var exports = []
    var uris = mod.resolve()

    for (var i = 0, len = uris.length; i < len; i++) {
      exports[i] = cachedMods[uris[i]].exec()
    }

    if (callback) {
      callback.apply(global, exports)
    }

    delete mod.callback
  }

  mod.load()
}


// Public API

seajs.use = function(ids, callback) {
  Module.use(ids, callback, data.cwd + "_use_" + cid())
  return seajs
}

Module.define.cmd = {}
global.define = Module.define


// For Developers

seajs.Module = Module
data.fetchedList = fetchedList
data.cid = cid

seajs.require = function(id) {
  var mod = Module.get(Module.resolve(id))
  if (mod.status < STATUS.EXECUTING) {
    mod.onload()
    mod.exec()
  }
  return mod.exports
}


/**
 * config.js - The configuration for the loader
 */

// The root path to use for id2uri parsing
data.base = loaderDir

// The loader directory
data.dir = loaderDir

// The current working directory
data.cwd = cwd

// The charset for requesting files
data.charset = "utf-8"

// data.alias - An object containing shorthands of module id
// data.paths - An object containing path shorthands in module id
// data.vars - The {xxx} variables in module id
// data.map - An array containing rules to map module uri
// data.debug - Debug mode. The default value is false

seajs.config = function(configData) {

  for (var key in configData) {
    var curr = configData[key]
    var prev = data[key]

    // Merge object config such as alias, vars
    if (prev && isObject(prev)) {
      for (var k in curr) {
        prev[k] = curr[k]
      }
    }
    else {
      // Concat array config such as map
      if (isArray(prev)) {
        curr = prev.concat(curr)
      }
      // Make sure that `data.base` is an absolute path
      else if (key === "base") {
        // Make sure end with "/"
        if (curr.slice(-1) !== "/") {
          curr += "/"
        }
        curr = addBase(curr)
      }

      // Set config
      data[key] = curr
    }
  }

  emit("config", configData)
  return seajs
}

})(this);

!function(){function a(a){h[a.name]=a}function b(a){return a&&h.hasOwnProperty(a)}function c(a){for(var c in h)if(b(c)){var d=","+h[c].ext.join(",")+",";if(d.indexOf(","+a+",")>-1)return c}}function d(a,b){var c=g.ActiveXObject?new g.ActiveXObject("Microsoft.XMLHTTP"):new g.XMLHttpRequest;return c.open("GET",a,!0),c.onreadystatechange=function(){if(4===c.readyState){if(c.status>399&&c.status<600)throw new Error("Could not load: "+a+", status = "+c.status);b(c.responseText)}},c.send(null)}function e(a){a&&/\S/.test(a)&&(g.execScript||function(a){(g.eval||eval).call(g,a)})(a)}function f(a){return a.replace(/(["\\])/g,"\\$1").replace(/[\f]/g,"\\f").replace(/[\b]/g,"\\b").replace(/[\n]/g,"\\n").replace(/[\t]/g,"\\t").replace(/[\r]/g,"\\r").replace(/[\u2028]/g,"\\u2028").replace(/[\u2029]/g,"\\u2029")}var g=window,h={},i={};a({name:"text",ext:[".tpl",".html"],exec:function(a,b){e('define("'+a+'#", [], "'+f(b)+'")')}}),a({name:"json",ext:[".json"],exec:function(a,b){e('define("'+a+'#", [], '+b+")")}}),a({name:"handlebars",ext:[".handlebars"],exec:function(a,b){var c=['define("'+a+'#", ["handlebars"], function(require, exports, module) {','  var source = "'+f(b)+'"','  var Handlebars = require("handlebars")',"  module.exports = function(data, options) {","    options || (options = {})","    options.helpers || (options.helpers = {})","    for (var key in Handlebars.helpers) {","      options.helpers[key] = options.helpers[key] || Handlebars.helpers[key]","    }","    return Handlebars.compile(source)(data, options)","  }","})"].join("\n");e(c)}}),seajs.on("resolve",function(a){var d=a.id;if(!d)return"";var e,f;(f=d.match(/^(\w+)!(.+)$/))&&b(f[1])?(e=f[1],d=f[2]):(f=d.match(/[^?]+(\.\w+)(?:\?|#|$)/))&&(e=c(f[1])),e&&-1===d.indexOf("#")&&(d+="#");var g=seajs.resolve(d,a.refUri);e&&(i[g]=e),a.uri=g}),seajs.on("request",function(a){var b=i[a.uri];b&&(d(a.requestUri,function(c){h[b].exec(a.uri,c),a.onRequest()}),a.requested=!0)}),define("seajs/plugins/seajs-text/1.0.2/seajs-text",[],{})}();
!function(){var a,b=/\W/g,c=document,d=document.getElementsByTagName("head")[0]||document.documentElement;seajs.importStyle=function(e,f){if(!f||(f=f.replace(b,"-"),!c.getElementById(f))){var g;if(!a||f?(g=c.createElement("style"),f&&(g.id=f),d.appendChild(g)):g=a,g.styleSheet){if(c.getElementsByTagName("style").length>31)throw new Error("Exceed the maximal count of style tags in IE");g.styleSheet.cssText+=e}else g.appendChild(c.createTextNode(e));f||(a=g)}},define("seajs/plugins/seajs-style/1.0.2/seajs-style",[],{})}();
!function(){var a=seajs.data;seajs.log=function(b,c){window.console&&(c||a.debug)&&console[c||(c="log")]&&console[c](b)},define("seajs/plugins/seajs-log/1.0.1/seajs-log",[],{})}();
!function(){function h(a){return"[object String]"=={}.toString.call(a)}function i(){try{return e in b&&b[e]}catch(a){return!1}}function m(a){return function(){var b=Array.prototype.slice.call(arguments,0);b.unshift(g),j.appendChild(g),g.addBehavior("#default#userData"),g.load(e);var c=a.apply(d,b);return j.removeChild(g),c}}function o(a){return a.replace(n,"___")}function p(a){for(var b=" \n\r    \f\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000",c=0,d=a.length;d>c;c++)if(-1===b.indexOf(a.charAt(c))){a=a.substring(c);break}for(c=a.length-1;c>=0;c--)if(-1===b.indexOf(a.charAt(c))){a=a.substring(0,c+1);break}return-1===b.indexOf(a.charAt(0))?a:""}function A(){this.element=null,this._rendered=!1,this.children=["header","meta"]}function C(a){var b=new RegExp("^(http|https|ftp)://([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&amp;%$-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]).(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0).(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0).(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9-]+.)*[a-zA-Z0-9-]+.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(/($|[a-zA-Z0-9.,?'\\+&amp;%$#=~_-]+))*$");return b.test(a)}function D(a,b,c,d){a&&(a.addEventListener?a.addEventListener(b,c,!!d):a.attachEvent&&a.attachEvent("on"+b,c))}function p(a){for(var b=" \n\r  \f\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000",c=0,d=a.length;d>c;c++)if(-1===b.indexOf(a.charAt(c))){a=a.substring(c);break}for(c=a.length-1;c>=0;c--)if(-1===b.indexOf(a.charAt(c))){a=a.substring(0,c+1);break}return-1===b.indexOf(a.charAt(0))?a:""}function F(b,c){v++,a.body?(a.body.appendChild(b),c&&c()):u>v&&setTimeout(function(){F(b,c)},200)}function G(a,b){if(null==a)return-1;var c=0,d=a.length,e=Array.prototype.indexOf;if(e&&a.indexOf===e)return a.indexOf(b);for(;d>c;c++)if(a[c]===b)return c;return-1}seajs.importStyle("@font-face {    font-family: 'fontello';    src: url('http://www.poco.cn/js_common/seajs/plugins/seajs-debug/1.1.1/fontello.eot');    src: url('http://www.poco.cn/js_common/seajs/plugins/seajs-debug/1.1.1/fontello.eot#iefix') format('embedded-opentype'), /* IE6-IE8 */ url('http://www.poco.cn/js_common/seajs/plugins/seajs-debug/1.1.1/fontello.woff') format('woff'),  /* chrome 6+\u3001firefox 3.6+\u3001Safari5.1+\u3001Opera 11+ */ url('http://www.poco.cn/js_common/seajs/plugins/seajs-debug/1.1.1/fontello.ttf') format('truetype'), /* chrome\u3001firefox\u3001opera\u3001Safari, Android, iOS 4.2+ */ url('http://www.poco.cn/js_common/seajs/plugins/seajs-debug/1.1.1/fontello.svg') format('svg'); /* iOS 4.1- */    font-weight: normal;    font-style: normal;}#seajs-debug-console #seajs-debug-status button,#seajs-debug-console #seajs-debug-meta button,#seajs-debug-console #seajs-debug-map button {    font-family: 'fontello';}#seajs-debug-console, #seajs-debug-console * {    margin: 0;    padding: 0;    border: none;    font: 14px/1.2 Arial}#seajs-debug-console {    position: fixed;    width: 520px;    right: 10px;    bottom: 10px;    border: 2px solid #564F8A;    z-index: 2147483647;    background: #fafafa;}#seajs-debug-console a, #seajs-debug-console a:hover, #seajs-debug-console a:active, #seajs-debug-console a:link {    text-decoration: none;}#seajs-debug-console button {    border: none;    background: transparent;    cursor: pointer;    -webkit-user-select: none;    -moz-user-select: none;    -ms-user-select: none;    -o-user-select: none;    user-select: none;}#seajs-debug-console #seajs-debug-header,#seajs-debug-console #seajs-debug-editor,#seajs-debug-console #seajs-debug-map,#seajs-debug-console #seajs-debug-health {    border: none;    border-bottom: 1px solid lightgrey;}#seajs-debug-console #seajs-debug-header {    margin: 0;    padding: 5px 5px 5px 10px;    height: 20px;    line-height: 20px;    font-weight: bold;    font-size: 16px;    background: #564F8A;    color: #cdbfe3;}#seajs-debug-console #seajs-debug-editor,#seajs-debug-console #seajs-debug-map,#seajs-debug-console #seajs-debug-health {    min-height: 100px;    _height: 100px;    background: #FFF;}#seajs-debug-console #seajs-debug-editor,#seajs-debug-console #seajs-debug-map p input {    font-family: Courier, monospace;    color: #666;}#seajs-debug-console #seajs-debug-editor {    display: block;    width: 510px;    padding: 5px;    resize: vertical;}#seajs-debug-console #seajs-debug-map {    padding: 5px 0;}#seajs-debug-console #seajs-debug-map p {    height: 30px;    line-height: 30px;    overflow: hidden;    padding-left: 10px;}#seajs-debug-console #seajs-debug-map p input {    padding-left: 6px;    height: 24px;    line-height: 24px;    border: 1px solid #dcdcdc;    width: 200px;    vertical-align: middle;    *vertical-align: bottom; -webkit-user-select: auto}#seajs-debug-console #seajs-debug-map .seajs-debug-hit input {    border-color: #cdbfe3;    background-color: #F6F0FF;}#seajs-debug-console #seajs-debug-map button {    color: #999;}#seajs-debug-console #seajs-debug-map button,#seajs-debug-console #seajs-debug-meta button {    width: 30px;    height: 30px;    line-height: 30px;    text-align: center;}#seajs-debug-console #seajs-debug-status {    height: 35px;}#seajs-debug-console #seajs-debug-status span {    display: inline-block;    *display: inline;    *zoom: 1;    height: 35px;    line-height: 35px;    padding-left: 8px;    color: #AAA;    vertical-align: middle;}#seajs-debug-console #seajs-debug-status button {    width: 35px;    height: 35px;    line-height: 35px;    color: #999;    border: none;    font-size: 16px;    vertical-align: middle;    _vertical-align: top;}#seajs-debug-console #seajs-debug-status button:hover,#seajs-debug-console #seajs-debug-status button.seajs-debug-status-on:hover {    background-color: #f0f0f0;    color: #000;}#seajs-debug-console #seajs-debug-status button:active,#seajs-debug-console #seajs-debug-status button.seajs-debug-status-on {    color: #563d7c;    text-shadow: 0 0 6px #cdbfe3;    background-color: #f0f0f0;}#seajs-debug-console #seajs-debug-action {    float: right;    margin-top: -31px;    *margin-top: -32px;    margin-right: 2px;}#seajs-debug-console #seajs-debug-action button {    position: relative;    z-index: 2;    width: 60px;    height: 28px;    border-radius: 2px;    text-align: center;    color: #333;    background-color: #fff;    border: 1px solid #ccc;    text-transform: uppercase;    *margin-left: 4px;}#seajs-debug-console #seajs-debug-action button:hover,#seajs-debug-console #seajs-debug-action button:focus,#seajs-debug-console #seajs-debug-action button:hover,#seajs-debug-console #seajs-debug-action button:active {  background-color: #ebebeb;  border-color: #adadad;}#seajs-debug-console #seajs-debug-action button:active {    position: relative;    top: 1px;    -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);    -moz-box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);    box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);}#seajs-debug-console #seajs-debug-meta {    position: absolute;    right: 0;    top: 0;}#seajs-debug-console #seajs-debug-meta button {    background: #3f386d;    color: white;}#seajs-debug-console #seajs-debug-health {    height: 500px;    /* loading */}/*-webkit-animation: spin 2s infinite linear;-moz-animation: spin 2s infinite linear;-o-animation: spin 2s infinite linear;-ms-animation: spin 2s infinite linear;animation: spin 2s infinite linear;*/#seajs-debug-console.seajs-debug-mini {    width: 30px;    height: 30px;    border: none;}/* ie6 */#seajs-debug-console {    _position: absolute;    _top: expression(documentElement.scrollTop+documentElement.clientHeight-this.clientHeight-5);}* html {    _background: url(null) no-repeat fixed;}");var g,a=document,b=window,c=location,d={},e="localStorage",f="__storejs__";if(d.disabled=!1,d.set=function(){},d.get=function(){},d.serialize=function(a){if(h(a))return a;var b=[];for(var c in a){var d=a[c];if(h(d))d=d.replace(/'/g,'"').replace(/\n/g,"\\n").replace(/\r/g,"\\r"),d="'"+d+"'";else if(d.hasOwnProperty("length")){for(var e=[],f=0;f<d.length;f++){var g=p(d[f][0]),i=p(d[f][1]);g.length&&i.length&&e.push('["'+g+'","'+i+'"]')}d="["+e.join(",")+"]"}b.push(""+c+":"+d)}return"{"+b.join(",")+"}"},d.deserialize=function(a){if(!h(a))return void 0;try{return new Function("return "+a)()}catch(b){return void 0}},i())g=b[e],d.set=function(a,b){return void 0===b?d.remove(a):(g.setItem(a,d.serialize(b)),b)},d.get=function(a){return d.deserialize(g.getItem(a))};else if(a.documentElement.addBehavior){var j,k;try{k=new ActiveXObject("htmlfile"),k.open(),k.write('<script>document.w=window</script><iframe src="/favicon.ico"></iframe>'),k.close(),j=k.w.frames[0].document,g=j.createElement("div")}catch(l){g=a.createElement("div"),j=a.body}var n=new RegExp("[!\"#$%&'()*+,/\\\\:;<=>?@[\\]^`{|}~]","g");d.set=m(function(a,b,c){return b=o(b),void 0===c?d.remove(b):(a.setAttribute(b,d.serialize(c)),a.save(e),c)}),d.get=m(function(a,b){return b=o(b),d.deserialize(a.getAttribute(b))})}try{d.set(f,f),d.get(f)!=f&&(d.disabled=!0),d.remove(f)}catch(l){d.disabled=!0}var q={debug:!0,show:!0,source:!1,nocache:!1,combo:!1,log:!1,health:!1,mode:!1,custom:"",mapping:[]},r=d.get("seajs-debug-config");for(var s in r)q[s]=r[s];var t=window,u=100,v=0,w="seajs-debug-",x=w+"status-on",y=w+"mini",z=w+"hit";A.prototype.render=function(b,c){var d=this;if(this._rendered)return c&&c(),void 0;this._rendered=!0;for(var e=0;e<b.length;e++)-1===G(this.children,b[e])&&this.children.push(b[e]);this.element=a.createElement("div"),this.element.id=w+"console",this.element.style.display="none";for(var f="",e=0;e<this.children.length;e++){var g=this.children[e],h=this["_render"+g.charAt(0).toUpperCase()+g.substring(1)];h&&(f+=h.call(d))}this.element.innerHTML=f,F(this.element,function(){for(var b=0;b<d.children.length;b++){var e=d.children[b];d[e+"Element"]=a.getElementById(w+e);var f=d["_bind"+e.charAt(0).toUpperCase()+e.substring(1)];f&&f.call(d)}d.element.style.display="block",d[q.show?"show":"hide"](),c&&c()})},A.prototype._renderHeader=function(){return'<h3 id="'+w+'header" style="display: none;">Sea.js Debug Console</h3>'},A.prototype._renderMap=function(){var a="";q.mapping.push(["","",!0]);for(var b=0;b<q.mapping.length;b++){var c=q.mapping[b];a+='<p><input type="text" placeholder="Input source URI" title="Source URI" value="'+c[0]+'" />'+'<button style="cursor: default;">&#xe80c;</button>'+'<input type="text" placeholder="Input target URI" title="Target URI" value="'+c[1]+'" />'+'<button data-name="add" '+(c[2]?"":'style="display: none;"')+">&#xe804;</button>"+'<button data-name="red" '+(c[2]?'style="display: none;"':"")+">&#xe805;</button>"+"</p>"}return'<div id="'+w+'map" style="display: none;">'+a+"</div>"},A.prototype._renderEditor=function(){return'<textarea id="'+w+'editor" style="display: none;">'+q.custom+"</textarea>"},A.prototype._renderStatus=function(){this.statusInfo=[["source","Switch to min files","Switch to source files","&#xe80b;"],["combo","Enable combo","Disable combo","&#xe801;"],["nocache","Enable cache","Disable cache","&#xe806;"],["log","Hide seajs log","Show seajs log","&#xe809;"],["mode","Switch mapping mode","Switch editor mode","&#xe808;",function(){this.show()}]];for(var a="",b=0;b<this.statusInfo.length;b++){var c=this.statusInfo[b],d=c[0];a+="<button "+(q[d]?'class="'+x+'"':"")+' title="'+c[q[d]?1:2]+'">'+c[3]+"</button>"}return'<div id="'+w+'status" style="display: none;">'+a+"<span></span>"+"</div>"},A.prototype._renderAction=function(){this.actionInfo=[["Save",function(){for(var a=[],b=this.mapElement.getElementsByTagName("input"),c=0;c<b.length;){var d=p(b[c].value),e=p(b[c+1].value);if(d.length&&e.length){if(!C(d))return b[c].focus(),b[c].select(),alert("Invalid URL: "+d),!1;if(!C(e))return b[c+1].focus(),b[c+1].select(),alert("Invalid URL: "+e),!1;a.push([d,e])}c+=2}q.mapping=a;try{return new Function("return "+this.editorElement.value+";")(),q.custom=p(this.editorElement.value),!0}catch(f){return alert("invalid config"),this.editorElement.focus(),!1}},!1],["Exit",function(){return q.debug=!1,!0},!0]];for(var a="",b=0;b<this.actionInfo.length;b++){var c=this.actionInfo[b];a+="<button>"+c[0]+"</button> "}return'<div id="'+w+'action" style="display: none;">'+a+"</div>"},A.prototype._renderMeta=function(){this.metaInfo=[[q.show,"&#xe80a;","Go to help",function(){t.open("https://github.com/seajs/seajs-debug/issues/4","_blank")}],[q.show,"&#xe802;","Minimize console",function(){this.hide()}],[!q.show,"&#xe803;","Maximize console",function(){this.show()}]];for(var a="",b=0;b<this.metaInfo.length;b++){var c=this.metaInfo[b];a+='<button title="'+c[2]+'">'+c[1]+"</button>"}return'<div id="'+w+'meta">'+a+"</div>"},A.prototype._renderHealth=function(){return'<div id="'+w+'health" style="display: none;">'+"</div>"},A.prototype._bindMap=function(){D(this.mapElement,"click",function(b){var c=b.target||b.srcElement;if("button"===c.tagName.toLowerCase()){var d=c.parentNode,e=d.parentNode;if("add"===c.getAttribute("data-name")){var f=a.createElement("p");f.innerHTML=d.innerHTML,e.appendChild(f),f.getElementsByTagName("input")[0].focus(),c.style.display="none",c.nextSibling.style.display="inline-block"}else"red"===c.getAttribute("data-name")&&e.removeChild(d)}})},A.prototype._bindStatus=function(){this.statusTipElement=this.statusElement.getElementsByTagName("span")[0];for(var a=this,b=this.statusElement.getElementsByTagName("button"),c=0;c<b.length;c++)!function(b,c){D(b,"click",function(){var d=a.statusInfo[c],e=!q[d[0]];q[d[0]]=e,a.save(),b.setAttribute("title",d[e?1:2]),a.statusTipElement.innerHTML=d[e?1:2],b.className=e?x:"",d[4]&&d[4].call(a,e)}),D(b,"mouseover",function(){var b=a.statusInfo[c];a.statusTipElement.innerHTML=b[q[b[0]]?1:2]}),D(b,"mouseout",function(){a.statusTipElement.innerHTML=""})}(b[c],c)},A.prototype._bindAction=function(){for(var a=this,b=this.actionElement.getElementsByTagName("button"),d=0;d<b.length;d++)!function(b,d){D(b,"click",function(){var b=a.actionInfo[d];b[1].call(a)&&(a.save(),b[2]?c.replace(c.href.replace("seajs-debug","")):c.reload())})}(b[d],d)},A.prototype._bindMeta=function(){for(var a=this,b=this.metaElement.getElementsByTagName("button"),c=0;c<b.length;c++)!function(b,c){D(b,"click",function(){var b=a.metaInfo[c];b[3]&&b[3].call(a),a.save()})}(b[c],c)},A.prototype._bindHealth=function(){},A.prototype.show=function(){this.element.className="",q.show=!0;for(var a=0;a<this.children.length;a++){var b=this.children[a];-1===G(["meta","health","editor","map"],b)&&this[b+"Element"]&&(this[b+"Element"].style.display="block")}q.health?this.switchTo("health"):q.mode?(this.switchTo("editor"),this.editorElement.focus()):this.switchTo("map");var c=this.metaElement.getElementsByTagName("button");c[0].style.display="inline-block",c[1].style.display="inline-block",c[2].style.display="none"},A.prototype.hide=function(){this.element.className=y,q.show=!1;for(var a=0;a<this.children.length;a++){var b=this.children[a];"meta"!==b&&this[b+"Element"]&&(this[b+"Element"].style.display="none")}var c=this.metaElement.getElementsByTagName("button");c[0].style.display="none",c[1].style.display="none",c[2].style.display="inline-block"},A.prototype.switchTo=function(a){for(var b=["health","editor","map"],c=0;3>c;c++){var d=b[c];this[d+"Element"]&&(this[d+"Element"].style.display=a===d?"block":"none")}},A.prototype.save=function(){d.set("seajs-debug-config",q)},A.prototype.setHitInput=function(a,b){if(this.mapElement){var c=this.mapElement.getElementsByTagName("p")[a];c&&b&&(c.className=z)}},A.prototype.destory=function(a){var b=this["_destory"+a.charAt(0).toUpperCase()+a.substring(1)];b&&b.call(this)};var B=new A;if(B.config=q,c.search.indexOf("seajs-debug")>-1&&(q.debug=!0),seajs.config({debug:q.debug}),q.debug){if(B.render(["map","editor","health","status","action"]),a.title="[Sea.js Debug Mode] - "+a.title,seajs.config({map:[function(a){for(var b=a,c=0;c<q.mapping.length;c++)q.mapping[c][0].length&&q.mapping[c][1]&&(a=a.replace(q.mapping[c][0],q.mapping[c][1]),B.setHitInput(c,a!==b));return q.source&&!/\-debug\.(js|css)+/g.test(a)&&(a=a.replace(/\/(.*)\.(js|css)/g,"/$1-debug.$2")),a}]}),q.nocache){var H=(new Date).getTime();seajs.on("fetch",function(a){if(a.uri){if(/cb(?:-c|).poco.cn\//.test(a.uri)&&!/cb(?:-c|).poco.cn\/assets\//.test(a.uri))return;var b=a.requestUri||a.uri;a.requestUri=(b+(-1===b.indexOf("?")?"?t=":"&t=")+H).slice(0,2e3)}}),seajs.on("define",function(a){a.uri&&(a.uri=a.uri.replace(/[\?&]t*=*\d*$/g,""))})}if(q.combo&&seajs.config({comboExcludes:/.*/}),q.log&&seajs.config({preload:"seajs-log"}),q.health&&seajs.config({preload:"seajs-health"}),q.custom){var r={};try{r=new Function("return "+q.custom)()}catch(l){}seajs.config(r)}}if(!seajs.find){var I=seajs.cache;seajs.find=function(a){var b=[];for(var c in I)if(I.hasOwnProperty(c)&&("string"==typeof a&&c.indexOf(a)>-1||a instanceof RegExp&&a.test(c))){var d=I[c];d.exports&&b.push(d.exports)}return b}}define("seajs/plugins/seajs-debug/1.1.1/seajs-debug",[],{})}();

/**
 * Localcache
 * (c) 2012-2013 dollydeng@qzone
 * Distributed under the MIT license.
 */
define("seajs-localcache", function(require){
    if(!window.localStorage || seajs.data.debug) return

    var module = seajs.Module,
        data = seajs.data,
        fetch = module.prototype.fetch,
        defaultSyntax = ['??',',']
    var remoteManifest = (data.localcache && data.localcache.manifest) || {} 

    var storage = {
        _maxRetry: 1,
        _retry: true,
        get: function(key, parse){
            var val
            try{
                val = localStorage.getItem(key)
            }catch(e){
                return undefined
            }
            if(val){
                return parse? JSON.parse(val):val
            }else{
                return undefined
            }
        },
        set: function(key, val, retry){
            retry = ( typeof retry == 'undefined' ) ? this._retry : retry
            try{
                localStorage.setItem(key, val)
            }catch(e){
                if(retry) {
                    var max = this._maxRetry
                    while(max > 0) {
                        max --
                        this.removeAll()
                        this.set(key, val, false)
                    }
                }
            }
        },
        remove: function(url){
            try{
                localStorage.removeItem(url)
            }catch(e){}
        },
        removeAll: function(){
            /**
             * Default localstorage clean
             * delete localstorage items which are not in latest manifest
             */
            var prefix = (data.localcache && data.localcache.prefix) || /^https?\:/
            for(var i=localStorage.length-1; i>=0; i--) {
                var key = localStorage.key(i)
                if(!prefix.test(key)) continue  //Notice: change the search pattern if not match with your manifest style
                if(!remoteManifest[key]){
                    localStorage.removeItem(key)
                }
            }
        }
    }

    var localManifest = storage.get('manifest',true) || {}

    if(!remoteManifest){
        //failed to fetch latest version and local version is broken.
        return
    }

    /**
     * Check whether the code is complete and clean
     * @param url
     * @param code
     * @return {Boolean}
     */
    var validate = (data.localcache && data.localcache.validate) || function(url, code){
        if(!code || !url) return false
        else return true
    }

    var fetchAjax = function(url, callback){
        var xhr = new window.XMLHttpRequest()
        var timer = setTimeout(function(){
            xhr.abort()
            callback(null)
        }, (data.localcache && data.localcache.timeout) || 30000)
        xhr.open('GET',url,true)
        xhr.onreadystatechange = function(){
            if(xhr.readyState === 4){
                clearTimeout(timer)
                if(xhr.status === 200){
                    callback(xhr.responseText)
                }else{
                    callback(null)
                }
            }
        }
        xhr.send(null)
    }

    /**
     * run code in window environment
     * @param url
     * @param code
     */
    var use = function(url, code){
        if(code && /\S/.test(code)){
            if(/\.css(?:\?|$)/i.test(url)) {
                var doc = document,  
                    node = doc.createElement('style')
                doc.getElementsByTagName("head")[0].appendChild(node)
                if(node.styleSheet) {
                  node.styleSheet.cssText = code
                } else {
                  node.appendChild(doc.createTextNode(code))
                }
            } else {
                try{
                    code += '//@ sourceURL='+ url  //for chrome debug
                    ;(window.execScript || function(data){ window['eval'].call(window,data)})(code)
                }catch(e){
                    return false
                }
            }
        }
        return true
    }

    var isCombo = function(url){
        var sign = (data.comboSyntax && data.comboSyntax[0]) || '??'
        return url.indexOf(sign) >= 0
    }

    var splitComboUrl = function(url){
        var syntax = data.comboSyntax || defaultSyntax
        var arr = url.split(syntax[0])
        if(arr.length != 2) return url
        var host = arr[0]
        var urls = arr[1].split(syntax[1])
        var result = {}
        result.host = host
        result.files = []
        for(var i= 0,len = urls.length;i<len;i++){
            result.files.push(urls[i])
        }
        return result
    }

    /**
     * Warning: rewrite this function to fit your combo file structure
     * Default: split by define(function(){})
     * @param code
     */
    var splitCombo = (data.localcache && data.localcache.splitCombo) || function(code, url, files){
        var arr = code.split('define')
        var result = []
        for(var i= 0,len = arr.length;i<len;i++){
            if(arr[i]){
                result.push('define'+arr[i])
            }
        }
        return result
    }


    var fetchingList = {}
    var onLoad = function(url){
        var mods = fetchingList[url]
        delete fetchingList[url]
        while ((m = mods.shift())) m.load()
    }

    module.prototype.fetch = function(requestCache){
        var mod = this
        seajs.emit('fetch',mod)
        var url = mod.requestUri || mod.uri
        var isComboUrl = isCombo(url)

        if(fetchingList[url]){
            fetchingList[url].push(mod)
            return
        }
        fetchingList[url] = [mod]

        var fallback = function(url){
            delete fetchingList[url]
            fetch.call(mod,requestCache)
        }

        if(!isComboUrl && remoteManifest[url]){
            //in version control
            var cached = storage.get(url)
            var cachedValidated = validate(url, cached)
            if(remoteManifest[url] == localManifest[url] && cachedValidated){
                //cached version is ready to go
                if(!use(url, cached)){
                    fallback(url)
                }else{
                    onLoad(url)
                }
            }else{
                //otherwise, get latest version from network
                fetchAjax(url + '?v='+Math.random().toString(), function(resp){
                    if(resp && validate(url, resp)){
                        if(!use(url, resp)){
                            fallback(url)
                        }else{
                            localManifest[url] = remoteManifest[url]
                            storage.set('manifest', JSON.stringify(localManifest))  //update one by one
                            storage.set(url, resp)
                            onLoad(url)
                        }
                    }else{
                        fallback(url)
                    }
                })
            }
        }else if(isComboUrl){
            //try to find available code cache
            var splited = splitComboUrl(url), needFetchAjax = false
            for(var i= splited.files.length - 1;i>=0;i--){
                var file = splited.host + splited.files[i]
                var cached = storage.get(file)
                var cachedValidated = validate(file, cached)
                if(remoteManifest[file]){
                    needFetchAjax = true
                    if(remoteManifest[file] == localManifest[file] && cachedValidated) {
                      use(file, cached)
                      splited.files.splice(i,1)  //remove from combo
                    }
                }
            }
            if(splited.files.length == 0){
                onLoad(url)  //all cached
                return
            }
            // call fetch directly if all combo files are not under version control
            if(!needFetchAjax) {
                fallback(url)
                return
            }
            var syntax = data.comboSyntax || defaultSyntax,
                comboUrl = splited.host + syntax[0] + splited.files.join(syntax[1])
            fetchAjax(comboUrl + '?v='+Math.random().toString(), function(resp){
                if(!resp){
                    fallback(url)
                    return
                }
                var splitedCode = splitCombo(resp, comboUrl, splited.files)
                if(splited.files.length == splitedCode.length){
                    //ensure they are matched with each other
                    for(var i= 0,len = splited.files.length;i<len;i++){
                        var file = splited.host + splited.files[i]
                        if(!use(file, splitedCode[i])){
                            fallback(url)
                            return
                        }else{
                            localManifest[file] = remoteManifest[file]
                            storage.set(file, splitedCode[i])
                        }
                    }
                    storage.set('manifest', JSON.stringify(localManifest))
                    onLoad(url)
                }else{
                    //filenames and codes not matched, fetched code is broken at somewhere.
                    fallback(url)
                }
            })
        }else{
            //not in version control, use default fetch method
            if(localManifest[url]){
                delete localManifest[url]
                storage.set('manifest', JSON.stringify(localManifest))
                storage.remove(url)
            }
            fallback(url)
        }
    }
})