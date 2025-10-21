free<?php 
$gnTotal = "";
for ($i = 1; $i <= 12; $i++) { 
	$gnTotal = "gTotal_" . sprintf('%02d', $i);
	$$gnTotal = 0;
}
$gntotal_budget = 0;
$gntotal_le = 0;
foreach($mst_account[0] as $m0) { 
	
	if(count(@$mst_account[$m0->id]) >=1 ) {
		// $bgedit ="#A9A9A9";
		$bgedit ="";
		$contentedit ="false" ;
		$prsn = '';
	}else{
		$bgedit ="";
		$contentedit ="true" ;
		$prsn = '%';
	}
	

	?>
	<tr>
		<td><b><?php echo $m0->account_code . '-' .$m0->account_name; ?></b></td>
		
			<?php
			$field0 = '';
			if(!in_array($m0->id,$id_labour)) {
				$x0 = 0;
				$gnTotal ="";

				$xtotal0 = ($contentedit == 'true'  && in_array($m0->account_code,$user_akses_account)  ? number_format($m0->total_budget) : '');
				$xtotal00 = ($contentedit == 'true'  ? number_format($m0->total_le) : '');	
				$gnTotal_budget += str_replace(['.',','],'',$xtotal0) ;
				$gnTotal_le += str_replace(['.',','],'',$xtotal00) ;
				for ($i = 1; $i <= 12; $i++) { 
					$field0 = 'B_' . sprintf('%02d', $i);
					$x0 = ($contentedit == 'true'  && in_array($m0->account_code,$user_akses_account) ? number_format($m0->$field0) : '');
					
					$gnTotal = "gTotal_" . sprintf('%02d', $i);
					$$gnTotal += str_replace(['.',','],'',$x0) ;


			

					$incr0 = ($contentedit == 'true' || $m0->total_budget !=0 ? (($m0->total_le / $m0->total_budget) - 1) * 100 : '');
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'"  data-value="'.$x0.'">'.$x0.'</td>';
				}
			}else{
				foreach($total_labour as $v => $t){
					if($m0->id == $v) {
						$x0 = 0;
						for ($i = 1; $i <= 12; $i++) { 
							$field0 = 'B_' . sprintf('%02d', $i);
							$x0 =  ($contentedit == 'true'  ? number_format($t[$field0]) :'');
							$xtotal0 = ($contentedit == 'true'  ? number_format($t['total']) :'');
							$xtotal00 = ($contentedit == 'true'  ? number_format($t['total_le']) :'');

							$incr0 = ($contentedit == 'true' && $m0->total_budget !=0 ? (($m0->total_le / $m0->total_budget) -1) * 100 : '');

							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'"  data-value="'.$x0.'">'.$x0.'</td>';
						}
					}
				}
			}


			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_budget" data-name="total_budget"  data-value="'.$xtotal0.'">'.$xtotal0.'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_le" data-name="total_le"  data-value="'.$xtotal00.'">'.$xtotal00.'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right increase money-custom" data-name="increase"  data-value=""></td>';
			$gtotal0 += $m0->total_budget; 
			$jan0 += $m0->B_01;
			$feb0 += $m0->B_02;
			$mar0 += $m0->B_03;
			$apr0 += $m0->B_04;
			$may0 += $m0->B_05;
			$jun0 += $m0->B_06;
			$jul0 += $m0->B_07;
			$aug0 += $m0->B_08;
			$sep0 += $m0->B_09;
			$oct0 += $m0->B_10;
			$nov0 += $m0->B_11;
			$dec0 += $m0->B_12;
			$jan00 = $m0->B_01;
			?>
	</tr>
	<?php 

	$sTotal = "";
	for ($i = 1; $i <= 12; $i++) { 
		$sTotal = "sTotal_" . sprintf('%02d', $i);
		$$sTotal = 0;
	}
	$sTotal_budget = 0;
	$sTotal_le = 0;
	foreach($mst_account[$m0->id] as $m1) { 

		if(count(@$mst_account[$m1->id]) >=1 ) {
			// $bgedit ="#A9A9A9";
			$bgedit ="";
			$contentedit ="false" ;
			$prsn = '';
		}else{
			$bgedit ="";
			$contentedit ="true" ;
			$prsn = '%';
		}

		?>

		<tr>
			<td class="sub-1"><b><?php echo $m1->account_code . '-' .$m1->account_name; ?></b></td>
			<?php
			$field1 = '';
			if(!in_array($m1->id,$id_labour)) {
					$x1 = 0;
					$sTotal = "";

					$xtotal1 = ($contentedit == 'true'  && in_array($m1->account_code,$user_akses_account)  ? number_format($m1->total_budget) : '');
					$xtotal11 = ($contentedit == 'true'  && in_array($m1->account_code,$user_akses_account)  ? number_format($m1->total_le) : '');

					$sTotal_budget += str_replace(['.',','],'',$xtotal1) ;
					$sTotal_le += str_replace(['.',','],'',$xtotal11) ;
					$gnTotal = "";

					$gnTotal_budget += str_replace(['.',','],'',$xtotal1) ;
					$gnTotal_le += str_replace(['.',','],'',$xtotal11) ;

					for ($i = 1; $i <= 12; $i++) { 
						$field1 = 'B_' . sprintf('%02d', $i);
						$x1 = ($contentedit == 'true'  && in_array($m1->account_code,$user_akses_account) ? number_format($m1->$field1) : '');
						
						$sTotal = "sTotal_" . sprintf('%02d', $i);
						$$sTotal += str_replace(['.',','],'',$x1) ;

						$gnTotal = "gTotal_" . sprintf('%02d', $i);
						$$gnTotal += str_replace(['.',','],'',$x1) ;

						$incr1 = ($contentedit == 'true' && $m1->total_le !=0 ? (($m1->total_budget / $m1->total_le) -1) * 100 : '');

						echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field1.'" data-name="'.$field1.'" data-id="'.$m1->id_trx.'" data-value="'.$x1.'">'.$x1.'</td>';
					}

			}else{
				foreach($total_labour as $v => $t){
					if($m1->id == $v) {
						$x1 = 0;
						for ($i = 1; $i <= 12; $i++) { 
							$field1 = 'B_' . sprintf('%02d', $i);
							$x1 =  ($contentedit == 'true'  ? number_format($t[$field1]) :'');
							$xtotal1 = ($contentedit == 'true'  ? number_format($t['total']) : '');
							$xtotal11 = ($contentedit == 'true'  ? number_format($t['total_le']) : '');

							$incr1 = ($contentedit == 'true' || $t['total_le'] !=0 ? (($t['total'] / $t['total_le']) -1) * 100 : '');

							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field1.'" data-name="'.$field1.'"  data-value="'.$x1.'">'.$x1.'</td>';

						}
					}
				}
			}
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_budget" data-name="total_budget" data-id="'.$m1->id_trx.'" data-value="'.$xtotal1.'">'.$xtotal1.'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_le" data-name="total_le"  data-value="'.$xtotal11.'">'.$xtotal11.'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right increase money-custom" data-name="increase"  data-value="">'.number_format($incr1,2) . ' ' .$prsn.'</td>';

			$gtotal1 += $m1->total_budget; 
			$jan1 += $m1->B_01;
			$feb1 += $m1->B_02;
			$mar1 += $m1->B_03;
			$apr1 += $m1->B_04;
			$may1 += $m1->B_05;
			$jun1 += $m1->B_06;
			$jul1 += $m1->B_07;
			$aug1 += $m1->B_08;
			$sep1 += $m1->B_09;
			$oct1 += $m1->B_10;
			$nov1 += $m1->B_11;
			$dec1 += $m1->B_12;
			$jan00 = $m1->B_01;
			?>
		</tr>
		<?php 
		foreach($mst_account[$m1->id] as $m2) { 
			if(count(@$mst_account[$m2->id]) >=1 ) {
				// $bgedit ="#A9A9A9";
				$bgedit ="";
				$contentedit ="false" ;
				$prsn = '';
			}else{
				$bgedit ="";
				$contentedit ="true" ;
				$prsn = '%';
			}
			
			?>
			<tr>
				<td class="sub-2"><?php echo $m2->account_code . '-' .$m2->account_name; ?></td>
				<?php
			$field2 = '';
			if(!in_array($m2->id,$id_labour)) {
				$x2 = 0;
				$sTotal ="";
				$xtotal2 = ($contentedit == 'true'  && in_array($m2->account_code,$user_akses_account) ? number_format($m2->total_budget) : '');
				$xtotal22 = ($contentedit == 'true'  && in_array($m2->account_code,$user_akses_account) ? number_format($m2->total_le) : '');
				
				$sTotal_budget += str_replace(['.',','],'',$xtotal2) ;
				$sTotal_le += str_replace(['.',','],'',$xtotal22) ;

				$gnTotal_budget += str_replace(['.',','],'',$xtotal2) ;
				$gnTotal_le += str_replace(['.',','],'',$xtotal22) ;

				$gnTotal = "";

				for ($i = 1; $i <= 12; $i++) { 
					$field2 = 'B_' . sprintf('%02d', $i);
					$x2 =  ($contentedit == 'true'  && in_array($m2->account_code,$user_akses_account)  ? number_format($m2->$field2) : '');
					
					$sTotal = "sTotal_" . sprintf('%02d', $i);
					$$sTotal += str_replace(['.',','],'',$x2) ;

					$gnTotal = "gTotal_" . sprintf('%02d', $i);
					$$gnTotal += str_replace(['.',','],'',$x2) ;


					$incr2 = ($contentedit == 'true' && $m2->total_le !=0 ? (($m2->total_budget / $m2->total_le)-1) * 100 : '');
		
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field2.'" data-name="'.$field2.'" data-id="'.$m2->id_trx.'" data-value="'.$x2.'">'.$x2.'</td>';
				}
			}else{
				foreach($total_labour as $v => $t){
					if($m2->id == $v) {
						$x2 = 0;
						for ($i = 1; $i <= 12; $i++) { 
							$field2 = 'B_' . sprintf('%02d', $i);
							$x2 =  ($contentedit == 'true'  ? number_format($t[$field2]) :'');
							$xtotal2 = ($contentedit == 'true'  ? number_format($t['total']) :'');
							$xtotal22 = ($contentedit == 'true'  ? number_format($t['total_le']) :'');
							$incr2 = ($contentedit == 'true' || $t['total_le'] !=0 ? (($t['total'] / $t['total_le'])-1) * 100 : 0);

							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field2.'" data-name="'.$field2.'"  data-value="'.$x2.'">'.$x2.'</td>';

						}
					}
				}
			}
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_budget" data-name="total_budget" data-id="'.$m2->id_trx.'" data-value="'.$xtotal2.'">'.$xtotal2.'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_le" data-name="total_le"  data-value="'.$xtotal22.'">'.$xtotal22.'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right increase money-custom" data-name="increase"  data-value="">'.number_format($incr2,2) . ' ' .$prsn.'</td>';

			?>
			</tr>
			
			<?php 
			foreach($mst_account[$m2->id] as $m3) { 
				if(count(@$mst_account[$m3->id]) >=1 ) {
					// $bgedit ="#A9A9A9";
					$bgedit ="";
					$contentedit ="false" ;
					$prsn = '';
				}else{
					$bgedit ="";
					$contentedit ="true" ;
					$prsn = '%';
				}

				?>
				<tr>
					<td class="sub-3"><?php echo $m3->account_code . '-' .$m3->account_name; ?></td>
					<?php
					$field3 = '';
					if(!in_array($m3->id,$id_labour)) {
						$x3 = 0;
						$sTotal="";
						
						$xtotal3 = ($contentedit == 'true'  && in_array($m3->account_code,$user_akses_account)  ? number_format($m3->total_budget) : '');
						$xtotal33 = ($contentedit == 'true'  && in_array($m3->account_code,$user_akses_account)  ? number_format($m3->total_le) : '');
						$sTotal_budget += str_replace(['.',','],'',$xtotal3) ;
						$sTotal_le += str_replace(['.',','],'',$xtotal33) ;

						$gnTotal = "";

						$gnTotal_budget += str_replace(['.',','],'',$xtotal3) ;
						$gnTotal_le += str_replace(['.',','],'',$xtotal33) ;

						for ($i = 1; $i <= 12; $i++) { 
							$field3 = 'B_' . sprintf('%02d', $i);
							$x3 =  ($contentedit == 'true'  && in_array($m3->account_code,$user_akses_account)  ? number_format($m3->$field3) : '');
							
							$sTotal = "sTotal_" . sprintf('%02d', $i);
							$$sTotal += str_replace(['.',','],'',$x3) ;

							$gnTotal = "gTotal_" . sprintf('%02d', $i);
							$$gnTotal += str_replace(['.',','],'',$x3) ;

							$incr2 = ($contentedit == 'true' || $m2->total_le !=0 ? (($m2->total_budget / $m2->total_le)-1) * 100 : '');


							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field3.'" data-name="'.$field3.'" data-id="'.$m3->id_trx.'" data-value="'.$x3.'">'.$x3.'</td>';
						}
					}else{
						foreach($total_labour as $v => $t){
							if($m3->id == $v) {
								$x3 = 0;
								for ($i = 1; $i <= 12; $i++) {  
									$field3 = 'B_' . sprintf('%02d', $i);
									$x3 =  ($contentedit == 'true'  ? number_format($t[$field3]):'');
									$xtotal3 = ($contentedit == 'true'  ? number_format($t['total']) : '');
									$xtotal3 = ($contentedit == 'true'  ? number_format($t['total_le']) : '');
									$incr3 = ($contentedit == 'true' || $t['total_le'] !=0 ? (($t['total'] / $t['total_le'])-1) * 100 : '');
									echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field3.'" data-name="'.$field3.'"  data-value="'.$x3.'">'.$x3.'</td>';
		
								}
							}
						}

					}
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_budget" data-name="total_budget" data-id="'.$m3->id_trx.'" data-value="'.$xtotal3.'">'.$xtotal3.'</td>';
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_le" data-name="total_le"  data-value="'.$xtotal33.'">'.$xtotal33.'</td>';
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right increase money-custom" data-name="increase"  data-value="">'.number_format($incr3,2) . ' ' .$prsn.'</td>';		


			?>
				</tr>
			<?php } ?>
		<?php } ?>
	<?php } ?>
	<tr>
		<td bgcolor="#778899" style="color: white; font-weight: bold; background-color: #778899 !important;">SUB TOTAL <?php echo strtoupper($m0->account_name);?></td>
		<?php
		$field0 = '';
		foreach($total_header as $h => $th){
			if($m0->id == $h) {
				$sTotal = "";
				for ($i = 1; $i <= 12; $i++) { 
					$sTotal = "sTotal_" . sprintf('%02d', $i);
	
				echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.number_format($$sTotal).'</td>';
			}

			$sub_total_incr_ = ($sTotal_le !=0 ? (($sTotal_budget / $sTotal_le)-1) * 100 : 0);
			echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.number_format($sTotal_budget).'</td>';
			echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.number_format($sTotal_le).'</td>';
			echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.number_format($sub_total_incr_,2).' %</td>';
			}
		}

		foreach($total_labour as $h => $th){

			if($m0->id == $h) {
				$xtotal00 = 0;
				$x00 = 0;
				$field00 = '';
				for ($i = 1; $i <= 12; $i++) { 
					$field00 = 'B_' . sprintf('%02d', $i);
					$x00 =  number_format($th[$field00]);
					$xtotal00 = number_format($th['total']);
					$xtotal01 = number_format($th['total_le']);
		
					echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.$x00.'</td>';
				}

				$sub_total_incr = ($xtotal01 !=0 ? (($xtotal00 / $xtotal01)-1) * 100 : 0);

				echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.$xtotal00.'</td>';
				echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.$xtotal01.'</td>';
				echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.number_format($sub_total_incr,2).' %</td>';
			}
		}
		

		?>
	</tr>
<?php } ?>


	<tr>
		<th bgcolor="#D2691E" style="color: white; font-weight: bold; background-color: #D2691E !important;" colspan=""><b>GRAND TOTAL</b></th>
		<?php
		for ($i = 1; $i <= 12; $i++) { 
			$gnTotal = "gTotal_" . sprintf('%02d', $i);
			?>
			<td class="text-right" bgcolor="#D2691E" style="color: white;"><?php echo number_format($$gnTotal);?></td>
			<?php
		}

		$total_incr = ($gnTotal_le !=0 ? (($gnTotal_budget / $gnTotal_le)-1) * 100 : 0);
		echo '<td class="text-right" bgcolor="#D2691E" style="color: white;"><div style="min-height: 10px; overflow: visible;" contenteditable="false" class="edit-value text-right"  data-id="" data-value="">'.number_format($gnTotal_budget).'</div></td>';
		echo '<td class="text-right" bgcolor="#D2691E" style="color: white;"><div style="min-height: 10px; overflow: visible;" contenteditable="false" class="edit-value text-right"  data-id="" data-value="">'.number_format($gnTotal_le).'</div></td>';
		echo '<td class="text-right" bgcolor="#D2691E" style="color: white;"><div style="min-height: 10px; overflow: visible;" contenteditable="false" class="edit-value text-right money-custom2"  data-id="" data-value="">'.number_format($total_incr,2).' %</div></td>';

		?>	
	
	</tr>





			
