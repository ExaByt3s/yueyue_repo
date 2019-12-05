define('manifest',function(){
    var mod = {
        '/model_date/hot/index.js': '0.0.1',
    }
    var manifest = {}
    for(var key in mod){
        manifest['http://yp.yueus.com/mobile/js'+key] = mod[key];
    }
    return manifest;
})