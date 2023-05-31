<?

//----------------------------------Mencari Jumlah Hari & angka dlm seminggu
function Hari($BlnSkr,$ySkr) {
	$JmlHari = date("t", mktime(0,0,0,$BlnSkr,1,$ySkr));
	$strHr = date("D", mktime(0,0,0,$BlnSkr,1,$ySkr));
	if ($strHr == "Mon") {
		 $hari = 0;
	} elseif ($strHr == "Tue") {
		 $hari = 1;
	} elseif ($strHr == "Wed") {
		 $hari = 2;
	} elseif ($strHr == "Thu") {
		 $hari = 3;
	} elseif ($strHr == "Fri") {
		 $hari = 4;
	} elseif ($strHr == "Sat") {
		 $hari = 5;
	} elseif ($strHr == "Sun") {
		 $hari = 6;
	}
	return $Hari = array ("Jml" => $JmlHari,
							"Hr" => $hari);
}

//fungsi tampil tanggal
function TglMenu() {
	$dateSkr = date('d');
	for ($g=1;$g<=31;$g++) {
		if ($g<10) {
			$val = "0" . $g;
		} else {
			$val = $g;
		}
		if ($g == $dateSkr) {
			echo "<option value='$val' selected>$g</option>";
		} else {			
			echo "<option value='$val'>$g</option>";
		}
	}
}

//Fungsi tampil bulan
function BulanMenu() {
			$monthSkr = date('m');
			$Bulan = array ("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
			for ($g=1;$g<=12;$g++) {
				if ($g<10) {
					$val = "0" . $g;
				} else {
					$val = $g;
				}
					$a = $g - 1;
					if ($g == $monthSkr) {
			      		echo "<option value='$val' selected>$Bulan[$a]</option>";
					} else {
						echo "<option value='$val'>$Bulan[$a]</option>";
					}			
			}
}

//fungsi Tampil Tahun
function TahunMenu() {
	$yearSkr = date('Y');
	for ($g=($yearSkr-1);$g<=($yearSkr+5);$g++) {
		if ($g == $yearSkr) {
			echo "<option value='$g' selected>$g</option>";
		} else {
			echo "<option value='$g'>$g</option>";
		}			
	}
}

//--------Cek Hari Libur Nasional atau Hari Minggu
function CekHariLiburMinggu($beginTimestamp) {
	$Minggu = date('D',$beginTimestamp);
	if ($Minggu == "Sun") {
		$info = "Sunday";
	} else {
		$awalDate = date('Y-m-d',$beginTimestamp);
		$aTgl = date('d',$beginTimestamp);
		$HLibur = libur($awalDate,$aTgl,$beginTimestamp);
		if ($HLibur == "HOLIDAY") {
			$info = "Holiday";
		} else {
//			$DurasiTdkMsk = $DurasiTdkMsk;
			$info = "Not";
		}
	}
	return $info;
}
?>