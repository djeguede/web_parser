<?php
  require("simple_html_dom.php");
  header('Content-Type: text/html; charset=utf');
  
  $anchors=[];
  $exist = false;
  $file = fopen("test.txt",'w');
  
  
  function url_gathering($url = 'http://dami.ru/index.php/shopping'  )
  {
	 global $anchors, $exist, $file;
	
	//$categoryList = $html->find('a[title]');
	$categoryList[0] = "/index.php/shopping/dopolneniya-k-partam"; 
	$categoryList[1] = "/index.php/shopping/detskaya-skladnaya-mebel"; 
	$categoryList[2] = "/index.php/shopping/detskie-modulnye-stenki";
	$categoryList[3] = "/index.php/shopping/sanki-i-ledyanki"; 
	$categoryList[4] = "/index.php/shopping/molberty"; 
	$categoryList[5] = "/index.php/shopping/rastushchie-party";    
	$categoryList[6] = "/index.php/shopping/tumby" ;
	
		foreach($categoryList as $categ)
		{
			$linkC = "http://dami.ru".$categ;
			
			$uagent='Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
			$ch1 = curl_init();		
			curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch1, CURLOPT_HEADER, 0);
			curl_setopt($ch1, CURLOPT_URL, $linkC );
			curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
			curl_setopt($ch1, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
			curl_setopt($ch1, CURLOPT_USERAGENT, $uagent);  // useragent
			curl_setopt($ch1, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
			curl_setopt($ch1, CURLOPT_TIMEOUT, 120);        // таймаут ответа
			curl_setopt($ch1, CURLOPT_MAXREDIRS, 10);
			
			$result= curl_exec($ch1);
			curl_close($ch1);
	  
	  
			$atable=str_get_html($result);
			$itemList = $atable->find('a');
			
			foreach($itemList as $item)
			{
				if( preg_match('/detail/i', $item->href) )
				{
					$link= 'http://dami.ru'.$item->href;
					//echo $link."\n";
					$exist = false;
			
					for($i=0; $i<count($anchors); $i++)
					{
						if($link==$anchors[$i])
						{
							$exist=true;
						}
					}
			
					if( !$exist )
					{
						$anchors[]=$link;	
						echo $link."\n";
						fwrite($file, $link."\n");
					}
			
				}
			}
			
		}
	
	
  }	
	
	 url_gathering();
	
	
   $dami_parsed =fopen("dami_parsed.csv", 'w');
   //**************Connect to  server
   $servername = "localhost";
   $username = "infomi94_1";
   $password = "infomi94_1";
   $dbname = "infomi94_1";
   
   	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$categ_id['Растушие парты']=17;
	$categ_id['Складная мебель']= 59;
	$categ_id['Модульные стенки']= 60;
	$categ_id['Зимные товары']= 61;
	$categ_id['Тумбы']=62;
	$categ_id['Аксессуары']=33;
	$categ_id['Мольберты']=63;
	

    //************Connect to Server 
   
  foreach($anchors as $item )
  {
	$uagent='Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
	$ch =curl_init();
			
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_URL, $item );
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
	curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
	curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			
	$rlt= curl_exec($ch);
	curl_close($ch);
	
	$htm = str_get_html($rlt);
	
	if ( strpos($item, 'rastushchie-party') ) $category = 'Растушие парты';
	if ( strpos($item, 'detskaya-skladnaya-mebel') ) $category = 'Складная мебель';
	if ( strpos($item, 'detskie-modulnye-stenki') ) $category = 'Модульные стенки';
	if ( strpos($item, 'sanki-i-ledyanki') ) $category = 'Зимные товары';
	if ( strpos($item, 'tumby') ) $category = 'Тумбы';
	if ( strpos($item, 'dopolneniya-k-partam') ) $category = 'Аксессуары';
	if ( strpos($item, 'molberty') ) $category = 'Мольберты';
	
	//$category = $htm->find('div h3.moduletitle', 0)->plaintext;
	$name =$htm->find('div h1.moduletitle', 0)->plaintext;
	$content= $htm->find('div [class=product-short-description] ', 0)->plaintext;
	$image = $htm->find('div[class=main-image]',0)->find('a',0)->href;
	$price = $htm->find('div [class=PricesalesPrice] ',0 )->plaintext;
	$description = $htm->find('div [class=product-description] ', 0)->plaintext;

	$str[]= $category; 
   	$str[]= $name ;
	$str[]=	$content; 
	$str[]=$image;
	$str[]= $price;
	$str[]=	$description;
	fputcsv($dami_parsed, $str);
	
		//Insert into det_product
		$sql1 = "INSERT INTO det_product(model, image, price )
		VALUES ( $name, $image, $price)";
		if ($conn->query($sql1) === TRUE) {
			echo "New record created successfully";
			$lastprod_id = $conn->insert_id;
		} 
		else 
		{
			echo " Insert into det_product >> Error: " . $sql1 . "<br>" . $conn->error;
		}
		
		//Insert into det_product_description
		$sql2 = "INSERT INTO det_product_description(product_id,language_id, name, description, meta_description  )
		VALUES ( $lastprod_id , '1' ,$name, $description, $content ),( $lastprod_id , '2', $name, $description, $content )";
		if ($conn->query($sql2) === TRUE) {
			echo "New record created successfully";
		} 
		else 
		{
			echo " Insert into det_product_description >> Error: " . $sql2 . "<br>" . $conn->error;
		}
		
		//Insert into det_product_to_category
		$sql3 = "INSERT INTO det_product_to_category(product_id, category_id, main_category  ) VALUES($lastprod_id, $categ_id[$category] , '1' )";
		if ($conn->query($sql3) === TRUE) {
		echo "New record created successfully";
		} 
		else 
		{
			echo " Insert into det_product_to_category >> Error: " . $sql3 . "<br>" . $conn->error;
		}
  }
  
  	$conn->close(); //****** close connection
  fclose($dami_parsed); 
  fclose($file);
?>