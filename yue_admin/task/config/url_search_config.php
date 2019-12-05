<?php 

//短连接的标记,分了几大属性 如商品属性gd_,gt_,go_  商家属性ss_ 订单属性dd_ 公共属性gg_(商品,商家,订单的公共)

//url的参数系统化，如商品搜索index.php?goods_params='...' 会用goods_params接商品搜索参数 
//商家会用seller_params 订单会用order_params 保证&test = 1 这样再加参数,不会被影响

return array(
    'goods'=>array( //商品属性 前缀是go_
        'gd'=>'detail', //放进全文的参数detail 前缀是gd_
        'gt'=>'third',  //放进全文的第三级参数  前缀是gt_
    ),
    'seller'=>array( //商家属性  前缀是ss_
        '1'=>'t_teacher', //老师类型
        '2'=>'t_experience', //培训经验
        '3'=>'m_sex', //性别
        '4'=>'m_height',//身高
        '5'=>'m_cups',//cup 30 32
        '6'=>'m_cup', //cup A B
        '7'=>'p_experience', //摄影从业年限
        '8'=>'p_goodat', //擅长类型
        '9'=>'p_team', //摄影团队
        '10'=>'p_order_income', //每月交易额
        '11'=>'hz_order_way',
        '12'=>'hz_team',
        '13'=>'hz_place',
        '14'=>'hz_experience',
        '15'=>'hz_goodat',
        '16'=>'yp_area',
        '17'=>'yp_background',
        '18'=>'yp_can_photo',
        '19'=>'yp_lighter',
        '20'=>'yp_other_equitment',
        '21'=>'ms_experience',
        '22'=>'ms_forwarding',
        '23'=>'ms_certification',
        '24'=>'ot_label',
    ),
    'order'=>array( //订单属性  //dd_
        
    ),
    'common'=>array( //公共属性  前缀是gg_
        '|'=>'&', //参数的连接符
        ':'=>'=', //url参数的=
        '1'=>'type_id',//分类id
        '2'=>'status',//状态
        '3'=>'is_show',//是否上架
        '4'=>'location_id',//区域
        '5'=>'is_black',//是否黑名单
        '6'=>'add_time_s',//添加时间起
        '7'=>'add_time_e',//添加时间止
        '8'=>'begin_time',//开始时间起
        '9'=>'end_time',//开始时间止
        '10'=>'audit_time_s',//审核时间起
        '11'=>'audit_time_e',//审核时间止
        '12'=>'keywords',//关键字
        '13'=>'user_id',//用户id
        '14'=>'prices_list',//针对多价钱的价格列表
        '15'=>'orderby',//排序
        '16'=>'store_id',//店铺id
        '17'=>'price_s',//针对单价钱的起
        '18'=>'price_e',//针对单价钱的止
        '19'=>'total_times_s',//交易次数起
        '20'=>'total_times_e',//交易次数止
        '21'=>'total_money_s',//总金额起
        '22'=>'total_money_e',//总金额止
        '23'=>'rating',//商家评级
        '24'=>'basic_type',//商家类型
        '25'=>'list_s',//上架个数起
        '26'=>'list_e',//上架个数止
        '27'=>'operator_id',//操作id
    ),


);




