<?php
/*
Plugin Name: Math ClassRoom Booking
Plugin URI:
Description:此php檔案為wordpress之外掛，註冊了教室之post type，並且提供在wordpress後台填寫教室課表以及在post的前台顯示課表的功能
Version:
Author: LJT and Emmanuel and Cathy
Author URI:
*/
function math_register_classroom(){
 #註冊新的文章類型
 register_post_type( 'math_classroom',
     array(
     #這是一組用於界面和管理面板中的顯示文本。這些標籤使你的自定義文章類型更具可讀性。
       'labels' => array( 
          'name' => '教室',
          'singular_name' => '教室',
          'add_new' => '新增教室',
          'add_new_item' => '新增教室',
          'edit_item' => '編輯教室',
          'all_items' => '所有教室',
          'view_item' => '教室列表',
          'view_items' => '教室列表',
          'search_items' => '搜尋教室',
          'feature_images' => '教室圖片',
          'insert_into_item' => '新增教室',
          'set_featured_image' => '設定教室圖片',
          'remove_featured_image' => '移除教室圖片'
          ),
        'public' => true,
        'has_archive' => true, #設置是否啟用該文章類型的存檔頁面
        'show_in_admin_bar' => true,
        'supports' => array('title','thumbnail') //移除'editor'
        ) 
      );
}
add_action('init', 'math_register_classroom');


function math_register_meta_box(){
    add_meta_box('math_classroom_meta', '教室資訊',
                 'math_classroom_meta_box', 'math_classroom','normal','default');
}

function math_classroom_meta_box( $post ){
	$html = 
		"<style>
			#classroom-metabox {
				width: 90%; /* 設置 div 的寬度 */
        		margin: 0 auto; /* 將 div 水平居中 */
        		text-align: center; /* 讓 div 的內容居中 */
        		background-color: #f0ffff; /* 設置背景顏色 */
        		padding: 15px; /* 添加內邊距 */
        		border-radius: 8px; /* 添加圓角 */
        		box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* 添加陰影 */
				overflow: auto; /* 允許滾動，防止內容超出容器 */
			}
			
			#schedule {
				width: 100%; /* 確保表格佔據 div 的全寬 */
        		margin: 0 auto; /* 確保表格在 div 中居中 */
        		border-collapse: collapse; /* 合併邊框 */
				border: 2px solid #dcdcdc; /* 添加邊框並設置顏色 灰 */
				table-layout: auto;/* td大小動態調整 */
				}
				
			.schedule-col-title {
        		border: 2px solid #dcdcdc; /* 單個方框的邊框顏色和寬度 */
        		padding: 10px; /* 內邊距，讓內容和邊框之間有空間 */
        		justify-content: center; /* 內容水平居中 */
        		align-items: center; /* 內容垂直居中 */
    			height: 10px; /* 設置特定高度 */
				font-weight: bold; /* 加粗字體 */
				font-size: 16px; /* 設置字體大小 */
			}
			
    		.schedule-row-title {
        		border: 2px solid #dcdcdc; /* 添加邊框並設置顏色 */
				padding: 10px; /* 添加內邊距 */
				justify-content: center; /* 內容水平居中 */
				align-items: stretch; /* 垂直居中對齊子元素 */
				font-weight: bold; /* 加粗字體 */
				font-size: 16px; /* 設置字體大小 */
    		}
			
			.schedule-cell {			
				justify-content: space-around; /*使子元素距離均勻分布*/
        		padding: 10px; /* 添加內邊距 */
        	}
				
			.schedule-content{
				display: flex; /* 使用 flexbox 來進行布局 */
			    border: 2px solid #333; /* 單個方框的邊框顏色和寬度 */
    			padding: 10px; /* 內邊距，讓內容和邊框之間有空間 */
    			border-radius: 5px; /* 添加圓角，使方框的角變得圓滑 */
    			background-color: #2b76b6; /* 背景顏色 */
				align-items: stretch; /* 垂直居中對齊子元素 */
				cursor:pointer; /* 調整鼠標顯示方式 */
				border: 2px solid #cdcdcd; /* 單個方框的邊框顏色和寬度 */
				word-wrap: break-word;
			}

        	.schedule-content.selected {
    			background-color: #dcdcdc; /* 背景顏色 灰 */
        	}
			
			.class-name{
				display: block; /* 確保每個時間在新的一行 */
    			text-align: center; /* 文字水平居中 */
    			font-size: 16px; /* 設置字體大小 */
				font-weight: bold; /* 加粗字體 */
				color: white; /* 更改文字顏色為白色 */
			}
			
			#add-class {
				margin-top: 20px;
			    padding: 10px 20px; /* 調整按鈕的內邊距 */
    			font-size: 16px; /* 增大按鈕的字體大小 */
				font-weight: bold; /* 加粗字體 */
    			width: 150px; /* 調整按鈕寬度 */
    			background-color: #d3d3d3; /* 設置按鈕背景顏色 */
    			color: black; /* 設置文字顏色 */
    			border: 2px solid #ffffff; /* 淡藍色邊框 */
    			border-radius: 5px; /* 添加圓角 */
    			cursor: pointer; /* 鼠標移上去時顯示為指標 */
			}
 		</style>";
    echo $html;
	
    global $post;
    #$idname["Mon"] 更新為包含 "Mon1" 到 "Mon9" 的陣列
    $idname = array(
	"Mon" => [],
	"Tues" => [],
	"Wed" => [],
	"Thur" => [],
	"Fri" => [],
	"Sat" => [],
	"Sun" => [],
	);
?>
	<div id="classroom-metabox" class='classroom-metabox'>
	<?php wp_nonce_field('save_math_classroom', 'math_classroom_nonce'); ?>
	<input type="hidden" name="selected_cells" id="selected_cells" value="">
	<table id="schedule">
	<tr>
	<td class="schedule-cell schedule-row-title">星期</td>
<?php
	for($i=1;$i<=9;$i++){
    	echo '<td class="schedule-cell schedule-col-title">';
    	if ($i < 5){
        	echo "D"."$i ";
    	}
    	elseif ($i>5){
        	$t = $i-1;
        	echo "D"."$t ";
    	}
    	else{
        	echo "DN ";
    	}
    	echo "</td>\n";
	}
?>
	</tr>
<?php
	foreach($idname as $key => $value){
		for($i=1;$i<=9;$i++){
    		if ($i<5){
                $idname["$key"][]=$key."$i";
            }
            elseif ($i>5){
                $t = $i-1;
                $idname["$key"][]=$key."$t";
            }
            else{
                $idname["$key"][]=$key."N";
            }
        }
    $value = $idname["$key"]; #更新value,key為MON,TUE...等
    $el_meta = get_post_meta($post->ID, "$key", true );
    $course=[];
    for($i=0;$i<=8;$i++){ #為了重新點選舊教室時能夠出現
    #檢查是否為空
    #現在 $value 是 ["Mon1", "Mon2", ..., "Mon9"] 這樣的一個陣列
    	$course[] = !empty($el_meta["$value[$i]"]) ? $el_meta["$value[$i]"] : ' '; #course[]為長度為9之陣列,儲存post meta中MON之一天的課程
    	}
    	echo "<tr>";
    	echo "<td class='schedule-cell schedule-row-title'>".$key.":</td>";
    # 輸出表格
		for($i=0;$i<=8;$i++){
        	echo "<td class = 'schedule-cell'>";
			if ($i<4){
				echo "<div data-day='".$key."' data-period= 'D".($i+1)."' class='schedule-content'>";
      		}
      		elseif ($i>4){
        		echo "<div data-day='".$key."' data-period= 'D".($i)."' class='schedule-content'>";
      		}
      		else{
          		echo "<div data-day='".$key."' data-period= 'DN' class='schedule-content'>";
      		}
	  #將課程資料放入 <div class='schedule-content'>
      		echo "<span id='".$value[$i]."' class='class-name'>".$course[$i]."</span>"; #顯示"$course[$i]"
     		echo "<input type='hidden' name='".$value[$i]."' value='". $course[$i] ."'>";
	  		echo "</div>";
      		echo "</td>";
     		}
    echo "</tr>";
   }
?>

	</table>
	<button id="add-class">Add Class</button>
	</div>
 
<script> //這段 JavaScript 代碼實現了在網頁上動態選擇教室時段並為其指定課程名稱的功能
document.addEventListener('DOMContentLoaded', function() {
    const schedule = document.getElementById('schedule');
    const addClassButton = document.getElementById('add-class');
    let selectedCells = [];
    // 設定二維陣列的行數和列數
		const rows = 7;
		const cols = 9;

    schedule.addEventListener('click', function(event) {
        const target = event.target.closest('.schedule-content'); // 找到最接近的包含 .schedule-content 類別的元素
        if (target && target.classList.contains('schedule-content')) { //檢查被點擊的元素是否是表格單元格（<td>）
            target.classList.toggle('selected'); //切換單元格的選中狀態（背景顏色變灰）
            const day = target.getAttribute('data-day');
            const period = target.getAttribute('data-period');
            // Manage selected cells
            const cellIndex = selectedCells.findIndex(cell => cell.day === day && cell.period === period);
            //如果單元格已經在 selectedCells 中，則從陣列中移除；否則，將其添加到 selectedCells 中
            if (cellIndex > -1) {
                selectedCells.splice(cellIndex, 1);
            } else {
                selectedCells.push({ day, period, element: target });
            }
		//alert(day+','+period+','+cellIndex); //???
        }
        else{
			alert("無效的操作");
		}
    });
    
	function updateInputValue(className, day, period) {
    	let s = day + period[1]; // 使用 let 允許重新賦值
    	let spanSelector = '#' + s;  //<span>'s id, MON1,MON2...,MON9
    	s = 'input[name="' + s + '"]'; // 更新 s 變數的值
		//console.log(`seletor value: ${s}`);
		//const inputs = document.querySelectorAll('input[type="hidden"]');
		//inputs.forEach(i => {
		//	console.log(`Found Input: ${i.name} and ${i.value}`)
		//});
    	const input = document.querySelector(s);
    	const span = document.querySelector(spanSelector);
    	if (input && span) {
        	//console.log(`Updating input value: ${className}`);
        	input.value = className;
        	span.textContent = className;
        	//console.log(`Input value updated: ${input.value}`);
        	//console.log(`Input in DOM: ${input.outerHTML}`);
        	//console.log(`Span in DOM: ${span.outerHTML}`);
    	}
		else{
			//console.log(`not found`);
		}
    }


    // Add class button event
    addClassButton.addEventListener('click', function() { //add class button點擊後儲存至資料庫
    //var count = 0;
    //selectedCells.forEach(cell => {
     //  count = count + 1;
     //  alert(count + ' cell: ' + (selectedCells[count-1] ? JSON.stringify(selectedCells[count-1]) : 'No cell'));
    //});
    event.preventDefault(); // Prevent default button action (form submission)
        if (selectedCells.length > 0) {
            const className = prompt('Enter class name:');
            if (className) {
            //const dd = document.querySelector('td');
                selectedCells.forEach(cell => {
                //迭代所有選中的單元格，將課程名稱填充到單元格中，並移除選中狀態                    
                    //alert(JSON.stringify(cell.element.textContent)); //為什麼只出現一個???因為input
                    updateInputValue(className, cell.day, cell.period);
					//cell.element.textContent = className; //這邊會把input tag移除掉
                    cell.element.classList.remove('selected'); //移除單元格的選中狀態
                    // Update hidden input value
                    //const input = cell.element.querySelector('input[type="hidden"]');
                    //input.value = className;            
                   
                });
                selectedCells = [];
            }
        } else {
            alert('請先選擇時段'); //如果沒有選中任何單元格，彈出提示框告訴用戶需要先選擇時段
        }
 	});
});
</script>
<script>
	function adjustHeights() { //依據輸入字串大小動態調整單元格的高度
    	const rows = document.querySelectorAll('#schedule tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('.schedule-content'); //DOM tree以row為root之subtree
                let maxHeight = 0;

                // 找到最大高度
                cells.forEach(cell => {
                    const height = cell.getBoundingClientRect().height;
                    if (height > maxHeight) {
                        maxHeight = height;
                    }
                });

                // 設置所有單元格為最大高度
                cells.forEach(cell => {
                    cell.style.height = maxHeight + 'px';
                });
            });
        }

    // 調整高度
	adjustHeights();

    // 在窗口調整大小時重新調整高度
    window.addEventListener('resize', adjustHeights);
</script>
 

<?php

wp_nonce_field( 'meta-box-save', 'math-classroom-list' );
}


add_action('add_meta_boxes', 'math_register_meta_box');

function math_save_meta_box( $post_id ){ //這段程式碼的主要功能是將課程時間表的資料保存到 post meta 中
	 //console.log(JSON.stringify($_POST));
     $idname = array(
        "Mon" => [],
        "Tues" => [],
        "Wed" => [],
        "Thur" => [],
        "Fri" => [],
        "Sat" => [],
        "Sun" => [],
        );
  if ( ( get_post_type( $post_id ) == 'math_classroom' )){
    wp_verify_nonce( 'meta-box-save', 'math-classroom-list' );
    foreach($idname as $key => $value){
        $course=[];
        for($i=1;$i<=9;$i++){
            if ($i<5){
                $idname["$key"][]=$key."$i";
            }
            elseif ($i>5){
                $t = $i-1;
                $idname["$key"][]=$key."$t";
            }
            else{
                $idname["$key"][]=$key."N";
            }
        }
        $value=$idname["$key"];
        
        for($i=0;$i<=8;$i++){
        $course[$value[$i]]=sanitize_text_field($_POST[$value[$i]]);
        }
    update_post_meta( $post_id, $key, $course );
   }
  }
}
add_action('save_post', 'math_save_meta_box');


/*function classroom_course_display($atts){ //這段程式碼的主要目的是透過短代碼 [course_table room_no="教室編號"] 顯示某教室的課程時間表
  $idname = array(
        "Mon" => [],
        "Tues" => [],
        "Wed" => [],
        "Thur" => [],
        "Fri" => [],
        "Sat" => [],
        "Sun" => [],
        );
   foreach($idname as $key => $value){
     for($i=1;$i<=9;$i++){
            if ($i<5){
                $idname["$key"][]=$key."$i";
            }
            elseif ($i>5){
                $t = $i-1;
                $idname["$key"][]=$key."$t";
            }
            else{
                $idname["$key"][]=$key."N";
            }
        }
        $value=$idname["$key"];
   }

  $a=shortcode_atts(array('room_no' => "MA405"),$atts); //$atts是短代碼接收的屬性，例如'room_no' => "MA307"，此行設定預設值MA405

  $args = array('post_type' => 'math_classroom',
          'title' => $a['room_no'] ); #shortcode要打上"room_no"= MA405

  $result = new WP_Query( $args ); #實體化類別
  $output=' ';
  if ($result->have_posts()){
    $output.='<div class="classroom-metabox">
             <table>
             <tr>
             <td>" "</td>'; #table2的旁邊有""
    for($i=1;$i<=9;$i++){
    #輸出D1、D2...
    #有沒有辦法增加DN?
        $output=$output . "<td>D"."$i"."</td>";
     }
    $output=$output . "</tr>";
    while( $result->have_posts()){
     $result->the_post();
     $post_id = get_the_ID();
     $post_title = get_the_title();
     $post_status = get_post_status();
     if ($post_status != 'draft'){
     foreach($idname as $key => $value){
       $el_meta = get_post_meta( $post_id, "$key",true); //key為MON,TUE...
       $output=$output . "<tr><td>".$key.":</td>";
       for($i=0;$i<=8;$i++){
        $output=$output. "<td>".$el_meta["$value[$i]"]."</td>"; //value[1]為MON1之課
       }
      $output=$output. "</tr>";
     }
   $output=$output."</table></div>";
   wp_reset_postdata();
   return $output;
   }
  }
  } else {
      return "<p><em>請輸入教室編號</em></p>";
    }
}

add_shortcode( 'course_table', 'classroom_course_display');*/

function add_classroom_inline_styles() { //添加 classroom_course_display()所需之css style
    $custom_css = "
    #em_display_classroom_metabox {
        width: 90%;
        margin: 0 auto;
        text-align: center;
        background-color: #f0ffff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		overflow: auto; /* 允許滾動，防止內容超出容器 */
	}
	
	#em_display_schedule{
		width: 100%; /* 確保表格佔據 div 的全寬 */
        margin: 0 auto; /* 確保表格在 div 中居中 */
        border-collapse: collapse; /* 合併邊框 */
		border: 2px solid #dcdcdc; /* 添加邊框並設置顏色 灰 */
		table-layout: auto;/* td大小動態調整 */
	}

	.em_display_schedule_cell{
		border: 2px solid #dcdcdc; /* 單個方框的邊框顏色和寬度 */
		justify-content: space-around; /*使子元素距離均勻分布*/
        padding: 10px; /* 添加內邊距 */
	}
		
	.em_display_schedule_col_title {
        border: 2px solid #dcdcdc; /* 單個方框的邊框顏色和寬度 */
        padding: 10px; /* 內邊距，讓內容和邊框之間有空間 */
        justify-content: center; /* 內容水平居中 */
        align-items: center; /* 內容垂直居中 */
    	height: 10px; /* 設置特定高度 */
		font-weight: bold; /* 加粗字體 */
		font-size: 16px; /* 設置字體大小 */
	}	
	
	.em_display_schedule_row_title {
        border: 2px solid #dcdcdc; /* 添加邊框並設置顏色 */
		padding: 10px; /* 添加內邊距 */
		justify-content: center; /* 內容水平居中 */
		align-items: stretch; /* 垂直居中對齊子元素 */
		font-weight: bold; /* 加粗字體 */
		font-size: 16px; /* 設置字體大小 */
	}";
    wp_add_inline_style('wp-block-library', $custom_css);
}
add_action('wp_enqueue_scripts', 'add_classroom_inline_styles');

function classroom_course_display() { //在目前所在的post前端顯示出目前所在post之課表
	
    $idname = array(
        "Mon" => [],
        "Tues" => [],
        "Wed" => [],
        "Thur" => [],
        "Fri" => [],
        "Sat" => [],
        "Sun" => [],
    );
    foreach ($idname as $key => $value) {
        for ($i = 1; $i <= 9; $i++) {
            if ($i < 5) {
                $idname["$key"][] = $key . "$i";
            } elseif ($i > 5) {
                $t = $i - 1;
                $idname["$key"][] = $key . "$t";
            } else {
                $idname["$key"][] = $key . "N";
            }
        }
        $value = $idname["$key"];
    }

    // 自動取得當前教室文章的標題
    $post_id = get_the_ID();
    $post_title = get_the_title($post_id);

    if (!$post_id || get_post_type($post_id) != 'math_classroom') {
        return "<p><em>無法顯示課表，請確定文章類型為math_classroom</em></p>";
    }

    // 查詢該教室的課表
    $output = '<div id="em_display_classroom_metabox">
               <table id="em_display_schedule">
               <tr>
               <td class="em_display_schedule_cell">星期</td>';
	
	for($i=1;$i<=9;$i++){// 輸出D1、D2... 並且加上 DN
    		if ($i<5){
                $output .= "<td class='em_display_schedule_cell em_display_schedule_col_title'>D" . "$i" . "</td>";
            }
            elseif ($i>5){
                $t = $i-1;
                $output .= "<td class='em_display_schedule_cell em_display_schedule_col_title'>D" . "$t" . "</td>";
            }
            else{
				$output .= "<td class='em_display_schedule_cell em_display_schedule_col_title'>D" . "N" . "</td>";//here
            }
        }
    $output .= "</tr>";

    foreach ($idname as $key => $value) {
        $el_meta = get_post_meta($post_id, "$key", true); // 獲取星期的排課
        $output .= "<tr><td class='em_display_schedule_cell em_display_schedule_row_title'>" . $key . ":</td>";
        for ($i = 0; $i <= 8; $i++) {
            $output .= "<td class='em_display_schedule_cell'>" . ($el_meta["$value[$i]"] ?? '') . "</td>"; // 如果沒有資料，顯示空白
        }
        $output .= "</tr>";
    }

    $output .= "</table></div>";
    
    return $output;
}

// 將課表自動嵌入到文章內容中
function add_classroom_course_table_to_content($content) {
    if (is_singular('math_classroom')) { // 確認是 math_classroom 類型文章
        $content .= classroom_course_display();
    }
    return $content;
}

add_filter('the_content', 'add_classroom_course_table_to_content'); 

/*function disallow_posts_with_same_title($messages) { //這段程式碼的目的是在保存 math_classroom 類型的文章時，檢查該文章的標題是否已經存在
    global $post;
    global $wpdb ;
    $title = $post->post_title;
    $post_id = $post->ID ;
    $wtitlequery = "SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'math_classroom' AND post_title = '{$title}' AND ID != {$post_id} " ;
 
    $wresults = $wpdb->get_results( $wtitlequery) ;
 
    if ( $wresults ) {
     
        $error_message = 'This title is already used. Please choose another';
        add_settings_error('post_has_links', '', $error_message, 'error');
        settings_errors( 'post_has_links' );
        $post->post_status = 'draft';
        wp_update_post($post);
        return;
     
      $messages['math_classroom'][6] = 'This title is already used. Please choose another';
      $post->post_status = 'draft';
      wp_update_post($post);
      return $messages;
    }
    return $messages;

}
add_filter('post_updated_messages', 'disallow_posts_with_same_title');*/

function disallow_posts_with_same_title($messages) { //這段程式碼的目的是在保存 math_classroom 類型的文章時，檢查該文章的標題是否已經存在
    global $wpdb;

    // 確認當前文章是否是 math_classroom 類型
    if (get_post_type() === 'math_classroom') {
        $post_id = get_the_ID();
        $title = get_the_title($post_id);

        // 檢查是否有其他相同標題的已發布文章
        $wtitlequery = $wpdb->prepare(
            "SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'math_classroom' AND post_title = %s AND ID != %d",
            $title,
            $post_id
        );

        $wresults = $wpdb->get_results($wtitlequery);

        // 如果有相同標題的文章，則顯示錯誤並將文章狀態設置為草稿
        if ($wresults) {
            // 設定自訂錯誤訊息
            $messages['math_classroom'][6] = 'This title is already used. Please choose another.';
            
            // 將文章狀態設置為草稿
            wp_update_post(array(
                'ID' => $post_id,
                'post_status' => 'draft'
            ));

            // 顯示錯誤訊息
            add_action('admin_notices', function() {
                echo '<div class="error"><p>This title is already used. Please choose another.</p></div>';
            });
        }
    }

    return $messages;
}

add_filter('post_updated_messages', 'disallow_posts_with_same_title');



//require_once plugin_dir_path(__FILE__) . 'user_booking.php';
//require_once plugin_dir_path(__FILE__) . 'user_regist.php';
//require_once plugin_dir_path(__FILE__) . 'ljt_user_booking.php';
?>