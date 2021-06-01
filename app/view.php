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

function app_get_tos_page($local_page_msg) {
  $rv = gen_h(2, app_get_page_title() . ' ' . gen_i('Terms of Service'));
  $rv .= '<div class="content">';

  $home_url = gen_url('login', 'silentlogout');
  $home_link = gen_link($home_url, 'Go back to the login page');
  $rv .= gen_p($home_link, 'page_heading');
  $c0 = 'The following langauge was derived from the MIT License';
  $cc = 'Copyright (c) 2021 Todd Perry';
  $c1 = 'Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:';
  $c2 = 'The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.';
  $c3 = 'THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.';
  $tosmsg = 'Please agree to the following terms before proceeding.';
  $rv .= gen_p(gen_i($tosmsg), 'lastline');
  if ($local_page_msg != '') {
    $rv .= gen_p($local_page_msg, 'page_msg');
  }
  $rv .= '<div class="content">';
  $rv .= gen_tos_form();
  $rv .= gen_p(gen_u($c0) . ':');
  $rv .= gen_p($cc);
  $rv .= gen_p($c1);
  $rv .= gen_p($c2);
  $rv .= gen_p($c3);
  $rv .= '</div></div>';
  $rv .= gen_copyright_notice();
  return $rv;
}

function app_get_header_extra($inpage, $add_el = true) {
  $css_class = 'header';
  $rv = PADDING_STR . SPACER_STR . PADDING_STR;
  
  switch ($inpage) {
   case 'login':
    $link_url = gen_url('tos');
    $rv .= gen_link($link_url, 'Reframe', $css_class);
    break;

   case 'home':
   case 'coin':
   case 'store':
    $link_url = gen_url('hall');
    $rv .= gen_link($link_url, 'Hall', $css_class);
    break;

   case 'profile':
   case 'library':
   case 'data':
   case 'search':
   case 'cart':
    $link_url = gen_url('cart');
    $rv .= gen_link($link_url, 'Cart', $css_class);
    break;

   case 'space':
   case 'settings':
   case 'archive':
   case 'action':
   case 'admin':
   case 'export':
   case 'admin':
   case 'stats':
    $link_url = gen_url('space');
    $rv .= gen_link($link_url, $GLOBALS['APPIDENT'], $css_class);
    break;

   case 'tos':
   case 'news':
   case 'about':
   case 'inspace':
    $link_url = gen_url('inspace');
    $rv .= gen_link($link_url, $GLOBALS['APPIDENT'], $css_class);
    break;

   case 'faq':
   case 'art':
   case 'date':
   case 'hall':
    $rv .= gen_link($GLOBALS['FAMEURL'], $GLOBALS['FAMEIDENT'],
                    $css_class, false, true);
    break;

   default:
    util_except('unexpected page: '  . $inpage);
    break;
  }

  if ($add_el) $rv .= "\n";
  return $rv;
}

function app_get_header_links($inpage, $links_arr, $add_el = true) {
  $rv = PADDING_STR;

  $first = true;
  foreach ($links_arr as $link_name => $link_str) {

    if ($first) {
      $first = false;
    } else {
      $rv .= PADDING_STR . '|' . PADDING_STR;
    }

    if ($link_name == $inpage) {
      $rv .= gen_b($link_str);
    } else {
      $link_url = gen_url($link_name);
      $rv .= gen_link($link_url, $link_str, 'header');
    }
  }

  if ($add_el) $rv .= "\n";
  return $rv;
}

function app_get_smart_spacer($inpage, $add_el = true) {
  $rv = PADDING_STR;
  if ($inpage == 'hall' ||
      (!web_logged_in() &&
       $inpage == 'home')) {
    $rv .= gen_b(SPACER_STR);
  } else {
    $rv .= gen_link(gen_url('hall'), gen_b(SPACER_STR));
  }

  if ($add_el) $rv .= "\n";
  return $rv;
}

function app_get_page_title($inpage = '') {
  $rv = $GLOBALS['APPTITLE'];
  if ($inpage != '') {
    $rv .= SPACER_STR . $inpage;
  }
  return $rv;
}

function gen_gem_guess_form($gemdata, $add_el = true) {
  $rv = '';
  $elem_arr = array();
  $elem_arr []= gen_input('submit', 'cmd', 'Guess', $add_el);
  $elem_arr []= gen_txt_input('steptxt', '', GUESS_COLS,
                              '<your guess>', $add_el, true,true);
  $temp_str = PADDING_STR . gen_checkbox('one_line_chk', '', true);
  $temp_str .= ' One-Line Question';
  $elem_arr []= $temp_str;
  $elem_arr []= gen_input('hidden', 'gemid', $gemdata['gemid'], $add_el);
  $rv = gen_form($elem_arr, gen_url('search'));
  return $rv;
}

function gen_gem_quest_form($gemdata, $optional_str = '',
                            $is_one_line = true, $add_el = true) {
  $rv = '';
  $elem_arr = array();
  $elem_arr []= $optional_str;
  if ($is_one_line) {
    //the last parameter puts the autofocus on this text field
    $elem_arr []= gen_txt_input('steptxt', '', ANSWER_COLS,
                                '<your question>', $add_el, true);
  } else {
    $elem_arr []= gen_text_area('steptxt', '', QUESTION_ROWS,
                                ANSWER_COLS, '', $add_el);
  }

  $link_str = 'Click here for more information';
  $help_str = '(' . gen_link(gen_url('library'), $link_str) . ')';

  $button_str = gen_input('submit', 'cmd', 'Ask Question', $add_el);
  $elem_arr []= gen_p($button_str . PADDING_STR . $help_str);
  $elem_arr []= gen_input('hidden', 'gemid', $gemdata['gemid'], $add_el);
  $rv = gen_form($elem_arr, gen_url('search'));
  return $rv;
}

function gen_gem_answer_form($gemdata, $stepvalue, $lastsaved, $add_el = true) {
  $rv = '';
  $elem_arr = array();
  $elem_arr []= gen_text_area('steptxt', $stepvalue, $gemdata['ansrows'],
                              ANSWER_COLS, '<your answer>', $add_el);
  $recrow = gen_input('submit', 'cmd', 'Record Answer', $add_el);

  $recrow .= PADDING_STR . ' (';
  $tempstr = 'Click here to generate a new gem!';
  //the last parameter puts the autofocus on this link
  //so that gems can be created without any mouse clicks
  $recrow .= gen_link(gen_url('search', 'Create'), $tempstr, '', true);
  $recrow .= ')';
  
  $elem_arr []= gen_p($recrow);
  $elem_arr []= gen_input('hidden', 'gemid', $gemdata['gemid'], $add_el);
  $rv = gen_form($elem_arr, gen_url('search'));
  return $rv;
}

function gen_tos_form($add_el = true) {
  $rv = '';
  $elem_arr = array();
  $elem_arr []= gen_input('submit', 'cmd', 'Proceed', $add_el);
  $elem_arr []= gen_input('checkbox', 'toscheck', '1', $add_el);
  $elem_arr []= gen_span('I agree to the following terms:');
  $rv = gen_form($elem_arr, gen_url('profile'));
  return $rv;
}

function gen_search_form($safe_text = '', $safe_custom = '', $istextarea = false,
                         $selcat = '', $add_el = true, $auto_focus = true) {
  $rv = '';
  $elem_arr = array();

  //dynamically populate drop-down options from the book table
    $book_titles = mod_get_loadedBooks_title();
    $option_arr = array(DEFAULT_CATEGORY => 'Random FATE',
        'CLEAR' => 'CLEAR Results',
        'CUSTOM' => 'CUSTOM Category');

  foreach ($book_titles as $key => $title){
      $option_arr[$key] = $title;
  }


  $toggle_url = gen_url('search', TOGGLE_TEXT_CMD);
  if ($istextarea) {
    $inuser = web_get_user();
    $elem_arr []= gen_text_area('stxt', $safe_text, 3, SEARCH_AREA_COLS,
                                SEARCH_PLACEHOLDER, $add_el);
    $elem_arr []= '<br><span class="nextline">';
    $elem_arr []= gen_input('submit', TEMPLATE_CMD, 'Create', $add_el);
    $elem_arr []= gen_select_input('category', $option_arr, $selcat, $add_el);
    $elem_arr []= gen_txt_input('customtxt', $safe_custom, CUSTOM_COLS,
                                CUSTOM_PLACEHOLDER, $add_el);
    $elem_arr []= '</span>';
    $left_col = gen_form($elem_arr, gen_url('search'));
    $togglestr = '--<br>--<br>--<br>--<br>';
    $right_col = gen_div(gen_link($toggle_url, $togglestr, 'plain'),
                       'gem_step');
    $rv = gen_two_cols($left_col, $right_col);
  } else {
    $elem_arr []= gen_input('submit', TEMPLATE_CMD, 'Create', $add_el);
    $elem_arr []= gen_select_input('category', $option_arr, $selcat, $add_el);
    $elem_arr []= gen_txt_input('stxt', $safe_text, SEARCH_COLS,
                                SEARCH_PLACEHOLDER, $add_el, $auto_focus);
    $atf = web_get_user_flag(web_get_user(), TEXT_AREA_FLAG);
    if (!$atf) {
      $elem_arr []= gen_link($toggle_url, '++', 'plain');
    }
    $rv = gen_form($elem_arr, gen_url('search'));
  }
  return $rv;
}

function gen_login_form($add_el = true) {
  $rv = '';
  $insize = LOGIN_COLS;

  $elem_arr = array();

  $loginAsGuestMessage = gen_p('If you have no account, check the guest box and press Login');

  $elem_arr [] = $loginAsGuestMessage;

  $usertxt = gen_txt_input('username', '', $insize,
                           'Username', $add_el);
  $usertxt .= '<br>';

  $passtxt = gen_txt_input('password', '', $insize,
                           'Password', $add_el);
  $passtxt = gen_span($passtxt, 'nextline');
  //$passtxt .= '<br>';
  
  $elem_arr []= $usertxt;
  $elem_arr []= $passtxt;

  //add login as guest check box element
  $loginAsGuest = gen_checkbox('login_as_a_guest','yes');
  //add label for the checkbox
  $loginAsGuest .=  gen_span('Login as a guest ');
  $loginAsGuest .=  '<br>';
  $elem_arr [] = $loginAsGuest;

  $elem_arr []= gen_input('submit', TEMPLATE_CMD, 'Login', $add_el);

  $rv = gen_form($elem_arr, gen_url('login'));
  return $rv;
}

function gen_chat_with_fate($inpage, $is_open, $max_gems = NUM_CHAT_ROWS) {
  $rv = '';
  if ($is_open) {
    $chat_arr = array();
    //TODO $chat_index = user_get_chat_index(web_get_user());
    $gemarr = mod_get_user_gems(web_get_user(), $max_gems);
    if (count($gemarr) == 0) {
      $chat_arr []= gen_i('No gems.');
    } else {
      foreach ($gemarr as $gem) {
        $dateurl = gen_url('search', 'loadgem');
        $dateurl .= gen_url_param('gemid', $gem['gemid']);
        $datestr = fd($gem['datecreated']);
        $chatstr = gen_link($dateurl, $datestr, 'header') . '<br>';
        if ($gem['stepint'] == 0) {
          $chatstr .= $gem['wordcount'] . ' words (';
          $chatstr .= $gem['charcount'] . ' letters)<br>';
          if ($maxgems == 0) {
            $chatstr .= gen_i('found: ') . gen_b($gem['tokstr']);
          } else {
            $chatstr .= gen_i('guess: ') . gen_b('_______!');
          }
        } else {
          $chatstr .= gen_i('blank: ') . gen_b($gem['tokstr']) . '<br>';
          $guessdata = mod_load_step($gem['gemid'], 1);
          $chatstr .= gen_i('guess: ');
          if ($guessdata['stepstr'] == '') {
            $chatstr .= 'n/a';
          } else {
            $chatstr .= gen_u($guessdata['stepstr']);
          }
          if ($gem['stepint'] > 1) {
            $guessdata = mod_load_step($gem['gemid'], 2);
            $chatstr .= '<br>';
            if ($guessdata['stepstr'] == '') {
              $chatstr .= '"n/a"';
            } else {
              $quest_str = $guessdata['stepstr'];
              $quest_str_len = strlen($quest_str);
              if ($quest_str_len > 80) {
                $quest_str = substr($quest_str, 0, 90) . '...';
              }
              if ($max_gems != 0) {
                $quest_str = wordwrap($quest_str, 25, '<br>');
              }
              $chatstr .= '::' . gen_link($dateurl, $quest_str);
            }
          } //end if stepint > 1
        } //end stepint == 0
        $chat_arr []= $chatstr;
      } //end foreach gems
    } //end if no gems
    
    /*$tempstr = gen_link(gen_url($inpage), 'top');
    $tempstr .= ' | ' . gen_link(gen_url($inpage), 'prev');
    $tempstr .= ' | ' . gen_link(gen_url($inpage), 'next');
    $tempstr .= ' | ' . gen_link(gen_url($inpage), 'end');*/
    //TODO $chat_arr []= $tempstr;
    if ($max_gems == 0) {
      $rv = gen_chat_win($chat_arr);
    } else {
      $chat_win = gen_chat_win($chat_arr);
      $title_html = gen_chat_title($inpage, TOGGLE_CHAT_CMD, '-');
      $title_bar = gen_title_bar($title_html);
      $rv = gen_title_box($title_bar, $chat_win);
    }
  } else {
    $title_html = gen_chat_title($inpage, TOGGLE_CHAT_CMD, '+');
    $rv = gen_title_bar($title_html, true);
  }
  return $rv;
}

function gen_chat_title($inpage, $action_cmd, $action_char) {
  $chat_url = gen_url($inpage, $action_cmd);
  $rv = '<div class="row"><div class="column">';
  $action_str = 'Chat with ' . app_get_page_title();
  $rv .= gen_link($chat_url, $action_str, 'plain');
  $rv .= '</div><div class="right_column">';
  $rv .= gen_link($chat_url, '&nbsp;&nbsp;&nbsp;&nbsp;[', 'chars');
  $rv .= gen_link($chat_url, $action_char);
  $rv .= gen_link($chat_url, ']', 'chars');
  $rv .= '</div></div>';
  return $rv;
}

function gen_chat_win($chat_arr, $add_el = true) {
  $rv = '';
  foreach ($chat_arr as $msg) {
    $rv .= $msg . '<br><br>';
  }
  if ($add_el) $rv .= "\n";
  return $rv;
}
