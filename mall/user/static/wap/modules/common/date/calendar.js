define('common/date/calendar', function(require, exports, module){ /**
 *
 */
(function (factory) {
    var global = typeof window != 'undefined' ? window : this;

    if (typeof module === "object" && typeof module.exports === "object") {
        module.exports = global.document ? factory(global) : function(win) {
            if (!win.document)throw new Error("document is a undefined");
            return factory(win);
        };
    } else {
        factory(global);
    }

}(function (window) {

    var mNow = 0,       // ��ǰ����·�
        yNow = 0,       // ��ǰ������
        silde = false;  // �����б����ڻ���
    
    var oCalenWrap = create('div', {"class" : 'calen-wrap'}),           // ��󸸼�
        oCalenMask = create('div', {"class" : 'calen-mask'}),           // �ҿ�����
        oCalen = create('div', {"class" : 'calen'}),                    // ����box
        calendarList = create('div', {"class" : 'calen-list'}),         // �����б�

        past,           // ��ȥ��ʱ���Ƿ��ѡ
        calenTitles,    // �꣬�±���
        aMonths,        // ����ѡ��������·�
        aYears,         // ����ѡ����������
        yearTitle,      // ��ǰ�����
        monthTitle,     // ��ǰ�±���
        prevYearBtn,    // ��һ��
        nextYearBtn,    // ��һ��
        prevMonthBtn,   // �ϸ���
        nextMonthBtn,   // �¸���
        selectYearBox,  // ���ѡ��
        selectMonthBox; // �·�ѡ��

    function Calendar(){
        var oDate       = new Date();
        this.hours      = false;
        this.hoursPast  = false;
        this.focusObj   = null;
        this.shield     = '[]';
        this.startDate  = '';
        this.startJSON  = {};
        this.fixDate    = {y : oDate.getFullYear(), m : oDate.getMonth() + 1, d : 0};

        // ��ʼ��ʼ��
        this.init();
    }

    // ��ʼ��
    Calendar.prototype.init = function(){
        var _this = this;

        var aCalendars = getObj(document, '.calendars');
        if(!aCalendars.length)return;

        document.body.appendChild(oCalenWrap);
        oCalenWrap.appendChild(oCalenMask);
        oCalenWrap.appendChild(oCalen);
        calenTitle = getObj(oCalen, '.calen-title');


        // ����ͷ��
        this.createHeader(function(){

            // �������ڱ���ͷ
            _this.createWeek();

            oCalen.appendChild(calendarList);
            
            // �����л�������
            sildeSwitch(calendarList, function(obj, dir){
                dir > 0 ? mNow-- : mNow++;

                _this.startJSON.prev.m = mNow - 1;
                _this.startJSON.now.m = mNow;
                _this.startJSON.next.m = mNow + 1;
                _this.transitions(obj, dir);
            })

            // ��ʾ/���� ��/�� ��ѡ��
            for(var i = 0 ; i < calenTitles.length ; i++){

                calenTitles[i].onclick = function(){

                    if(toolClass(this, 'calen-month-txt', 'has')){

                        // ��ʾ��������
                        toolClass(selectMonthBox, 'active', (selectMonthBox.show ? 'remove' : 'add'));

                        // ͬʱ�������·�
                        if(selectYearBox.show){
                            toolClass(selectYearBox, 'active', 'remove');
                            selectYearBox.show = false;
                        }

                        // ���õ�ǰ�·ݸ���
                        for(var x = 0 ; x < aMonths.length ; x++){
                            if(attr(aMonths[x], 'data-value') == attr(this, 'data-value')){
                                toolClass(aMonths[x], 'active');
                            } else {
                                toolClass(aMonths[x], 'active', 'remove');
                            }
                        }

                        selectMonthBox.show = !selectMonthBox.show;
                    }
                    else if(toolClass(this, 'calen-year-txt', 'has')){

                        toolClass(selectYearBox, 'active', (selectYearBox.show ? 'remove' : 'add'));

                        if(selectMonthBox.show){
                            toolClass(selectMonthBox, 'active', 'remove');
                            selectMonthBox.show = false;
                        }

                        // ���õ�ǰ��ݸ���
                        for(var x = 0 ; x < aYears.length ; x++){

                            if(attr(aYears[x], 'data-value') == attr(this, 'data-value')){
                                toolClass(aYears[x], 'active');
                            } else {
                                toolClass(aYears[x], 'active', 'remove');
                            }
                        }

                        selectYearBox.show = !selectYearBox.show;
                    }
                }
            }
        });

        // ��
        this.createDate({}, function(months){

            for(var i = 0 ; i < aMonths.length ; i++){

                months[i].onclick = function(){
                    for(var x = 0 ; x < months.length ; x++)toolClass(months[x], 'active', 'remove');

                    mNow += attr(this, 'data-value') - attr(monthTitle, 'data-value');
                    _this.selectDate(this, selectMonthBox, "m", mNow);
                }
            }
        });

        // ��ʾ����
        for(var i = 0 ; i < aCalendars.length ; i++){

            attr(aCalendars[i], 'readonly', 'true');
            aCalendars[i].onfocus = function(){
                if(attr(this, 'disabled') != null)return;

                var start = Number(attr(this, 'start')) || 1915,
                    end = Number(attr(this, 'end')) || 2020;

                past = !(attr(this, 'past') == null);
                _this.hours = !(attr(this, 'hours') == null);
                _this.hoursPast = !(attr(this, 'hours-past') == null);

                _this.shield = getDate(attr(this, 'shield') || '');
                _this.startDate = getDate(attr(this, 'start-date') || '');
                var prev,now,next, oDate = new Date();

                if(_this.startDate instanceof Array && _this.startDate.length){
                    var startDate = _this.startDate[0];

                    yNow = startDate.y - oDate.getFullYear();
                    mNow = startDate.m - (oDate.getMonth() + 1);

                    for(var a in startDate)_this.fixDate[a] = startDate[a];

                    prev = { y : yNow, m : mNow - 1, d : startDate.d };
                    now  = { y : yNow, m : mNow, d : startDate.d };
                    next = { y : yNow, m : mNow + 1, d : startDate.d };

                    _this.startJSON = {"prev" : prev, "now" : now, "next" : next};
                }
                else {
                    _this.fixDate.y = oDate.getFullYear();
                    _this.fixDate.m = oDate.getMonth() + 1;
                    _this.fixDate.d = 0;
                }

                if(_this.focusObj != this){

                    if(!_this.startDate instanceof Array || !_this.startDate){
                        mNow = 0 ;
                        yNow = 0;

                        _this.startJSON.prev = { y : yNow, m : mNow - 1 };
                        _this.startJSON.now = { y : yNow, m : mNow };
                        _this.startJSON.next = { y : yNow, m : mNow + 1 };
                    }
                    
                    // �������������б�
                    _this.appendList(_this.startJSON, function(){
                        _this.addEvent();
                    });
                    
                    // ��
                    _this.createDate({"start" : start, "end" : end, "type" : 'year'}, function(years){
                        for(var k = 0 ; k < years.length ; k++){

                            years[k].onclick = function(){
                                for(var x = 0 ; x < years.length ; x++)toolClass(years[x], 'active', 'remove');

                                yNow += attr(this, 'data-value') - attr(yearTitle, 'data-value');
                                _this.selectDate(this, selectYearBox, "y", yNow);
                            }
                        }

                        sildeSwitch(selectYearBox, function(obj, dir){
                            selectYearBox.index = selectYearBox.index || 0;
                            var count = selectYearBox.children.length;

                            if(dir > 0){
                                selectYearBox.index++;
                                if(selectYearBox.index >= 0)selectYearBox.index = 0;
                            } else {
                                selectYearBox.index--;
                                if(selectYearBox.index <= -count)selectYearBox.index = -(count - 1);
                            }

                            var val = 'translateX('+ (selectYearBox.index * (100 / count)) +'%)';

                            selectYearBox.style.WebkitTransform = val;
                            selectYearBox.style.transform = val;

                            setTimeout(function(){
                                silde = false;
                            }, 500);
                        })
                    });
                }

                toolClass(oCalenWrap, 'active');
                _this.focusObj = this;
            }
        }
        oCalen.onclick = function(ev){
            var oEv = ev.targetTouches ? ev.targetTouches[0] : (ev || event);
            oEv.cancelBubble = true;
        }
        
        oCalenMask.onclick = hideCalen;
    }

    /**
     * ���������б�
     * @return {[type]}        [description]
     */
    Calendar.prototype.createCalenList = function(data, setTitle){
        var oList = document.createElement('div'),
            created = 0,
            _this = this;

        data = data || {};
        data.m = data.m || 0;
        data.y = data.y || 0;
        var date = new Date();

        //
        var date = new Date(),
            tDay = date.getDate();

            date.setFullYear(date.getFullYear() + data.y, (date.getMonth() + data.m + 1), 1, 0, 0, 0);
            date.setDate(0);

        var dSun = date.getDate();

            date.setDate(1);
        var dWeek = date.getDay();

        var date = new Date();
            date.setFullYear(date.getFullYear() + data.y, date.getMonth() + data.m, 1, 0, 0, 0);

        // ��ȡ��ǰ����
        var tMonth = date.getMonth() + 1,
            tYear = date.getFullYear();

        // ������һ���µ����һ��
            date.setDate(0);

        var lastDay = date.getDate(), lastMonths = [];
        for(var i = lastDay ; i > 0 ; i--)lastMonths.push(i);

        // ���ñ���
        if(setTitle){
            yearTitle.innerHTML = tYear;
            monthTitle.innerHTML = (tMonth < 10 ? '0' + tMonth : tMonth);
            attr(yearTitle, 'data-value', tYear);
            attr(monthTitle, 'data-value', tMonth - 1);
        }

        // ��������β����
        var lastMonthDay = dWeek + 7;
            lastMonthDay = lastMonthDay >= 10 ? lastMonthDay - 7 : lastMonthDay;

        for(var i = 0 ; i < lastMonthDay ; i++){

            var oSpan = create('span'),
                oNum = create('a', {
                    "data-calen" : (tYear + '/' + (tMonth - 1) + '/' + lastMonths[i]),
                    "class" : 'prev-m prev-to-month pasted',
                    "href" : 'javascript:;'
                }, lastMonths[i]);

            if(lastMonths[i] == tDay && data.m == 1 && !data.y && !data.d || !data.y && Number(_this.fixDate.m) + 1 == tMonth && _this.fixDate.d == lastMonths[i]){
                toolClass(oNum, 'today');
            }

            // ���ý�������
            if(setShiled(tYear, tMonth - 1, lastMonths[i]))toolClass(oNum, 'pasted shield');

            oSpan.appendChild(oNum);

            if(oList.children.length){
                oList.insertBefore(oSpan, oList.children[0]);
            } else {
                oList.appendChild(oSpan);
            }

            created++;
        }

        // �⵱ǰ�µ������б�
        for(var i = 0 ; i < dSun ; i++){
            created++;

            var n = i + 1,
                oSpan = create('span'),
                oNum = create('a', {
                    "data-calen" : (tYear + '/' + tMonth + '/' + n),
                    "href" : 'javascript:;'
                }, n),
                oDate = new Date();

            switch(created % 7){
                case 0: case 1: oNum.className = 'weekend'; break;
            }

            if(!data.m && !data.y || !data.y && _this.fixDate.m == tMonth){
                if((_this.fixDate.d == n && _this.fixDate.m == tMonth) || (!_this.fixDate.d && n == tDay)){

                    oNum.className = oNum.className + ' today';
                }
                else if((past || _this.hoursPast) && n < tDay){
                    oNum.className = oNum.className + ' expire pasted';
                }
            }
            else if((past || _this.hoursPast) && data.m < 0 && data.y <= 0){
                oNum.className = ' expire pasted';
            }

            // �����Ƿ�С���û�����Ŀ�ʼ����
            if(tYear <= _this.fixDate.y && tMonth <= _this.fixDate.m && n < data.d || tYear <= _this.fixDate.y && tMonth < _this.fixDate.m){
                if(_this.startDate)toolClass(oNum, 'expire pasted');
            }

            // ���ý�������
            if(setShiled(tYear, tMonth, n))toolClass(oNum, 'pasted shield');

            oSpan.appendChild(oNum);
            oList.appendChild(oSpan);
        }

        // ��������β����
        var nextMonths = 42 - oList.children.length;

        for(var i = 0 ; i < nextMonths ; i++){
            var n = i + 1,
                oSpan = create('span'),
                oNum = create('a', {
                    "data-calen" : (tYear + '/' + (tMonth + 1) + '/' + n),
                    "class" : 'next-m next-to-month',
                    "href" : 'javascript:;'
                    }, n);

            if(n == tDay && data.m == -1 && !data.y && !data.d || !data.y && _this.fixDate.m - 1 == tMonth && _this.fixDate.d == n){
                toolClass(oNum, 'today');
            }

            // ���ý�������
            if(setShiled(tYear, tMonth + 1, n))toolClass(oNum, 'pasted shield');

            oSpan.appendChild(oNum);
            oList.appendChild(oSpan);
        }

        // ���ý�������
        function setShiled(iyear, imonth, idate){
            if(!_this.shield)return false;

            for(var k = 0 ; k < _this.shield.length ; k++){
                _this.shield[k].y = _this.shield[k].y || data.getFullYear();
                _this.shield[k].m = _this.shield[k].m || data.getMonth() + 1;
                _this.shield[k].d = _this.shield[k].d || data.getDate();

                if(iyear == _this.shield[k].y && imonth == _this.shield[k].m && idate == _this.shield[k].d)return true;
            }
            return false;
        }

        return oList;
    }

    /**
     * ��������
     * @param  {[type]} data.start  [��ʼ����]
     * @param  {[type]} data.end    [��������]
     * @param  {[type]} data.type   ["year"/"month"(Ĭ��)]
     * @param  {[type]} cb          [description]
     * @return {[type]}             [description]
     */
    Calendar.prototype.createDate = function(data, cb){
        data = data || {};
        data.start = data.start || 1;
        data.end = data.end || 12;
        data.type = data.type || 'month';

        var oDateList = create('div', {
            "class" : (data.type == 'month' ? 'calen-months' : 'calen-years')
        });

        var oList = create('div'),
            arr = [],
            count = 0,
            len = 0,
            now = 0,
            nowY = (new Date()).getFullYear();

        for(var i = data.start ; i <= data.end ; i++){

            var oSpan = create('span'),
                oNum = create('a', {
                    "data-value" : (data.type == 'year' ? i : i - 1),
                    "href" : 'javascript:;'
                }, (i < 10 ? '0' + i : i));

            arr.push(oNum);

            if(data.type == 'year'){

                if(count >= 12){
                    oDateList.appendChild(oList);
                    oList = create('div');
                    count = 0;
                    len++;
                }

                if(i == nowY)now = len;

                oSpan.appendChild(oNum);
                oList.appendChild(oSpan);
            }
            else {
                oSpan.appendChild(oNum);
                oDateList.appendChild(oSpan);
            }
            count++;
        };

        if(data.type == 'year'){
            if(selectYearBox && oCalen)oCalen.removeChild(selectYearBox);
            oDateList.appendChild(oList);
            selectYearBox = oDateList;
            aYears = arr;

            if(count)len++;

            oDateList.style.width = (len * 100) + '%';

            for(var i = 0 ; i < len ; i++){
                oDateList.children[i].style.width = 100 / len + '%';
            }

            // ���õ�ǰ��ʾ��ҳ
            oDateList.style.WebkitTransform = 'translateX(-'+ (now * (100 / len)) +'%)';
            oDateList.style.transform = 'translateX(-'+ (now * (100 / len)) +'%)';
            selectYearBox.index = -now;
        }
        else {
            if(selectMonthBox && oCalen)oCalen.removeChild(selectMonthBox);
            selectMonthBox = oDateList;
            aMonths = arr;
        }
        oCalen.appendChild(oDateList);

        cb && cb(arr);
    }

    /**
     * ����ʱ��
     * @return {[type]} [description]
     */
    Calendar.prototype.createTime = function(obj, date, today, past){
        var oTime = getObj(oCalen, '.calen-time'),
            child = [],
            oDate = new Date(),
            day = oDate.getDate(),
            hours = oDate.getHours(),
            _this = this;

        if(!oTime.length){
            oTime = create('div', {"class" : 'calen-time'});

            for(var i = 0 ; i < 24 ; i++){

                var time = i < 10 ? '0' + i : i ;
                    time += ':00';

                var oSpan = create('span'),
                    oNum = create('a', {"href" : 'javascript:;', "data-time" : time}, time);

                oSpan.appendChild(oNum);
                oTime.appendChild(oSpan);
                child.push({"obj" : oNum, "time" : parseInt(time, 10)});
            }
        }
        else {
            oTime = oTime[0];
            var arr = getObj(oTime, 'a');

            for(var i = 0 ; i < arr.length ; i++){
                child.push({"obj" : arr[i], "time" : parseInt(attr(arr[i], 'data-time'), 10)});
            }
        }

        toolClass(oTime, 'active');

        for(var i = 0 ; i < child.length ; i++){

            if(_this.hoursPast && ((mNow < 0 && yNow <= 0) || (today == day &&  child[i].time <= hours) || (mNow <= 0 && yNow <= 0 && today < day))){
                toolClass(child[i].obj, 'expire pasted');
                child[i].obj.active = false;
            } else {
                toolClass(child[i].obj, 'expire pasted', 'remove');
                child[i].obj.active = true;
            }

            (function(time){
                child[i].obj.onclick = function(){

                    // ��������ʱ��
                    if(this.active){
                        var val = date + ' ' + (time < 10 ? '0' + time : time) + ':00';

                        if(obj.value != null){
                            obj.value = val;
                        } else if(obj.innerHTML != null) {
                            obj.innerHTML = val;
                        }
                        hideCalen();
                        _this.changes();
                    }
                    toolClass(oTime, 'active', 'remove');
                }
            })(child[i].time);
        }

        oCalen.appendChild(oTime);
    }

    /**
     * ����ͷ��
     * @return {[type]}      [description]
     */
    Calendar.prototype.createHeader = function(cb){
        calenTitles = calenTitles || [];

        var _this = this;
        var header = create('div', {"class" : 'calen-header'});

        var year = create('div', {"class" : 'calen-year'}),
            prevYear = create('a', {"class" : 'float-l year-prev switch-btn', "href" : 'javascript:;'}, '&lt;'),
            nextYear = create('a', {"class" : 'float-r year-next switch-btn', "href" : 'javascript:;'}, '&gt;'),
            calenYearTxt = create('a', {"class" : 'calen-year-txt calen-title', "href" : 'javascript:;'});

        year.appendChild(prevYear);
        year.appendChild(calenYearTxt);
        year.appendChild(nextYear);

        var month = create('div', {"class" : 'calen-month'}),
            prevMonth = create('a', {"class" : 'float-l month-prev switch-btn', "href" : 'javascript:;'}, '&lt;'),
            nextMonth = create('a', {"class" : 'float-r month-next switch-btn', "href" : 'javascript:;'}, '&gt;'),
            calenMonthTxt = create('a', {"class" : 'calen-month-txt calen-title', "href" : 'javascript:;'});


        month.appendChild(prevMonth);
        month.appendChild(calenMonthTxt);
        month.appendChild(nextMonth);

        header.appendChild(year);
        header.appendChild(month);

        calenTitles.push(calenYearTxt, calenMonthTxt);

        monthTitle = calenMonthTxt;
        yearTitle = calenYearTxt;

        // ��ť�л�������/��
        prevMonth.onclick = function(){ _this.switchDate(-1); }
        nextMonth.onclick = function(){ _this.switchDate(1); }
        prevYear.onclick = function(){ _this.switchDate(-1, 'year'); }
        nextYear.onclick = function(){ _this.switchDate(1, 'year'); }

        if(oCalen.children.length){
            oCalen.insertBefore(header, oCalen.children[0]);
        } else {
            oCalen.appendChild(header);
        }

        for(var i = 0 ; i < header.children.length ; i++){
            header.children[i].ontouchstart = function(){
                toolClass(this, 'active');
            }
            header.children[i].ontouchend = function(){
                toolClass(this, 'active', 'remove');
            }
        }

        cb && cb();
    }

    /**
     * ����ͷ��
     * @return {[type]}      [description]
     */
    Calendar.prototype.createWeek = function(){
         var week = create('div', {"class" : 'calen-week'}),
             weeks = '��һ����������';

         for(var i = 0 ; i < 7 ; i++){
            var n = i + 1, data = {};
            if(n % 7 == 1 || n % 7 == 0)data["class"] = 'weekend';

             week.appendChild(create('span', data, weeks.charAt(i)));
         }
         oCalen.appendChild(week);
    }

    /**
     *
     * ������������
     * @param  {Function} cb [description]
     * @return {[type]}      [description]''
     */
    Calendar.prototype.appendList = function(data, cb){
        data = data || {};
        data.prev = data.prev || {m : mNow - 1, y : yNow};
        data.now = data.now || {m : mNow, y : yNow};
        data.next = data.next || {m : mNow + 1, y : yNow};

        calendarList.innerHTML = '';

        calendarList.appendChild(this.createCalenList(data.prev));
        calendarList.appendChild(this.createCalenList(data.now, true));
        calendarList.appendChild(this.createCalenList(data.next));

        cb && cb();
    }

    /**
     * ���������¼�
     */
    Calendar.prototype.addEvent = function(){
        var _this = this;
        var aCalenSet = calendarList.getElementsByTagName('a');

        for(var i = 0 ; i < aCalenSet.length ; i++){
            aCalenSet[i].onclick = function(){

                if(toolClass(this, 'prev-to-month', 'has')){
                    _this.switchDate(-1);
                }
                else if(toolClass(this, 'next-to-month', 'has')){
                    _this.switchDate(1);
                }
                else if(!toolClass(this, 'pasted', 'has') && !toolClass(this, 'shield', 'has')){

                    var date = attr(this, 'data-calen'), today = this.innerHTML;
                        date = format(date, (attr(_this.focusObj, 'format') || false));

                    if(_this.hours){
                        _this.createTime(_this.focusObj, date, today, past);
                    }
                    else {
                        if(_this.focusObj && typeof _this.focusObj.value == 'undefined'){
                            _this.focusObj.innerHTML = date;
                        }
                        else if(_this.focusObj) {
                            var type = typeof _this.focusObj.value;
                            if(type === 'string' || type === 'number'){
                                if(_this.focusObj.oldValue != date){

                                    _this.focusObj.value = date;
                                    _this.focusObj.oldValue = date;

                                    _this.changes();
                                }
                            }
                        }
                        hideCalen();
                    }
                }
            }
        }
    }

    /**
     * �л�������
     * @param  {[type]} dir  [description]
     * @param  {[type]} type [description]
     * @return {[type]}      [description]
     */
    Calendar.prototype.switchDate = function(dir, type){
        var _this = this;
        type = type || 'month';

        switch(type){
            case 'month':
                dir > 0 ? mNow++ : mNow-- ;

                _this.startJSON.prev.m = mNow - 1;
                _this.startJSON.now.m = mNow;
                _this.startJSON.next.m = mNow + 1;

                _this.transitions(calendarList, dir > 0 ? -1 :1);
                break;
            case 'year':
                _this.appendList({
                    prev : {
                        m : mNow,
                        y : yNow - 1
                    },
                    next : {
                        m : mNow,
                        y : yNow + 1
                    }
                }, function(){
                    dir > 0 ? yNow++ : yNow-- ;
                    _this.startJSON.prev.y = yNow;
                    _this.startJSON.now.y = yNow;
                    _this.startJSON.next.y = yNow;
                    _this.transitions(calendarList, dir > 0 ? -1 : 1);
                });
                break;
        }
    }

    /**
     * �л��·ݶ���
     * @param  {[type]} obj [description]
     * @param  {[type]} dir [�ϸ��»����¸���]
     */
    Calendar.prototype.transitions = function(obj, dir){
        var _this = this;

        if(dir > 0){
            toolClass(obj, 'silde prev-to');
        }
        else {
            toolClass(obj, 'silde next-to');
        }

        setTimeout(function(){
            end();
        }, 500)

        function end(){
            _this.appendList(_this.startJSON, function(){
                toolClass(obj, 'silde prev-to next-to', 'remove');
                _this.addEvent();
                silde = false;
            })
        }
    }

    /**/
    Calendar.prototype.selectDate = function(obj, obj2, attr, val){
        var _this = this;

        this.startJSON.prev[attr] = (attr == 'm' ? val - 1 : val);
        this.startJSON.now[attr] = val;
        this.startJSON.next[attr] = (attr == 'm' ? val + 1 : val);

        this.appendList(this.startJSON, function(){
            _this.addEvent();
        });

        toolClass(obj, 'active');
        toolClass(obj2, 'active', 'remove');

        selectYearBox.show = false;
        selectMonthBox.show = false;
    }

    /**/
    Calendar.prototype.changes = function(){
        var jQuery = (window.jQuery || window.$) || null;

        if(jQuery){
            if(jQuery(this.focusObj) && jQuery(this.focusObj).change){
                jQuery(this.focusObj).change();
            }
        } else {

            this.focusObj.onchange && this.focusObj.onchange();
        }
    }

    /**
     * �����л�����
     * @param  {[type]} ev [description]
     * @return {[type]}    [description]
     */
    function sildeSwitch(obj, callBack){
        obj.ontouchstart = start;
        obj.onmousedown = start;

        function start(ev){
            var oEv = ev.targetTouches ? ev.targetTouches[0] : (ev || event);
            var disX = oEv.pageX;
            var needW = parseInt(document.documentElement.clientWidth / 5, 10);
            var dir;

            var _this = this;

            function move(ev){
                var oEv = ev.targetTouches ? ev.targetTouches[0] : (ev || event);
                dir = oEv.pageX - disX;
                if(silde)return false;

                if(Math.abs(dir) >= needW){
                    silde = true;

                    callBack && callBack(_this, dir);
                }

                oEv.preventDefault && oEv.preventDefault();
                return false;
            }

            function end(ev){
                this.ontouchmove && (this.ontouchmove = null);
                this.ontouchend && (this.ontouchend = null);
                this.onmousemove && (this.onmousemove = null);
                this.onmouseup && (this.onmouseup = null);
            }

            // �ƶ�
            this.ontouchmove = move;
            this.onmousemove = move;

            // ����
            this.ontouchend = end;
            this.onmouseup = end;
        }
    }

    /**
     * ����/���/ɾ�� className
     * @param  {[type]} obj    [description]
     * @param  {[type]} sClass [��Ҫ�����class]
     * @param  {[type]} type   ['add:���'(Ĭ��), 'remove:ɾ��', 'has:����']
     */
    function toolClass(obj, sClass, type){
        if(!sClass)return;

        var nowClass = obj.className.replace(/\s+/g, ' ');
            nowClass = nowClass.split(' ');

            sClass = sClass.replace('^\s+|\s+$').replace(/\s+/, ' ').split(' ');
            type = type || 'add';

        for(var i = 0 ; i < nowClass.length ; i++){
            switch(type){
                case 'has': if(sClass[0] == nowClass[i])return true; break;
                case 'add':
                case 'remove': 
                    for(var x = 0 ; x < sClass.length ; x++){
                        if(sClass[x] == nowClass[i])nowClass.splice(i, 1);
                    }
                break;
            }
        }

        if(type == 'add')nowClass = nowClass.concat(sClass);

        obj.className = nowClass.join(' ');
    }

    /**
     * ��ȡԪ��
     * @param  {[type]} parent [description]
     * @param  {[type]} str    [type]
     */
    function getObj(parent, str){
        var type = str.charAt(0), result;
        switch(type){
            case '#': result = parent.getElementById(str.substring(1)); break;
            case '.': result = parent.getElementsByClassName(str.substring(1)); break;
            default: result = parent.getElementsByTagName(str); break;
        }

        return result;
    }

    /**
     * ����Ԫ��
     * @param  {[type]} tagname [��ǩ����]
     * @param  {[type]} attr    [����(���)]
     * @param  {[type]} html    [����]
     */
    function create(tagname, attr, html){
        if(!tagname)return;

        attr = attr || {};
        html = html || '';

        var tag = document.createElement(tagname);

        for(var i in attr){
            tag.setAttribute(i, attr[i]);
        }

        tag.innerHTML = html;
        return tag;
    }
    
    /**
     * ��������
     */
    function hideCalen(){
        toolClass(oCalenWrap, 'close');
        setTimeout(function(){
            toolClass(oCalenWrap, 'active close', 'remove');
        }, 290);
    }
    
    /**
     * �����ĸ�ʽ
     * @param  {[type]} str  [description]
     * @param  {[type]} fmat [description]
     * @return {[type]}      [description]
     */
    function format(str, fmat){
        if(!str)return false;
        str = str.split('/');
        fmat = fmat || 'y/m/d';
        
        var n = fmat.charAt(0), count = 0;
        
        for(var i = 0 ; i < fmat.length ; i++){
            if(n.charAt(count) != fmat.charAt(i)){
                n += fmat.charAt(i);
                count++;
            }
        }        
        
        var data = {"y" : str[0], "m" : str[1], "d" : str[2]}, symbol = '', result = '';
        
        if(/\//g.test(n)){
            symbol = '/';
        } else if(/\-/g.test(n)) {
            symbol = '-';
        }
        
        n = n.split(symbol);
        
        for(var i = 0 ; i < n.length ; i++){
            result += data[n[i]];
            if(i < n.length - 1)result += symbol;
        }
        
        return result;
    }

    /**
     * / �ַ�����ȡ������
     * @param  {[type]} str [description]
     * @param  {[type]} one [description]
     */
    function getDate(str, one){
        str = str.replace(/[\'\s]+/g, '');
        if(!str)return;

        str = str.match(/(\d+[\/\-]\d+[\/\-]\d+)/g);

        var data = [];

        for(var i = 0 ; i < str.length ; i++){
            var arr = str[i].match(/\d+/g), result = {};

            if(arr.length == 3){
                result["m"] = arr[1];

                if(arr[0].length == 4){
                    result["y"] = arr[0];
                    result["d"] = arr[2];
                } else {
                    result["d"] = arr[0];
                    result["y"] = arr[2];
                }
            }
            else if(arr.length == 2) {
                if(arr[0].length == 4){
                    result["y"] = arr[0];
                    result["m"] = arr[1];
                }
                else if(arr[0].length <= 2){
                    result["m"] = arr[0];
                    result["d"] = arr[1];
                }
            }
            data.push(result);
        }

        return data;
    }

    /**
     * ������������
     */
    function attr(obj, attr, val){
        if(!obj)return null;
        switch(arguments.length){
            case 3: obj.setAttribute(attr, val); break;
            case 2: return obj.getAttribute(attr); break;
        }
    }

    window.addEventListener('load', function(){
        new Calendar();
    }, false);

    return Calendar;
})); 
});