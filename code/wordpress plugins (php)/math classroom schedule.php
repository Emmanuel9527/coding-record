<?php
/*
Plugin Name: Math ClassRoom Schedule
Plugin URI:
Description:
Version:
Author:
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
        'supports' => array('title','editor','thumbnail')
        ) 
      );
}
add_action('init', 'math_register_classroom');


function math_register_meta_box(){
    add_meta_box('math_classroom_meta', '教室資訊',
                 'math_classroom_meta_box', 'math_classroom','normal','default');
}

function math_classroom_meta_box( $post ){
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
<div class='classroom-metabox'>
<?php wp_nonce_field('save_math_classroom', 'math_classroom_nonce'); ?>
<input type="hidden" name="selected_cells" id="selected_cells" value="">
<table id="schedule">
<tr>
<td>' '</td>
<?php
for($i=1;$i<=9;$i++){
    echo "<td>\n";
    if ($i < 5){
        echo "D"."$i \n";
    }
    elseif ($i>5){
        $t = $i-1;
        echo "D"."$t \n";
    }
    else{
        echo "DN \n";
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
    $value = $idname["$key"]; #更新value
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
    $course[] = !empty($el_meta["$value[$i]"]) ? $el_meta["$value[$i]"] : '';
    }
    echo "<tr>\n";
    echo "<td>".$key.":</td>\n";
    # 輸出表格
      for($i=0;$i<=8;$i++){
      #echo "<td>\n";
      if ($i<4){
          echo "<td data-day='".$key."' data-period= 'D".($i+1)."' class='schedule-cell'>";
      }
      elseif ($i>4){
          echo "<td data-day='".$key."' data-period= 'D".($i)."' class='schedule-cell'>";
      }
      else{
          echo "<td data-day='".$key."' data-period= 'DN' class='schedule-cell'>";
      }
      echo "<span id='".$value[$i]."' class='class-name'>".$course[$i]."</span>";
		  //echo "$course[$i]"; #course[i]未知 #$value[$i]已知
      echo "<input type='hidden' name='".$value[$i]."' value='". $course[$i] ."'>";
      echo "</td>";
      #echo "</td>\n";
     }
    echo "</tr>\n";
   }
   ?>
</table>
<button id="add-class">Add Class</button>
</div>

<style>
        .schedule-cell {
            border: 1px solid #ccc;
            padding: 10px;
            cursor: pointer;
            text-align: center;
        }

        .schedule-cell.selected {
            background-color: #d3d3d3;
        }
 </style>
 
<script>
document.addEventListener('DOMContentLoaded', function() {
    const schedule = document.getElementById('schedule');
    const addClassButton = document.getElementById('add-class');
    let selectedCells = [];
    // 設定二維陣列的行數和列數
		const rows = 7;
		const cols = 9;
		//const week = ["Mon", "Tues", "Wed", "Thus", "Fri", "Sat", "Sun"];
		// 創建二維陣列
		//const twoDimArray = []; //用來設定[week][Mon1...]
		
		// 使用外層迴圈創建每一行
		//for (let i = 0; i < rows; i++) {
		 // twoDimArray[i] = week[i]; 
		
		  // 使用內層迴圈填充每一行的列
		  //for (let j = 1; j <= cols; j++) {
		    //twoDimArray[i][j] = week[i] + j; 
		  //}
		//}
		
    // Add click event to each cell
    schedule.addEventListener('click', function(event) {
        const target = event.target.closest('td'); // 確保獲取的是最近的 <td> 標籤
        if (target && target.classList.contains('schedule-cell')) { //檢查被點擊的元素是否是表格單元格（<td>）
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
    let spanSelector = '#' + s;
    s = 'input[name="' + s + '"]'; // 更新 s 變數的值
		console.log(`seletor value: ${s}`);
		const inputs = document.querySelectorAll('input[type="hidden"]');
		inputs.forEach(i => {
			console.log(`Found Input: ${i.name} and ${i.value}`)
		});
    const input = document.querySelector(s);
    const span = document.querySelector(spanSelector);
    if (input && span) {
        console.log(`Updating input value: ${className}`);
        input.value = className;
        span.textContent = className;
        console.log(`Input value updated: ${input.value}`);
        
        console.log(`Input in DOM: ${input.outerHTML}`);
        console.log(`Span in DOM: ${span.outerHTML}`);
    }
		else{
			console.log(`not found`);
		}
    }


    // Add class button event
    addClassButton.addEventListener('click', function() {
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
            alert('Please select time slots first.'); //如果沒有選中任何單元格，彈出提示框告訴用戶需要先選擇時段
        }
 });
 
 
    });
  </script>
 
 

<?php

wp_nonce_field( 'meta-box-save', 'math-classroom-list' );
}


add_action('add_meta_boxes', 'math_register_meta_box');

function math_save_meta_box( $post_id ){
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


function classroom_course_display($atts){
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

  $a=shortcode_atts(array('room_no' => "MA405"),$atts);

  $args = array('post_type' => 'math_classroom',
          'title' => $a['room_no'] ); #shrtcode要打上"room_no"=MA405

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
       $el_meta = get_post_meta( $post_id, "$key",true);
       $output=$output . "<tr><td>".$key.":</td>";
       for($i=0;$i<=8;$i++){
        $output=$output. "<td>".$el_meta["$value[$i]"]."</td>";
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

add_shortcode( 'course_table', 'classroom_course_display');

function disallow_posts_with_same_title($messages) {
    global $post;
    global $wpdb ;
    $title = $post->post_title;
    $post_id = $post->ID ;
    $wtitlequery = "SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'math_classroom' AND post_title = '{$title}' AND ID != {$post_id} " ;
 
    $wresults = $wpdb->get_results( $wtitlequery) ;
 
    if ( $wresults ) {
     /*
        $error_message = 'This title is already used. Please choose another';
        add_settings_error('post_has_links', '', $error_message, 'error');
        settings_errors( 'post_has_links' );
        $post->post_status = 'draft';
        wp_update_post($post);
        return;
     */
      $messages['math_classroom'][6] = 'This title is already used. Please choose another';
      $post->post_status = 'draft';
      wp_update_post($post);
      return $messages;
    }
    return $messages;

}
add_filter('post_updated_messages', 'disallow_posts_with_same_title');

?>