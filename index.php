<?php /* MIT License

Copyright (c) 2021 Todd Perry

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE. */

include('serverconfig.php');
include($GLOBALS['FATEPATH'] . '/fate.php');

/*To bypass the login and tos page, set this variable true;
if it is set to false, the login and tos pages will appear*/
$loginAsGuestEnable = true;

//set $data[TEMPLATE_PAGE] = 'home' or 'login' by default
fl();
if (web_logged_in()) {
   $data = web_init_data('home');
} else {
  //if no user login before and if $loginAsGuestEnable is enabled
  if($loginAsGuestEnable){
    //login as a guest and land on the search page
    $data = web_init_data('search');


    $data['username'] = 'guest';
    $data['password'] = 'guest';
  //make sure if guest user is in the system - if not create one
    mod_add_guest_user();
    web_login_user($data);
    $data['toscheck'] = true; //pass the termsof service page
    con_tos_action($data);



  }else{
    //go to the login page
    $data = web_init_data('login');
  }
}
util_log('debug', 'web_init_data() done', LLDEBUG);

try {

  con_do_cmd($data);

  $ds = 'con_do_cmd(' . print_r($data, true) . ')';
  util_log('debug', 'finished: ' . $ds, LLDEBUG);

  net_log_user_and_session_info();
  echo util_show_page($data);

  $ds = 'con_show_page(' . print_r($data, true) . ')';
  util_log('debug', 'finished: ' . $ds, LLDEBUG);

} catch (Exception $ex) {

  util_log('Uncaught exception', $ex->getMessage());
  include('error.php');

} //end try

db_make_log_entry($data);
fl(); print_log();
?>
