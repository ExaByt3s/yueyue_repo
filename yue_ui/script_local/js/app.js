requirejs.config({
    //By default load any module IDs from js/lib
    baseUrl: 'js',
    //except, if the module ID starts with "app",
    //load it from the js/app directory. paths
    //config is relative to the baseUrl, and
    //never includes a ".js" extension since
    //the paths config could be for a directory.
    paths: {
        widget1: 'defines/widget1',
        widget2: 'defines/widget2',
        jquery : 'lib/jquery-1.10.2.min'
    }
});