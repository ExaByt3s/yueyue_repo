Handlebars.registerHelper("compare",function(r,e,n,t){if(arguments.length<3)throw new Error('Handlerbars Helper "compare" needs 2 parameters');var o={"==":function(r,e){return r==e},"===":function(r,e){return r===e},"!=":function(r,e){return r!=e},"!==":function(r,e){return r!==e},"<":function(r,e){return e>r},">":function(r,e){return r>e},"<=":function(r,e){return e>=r},">=":function(r,e){return r>=e},"typeof":function(r,e){return typeof r==e}};if(!o[e])throw new Error('Handlerbars Helper "compare" doesn\'t know the operator '+e);var u=o[e](r,n);return u?t.fn(this):t.inverse(this)}),Handlebars.registerHelper("if_equal",function(r,e,n){return r==e?n.fn(this):n.inverse(this)});