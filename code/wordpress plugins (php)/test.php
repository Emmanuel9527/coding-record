<?php
/*
Plugin Name:Test 
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

function add_booking_submenus() {
    add_submenu_page(
        'edit.php?post_type=math_booking', 
        '查詢預約', 
        '查詢預約', 
        'manage_options', 
        'search_booking', 
        'render_search_booking_page' 
    );
}
add_action('admin_menu', 'add_booking_submenus');

function custom_table_and_td_styles() {
    echo '<style>
    .em_reserv {
        width: 80%; /* 設置 div 的寬度 */
        margin: 0 auto; /* 將 div 水平居中 */
        text-align: center; /* 讓 div 的內容居中 */
        background-color: #e0ffff; /* 設置背景顏色 */
        padding: 20px; /* 添加內邊距 */
        border-radius: 8px; /* 添加圓角 */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* 添加陰影 */
    }

    .em_table {
        width: 100%; /* 確保表格佔據 div 的全寬 */
        margin: 0 auto; /* 確保表格在 div 中居中 */
        border-collapse: collapse; /* 合併邊框 */
		border: 2px solid #90ee90; /* 添加邊框並設置顏色 綠 */
    }

    .em_content_title{
        text-align: center; /* 文字水平居中 */
        vertical-align: middle; /* 文字垂直居中 */
        padding: 10px; /* 添加內邊距 */
        border: 2px solid #8b4513; /* 添加邊框並設置顏色 咖啡 */
		font-weight: bold; /* 加粗字體 */
		font-size: 20px; /* 設置字體大小 */
    }
	
	.em_content_time{
		display: flex; /* 使用 flexbox 來進行布局 */
		justify-content: space-around; /*使子元素距離均勻分布*/
        align-items: center; /* 垂直居中對齊子元素 */
        padding: 10px; /* 添加內邊距 */
        border: 2px solid #ffe4b5; /* 添加邊框並設置顏色 */
    }

	
	.em_time_slot {
    	border: 2px solid #333; /* 單個方框的邊框顏色和寬度 */
    	padding: 10px; /* 內邊距，讓內容和邊框之間有空間 */
    	margin: 10px 0; /* 外邊距，設置每個方框之間的垂直距離 */
    	border-radius: 5px; /* 添加圓角，使方框的角變得圓滑 */
    	background-color: #A5DEE4; /* 背景顏色 */
		cursor:pointer; /* 調整鼠標顯示方式 */
	}

	/* 每個方框內的內容 */
	.em_time_item {
    	display: flex; /* 使用 flexbox 來進行布局 */
    	flex-direction: column; /* 垂直排列時間項目 */
    	align-items: center; /* 垂直方向上居中對齊 */
    	font-size: 16px; /* 設置字體大小 */
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




function generate_workday_table($selected_day, $weekdays_time){
	//echo "<script type='text/javascript'>
    //  var weekdaysTime = " . json_encode($weekdays_time) . ";
    //  alert(JSON.stringify(weekdaysTime));
    //</script>";
	//echo "<script type='text/javascript'>alert('$selected_day');</script>";
     $day_map = array(
        'Mon' => 'Mon',
        'Tue' => 'Tues',
        'Wed' => 'Wed',
        'Thu' => 'Thus',
        'Fri' => 'Fri',
        'Sat' => 'Sat',
        'Sun' => 'Sun'
    );
    $selected_day = $day_map[$selected_day];
    // 獲取所有 post_type==math_classroom 的文章
    $args = array(
        'post_type' => 'math_classroom',
        'posts_per_page' => -1
    );
    // 執行查詢
	//$query = new WP_Query($args);

    $posts = get_posts($args);
    $html = "<div id='".$selected_day."' class='em_reserv'>"; //透過id去顯示(用js)
    $html .= "<table name='todaycal' class='em_table'>";
    $html .= "<tbody>";
    foreach ($posts as $current_post) {
    	$html .= "<tr>";
        $post_id = $current_post -> ID;
        $pp = get_post($post_id);
        
		//setup_postdata($post_);// 設置全域 $post 物件
        $title = $pp -> post_title; // 獲取文章標題
        $meta_value = get_post_meta($current_post->ID, $selected_day, true); //selected_day 為 MON TUE WED... SUN
		
		// 反序列化 meta_value
        $meta_value = maybe_unserialize($meta_value);
        $html .= "<td class='em_content_title'>".$title."</td>";
        $html .= "<td class='em_content_time'>"; 
        foreach ($weekdays_time as $time){ //跑 開始時間 與 結束時間 label
        	$parts = explode('-', $time);
            // 確保分隔成功，並檢查結果
            if (count($parts) == 2) {
                $start_time = $parts[0]; // 開始時間
                $end_time = $parts[1];   // 結束時間
                $html .= "<div class='em_time_slot'>";
	            	$html .= "<div class='em_time_item'>";
                $html .= "<span class='em_start_time'>".$start_time."</span>";
								$html .= "<span class='em_end_time'>"."~".$end_time."</span>";
	            	$html .= "</div>";
	          		$html .= "</div>"; 
                 } 
			else {
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
     return $html;
}




function render_search_booking_page() { //計算課程時間
    $idname = array(
		"Mon" => [],
		"Tues" => [],
		"Wed" => [],
		"Thus" => [],
		"Fri" => [],
		"Sat" => [],
		"Sun" => [],
		);
		
    
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
    
    foreach($idname as $key => $value){ //之後再做微調D1-D9，因為理論上有DN
        for($i=1;$i<=9;$i++){
            #Mon1, Mon2...
            $idname["$key"][]=$key."$i";
        }
        $subject = $idname["$key"]; 
        /*get_post_meta()用於檢索文章（或其他自定義文章類型）的自定義字段（meta data）。
        這些自定義字段是與文章相關聯的鍵值對數據，可以存儲各種額外的信息，
        例如自定義設定、選項、描述等。
        */
        #我的確沒有辦法使用button傳回資料，因為他不是input
        $el_meta = get_post_meta($post->ID, "$key", true );
        $course=[];
        for($i=0;$i<=8;$i++){ #為了重新點選舊教室時能夠出現
            #檢查是否為空
            #現在 $value 是 ["Mon1", "Mon2", ..., "Mon9"] 這樣的一個數組
            #不用add_list之類的?
            $course[] = !empty($el_meta["$subject[$i]"]) ? $el_meta["$subject[$i]"] : '';
        }
       
    }


    $html= "<body>";
    $html .= '<h1>課表切換</h1>';
    $html .= '<form method="post">';
    $html .= '<label for="workdays">請選擇預約時段：</label>';
	$html .= '<select id="workdays" name = "workdays">';
    
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

    //$html .= '<input type="submit" value="提交">';
    //$html .= '</form>';
    
   if (isset($_POST['workdays'])) {
       $selected_date = sanitize_text_field($_POST['workdays']);
       $selected_day = (new DateTime($selected_date))->format('D'); // 獲取星期幾縮寫
       $html .= generate_workday_table($selected_day, $weekdays_time);
      // $html .= ($selected_day);
    
    }

                         
    $html .= '</body>';
    echo $html;
  
}
