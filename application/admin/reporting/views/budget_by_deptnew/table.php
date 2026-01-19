<?php 

$gnTotal = "";
for ($i = 1; $i <= 12; $i++) { 
	$gnTotal = "gTotal_" . sprintf('%02d', $i);
	$$gnTotal = 0;
}

foreach ($mst_account[0] as $m0): 
	if(count(@$mst_account[$m0->id]) >=1 ) {
		$bgedit ="";
		$contentedit ="false" ;
	}else{
		$bgedit ="";
		$contentedit ="true" ;
	}
	?>
	<tr>
		<td><b><?php echo $m0->account_code . '-' .$m0->account_name; ?></b></td>
			<?php
			$field0 = '';
			if(!in_array($m0->id,$id_labour)) {
				$x0 = 0;
				
				$sTotal = "";
				$gnTotal = "";
				$gntotal_le = 0;
				$xtotal0 = ($contentedit == 'true' && in_array($m0->account_code,$user_akses_account) ? number_format($m0->total_le) : '');
				$gnTotal_le += str_replace(['.',','],'',$xtotal0) ;
				for ($i = 1; $i <= 12; $i++) { 
					$field0 = 'EST_' . sprintf('%02d', $i);
					$x0 = ($contentedit == 'true' && in_array($m0->account_code,$user_akses_account) ? number_format($m0->$field0) : '');
					
					$sTotal = "sTotal_" . sprintf('%02d', $i);
					$$sTotal += str_replace(['.',','],'',$x0) ;

					$gnTotal = "gTotal_" . sprintf('%02d', $i);
					$$gnTotal += str_replace(['.',','],'',$x0) ;

					
					if($i <= setting('actual_budget')) {
						$bgedit = '#F7F7EB';
						$contentedit = "false";
					}
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'"  data-value="'.$x0.'">'.$x0.'</td>';
					if(count(@$mst_account[$m0->id]) >=1 ) {
						$bgedit ="";
						$contentedit ="false" ;
					}else{
						$bgedit ="";
						$contentedit ="true" ;
					}
			
				}
			}else{
				foreach($total_labour as $v => $t){
					if($m0->id == $v) {
						$x0 = 0;
						for ($i = 1; $i <= 12; $i++) { 
							$field0 = 'EST_' . sprintf('%02d', $i);
							$x0 =  ($contentedit == 'true'  ? number_format($t[$field0]) :'');
							$xtotal0 = ($contentedit == 'true'  ? number_format($t['total']) :'');
							if($i <= setting('actual_budget')) {
								$bgedit = '#F7F7EB';
								$contentedit = "false";
							}
							if(count(@$mst_account[$m0->id]) >=1 ) {
								$bgedit ="";
								$contentedit ="false" ;
							}else{
								$bgedit ="";
								$contentedit ="true" ;
							}
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'"  data-value="'.$x0.'">'.$x0.'</td>';
						}
					}
				}
			}

			echo '<td style="background: '.($bgedit !== '' ? $bgedit : '#f8f9fa').'"><div style="background:'.($bgedit !== '' ? $bgedit : '#f8f9fa').'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right total_le" data-name="total_le"  data-value="'.$xtotal0.'">'.$xtotal0.'</td>';

			?>
	</tr>
	<?php 

	$sTotal = "";
	for ($i = 1; $i <= 12; $i++) { 
		$sTotal = "sTotal_" . sprintf('%02d', $i);
		$$sTotal = 0;
	}

	$sTotalH = 0;
	$sTotal_le = 0;
	foreach ($mst_account[$m0->id] as $m1): 

		if(count(@$mst_account[$m1->id]) >=1 ) {
			// $bgedit ="#A9A9A9";
			$bgedit ="";
			$contentedit ="false" ;
		}else{
			$bgedit ="";
			$contentedit ="true" ;
		}

		?>

		<tr>
			<td class="sub-1"><b><?php echo $m1->account_code . '-' .$m1->account_name; ?></b></td>
			<?php
			$field1 = '';
			if(!in_array($m1->id,$id_labour)) {
					$x1 = 0;
					$sTotal = "";

					$gnTotal = "";
					$xtotal1 = ($contentedit == 'true' && in_array($m1->account_code,$user_akses_account) ? number_format($m1->total_le) : '');
					$gnTotal_le += str_replace(['.',','],'',$xtotal1) ;

					$sTotal_le += str_replace(['.',','],'',$xtotal1) ;


					for ($i = 1; $i <= 12; $i++) { 
						$field1 = 'EST_' . sprintf('%02d', $i);
						$x1 = ($contentedit == 'true' && in_array($m1->account_code,$user_akses_account) ? number_format($m1->$field1) : '');

						$sTotal = "sTotal_" . sprintf('%02d', $i);
						$$sTotal += str_replace(['.',','],'',$x1) ;

						$gnTotal = "gTotal_" . sprintf('%02d', $i);
						$$gnTotal += str_replace(['.',','],'',$x1) ;



						if($i <= setting('actual_budget')) {
							$bgedit = '#F7F7EB';
							$contentedit = "false";
						}
						echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field1.'" data-name="'.$field1.'" data-id="'.$m1->id_trx.'" data-value="'.$x1.'">'.$x1.'</td>';
						if(count(@$mst_account[$m1->id]) >=1 ) {
							// $bgedit ="#A9A9A9";
							$bgedit ="";
							$contentedit ="false" ;
						}else{
							$bgedit ="";
							$contentedit ="true" ;
						}
					}

			}else{
				foreach($total_labour as $v => $t){
					if($m1->id == $v) {
						$x1 = 0;
						for ($i = 1; $i <= 12; $i++) { 
							$field1 = 'EST_' . sprintf('%02d', $i);
							$x1 =  ($contentedit == 'true'  ? number_format($t[$field1]) :'');
							$xtotal1 = ($contentedit == 'true'  ? number_format($t['total_le']) : '');
							if($i <= setting('actual_budget')) {
								$bgedit = '#F7F7EB';
								$contentedit = "false";
							}
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field1.'" data-name="'.$field1.'"  data-value="'.$x1.'">'.$x1.'</td>';
							if(count(@$mst_account[$m1->id]) >=1 ) {
								// $bgedit ="#A9A9A9";
								$bgedit ="";
								$contentedit ="false" ;
							}else{
								$bgedit ="";
								$contentedit ="true" ;
							}
						}
					}
				}
			}
			echo '<td style="background: '.($bgedit !== '' ? $bgedit : '#f8f9fa').'"><div style="background:'.($bgedit !== '' ? $bgedit : '#f8f9fa').'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right total_le" data-name="total_le" data-id="'.$m1->id_trx.'" data-value="'.$xtotal1.'">'.$xtotal1.'</td>';


			?>
		</tr>
		<?php 
		foreach ($mst_account[$m1->id] as $m2): 
			if(count(@$mst_account[$m2->id]) >=1 ) {
				$bgedit ="";
				$contentedit ="false" ;
			}else{
				$bgedit ="";
				$contentedit ="true" ;
			}
			
			?>
			<tr>
				<td class="sub-2"><?php echo $m2->account_code . '-' .$m2->account_name; ?></td>
				<?php
			$field2 = '';
			if(!in_array($m2->id,$id_labour)) {
				$x2 = 0;
				
				$sTotal = "";
				$gnTotal = "";
				$xtotal2 = ($contentedit == 'true' && in_array($m2->account_code,$user_akses_account) ? number_format($m2->total_le) : '');
				$gnTotal_le += str_replace(['.',','],'',$xtotal2) ;

				$sTotal_le += str_replace(['.',','],'',$xtotal2) ;

				for ($i = 1; $i <= 12; $i++) { 
					$field2 = 'EST_' . sprintf('%02d', $i);
					$x2 =  ($contentedit == 'true' && in_array($m2->account_code,$user_akses_account) ? number_format($m2->$field2) : '');
					
					$sTotal = "sTotal_" . sprintf('%02d', $i);
					$$sTotal += str_replace(['.',','],'',$x2) ;

					$gnTotal = "gTotal_" . sprintf('%02d', $i);
					$$gnTotal += str_replace(['.',','],'',$x2) ;

					
					if($i <= setting('actual_budget')) {
						$bgedit = '#F7F7EB';
						$contentedit = "false";
					}
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field2.'" data-name="'.$field2.'" data-id="'.$m2->id_trx.'" data-value="'.$x2.'">'.$x2.'</td>';
					if(count(@$mst_account[$m2->id]) >=1 ) {
						$bgedit ="";
						$contentedit ="false" ;
					}else{
						$bgedit ="";
						$contentedit ="true" ;
					}
				}
			}else{
				foreach($total_labour as $v => $t){
					if($m2->id == $v) {
						$x2 = 0;
						for ($i = 1; $i <= 12; $i++) { 
							$field2 = 'EST_' . sprintf('%02d', $i);
							$x2 =  ($contentedit == 'true'  ? number_format($t[$field2]) :'');
							$xtotal2 = ($contentedit == 'true'  ? number_format($t['total_le']) :'');
							if($i <= setting('actual_budget')) {
								$bgedit = '#F7F7EB';
								$contentedit = "false";
							}
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field2.'" data-name="'.$field2.'"  data-value="'.$x2.'">'.$x2.'</td>';
							if(count(@$mst_account[$m2->id]) >=1 ) {
								$bgedit ="";
								$contentedit ="false" ;
							}else{
								$bgedit ="";
								$contentedit ="true" ;
							}
						}
					}
				}
			}
			echo '<td style="background: '.($bgedit !== '' ? $bgedit : '#f8f9fa').'"><div style="background:'.($bgedit !== '' ? $bgedit : '#f8f9fa').'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right total_le" data-name="total_le" data-id="'.$m2->id_trx.'" data-value="'.$xtotal2.'">'.$xtotal2.'</td>';


			?>
			</tr>
			
			<?php 
			foreach ($mst_account[$m2->id] as $m3): 
				if(count(@$mst_account[$m3->id]) >=1 ) {
					$bgedit ="";
					$contentedit ="false" ;
				}else{
					$bgedit ="";
					$contentedit ="true" ;
				}

				?>
				<tr>
					<td class="sub-3"><?php echo $m3->account_code . '-' .$m3->account_name; ?></td>
					<?php
					$field3 = '';
					if(!in_array($m3->id,$id_labour)) {
						$x3 = 0;
						$sTotal = "";
						$gnTotal ="" ;
						$xtotal3 = ($contentedit == 'true' && in_array($m3->account_code,$user_akses_account) ? number_format($m3->total_le) : '');
						$gnTotal_le += str_replace(['.',','],'',$xtotal3) ;
						$sTotal_le += str_replace(['.',','],'',$xtotal3) ;

						for ($i = 1; $i <= 12; $i++) { 
							$field3 = 'EST_' . sprintf('%02d', $i);
							$x3 =  ($contentedit == 'true' && in_array($m3->account_code,$user_akses_account) ? number_format($m3->$field3) : '');
							
	
							$sTotal = "sTotal_" . sprintf('%02d', $i);
							$$sTotal += str_replace(['.',','],'',$x3) ;

							$gnTotal = "gTotal_" . sprintf('%02d', $i);
							$$gnTotal += str_replace(['.',','],'',$x3) ;

							if($i <= setting('actual_budget')) {
								$bgedit = '#F7F7EB';
								$contentedit = "false";
							}
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field3.'" data-name="'.$field3.'" data-id="'.$m3->id_trx.'" data-value="'.$x3.'">'.$x3.'</td>';
							if(count(@$mst_account[$m3->id]) >=1 ) {
								$bgedit ="";
								$contentedit ="false" ;
							}else{
								$bgedit ="";
								$contentedit ="true" ;
							}
						}
					}else{
						foreach($total_labour as $v => $t){
							if($m3->id == $v) {
								$x3 = 0;
								for ($i = 1; $i <= 12; $i++) { 
									$field3 = 'EST_' . sprintf('%02d', $i);
									$x3 =  ($contentedit == 'true'  ? number_format($t[$field3]):'');
									$xtotal3 = ($contentedit == 'true'  ? number_format($t['total_le']) : '');
									if($i <= setting('actual_budget')) {
										$bgedit = '#F7F7EB';
										$contentedit = "false";
									}
									echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field3.'" data-name="'.$field3.'"  data-value="'.$x3.'">'.$x3.'</td>';
									if(count(@$mst_account[$m3->id]) >=1 ) {
										$bgedit ="";
										$contentedit ="false" ;
									}else{
										$bgedit ="";
										$contentedit ="true" ;
									}
								}

						}
					}
				}

				$bgTotal = ($bgedit !== '' ? $bgedit : '#f8f9fa');
				echo '<td style="background: '.$bgTotal.'"><div style="background:'.$bgTotal.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right total_le" data-name="total_le" data-id="'.$m3->id_trx.'" data-value="'.$xtotal3.'">'.$xtotal3.'</td>';
			
	
			?>
				</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
	<?php endforeach; ?>
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
				echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.number_format($sTotal_le).'</td>';
			}
		}

		foreach($total_labour as $h => $th){

			if($m0->id == $h) {
				$xtotal00 = 0;
				$x00 = 0;
				$field00 = '';
				for ($i = 1; $i <= 12; $i++) { 
					$field00 = 'EST_' . sprintf('%02d', $i);
					$x00 =  number_format($th[$field00]);
					$xtotal00 = number_format($th['total_le']);
		
					echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.$x00.'</td>';
				}
				echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.$xtotal00.'</td>';
			}
		}
		

		?>
	</tr>
<?php endforeach; ?>


	<tr>
		<th bgcolor="#D2691E" style="color: white; font-weight: bold; background-color: #D2691E !important;" colspan=""><b>GRAND TOTAL</b></th>
		<?php
		for ($i = 1; $i <= 12; $i++) { 
			$gnTotal = "gTotal_" . sprintf('%02d', $i);
			?>
			<td class="text-right" bgcolor="#D2691E" style="color: white;"><?php echo number_format($$gnTotal);?></td>
			<?php
		}
		echo '<td class="text-right" bgcolor="#D2691E" style="color: white;"><div style="min-height: 10px; overflow: visible; background-color: #D2691E;" contenteditable="false" class="edit-value text-right"  data-id="" data-value="">'.number_format($gnTotal_le).'</div></td>';

		?>	
	
	</tr>





			
