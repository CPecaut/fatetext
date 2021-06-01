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

function mod_get_hall_categories() {
  $sql = 'SELECT category FROM hallart GROUP BY category';
  $rs = queryf_all($sql);
  $rv = array();
  if (count($rs) == 0) {
    $rv []= 'American';
  } else {
    foreach ($rs as $cat_arr) {
      $rv []= $cat_arr['category'];
    }
  }
  return $rv;
}

function mod_get_hall_art() {
  $sql = 'SELECT * FROM hallart ORDER BY artid DESC';
  $rs = queryf_all($sql);
  if (count($rs) == 0) {
    $rs = array(array('artid' => '1',
                      'category' => 'American',
                      'datestr' => '01_01_21',
                      'arturl' => 'http://thesuzy.com',
                      'sumstr' => 'TheSuzy Trilogy by Todd Perry'));
  }
  return $rs;
}

function mod_max_tokid() {
  $sql = 'SELECT MAX(tokid) as maxid FROM toks';
  $rs = queryf_one($sql);
  return $rs['maxid'];
}

function mod_max_chestid() {
  $sql = 'SELECT MAX(chestid) as maxid FROM chests';
  $rs = queryf_one($sql);
  return $rs['maxid'];
}



function mod_load_step($gemid, $whichint) {
  $sql = 'SELECT stepstr, datecreated FROM steps';
  $sql .= ' WHERE gemid = %d';
  $sql .= ' AND whichint = %d';
  $rs = queryf_one($sql, $gemid, $whichint);
  return $rs;
}

function mod_record_step($gemid, $guesstxt, $whichint) {
  $nowtime = time();
  $sql = 'DELETE FROM steps WHERE gemid = %d AND whichint = %d';
  queryf($sql, $gemid, $whichint);

  $sql = 'INSERT INTO steps (gemid, stepstr, whichint, datecreated)';
  $sql .= ' VALUES (%d, %s, %d, %d)';
  queryf($sql, $gemid, $guesstxt, $whichint, $nowtime);
}

function mod_get_book_title($book_id) {
  $sql = 'SELECT titlestr FROM books WHERE bookid = %d';
  $rs = queryf_one($sql, $book_id);
  //TODO error handling
  return $rs['titlestr'];
}



function mod_load_chest($chestid) {
  $sql = 'SELECT * FROM chests WHERE chestid = %d';
  $rv = queryf_one($sql, $chestid);
  return $rv;
}

function mod_get_user_gems($userid, $maxgems = 0) {
  $sql = 'SELECT gems.tokid, datecreated, tokstr, gemid,';
  $sql .= ' wordcount, charcount, stepint FROM gems, toks';
  $sql .= ' WHERE userid = %d AND toks.tokid = gems.tokid';
  $sql .= ' ORDER BY lastloaded DESC LIMIT 500';
  $rs = queryf_all($sql, $userid);
  if ($maxgems == 0) {
    return $rs;
  }

  $rv = array();
  foreach ($rs as $row) {
    $sql = 'SELECT * from steps WHERE gemid = %d AND whichint = 1';
    $rs2 = queryf_one($sql, $row['gemid']);
    if (isset($rs2)) {
      if (strlen($rs2['stepstr']) > 4) {
        $rv []= $row;
        if (count($rv) >= $maxgems) {
          break;
        }
      }
    }
  }
  return $rv;
}

function mod_log_search($logtxt) {
  $logtxt = '[' . fd(time()) . ']' . $logtxt . "\n";
  file_put_contents($GLOBALS['SLOGFILE'], $logtxt, FILE_APPEND);
}

function mod_flag_from_toggle($intoggle) {
  switch ($intoggle) {
   case TOGGLE_SPLASH_CMD: return FATE_SPLASH_FLAG;
   case TOGGLE_CHAT_CMD: return CHAT_OPEN_FLAG;
   case TOGGLE_TEXT_CMD: return TEXT_AREA_FLAG;
   case TOGGLE_OPTION_CMD: return AUTHORTEXT_FLAG;
   case TOGGLE_INVERTEDCS_CMD: return INVERTEDCS_FLAG;
   case TOGGLE_TOOLTIP_CMD: return HIDETOOLTIP_FLAG;
  }
  util_except('attempted to reference an unknown flag: '
              . $intoggle);
}

/**
 * return associative array of books title
 * keys are book's id
 */
function mod_get_allbooks_title(){
    $sql = 'SELECT bookid, titlestr FROM books';
    $rs = queryf_all($sql);
    $rv = array();
    if (count($rs) == 0) {
        $rv []= '';
    } else {
        foreach ($rs as $book) {
            $rv [$book['bookid']] = $book['titlestr'];
        }
    }
    return $rv;
}
/**
 * return associative array of loaded books title
 * keys are book's id
 * order alphabetically  by the books' title
 */
function mod_get_loadedBooks_title(){
    $sql = 'SELECT bookid, titlestr FROM books WHERE isLoaded = true ORDER BY titlestr';
    $rs = queryf_all($sql);
    $rv = array();
    if (count($rs) == 0) {
        $rv []= '';
    } else {
        foreach ($rs as $book) {
            $rv [$book['bookid']] = $book['titlestr'];
        }
    }
    return $rv;
}

/**
 * return id of the last inserted book
 *
 */
function mod_get_lastBookId(){
    $sql = 'SELECT bookid FROM books ORDER BY bookid DESC LIMIT 1';
    $rs = queryf_all($sql);
    $rv = array();
    if (count($rs) == 0) {
        $rv []= '';
    } else {
        foreach ($rs as $book) {
            $rv [] = $book['bookid'];
        }
    }
    return $rv;
}

/**
 * check if there is a guest user,
 * if there is no guest user create one
 */
function mod_add_guest_user(){
    $guestUserName = 'guest';
    $guestUserPass = util_hashpass('guest');
    $sql = 'SELECT userid FROM users WHERE username = %s and hashpass = %s';
    $rs = queryf_one($sql, $guestUserName, $guestUserPass);
    if (isset($rs) && count($rs) > 0) {
    } else {
        //create one
        //get the last user id
        $lastUserId = 0;
        $sql = 'SELECT userid FROM users ORDER BY userid DESC LIMIT 1';
        $rs = queryf_one($sql);
        if(isset($rs) && count($rs) > 0){
            $lastUserId = $rs['userid'] +1;
        }
        $sql = 'INSERT INTO users (userid, username, hashpass)';
        $sql .= ' VALUES (%d, %s, %s)';
        queryf($sql, $lastUserId,  $guestUserName, $guestUserPass);
    }
}