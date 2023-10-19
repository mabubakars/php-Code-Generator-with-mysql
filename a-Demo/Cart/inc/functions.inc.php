<?php
function search($text){
	$where = "";
	if(isset($_SESSION['category'])){
	$categories = $_SESSION['category'];
		if ($categories>=0) {
			// Parse the cart session variable
			$check = 0;
			if($categories>0){
				$where = " where `books`.`categoryid`=".$categories;
				if($text != ""){
					$where.= " and title like'%".$text."%'";
				}
			}else{
				$where = " where title like'%".$text."%'";
			}
			/*$categories = explode(',',$categories1);
			foreach ($categories as $category){
				if($check = 0){
					$where .= "'".$category."'";
					$check = 1;	
				}else{
					$where .= " and category='".$category."'";
				}
			}*/	
		}
	}else{
		if($text != ""){
			$where = " where title like'%".$text."%'";
		}
	}
	return $where;
}
function writeShoppingCart() {
	if(isset($_SESSION['cart'])){
	$cart = $_SESSION['cart'];
		if (!$cart) {
			return 0;
		} else {
			// Parse the cart session variable
			$items = explode(',',$cart);
			$s = (count($items) > 1) ? 's':'';
			count($items);
			return count($items);
		}
		
	}else{
		return 0;
	}
}

function saveCart($orderId){
	global $db;
	$cart = $_SESSION['cart'];
	if ($cart) {
		$items = explode(',',$cart);
		$contents = array();
		foreach ($items as $item) {
			$contents[$item] = (isset($contents[$item])) ? $contents[$item] + 1 : 1;
		}
		foreach ($contents as $id=>$qty) {
			
			$sql = 'insert into `orderdetail`(`orderId`,`bookId`,`count`)values('.$orderId.','.$id.','.$qty.')';
			mysqli_query($db,$sql);
		}
	}
}

function showCart() {
	global $db;
	$total = 0;
	$cart = $_SESSION['cart'];
	if ($cart) {
		$items = explode(',',$cart);
		$contents = array();
		foreach ($items as $item) {
			$contents[$item] = (isset($contents[$item])) ? $contents[$item] + 1 : 1;
		}
		$output[] = '<form action="cart.php?action=update" method="post" id="cart">';
		$counter = 1;
		foreach ($contents as $id=>$qty) {
			if($counter==1){
					$counter = "";
				}else{
					$counter = 1;
				}
			$sql = 'SELECT * FROM books WHERE id = '.$id;
			$result = mysqli_query($db,$sql);
			$row = mysqli_fetch_array($result);
			extract($row);
			$image = "";
			if($photo != ""){
				$image = $photo;
			}else{
				$image = 'th.jpg';
			}
			$output[] = '<div class="picscontent'.$counter.'">';
			$output[] = '<div class="prodpic">';
			$output[] = '<img src="images/'.$image.'" height="127" width="100" /></div>';
			$output[] = '<div class="prodtext">';
			$output[] = '<h2>'.$row['title'];
			$output[] = '<a href="cart.php?action=delete&id='.$row['id'].'">';
			$output[] = '<span style="float:right;">';
			$output[] = '<img src="images/remove.jpg" alt="delete" height="16" width="16" />';
			$output[] = '</a></span></h2>';
			$output[] = '<p>'.$row['Description'].'<br>';
			$output[] = '<strong>Autor:'.$row['author'].'</strong><br>';
			$output[] = '<strong>Price:$'.$row['price'].'</strong>';
			$output[] = '<strong> X <input type="text" name="qty'.$id.'" value="'.$qty.'" size="3" maxlength="3" /></strong>';
			$output[] = '<strong> = $'.($price * $qty).'</strong><br>';
			$output[] = '</p>';
			$total += $price * $qty;
			$output[] = '</div>';
			$output[] = '</div>';
		}
		$output[] = '<div class="picscontent" style="width:737px;"><p>Grand total: <strong>$'.$total.'</strong></p>';
		$output[] = '<button type="submit">Update cart</button></div>';
		$output[] = '</form>';
	} else {
		$output[] = '<h2>You shopping cart is empty.</h2>';
	}
	return join('',$output);
}

function generateHTML($row,$counter){
	extract($row);
	$image = "";
	$div = "";
	if($photo != ""){
		$image = $photo;
	}else{
		$image = 'th.jpg';
	}
	if($counter == 1){
		$div = '<div style="clear:both;"></div>';
	}
	$output[] = '<div class="picscontent'.$counter.'">';
	$output[] = '<div class="prodpic">';
	$output[] = '<img src="images/'.$image.'" height="127" width="100" /></div>';
	$output[] = '<div class="prodtext">';
	$output[] = '<h2>'.$row['title'].'</h2>';
	$output[] = '<div style="width:100%;word-wrap: break-word;">'.$row['Description'].'<br>';
	$output[] = '<strong>Autor:</strong> '.$row['author'].'<br>';
	$output[] = '<strong>Price:$</strong> '.$row['price'].'<br>';
	$output[] = '<strong>Category:</strong> '.$row['category'];
	$output[] = '<a href="cart.php?action=add&id='.$row['id'].'">';
	$output[] = '<span style="float:right;">';
	$output[] = '<img src="images/littlecart1.png" alt="Add to cart" height="20" width="20">';
	$output[] = '</span></a>';
	$output[] = '</div>';
	$output[] = '</div>';
	$output[] = '</div>';
	$output[] = $div;
	return join('',$output);
}

/**
* easy image resize function
* @param $file - file name to resize
* @param $width - new image width
* @param $height - new image height
* @param $proportional - keep image proportional, default is no
* @param $output - name of the new file (include path if needed)
* @param $delete_original - if true the original image will be deleted
* @param $use_linux_commands - if set to true will use "rm" to delete the image, if false will use PHP unlink
* @param $quality - enter 1-100 (100 is best quality) default is 100
* @return boolean|resource
*/
  function smart_resize_image($file,
                              $width = 0,
                              $height = 0,
                              $proportional = false,
                              $output = 'file',
                              $delete_original = true,
                              $use_linux_commands = false,
   $quality = 100
   ) {
      
    if ( $height <= 0 && $width <= 0 ) return false;

    # Setting defaults and meta
    $info = getimagesize($file);
    $image = '';
    $final_width = 0;
    $final_height = 0;
    list($width_old, $height_old) = $info;
	$cropHeight = $cropWidth = 0;

    # Calculating proportionality
    if ($proportional) {
      if ($width == 0) $factor = $height/$height_old;
      elseif ($height == 0) $factor = $width/$width_old;
      else $factor = min( $width / $width_old, $height / $height_old );

      $final_width = round( $width_old * $factor );
      $final_height = round( $height_old * $factor );
    }
    else {
      $final_width = ( $width <= 0 ) ? $width_old : $width;
      $final_height = ( $height <= 0 ) ? $height_old : $height;
		$widthX = $width_old / $width;
		$heightX = $height_old / $height;

		$x = min($widthX, $heightX);
		$cropWidth = ($width_old - $width * $x) / 2;
		$cropHeight = ($height_old - $height * $x) / 2;
    }

    # Loading image to memory according to type
    switch ( $info[2] ) {
      case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($file); break;
      case IMAGETYPE_GIF: $image = imagecreatefromgif($file); break;
      case IMAGETYPE_PNG: $image = imagecreatefrompng($file); break;
      default: return false;
    }
    
    
    # This is the resizing/resampling/transparency-preserving magic
    $image_resized = imagecreatetruecolor( $final_width, $final_height );
    if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
      $transparency = imagecolortransparent($image);
      $palletsize = imagecolorstotal($image);

      if ($transparency >= 0 && $transparency < $palletsize) {
        $transparent_color = imagecolorsforindex($image, $transparency);
        $transparency = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
        imagefill($image_resized, 0, 0, $transparency);
        imagecolortransparent($image_resized, $transparency);
      }
      elseif ($info[2] == IMAGETYPE_PNG) {
        imagealphablending($image_resized, false);
        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
        imagefill($image_resized, 0, 0, $color);
        imagesavealpha($image_resized, true);
      }
    }
    imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);


    # Taking care of original, if needed
    if ( $delete_original ) {
      if ( $use_linux_commands ) exec('rm '.$file);
      else @unlink($file);
    }

    # Preparing a method of providing result
    switch ( strtolower($output) ) {
      case 'browser':
        $mime = image_type_to_mime_type($info[2]);
        header("Content-type: $mime");
        $output = NULL;
      break;
      case 'file':
        $output = $file;
      break;
      case 'return':
        return $image_resized;
      break;
      default:
      break;
    }
    
    # Writing image according to type to the output destination and image quality
    switch ( $info[2] ) {
      case IMAGETYPE_GIF: imagegif($image_resized, $output); break;
      case IMAGETYPE_JPEG: imagejpeg($image_resized, $output, $quality); break;
      case IMAGETYPE_PNG:
        $quality = 9 - (int)((0.9*$quality)/10.0);
        imagepng($image_resized, $output, $quality);
        break;
      default: return false;
    }

    return true;
  }
?>
