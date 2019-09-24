<?php
error_reporting(E_ALL^E_NOTICE);
function get_field_data($table_name, $field, $unique_id, $value)
{
	global $conn;
	//QUERY ROLE
	$q_get_data = mysqli_query($conn,  "SELECT ".$field." FROM ".$table_name." where ".$unique_id." = '".$value."'");
	$d_data = mysqli_fetch_assoc($q_get_data);
	
	$field_result = $d_data[$field];
	
	return $field_result;	
}

function search_form($rolenya)
{
	if($rolenya == "KASIR")
	{
		$place = "Penjualan Belum Lunas";
	}
	elseif($rolenya == "GUDANG")
	{
		$place = "Pembelian Belum Lunas";
	}
	else
	{
		$place = "Pembelian/Penjualan Belum Lunas";
	}
	echo '
	<div class="nav-search" id="nav-search">
		<form class="form-search">
			<span class="input-icon">
				<input type="text" placeholder="'.$place.'" class="nav-search-input" id="nav-search-input" autocomplete="off" />
				<i class="ace-icon fa fa-search nav-search-icon"></i>
			</span>
		</form>
	</div><!-- /.nav-search -->';
}

function kas_flow_dtl($tipe, $source,$value_source, $nilai, $ket, $idnya)
{
	global $conn;
	$tgl_true = date('Y/m/d H:i:s', time());
	$tgl = date("Y-m-d", time());

	//input master
	$q_get = mysqli_query($conn, "select id_kasflow as maxid from kasflow where date_format(created_date,'%Y-%m-%d') = '$tgl' order by id_kasflow desc");
	$r_get = mysqli_num_rows($q_get);
	if($r_get > 0)
	{
		$d_get = mysqli_fetch_assoc($q_get);
		$maxid = $d_get['maxid'];
		$saldo_ak = get_field_data('kasflow','saldo_skrg','id_kasflow',$maxid);
		if($tipe == "plus")
		{
			$nilaimasuk_ak = get_field_data('kasflow','nilaimasuk','id_kasflow',$maxid);
			$nilaimasukfinal = $nilaimasuk_ak + $nilai;
			$saldo_final = $saldo_ak + $nilai;
			mysqli_query($conn, "update kasflow set 
						nilaimasuk = '".$nilaimasukfinal."',
						saldo_skrg = '".$saldo_final."',
						modified_date = '$tgl_true',
						modified_by = '$idnya'
						where id_kasflow = '$maxid'");
		}
		else
		{
			$nilaikeluarak = get_field_data('kasflow','nilaikeluar','id_kasflow',$maxid);
			$nilaikeluarfinal = $nilaikeluarak + $nilai;
			$saldo_final = $saldo_ak - $nilai;
			mysqli_query($conn, "update kasflow set 
						nilaikeluar = '".$nilaikeluarfinal."',
						saldo_skrg = '".$saldo_final."',
						created_by = '$idnya', 
						created_date='$tgl_true',
						modified_date = '$tgl_true',
						modified_by = '$idnya'
						where id_kasflow = '$maxid'");
		}
	}
	else
	{	
		$q_get2 = mysqli_query($conn, "select id_kasflow, saldo_skrg from kasflow order by id_kasflow desc");
		$r_get2 = mysqli_num_rows($q_get2);
		if($r_get2 == 0)
		{
			$saldo_kmrn = 0;
			
		}
		else
		{	
			$d_get2 = mysqli_fetch_assoc($q_get2);
			$saldo_kmrn = 1 * $d_get2['saldo_skrg'];
			$maxid =  $d_get2['id_kasflow'];
		}
		if($source == "id_cashin" || $source == "kode_trnsjl")
		{
			$saldo_skrg = $saldo_kmrn + $nilai;
			mysqli_query($conn, "insert into kasflow set 
							nilaimasuk = '".$nilai."',
							saldo_sblm = '".$saldo_kmrn."',
							saldo_skrg = '".$saldo_skrg."',
							created_by = '$idnya', 
							created_date='$tgl_true',
							modified_date = '$tgl_true',
							modified_by = '$idnya'");
		}
		else
		{
			$saldo_skrg = $saldo_kmrn - $nilai;
			mysqli_query($conn, "insert into kasflow set 
							nilaikeluar = '".$nilai."',
							saldo_sblm = '".$saldo_kmrn."',
							saldo_skrg = '".$saldo_skrg."',
							created_by = '$idnya', 
							created_date='$tgl_true',
							modified_date = '$tgl_true',
							modified_by = '$idnya'");
		}

		$q_maxid =  mysqli_query($conn, "select id_kasflow as maxid, saldo_sblm from kasflow where date_format(created_date,'%Y-%m-%d') = '$tgl' order by id_kasflow desc");
		$d_get2 = mysqli_fetch_assoc($q_maxid);
		$maxid =  $d_get2['maxid'];
		$saldo_kmrn2 =  $d_get2['saldo_sblm'];
		$saldo_ak = $saldo_kmrn2;
	}
	//input detail
	if($tipe == "plus")
	{
		$saldo_final = $saldo_ak + $nilai;
		mysqli_query($conn, "insert into kasflow_dtl set 
							id_kasflow = '$maxid',
							".$source." = '".$value_source."',  
							nilai = '".$nilai."',
							saldo_sblm = '".$saldo_ak."',
							saldo = '".$saldo_final."',
							ket = '".$ket."',
							tipe = '".$tipe."',
							created_by = '$idnya', 
							created_date='$tgl_true',
							modified_date = '$tgl_true',
						 	modified_by = '$idnya'");
	}
	else
	{
		$saldo_final = $saldo_ak - $nilai;		
		mysqli_query($conn, "insert into kasflow_dtl set 
							id_kasflow = '$maxid',
							".$source." = '".$value_source."',  
							nilai = '".$nilai."',
							saldo_sblm = '".$saldo_ak."',
							saldo = '".$saldo_final."',
							ket = '".$ket."',
							tipe = '".$tipe."',
							created_by = '$idnya', 
							created_date='$tgl_true',
							modified_date = '$tgl_true',
						 	modified_by = '$idnya'");
	}
}

function get_sandi($value)
{
	global $conn;
	
	$pjg = strlen($value);
	$hasil='';
	for($j=0;$j < $pjg; $j++)
	{
		$get = mysqli_query($conn, "select sandi from sandi where asli = '$value[$j]'");
		$r_get = mysqli_num_rows($get);
		if($r_get == 0)
		{
			$hasil .= "-";						
		}
		else
		{	
			$hsl = mysqli_fetch_assoc($get);
			$hasil .= $hsl['sandi'];
		}
	}
	return $hasil;
}

function get_current_dir(){
  $file = realpath(dirname(__FILE__)); // Current file path
	$patt = '/\/path\/to\/public\/directory/'; // Regex pattern for all non-public directories
	$url = preg_replace($patt,'', $file); // Take out those non-public directories
	return $url; // Return the URL with a trailing slash
}
class lenmEnc {
	private $skey 	= "w3g1V3YouTh3b3sT4nD54T1sf13Dy0UL";
    public  function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }	
    public  function encode($value){ 
	    if(!$value){return false;}
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext)); 
    }
}

function getMac()
{
	exec("ipconfig /all", $output);
	foreach($output as $line)
	{
		if (preg_match("/(.*)Physical Address(.*)/", $line))
		{
			$mac = $line;
			$mac = str_replace("Physical Address. . . . . . . . . :","",$mac);
			$macs .= "\t";
			$macs .= $mac;
		}
		if (preg_match("/(.*)Host Name(.*)/", $line))
		{
			$host = $line;
			$host = str_replace("Host Name . . . . . . . . . . . . :","",$host);
			$hosts .= "\t";
			$hosts .= $host;
		}
		if (preg_match("/(.*)Description(.*)/", $line))
		{
			$descrip = $line;
			$descrip = str_replace("Description . . . . . . . . . . . :","",$descrip);
			$descrips .= "\t";
			$descrips .= $descrip;
		}
	}
	return $macs."\n".$hosts."\n".$descrips;
}
/*
$ec = new lenmEnc();
///FROM CLIENT COMP
$hasil = getMac();
$hasil = trim($hasil);
$fullhasil = explode("\n",$hasil);

///FRLC
$ceklc = file_exists(get_current_dir()."/images/up2.jpg");
if(!$ceklc){
	header("location:http://www.lempos.com");
	exit;
}
$myfile = fopen(get_current_dir()."/images/up2.jpg", "r");
$dt = fread($myfile,filesize(get_current_dir()."/images/up2.jpg"));
fclose($myfile);
$dtlc = explode("[/----/]",$dt);
$dtlc = explode("[/--/]",$dtlc[0]);

if($ec->encode(md5(trim($fullhasil[1]))) != $dtlc[1])
{
	header("location:http://www.lempos.com");
	exit;
}
$pecahmac = explode("\t",$fullhasil[0]);
$hslmac = array_map('trim', $pecahmac);
foreach($hslmac as $xx){
	$yy .= $ec->encode($ec->encode(md5($xx))).'[/--/]';
}
$hslmac = explode("[/--/]",$yy);
if (!in_array($dtlc[0], $hslmac)) {
    header("location:http://www.lempos.com");
	exit;
}*/
?>