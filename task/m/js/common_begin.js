
    $('[data-role="common_btn_menu"]').on('click',function()
    {
        $('[data-role="common_menu_open"]').toggleClass("fn-hide");
    })

    $('[data-role="common_menu_open_business"]').on('click',function()
    {
        window.location.href = './list.php';
    })

    $('[data-role="common_menu_open_list"]').on('click',function()
    {
        window.location.href = './process.php';
    })

    $('[data-role="common_menu_open_mine"]').on('click',function()
    {
        window.location.href = './mine.php';
    })

    $('[data-role="common_menu_open_log_out"]').on('click',function()
    {
        $.ajax
        ({
            url: '../ajax/login_out.php',
            type: 'POST',
            cache: false,
            dataType: 'json',
            beforeSend: function () {

            },
            success: function (data) {
                window.location.href = './login.php';

            },
            error: function () {

            },
            complete: function () {

            }
        })
    })

    console.log("common.js")
