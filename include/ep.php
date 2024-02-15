<?php
  global $_xf;
  
   $_xf[3]=array(2,0,1,);
   $_xf[4]=array(1,2,0,3);
   $_xf[5]=array(2,4,0,3,1);
   $_xf[6]=array(3,5,2,0,4,1);
   $_xf[7]=array(2,4,0,6,1,5,3);
   $_xf[8]=array(6,7,3,2,0,5,4,1);
   $_xf[9]=array(6,7,8,3,5,5,0,1,2);
   $_xf[10]=array(8,9,6,5,4,3,2,7,0,1);
   $_xf[11]=array(9,10,2,3,4,5,6,7,8,0,1);
   $_xf[12]=array(10,11,9,5,6,3,4,8,7,2,0,1);
   $_xf[13]=array(11,9,2,3,7,5,8,4,6,1,10,0,12);
   $_xf[14]=array(11,9,2,3,7,5,8,4,6,1,10,0,12,13);
   $_xf[15]=array(11,9,2,3,7,5,8,4,6,1,10,0,12,13,14);
   $_xf[16]=array(14,12,3,2,13,5,7,6,8,10,9,11,1,4,0,15);
   $_xf[17]=array(14,12,3,17,13,5,7,6,8,10,9,11,1,4,0,1,2);
function decode_pas($pass)
{
   global $_xf;
   if (trim($pass)==''){return($pass);}
   $len=strlen(trim($pass));                                    
   $xpass=substr($pass,0,2).substr($pass,3,$len-4);

   
   $pass=$xpass;
	$vtmp=str_split($pass);
	$vpas=array_reverse($vtmp);
  $len=strlen($pass); 	

    $npass='';
    $vxf=$_xf[$len];
 
	  for ($j=0;$j<=count($vxf)-1;$j++)
	  {
		 	  $vpn[$j]=$vpas[$_xf[$len][$j]]; 
      }

	$npass=implode($vpn);
    
    
  return($npass);
}


function encode_pas($pass)
{
    global $_xf;

  //convertir clave a ascii y
if (trim($pass)==''){return($pass);}  
  $pass=trim($pass);
  $vpas=str_split($pass);
  
  $npass='';
  $len=count($vpas);

  for ($i=0;$i<=count($vpas)-1;$i++)
  {
	
	  $vpn[$i]=$vpas[$_xf[$len][$i]];
	 
      $vpx[$i]=chr($vpn[$i]);	
      $npass=$vpn[$i].$npass;	  
  }


	$npas01=ord(substr($npass,2,1));
	$npas02=ord(substr($npass,-1));
	
	
	$pas01=($npas01>90)?$npas01-10:$npas01+10;
	$pas02=($npas02>57)?$npas02+10:$npas01+5;
	
	
   $npass=substr($npass,0,2).chr($pas01).substr($npass,2).chr($pas02);
  return($npass);
  

  
  
}

?>