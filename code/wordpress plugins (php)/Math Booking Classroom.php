<?php
/*
Plugin Name: Math Booking Room
Plugin URI:
Description:
Version:
Author:
Author URI:
*/

function math_register_booking(){
 register_post_type( 'math_booking',
     array(
       'labels' => array(
          'name' => '預約教室',
          'singular_name' => '預約教室',
          'add_new' => '新增預約',
          'add_new_item' => '新增預約',
          'edit_item' => '編輯預約',
          'all_items' => '所有預約',
          'view_item' => '預約列表',
          'view_items' => '預約列表',
          'search_items' => '搜尋預約',
          'feature_images' => '預約圖片',
          'insert_into_item' => '新增預約',
          'set_featured_image' => '設定預約圖片',
          'remove_featured_image' => '移除預約圖片'
          ),
        'public' => true,
        'has_archive' => true,
        'show_in_admin_bar' => true,
        'supports' => array('title'),
        'capability_type' => array( 'book', 'books' ),
        'map_meta_cap'    => true,
        ) 
      );

}

add_action('init', 'math_register_booking');


//1天後 刪除 auto-draf
function wp_delete_autodraft() {
    global $wpdb;
    // Delete auto-drafts.
    $old_posts = $wpdb->get_col( "SELECT ID FROM $wpdb->posts 
        WHERE post_status = 'auto-draft' 
        and DATE_SUB( NOW(), INTERVAL 1 DAY )>post_date"）//get_col()中接sql語法
    foreach ( (array) $old_posts as $delete ) {
        // Force delete.
        wp_delete_post( $delete, true );
        } 
}

add_action( 'init', 'wp_delete_autodraft',100 );

//建立預約教室填寫表單
function math_regbook_meta_box(){
    add_meta_box('math_booking_meta', '預約資訊',
                 'math_booking_meta_box', 'math_booking','normal','default');
}

function math_booking_meta_box( $post ){
    global $post;
    global $wpdb;
    $post_id=$post->ID;

   // 取得登入者資料
   $current_user = wp_get_current_user();

   // 取得預約meta中 user login
   $userlogin = get_post_meta($post_id,'userlogin',true);

   // $userlogin 是空值表示新預約
    if ( empty($userlogin) ){
        $userlogin = $current_user->user_login;
        $user_id = $current_user->ID;
      } else {
        $user = get_user_by( 'login', $userlogin ); //取得登入者基本資料
        $user_id = $user->ID;
      }


    $idnumber = get_user_meta($user_id, 'idnumber', true);

    $fullname = get_user_meta($user_id,'fullname',true);

    $mobile = get_user_meta($user_id, 'mobile',true);


    $booking=1;
    if ( empty($idnumber))
      { echo "<p>請至個人資料完成學號或職編填入</p>";
        $booking=0;  }
    if ( empty($fullname))
      { echo "<p>請至個人資料完成姓名填入</p>"; 
        $booking=0; }
    if ( empty($mobile))
      { echo "<p>請至個人資料完成電話填入</p>";
        $booking=0;  }

    if ( $booking == 0 and $userlogin != "user" ){ 
    echo "<input type="button" value="取消&填寫基本資料" onclick="window.location.assign('http://140.136.158.223/wp-admin/profile.php')">"
        return;
      }
 // 取得預約者基本資料
 $user_data = get_post_meta($post_id,'user_data',true);

 // 取得預約資料
 $book_data = get_post_meta($post_id,'book_data',true);   
 $chroom = $book_data["roomno"];
 $chdate = $book_data["bookdate"];
 $chday = $book_data["week"];
 $chtime = $book_data["time"];
 $book_data_status=get_post_meta($post_id,'book_data_status',true);
?>

<div>
<table class=form-table>
<tr>
<th><label for=idnumber> ID Number </label>
</th>
<td><input type="text"
       id="idnumber"
       name="idnumber"
       value="<?= $idnumber; ?>" readonly >
</td>
</tr>
<tr>
<th><label for=userlogin> user login </label>
</th>
<td><input type="text"
       id="userlogin"
       name="userlogin"
       value="<?= $userlogin; ?>" readonly >
</td>
</tr>
<tr>
<th><label for=fullname> Full Name </label>
</th>
<td><input type="text"
       id="fullname"
       name="fullname"
       value="<?= $fullname; ?>" readonly >
</td>
</tr>
<tr>
<th><label for=mobile> Phone Number </label>
</th>
<td><input type="text"
       id="mobile"
       name="mobile"
       value="<?= $mobile; ?>" readonly >
</td>
</tr>
<tr>
<th><label for=roomno> Room Number </label>
</th>
<td>
<select id="roomno" name="roomno">

<?php
$rooms = array(" ", "LH102","MA301","MA306","MA307",
               "MA401","MA402","MA403","MA405","MA406","MA407","MA413");

foreach( $rooms as  $num ){
$ch=( $chroom == $num ? " selected='selected' " :" " ); 
echo '<option value="'.$num.'"'.$ch.' >'.$num.'</option>';
echo "\n";
}

if ( $book_data_status["roomno"]==1 ) { $roomnoErr="請選擇預約教室";}
  else { $roomnoErr=" "; }
?>
</select>
<span class="error" id="roomnoErr"><?=$roomnoErr;?></span>
<input type="text"
       id="roomno1"
       name="roomno1"
       value=<?= $book_data_status["roomno"]; ?> >
</td>
</tr>
<tr>
<th><label for=bookdate> Booking Date </label>
</th>
<td><input type="date"
       class="regular-text" ltr
       id="bookdate"
       name="bookdate"
       value=<?= $chdate; ?>
       pattern="(19[0-9][0-9]|20[0-9][0-9])-(1[0-2]|0[1-9])-(3[01]|[21][0-9]|0[1-9])"
       onchange="date_get_day()">
<?php
if ($book_data_status["bookdate"]==1) { $DateErr="請選擇預約日期";}
  else { $DateErr=" "; }
?>
<span class="error" id="DateErr"><?=$DateErr;?></span>
</td>
</tr>
<?php
if (empty( $chday )){
 $chday=" ";
}
$weeknames = array("Sun","Mon","Tues","Wed","Thur","Fri","Sat",);
?>
<tr>
<th><label for=week> Week </label>
</th>
<td>
<?php
foreach ( $weeknames as $weekday ){
echo '<input type="radio" ';
echo 'id="'.$weekday.'" ';
echo 'name="week" ';
echo 'value="'.$weekday.'" '.( $chday=="$weekday" ? " checked":' ' ). 
     '>'.'<label for="'.$weekday.'">'.$weekday.'</label>';
echo "\n";
}
?>
</td>
</tr>
<?php
if ( empty( $chtime ) ){
$chtime=[];
}
?>
<tr>
<th><label for=time> Time </label>
</th>
<td>

<?php
$set=array("1","2","3","4","N","5","6","7","8","9");
foreach ( $set as $i ){
  echo '<label for="'."D$i".'">'."D$i".'</label>';
  echo "\n";
  echo '<input type="checkbox" ';
  echo ' id="'."D$i".'" ';
  echo ' class="dtime" ';
  echo ' name="time[]" ';
  echo ' value="'."D$i".'"'.( in_array("D$i",$chtime)? " checked":" " );
  echo ' onchange="checkedbox()">';
  echo "\n";
}
if ($book_data_status["time"]==1) { $timeErr="請選擇預約時段";}
  else { $timeErr=" "; }
?>
<span class="error" id="timeErr"><?=$timeErr;?></span>
</td>
</tr>
</table>
   <input type="button" value="取消預約"
      onclick="window.location.assign('http://140.136.158.223/wp-admin')">
</div>
<p id="demo"></p>

<?php

print_r($post_id);
$title = get_the_title();
print_r($title);
//$screen = get_current_screen();
//print_r($screen);
}

add_action('add_meta_boxes', 'math_regbook_meta_box');

//加入script 與 css
function ljt_enqueue_script(){
 global $post;
 $my_script1=plugins_url('myscript1.js', __FILE__ );
 $my_script2=plugins_url('myscript2.js', __FILE__ );
 $my_style1=plugins_url('mystyle1.css', __FILE__ );
 if ( $post->post_type == "math_booking") {
  wp_register_script('my_js1',$my_script1, array(),null,false );
  wp_register_script('my_js2',$my_script2, array(),null,true );
  wp_register_script('my_js3',$my_script3, array(),null,true );
  wp_enqueue_script( 'my_js1' );
  wp_enqueue_script( 'my_js2' );
  wp_enqueue_style('my_css1',$my_style1,array(),null,'all');
 }
}
add_action( 'wp_enqueue_scripts', 'ljt_enqueue_script' );
add_action( 'login_enqueue_scripts', 'ljt_enqueue_script' );
add_action( 'admin_enqueue_scripts', 'ljt_enqueue_script' );


//將預約表單資料寫入資料庫
function math_booking_save_meta_box( $post_id,$post ){
  $user = $_POST['userlogin'];
  $user_data = array(
     'idnumber' => $_POST['idnumber'],
     'fullname' =>  $_POST['fullname'],
     'mobile' => $_POST['mobile'],
    );

  $book_data = array(
     'roomno' => $_POST['roomno'],
     'bookdate' => $_POST['bookdate'],
     'week' => $_POST['week'],
     'time' => $_POST['time'],
    );

  $book_data_status = array(
     'roomno' => 1,
     'bookdate' => 1,
     'week' => 1,
     'time' => 1, ); //1:預約資料空值, 0:預約資料非空值 
  $book_date_status = 0; //1:預約日期衝到, 0:預約日期未衝到
  $book_status =0; //1:預約失敗, 0:預約成功
  foreach ($book_data as $x => $y){
     if ( !empty( $y ) ){ 
        $book_data_status["$x"]=0;
     }
   }
   update_post_meta($post_id,'userlogin',$user);
   update_post_meta($post_id,'user_data',$user_data);
   update_post_meta($post_id,'book_data',$book_data);
   update_post_meta($post_id,'book_data_status',$book_data_status);
   update_post_meta($post_id,'book_date_status',$book_date_status);
   update_post_meta($post_id,'book_status',$book_status);
}

//Hook: save_post_{$post->post_type}
add_action('save_post_math_booking', 'math_booking_save_meta_box',10,2);

/*
function booking_messages_display($messages) {
  global $post;
  $current_user = wp_get_current_user();
  $idnumber=get_user_meta($current_user->ID,'idnumber',true);
  if ( ($post->post_type == 'math_booking' && $post->post_title != $idnumber ) 
        ){
      $messages['math_booking'][6]='新增標題要求輸入 學號 或 教職員編號';
      //$post->post_status = 'draft';
      //wp_update_post($post);
      return $messages;
  } 
  return $messages;
}
*/
add_filter('post_updated_messages', 'booking_messages_display');

function wp_post_status_private( $new_status, $old_status, $post ){
  $current_user = wp_get_current_user();
  $idnumber = get_user_meta($current_user->ID,'idnumber',true);
  if ( $post->post_title == $idnumber ){
    if ( ($post->post_type == 'math_booking') &&
         ($new_status == 'publish') && 
         ($old_status != $new_status) ) {
           $post->post_status = 'private';
           wp_update_post( $post );
     }
  } else { if ( ($post->post_type == 'math_booking') &&
                ($new_status == 'publish') &&
                ($old_status != $new_status) ) {
                $post->post_status = 'draft';
                wp_update_post( $post );
            }
  }
}

add_action('transition_post_status', 'wp_post_status_private', 10, 3 );

//在控制台列表建立每個欄位標題
function booking_add_column( $columns ){
  $new_columns = array(
       'fullname' => "姓名",
       'roomno' => "教室",
       'bookdate' => "預約日期",
       'week' => "星期",
       'time' => "使用時段",
     );
  return array_merge($columns, $new_columns);
}
//使用 hook: manage_{$post->post_type}_posts_columns
add_filter( 'manage_math_booking_posts_columns', 'booking_add_column');


//在控制台列表顯示每個欄位值
function booking_display_column( $column, $post_id ){
$user_data=get_post_meta($post_id, 'user_data', true);
$book_data=get_post_meta($post_id, 'book_data', true);
$name = $user_data["fullname"];
$roomno = $book_data["roomno"];
$bookdate = $book_data["bookdate"];
$week = $book_data["week"];
$time = $book_data["time"];
switch( $column ){
   case 'fullname':
        echo $name;
        break;

   case 'roomno':
        echo $roomno;
        break;

   case 'bookdate':
       echo $bookdate;
       break;

   case 'week':
       echo $week;
       break;

   case 'time':
      foreach( $time as $D ){
       echo $D.', ';
      }
       break;
 }
}
//使用 hook: manage_{$post->post_type}_posts_custom_column
add_action('manage_math_booking_posts_custom_column', 'booking_display_column',10,2);

?>
