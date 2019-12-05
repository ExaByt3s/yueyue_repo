define(["widget2","jquery"], function(w2,$) {
        //depend

        var good =
        {
            sex : 'fale',
            tax : 'therr',
            func : function(x)
            {
                var self = this;

                console.log(self.sex + x + "!");
            }
        };

        return good
    }
);