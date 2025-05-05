<?php
/*
 * @Author: Sodiqjon1998 sirojiddinovsodiqjon1998@gmail.com
 * @Date: 2024-11-11 10:06:40
 * @LastEditors: Sodiqjon1998 sirojiddinovsodiqjon1998@gmail.com
 * @LastEditTime: 2025-04-24 18:19:56
 * @FilePath: \quiz\routes\frontend\route.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */


Route::get('/', function () {
    return view('frontend.site.crm');
});

Route::get('/guest', function () {
    return view('frontend.site.guest');
})->name('guest');
