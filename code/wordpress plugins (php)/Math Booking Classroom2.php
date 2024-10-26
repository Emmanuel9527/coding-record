<?php
/*
Plugin Name: Math Booking Classroom
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


function custom_table_and_td_styles() {
    echo '<style>
    .em_reserv {
        width: 80%; /* 設置 div 的寬度 */
        margin: 0 auto; /* 將 div 水平居中 */
        text-align: center; /* 讓 div 的內容居中 */
        background-color: #f0f6fc; /* 設置背景顏色 */
        padding: 20px; /* 添加內邊距 */
        border-radius: 8px; /* 添加圓角 */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* 添加陰影 */
    }

    .em_table {
        width: 100%; /* 確保表格佔據 div 的全寬 */
        margin: 0 auto; /* 確保表格在 div 中居中 */
        border-collapse: collapse; /* 合併邊框 */
		border: 2px solid #dcdcdc; /* 添加邊框並設置顏色 灰 */
    }

    .em_content_title{
        text-align: center; /* 文字水平居中 */
        vertical-align: middle; /* 文字垂直居中 */
        padding: 10px; /* 添加內邊距 */
        border: 4px solid #dcdcdc; /* 添加邊框並設置顏色 灰 */
		font-weight: bold; /* 加粗字體 */
		font-size: 20px; /* 設置字體大小 */
    }
	
	.em_content_time{
		display: flex; /* 使用 flexbox 來進行布局 */
		justify-content: space-around; /*使子元素距離均勻分布*/
        align-items: center; /* 垂直居中對齊子元素 */
        padding: 10px; /* 添加內邊距 */
        border: 2px solid #dcdcdc; /* 添加邊框並設置顏色 灰 */
    }

	
	.time-slot {
    	border: 2px solid #333; /* 單個方框的邊框顏色和寬度 */
    	padding: 10px; /* 內邊距，讓內容和邊框之間有空間 */
    	margin: 10px 0; /* 外邊距，設置每個方框之間的垂直距離 */
    	border-radius: 5px; /* 添加圓角，使方框的角變得圓滑 */
    	background-color: #2b76b6; /* 背景顏色 */
		cursor:pointer; /* 調整鼠標顯示方式 */
	}
    
    .time-slot.active {
        background-color: #d3d3d3; /* 這是淺灰色 */
        cursor:default;
    }
    
    .time-slot.tested {
        background-color: #FF7575; /* 這是淺紅色 */
        cursor:default;
    }


	/* 每個方框內的內容 */
	.em_time_item {
    	display: flex; /* 使用 flexbox 來進行布局 */
    	flex-direction: column; /* 垂直排列時間項目 */
    	align-items: center; /* 垂直方向上居中對齊 */
    	font-size: 16px; /* 設置字體大小 */
        color:#ffffff;
	}
	

	/* 時間的樣式 */
	.em_start_time {
    	display: block; /* 確保每個時間在新的一行 */
    	text-align: center; /* 文字水平居中 */
    	font-size: 16px; /* 設置字體大小 */
		font-weight: bold; /* 加粗字體 */
	}
	
	.em_end_time {
    	display: block; /* 確保每個時間在新的一行 */
    	text-align: center; /* 文字水平居中 */
    	margin: 5px 0; /* 每個時間項目之間的垂直距離 */
    	font-size: 11px; /* 設置更小的字體大小 */
	}
    </style>';
}
// 將自定義的 CSS 添加到 WordPress 頁面的 <head> 部分
add_action('admin_head', 'custom_table_and_td_styles');

function add_custom_capabilities() {
    // 獲取管理員角色對象
    $role = get_role('administrator');

    if ($role) {
        // 添加自訂權限
        $role->add_cap('manage_search_booking'); // 創建自訂權限
    }
}
add_action('admin_init', 'add_custom_capabilities');

function add_custom_capabilities_to_subscribers() {
    $role = get_role('subscriber');

    if ($role) {
        // 添加自訂權限到訂閱者角色
        $role->add_cap('manage_search_booking');
    }
}
add_action('admin_init', 'add_custom_capabilities_to_subscribers');

function add_booking_submenus() {
    add_submenu_page(
        'edit.php?post_type=math_booking', 
        '查詢預約', 
        '查詢預約', 
        'manage_search_booking', // 使用自訂權限 
        'search_booking', 
        'render_search_booking_page' 
    );
}
add_action('admin_menu', 'add_booking_submenus');

function generate_workday_table($selected_day, $weekdays_time, $selected_date){ //當我選中了星期一
    //我就要去看post_type=math_classroom之星期一的post有沒有已經有東西的
    //
	//echo "<script type='text/javascript'>
    //    var weekdaysTime = " . json_encode($weekdays_time) . ";
    //    alert(JSON.stringify(weekdaysTime));
    //  </script>";
	//echo "<script type='text/javascript'>alert('$selected_day');</script>";
     $day_map = array(
        'Mon' => 'Mon',
        'Tue' => 'Tues',
        'Wed' => 'Wed',
        'Thu' => 'Thur',
        'Fri' => 'Fri',
        'Sat' => 'Sat',
        'Sun' => 'Sun'
    );
    $selected_day = $day_map[$selected_day];
    // 獲取所有 post_type==math_classroom 的文章
    $classroom_posts = get_posts(array(
        'post_type' => 'math_classroom',
        'posts_per_page' => -1
    ));
    
    global $wpdb;

    $query = $wpdb->prepare("
        SELECT post_id 
        FROM $wpdb->postmeta 
        WHERE meta_key = 'book_data' 
        AND meta_value LIKE %s
        AND meta_value LIKE %s
    ", '%' . $selected_date . '%', '%' . $selected_day . '%');

    $results = $wpdb->get_col($query);

    $filtered_posts = array();

    foreach ($results as $post_id) {
        $meta_value = get_post_meta($post_id, 'book_data', true);
        $meta_value = maybe_unserialize($meta_value);

        if (is_array($meta_value)) {
            if ($meta_value['bookdate'] === $selected_date && $meta_value['week'] === $selected_day) {
                $filtered_posts[] = $post_id;
            }
        }
    }

    if ($filtered_posts) {
        $booking_posts = get_posts(array(
            'post__in' => $filtered_posts,
            'post_type' => 'math_booking',
            'post_status' => 'private'
        ));
    }
    //check
	if ($booking_posts){
		foreach ($booking_posts as $post) {
		$meta_value = get_post_meta($post->ID, 'book_data', true);
		echo '<pre>' . print_r($meta_value, true) . '</pre>';
		}	
	}
    
    // 優化資料查詢
    //$booking_posts = get_posts(array(
    //'post_type' => 'math_booking',
    //'posts_per_page' => -1,
    //'post_status' => 'private',
    //));
    
    // 數據結構轉換
    $booking_data = array();
    foreach ($booking_posts as $current_post) {
        $mv = maybe_unserialize(get_post_meta($current_post->ID, 'book_data', true));
        if ($mv['bookdate'] == $selected_date && $mv['week'] == $selected_day) {
            $booking_data[$mv['roomno']] = $mv['time']; //key=預約過後的教室名稱, value=陣列，預約的時間段DX
            //我希望能夠在下面查詢對應的title和DX
            echo '<pre>' . print_r($booking_data, true) . '</pre>';
        }
        else{
            echo '<pre>' . print_r($mv, true) . '</pre>';
        }
        
    }
    
    $html = "<div id='".$selected_day."' class='em_reserv'>"; //透過id去顯示(用js)
    $html .= "<table name='todaycal' class='em_table'>";
    $html .= "<tbody>";
    foreach ($classroom_posts as $current_post) { //去遍歷每一個為math_classroom的文章
        $html .= "<tr>";
        $post_id = $current_post -> ID;
        $pp = get_post($post_id);
        //setup_postdata($post_);// 設置全域 $post 物件
        $title = $pp -> post_title; // 獲取文章標題=>教室名稱
        
        //檢查教室資訊
        $meta_value = get_post_meta($current_post->ID, $selected_day, true);
        $meta_value = maybe_unserialize($meta_value); // 反序列化 meta_value

        $html .= "<td class='em_content_title'>".$title."</td>";
        $html .= "<td class='em_content_time'>"; 
        foreach ($weekdays_time as $index => $time){ //跑 開始時間 與 結束時間 //將會跑出特定教室的所有時間段
            //如果$index(如果$index=0，代表D1;但如果$index=4，代表DN)與$arr = $arr_time[$indices[$i]]中$arr裡面的時間段對上，implies這個時段已經被人預約了
            //$arr裡面存放的是'DX' //每一次跑都是跑一個時間段       
            $parts = explode('-', $time);
            // 確保分隔成功，並檢查結果
            if (count($parts) == 2) {
                $start_time = $parts[0]; // 開始時間
                $end_time = $parts[1];   // 結束時間
                //要去修改math_classroom的教室名稱
                // 定義對應的 key，比如 'Mon1', 'Mon2', 'Mon3', 等
                $meta_key = $index < 4 ? $selected_day . ($index + 1) : ($index > 4 ? $selected_day . $index : $selected_day . 'N');

                // 確認對應的 meta_value 是否有值
                $class = isset($meta_value[$meta_key]) && !empty($meta_value[$meta_key]) ? 'time-slot active' : 'time-slot';
                //確認對應的 預約資料 是否為tested(代表該時段有預約)
                if (!empty($booking_data)) {
					$check_time = 'D' . ($index < 4 ? $index + 1 : ($index > 4 ? $index : 'N'));
					$booked_times = $booking_data[$title] ?? [];
					$class2 = in_array($check_time, $booked_times) ? 'tested' : '';
				} else {
					$class2 = '';
				}
                //以上$class2的執行過程是這樣的，假設現在$title是MA403，且目前是D3
                //上面將會去檢查D3是否存在於$booking_data['MA403'](array)當中，如果存在，回傳'tested'
                $final_class = trim($class . ' ' . $class2); // 使用 trim() 以防空格問題
                //$class和$class2絕對不會同時發生，但這個防衛機制還沒做
                //$class2只是為了跟課表的分開，將來看可否可刪
                $html .= "<div class='".esc_attr($final_class)."' data-time='".esc_attr($start_time)."' data-room='".esc_attr($title)."'>";
                $html .= "<div class='em_time_item'>";
                $html .= "<span class='em_start_time'>".$start_time."</span>";
                $html .= "<span class='em_end_time'>"."~".$end_time."</span>";
                $html .= "</div>";
                $html .= "</div>";
            } else {
                error_log("字串格式不正確");
            }

        }
       $html .= "</td>";
      $html .= "</tr>";
    }
    // 重置文章數據
    wp_reset_postdata();
    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</div>";

    $html .= '<script>';
    $html .= 'jQuery(document).ready(function($) {
    $(".time-slot").on("click", function() {
        var hasActiveClass = $(this).hasClass("active");
        var hasTestedClass = $(this).hasClass("tested");
        if (hasActiveClass || hasTestedClass) {
            return false; // 如果條件成立，則不執行後續操作
        }
    var time = $(this).data("time");
    var room = $(this).data("room");
    var date = "' . esc_js($selected_date) . '";
    var baseUrl = "' . esc_url(admin_url('post-new.php?post_type=math_booking')) . '";
    var url = baseUrl + "&time=" + encodeURIComponent(time) + "&room=" + encodeURIComponent(room) + "&date=" + encodeURIComponent(date);
    window.location.href = url;
    });
    });';
    $html .= '</script>';

    return $html;
}


function render_search_booking_page() {
    $idname = array(
		"Mon" => [],
		"Tues" => [],
		"Wed" => [],
		"Thur" => [],
		"Fri" => [],
		"Sat" => [],
		"Sun" => [],
		);
		
     if (isset($_POST['workdays'])) { //用於第一個改變workdays
       $selected_date = sanitize_text_field($_POST['workdays']);
    }
    $weekdays = array('星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'); //用於下拉式選單「星期幾」之用
    $weekdays_time = array(); //記得之後要改成DN
    $ArrStart_time = array('08:10', '12:40');
    $ArrEnd_time = array('12:00', '17:30');
    for ($i = 0; $i < count($ArrStart_time); $i++) {
        $start_time = $ArrStart_time[$i];
        $end_time = $ArrEnd_time[$i];
        while (strtotime($start_time) < strtotime($end_time)) {
            // 計算結束時間
            $end_interval = strtotime($start_time) + 50 * 60; // 50 分鐘
            $end_time_slot = date('H:i', $end_interval);

            // 把新時間段加入陣列
            $weekdays_time[] = $start_time . '-' . $end_time_slot;

            // 設定下一個時間段的開始時間
            $start_time = date('H:i', strtotime($start_time) + 60 * 60); // 加 1 小時 (50分鐘+10分鐘)
            }
    }
    
    // 獲取當前日期
    $currentDate = new DateTime();
    
    
    $html = '';

    
    $html .= "<body>";
    $html .= '<h1>課表切換</h1>';
    $html .= '<form method="post">';
    $html .= '<label for="workdays">請選擇預約時段：</label>';
	$html .= '<select id="workdays" name = "workdays" class="target">';
    
    // 初始化HTML字符串
    //$html = '<form method="post">';
    
    // 生成接下來的五個工作日
    $workdayCount = 0;
    while ($workdayCount < 5) {
        // 獲取當前日期的星期幾
        $dayOfWeek = (int) $currentDate->format('w'); // 返回的是數字（0-6）

        // 如果是工作日
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
            // 獲取星期幾的名稱
            $dayName = $weekdays[$dayOfWeek];
            
            // 獲取日期
            $dateString = $currentDate->format('Y-m-d');
            
            // 顯示下拉菜單選項
            // value="" 用來設定該選項的值（當用戶選中這個選項後，表單會提交這個值）
            //$num = "$dayName $dateString";
            //$ch=( $selected_date == $num ? " selected='selected' " :" " ); 
            $html .= "<option value=\"$dateString\">$dayName $dateString</option>"; 

            
            $workdayCount++;
        }
        
        // 增加一天
        $currentDate->modify('+1 day');
    }

    $html .= '</select>';
    $html .= '<button type="submit">提交</button>';
    $html .= '</form>';
    $html .= '<div style="width:100px; height:50px; background-color:#f0f0f0; margin:20px;"> </div>';
    //以下用來維持option顯示的值
    $html .= '<script>';
    $html .= 'jQuery(document).ready(function($) {
    $( ".target" ).on( "change", function() {
     localStorage.setItem("workdays", $("#workdays option:selected").index());
    } );

    if (localStorage.getItem("workdays")) {
        $("#workdays option").eq(localStorage.getItem("workdays")).prop("selected", true);
    } else {
        console.log("LocalStorage中沒有workdays的值，請用戶選擇一個選項。");
    }
    });';
    $html .= '</script>';

    //$html .= '<input type="submit" value="提交">';
    //$html .= '</form>';
    
   if (isset($_POST['workdays'])) {
       $selected_date = sanitize_text_field($_POST['workdays']);
       $selected_day = (new DateTime($selected_date))->format('D'); // 獲取星期幾縮寫
       $html .= generate_workday_table($selected_day, $weekdays_time, $selected_date);
      // $html .= ($selected_day);  
    }
                       
    $html .= '</body>';
    echo $html;
  
}


//1天後 刪除 auto-draf
function wp_delete_autodraft() {
    global $wpdb;
    // Delete auto-drafts.
    $old_posts = $wpdb->get_col( "SELECT ID FROM $wpdb->posts 
        WHERE post_status = 'auto-draft' 
        and DATE_SUB( NOW(), INTERVAL 1 DAY )>post_date");
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

// 預約教室表單
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

    // 使用者資訊
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
?>
   <input type="button" value="取消&填寫基本資料" 
      onclick="window.location.assign('http://140.136.158.236/wp-admin/profile.php')">
      <!--要怎麼取得現在的ip-->
<?php
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
     $book_data_status=get_post_meta($post_id,'book_data_status',true); //$book_data_status之後要改
    
     //????
     if (empty($book_data_staus)){
         echo "<script type='text/javascript'>console.log('hello')</script>";
     }
     else{
         echo "<script type='text/javascript'>console.log('bad')</script>";
     }
    
    // 從 URL 查詢參數中獲取預約時段和教室名稱
    $time_from_url = isset($_GET['time']) ? sanitize_text_field($_GET['time']) : '';
    $room_from_url = isset($_GET['room']) ? sanitize_text_field($_GET['room']) : '';
    $date_from_url = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';
    // 若 URL 查詢參數中有預約時段，則設定到預約資料中
    // 儲存所有開始時間的陣列
      $timeSlots = array(
        '08:10',
        '09:10',
        '10:10',
        '11:10',
        '12:40',
        '13:40',
        '14:40',
        '15:40',
        '16:40'
    );
    if ($time_from_url) {
        $targetTime = $time_from_url; //start_time, eg:10:10 
        //接著去判斷該時間是D幾
        $index = array_search($targetTime, $timeSlots);
        if ($index == 4){
            $chtime = array('DN');
        }
        elseif ($index > 4){
            $chtime = array('D'.($index));
        }
        else{
        	$chtime = array('D'.($index+1));
        }
    }
    if ($room_from_url) {
        $chroom = $room_from_url; //傳回教室名稱
    }
    if ($date_from_url) {
        $chdate = $date_from_url; //傳回日期名稱
        //eg: '2024-08-25'
        $timestamp = strtotime($chdate);
        $dayOfWeek = date('w', $timestamp); // 獲取星期幾的數字表示（0 表示星期日, 1-6 表示星期一到星期六）

        $weeknames = array("Sun","Mon","Tues","Wed","Thur","Fri","Sat");

        $chday = $weeknames[$dayOfWeek];
        //$dateParts = explode('-', $dateString);
        //$year = $dateParts[0];
        //$month = $dateParts[1];
        //$day = $dateParts[2];
    }
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
    // 各個教室
    $rooms = array(" ", "LH102","MA301","MA306","MA307",
                   "MA401","MA402","MA403","MA405","MA406","MA407","MA413");

    foreach( $rooms as  $num ){
    $ch=( $chroom == $num ? " selected='selected' " :" " ); 
    echo '<option value="'.$num.'"'.$ch.' >'.$num.'</option>';
    echo "\n";
    }
    
    if ( empty($book_data_status) ){
        $roomnoErr=" ";
    }
    else{
      if ( $book_data_status["roomno"]=='00' ) { $roomnoErr="請選擇預約教室";}
      else { $roomnoErr=" "; }   
    }
    ?>
    </select>
    <span class="error" id="roomnoErr"><?=$roomnoErr;?></span>
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
    if ( empty($book_data_status) ){
        $DateErr=" ";
    }
    else{
      if ($book_data_status["bookdate"]=='00') { $DateErr="請選擇預約日期";}
      else { $DateErr=" "; } 
    }
    ?>
    <span class="error" id="DateErr"><?=$DateErr;?></span>
    </td>
    </tr>
    <?php
    if (empty( $chday )){
     $chday=" ";
    }
    $weeknames = array("Sun","Mon","Tues","Wed","Thur","Fri","Sat");
    $chinese_weeknames = array('星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六');
    ?>
    <tr>
    <th><label for=week> Week </label>
    </th>
    <td>
    <?php
    $count = 0;
    foreach ( $weeknames as $weekday ) {
        echo '<input type="radio" ';
        echo 'id="'.$weekday.'" ';
        echo 'name="week" ';
        echo 'value="'.$weekday.'" '.( $chday=="$weekday" ? " checked" : '' ).' ';
        echo 'disabled> ';
        echo '<label for="'.$weekday.'">'.$chinese_weeknames[$count].'</label>';
        echo "\n";
        $count = $count + 1;
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
    
    if ( empty($book_data_status) ){
        $timeErr=" ";
    }
    else{
      if ($book_data_status["time"]=='00') { $timeErr="請選擇預約時段";}
      elseif ($book_data_status["time"]=='01'){
        $book_sametime = $book_data_status["book_sametime"];
        $timeErr = "下列這些時段已有人預約或有課程:";
        // 將陣列元素用逗號連接起來，並賦值給 $timeErr
        $timeErr .= implode(', ', $book_sametime);
        $timeErr .= "\n請修改時段";
      }
      else { $timeErr=" "; }
    }
?>
    <span class="error" id="timeErr"><?=$timeErr;?></span>
    </td>
    </tr>
    </table>
       <input type="button" value="取消預約"
          onclick="window.location.assign('http://140.136.158.236/wp-admin')"> 
          <!--要怎麼取得現在的ip-->
    </div>
    <p id="demo"></p>

<?php

//print_r($post_id);
//$title = get_the_title();
//print_r($title);

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
  
    global $wpdb;
	$book_data_status = array(
		 'roomno' => '11',
		 'bookdate' => '11',
		 'week' => '11',
		 'time' => '11',
		 'book_sametime' => []
		 ); //11:預約資料非空值(代表成功填寫), 00:預約資料空值, 01:有衝到

	if (!empty($_POST["roomno"])){
	   $book_data_status["roomno"] = '00';
	}
	if (!empty($_POST["bookdate"])){
	   $book_data_status["bookdate"] = '00';
	}
	if (!empty($_POST["time"])){
	   $book_data_status["time"] = '00';
	}

    
  
   update_post_meta($post_id,'userlogin',$user);
   update_post_meta($post_id,'user_data',$user_data);
   update_post_meta($post_id,'book_data',$book_data);
   update_post_meta($post_id,'book_data_status',$book_data_status);
}

//Hook: save_post_{$post->post_type}
add_action('save_post_math_booking', 'math_booking_save_meta_box',10,2);


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

add_filter('post_updated_messages', 'booking_messages_display');

function wp_post_status_private( $new_status, $old_status, $post ) {
    // 確保我們只處理'math_booking'的文章
    if ( $post->post_type != 'math_booking' ) {
        return;
    }
    
    // 獲取當前用戶的ID number
    $current_user = wp_get_current_user();
    $idnumber = get_user_meta($current_user->ID, 'idnumber', true);

    // 確保只處理pubish的情況
    if ( $new_status == 'publish' && $old_status != $new_status ) {
        // 檢查是否匹配ID number
        if ( $post->post_title == $idnumber ) {
            $post->post_status = 'private'; //私有
        } else {
            $post->post_status = 'draft'; //草稿
        }
        wp_update_post( $post );
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