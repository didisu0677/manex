<?php 

$gnTotal = "";
$gnTotal_le = 0;
$gnTotal_budget = 0;
$grandTotalLe = 0;
$grandTotalBudget = 0;
for ($i = 1; $i <= 12; $i++) { 
	$gnTotal = "gTotal_" . sprintf('%02d', $i);
	$$gnTotal = 0;
	$gnTotalBudget = "gTotalBudget_" . sprintf('%02d', $i);
	$$gnTotalBudget = 0;
}

foreach($mst_account[0] as $m0) { 
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
				// Calculate total_le based on actual_budget
				$xtotal0 = 0;
				if($contentedit == 'true' && in_array($m0->account_code,$user_akses_account)) {
					for($j = 1; $j <= setting('actual_budget'); $j++) {
						$fieldTemp = 'EST_' . sprintf('%02d', $j);
						$xtotal0 += $m0->$fieldTemp;
					}
					$xtotal0 = number_format($xtotal0);
				}else{
					$xtotal0 = '';
				}
				$xtotal0Numeric = (int) str_replace(['.',','],'',$xtotal0);
				$gnTotal_le += $xtotal0Numeric;
				$grandTotalLe += $xtotal0Numeric;
				// Total Budget
				$xtotal_budget0 = ($contentedit == 'true' && in_array($m0->account_code,$user_akses_account) ? number_format($m0->total_budget) : '');
				$xtotalBudget0Numeric = (int) str_replace(['.',','],'',$xtotal_budget0);
				$gnTotal_budget += $xtotalBudget0Numeric;
				$grandTotalBudget += $xtotalBudget0Numeric;
				for ($i = 1; $i <= 12; $i++) { 
					$field0 = 'EST_' . sprintf('%02d', $i);
					$fieldB = 'B_' . sprintf('%02d', $i);
					if($i > setting('actual_budget')) {
						$x0 = '';
						$xb0 = '';
					}else{
						$x0 = ($contentedit == 'true' && in_array($m0->account_code,$user_akses_account) ? number_format($m0->$field0) : '');
						$xb0 = ($contentedit == 'true' && in_array($m0->account_code,$user_akses_account) ? number_format($m0->$fieldB) : '');
					}
					
					$sTotal = "sTotal_" . sprintf('%02d', $i);
					$$sTotal += str_replace(['.',','],'',$x0) ;
					
					$sTotalBudget = "sTotalBudget_" . sprintf('%02d', $i);
					$$sTotalBudget += str_replace(['.',','],'',$xb0) ;

					$gnTotal = "gTotal_" . sprintf('%02d', $i);
					$$gnTotal += str_replace(['.',','],'',$x0) ;
					
					$gnTotalBudget = "gTotalBudget_" . sprintf('%02d', $i);
					$$gnTotalBudget += str_replace(['.',','],'',$xb0) ;

					
					if($i <= setting('actual_budget')) {
						$bgedit = '#F7F7EB';
						$contentedit = "false";
					}
					// Actual Column
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'"  data-value="'.$x0.'">'.$x0.'</td>';
					
					// Budget Column
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'"  data-value="'.$xb0.'">'.$xb0.'</td>';
					
					// Deviation Column
					if($x0 === '' || $xb0 === '') {
						$xdev0 = '';
					} else {
						$xdev0_numeric = str_replace(['.',','],'',$x0) - str_replace(['.',','],'',$xb0);
						$xdev0 = number_format($xdev0_numeric);
					}
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_dev"  data-value="'.$xdev0.'">'.$xdev0.'</td>';
					
					// Percentage Column
					if($x0 === '' || $xb0 === '' || $xdev0 === '') {
						$xpct0 = '';
					} else {
						$xdev0_numeric = str_replace(['.',','],'',$xdev0);
						$xb0_numeric = str_replace(['.',','],'',$xb0);
						$xpct0 = ($xb0_numeric != 0) ? number_format(($xdev0_numeric / $xb0_numeric) * 100, 2) : '';
					}
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_pct"  data-value="'.$xpct0.'">'.$xpct0.''.($xpct0 !== '' ? '%' : '').'</td>';
					
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
						// Calculate total_le for labour based on actual_budget
						$xtotal0 = 0;
						if($contentedit == 'true') {
							for($j = 1; $j <= setting('actual_budget'); $j++) {
								$fieldTemp = 'EST_' . sprintf('%02d', $j);
								$xtotal0 += $t[$fieldTemp];
							}
							$xtotal0 = number_format($xtotal0);
						}else{
							$xtotal0 = '';
						}
						// Total Budget
						$xtotal_budget0 = ($contentedit == 'true'  ? number_format($t['total_budget']) :'');
						
						for ($i = 1; $i <= 12; $i++) { 
							$field0 = 'EST_' . sprintf('%02d', $i);
							$fieldB = 'B_' . sprintf('%02d', $i);
						if($i > setting('actual_budget')) {
							$x0 = '';
							$xb0 = '';
						}else{
							$x0 =  ($contentedit == 'true'  ? number_format($t[$field0]) :'');
							$xb0 =  ($contentedit == 'true'  ? number_format($t[$fieldB]) :'');
						}
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
							// Actual Column
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'"  data-value="'.$x0.'">'.$x0.'</td>';
							
							// Budget Column
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'"  data-value="'.$xb0.'">'.$xb0.'</td>';
							
							// Deviation Column
							if($x0 === '' || $xb0 === '') {
								$xdev0 = '';
							} else {
								$xdev0_numeric = str_replace(['.',','],'',$x0) - str_replace(['.',','],'',$xb0);
								$xdev0 = number_format($xdev0_numeric);
							}
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_dev"  data-value="'.$xdev0.'">'.$xdev0.'</td>';
							
							// Percentage Column
							if($x0 === '' || $xb0 === '' || $xdev0 === '') {
								$xpct0 = '';
							} else {
								$xdev0_numeric = str_replace(['.',','],'',$xdev0);
								$xb0_numeric = str_replace(['.',','],'',$xb0);
								$xpct0 = ($xb0_numeric != 0) ? number_format(($xdev0_numeric / $xb0_numeric) * 100, 2) : '';
							}
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_pct"  data-value="'.$xpct0.'">'.$xpct0.''.($xpct0 !== '' ? '%' : '').'</td>';
							}
					}
				}
			}

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right total_le" data-name="total_le"  data-value="'.$xtotal0.'">'.$xtotal0.'</td>';
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right total_budget" data-name="total_budget"  data-value="'.$xtotal_budget0.'">'.$xtotal_budget0.'</td>';
		// BvA Analysis
	if($xtotal0 === '' || $xtotal_budget0 === '') {
		$bva0 = '';
	} else {
		$bva0 = number_format(str_replace(['.',','],'',$xtotal_budget0) - str_replace(['.',','],'',$xtotal0));
	}
	echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right bva_analysis" data-name="bva_analysis"  data-value="'.$bva0.'">'.$bva0.'</td>';
	// Ratio Budget
	if($xtotal0 === '' || $xtotal_budget0 === '') {
		$ratio0 = '';
	} else {
		$actual_numeric = str_replace(['.',','],'',$xtotal0);
		$budget_numeric = str_replace(['.',','],'',$xtotal_budget0);
		$ratio0 = ($budget_numeric != 0) ? number_format(($actual_numeric / $budget_numeric) * 100, 2) : '';
	}
	echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right ratio_budget" data-name="ratio_budget"  data-value="'.$ratio0.'">'.$ratio0.''.($ratio0 !== '' ? '%' : '').'</td>';
			?>
	</tr>
	<?php 

	$sTotal = "";
	for ($i = 1; $i <= 12; $i++) { 
		$sTotal = "sTotal_" . sprintf('%02d', $i);
		$$sTotal = 0;
		$sTotalBudget = "sTotalBudget_" . sprintf('%02d', $i);
		$$sTotalBudget = 0;
	}

	$sTotalH = 0;
	$sTotal_le = 0;
	$sTotal_budget = 0;
	foreach($mst_account[$m0->id] as $m1) { 

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
					// Calculate total_le based on actual_budget
					$xtotal1 = 0;
					if($contentedit == 'true' && in_array($m1->account_code,$user_akses_account)) {
						for($j = 1; $j <= setting('actual_budget'); $j++) {
							$fieldTemp = 'EST_' . sprintf('%02d', $j);
							$xtotal1 += $m1->$fieldTemp;
						}
						$xtotal1 = number_format($xtotal1);
					}else{
						$xtotal1 = '';
					}
					$xtotal1Numeric = (int) str_replace(['.',','],'',$xtotal1);
					$gnTotal_le += $xtotal1Numeric;
					$grandTotalLe += $xtotal1Numeric;

					$sTotal_le += $xtotal1Numeric;
					// Total Budget
					$xtotal_budget1 = ($contentedit == 'true' && in_array($m1->account_code,$user_akses_account) ? number_format($m1->total_budget) : '');
					$xtotalBudget1Numeric = (int) str_replace(['.',','],'',$xtotal_budget1);
					$sTotal_budget += $xtotalBudget1Numeric;
					$gnTotal_budget += $xtotalBudget1Numeric;
					$grandTotalBudget += $xtotalBudget1Numeric;


					for ($i = 1; $i <= 12; $i++) { 
						$field1 = 'EST_' . sprintf('%02d', $i);
						$fieldB = 'B_' . sprintf('%02d', $i);
						if($i > setting('actual_budget')) {
							$x1 = '';
							$xb1 = '';
						}else{
							$x1 = ($contentedit == 'true' && in_array($m1->account_code,$user_akses_account) ? number_format($m1->$field1) : '');
							$xb1 = ($contentedit == 'true' && in_array($m1->account_code,$user_akses_account) ? number_format($m1->$fieldB) : '');
						}

						$sTotal = "sTotal_" . sprintf('%02d', $i);
						$$sTotal += str_replace(['.',','],'',$x1) ;
						
						$sTotalBudget = "sTotalBudget_" . sprintf('%02d', $i);
						$$sTotalBudget += str_replace(['.',','],'',$xb1) ;

						$gnTotal = "gTotal_" . sprintf('%02d', $i);
						$$gnTotal += str_replace(['.',','],'',$x1) ;
						
						$gnTotalBudget = "gTotalBudget_" . sprintf('%02d', $i);
						$$gnTotalBudget += str_replace(['.',','],'',$xb1) ;

						if($i <= setting('actual_budget')) {
							$bgedit = '#F7F7EB';
							$contentedit = "false";
						}
						// Actual Column
						echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field1.'" data-name="'.$field1.'" data-id="'.$m1->id_trx.'" data-value="'.$x1.'">'.$x1.'</td>';
						
						// Budget Column
						echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'" data-id="'.$m1->id_trx.'" data-value="'.$xb1.'">'.$xb1.'</td>';
						
						// Deviation Column
						if($x1 === '' || $xb1 === '') {
							$xdev1 = '';
						} else {
							$xdev1_numeric = str_replace(['.',','],'',$x1) - str_replace(['.',','],'',$xb1);
							$xdev1 = number_format($xdev1_numeric);
						}
						echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_dev" data-id="'.$m1->id_trx.'" data-value="'.$xdev1.'">'.$xdev1.'</td>';
						
						// Percentage Column
						if($x1 === '' || $xb1 === '' || $xdev1 === '') {
							$xpct1 = '';
						} else {
							$xdev1_numeric = str_replace(['.',','],'',$xdev1);
							$xb1_numeric = str_replace(['.',','],'',$xb1);
							$xpct1 = ($xb1_numeric != 0) ? number_format(($xdev1_numeric / $xb1_numeric) * 100, 2) : '';
						}
						echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_pct" data-id="'.$m1->id_trx.'" data-value="'.$xpct1.'">'.$xpct1.''.($xpct1 !== '' ? '%' : '').'</td>';
						
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
						// Calculate total_le for labour based on actual_budget
						$xtotal1 = 0;
						if($contentedit == 'true') {
							for($j = 1; $j <= setting('actual_budget'); $j++) {
								$fieldTemp = 'EST_' . sprintf('%02d', $j);
								$xtotal1 += $t[$fieldTemp];
							}
							$xtotal1 = number_format($xtotal1);
						}else{
							$xtotal1 = '';
						}
						// Total Budget
						$xtotal_budget1 = ($contentedit == 'true'  ? number_format($t['total_budget']) : '');
						
						for ($i = 1; $i <= 12; $i++) { 
							$field1 = 'EST_' . sprintf('%02d', $i);
							$fieldB = 'B_' . sprintf('%02d', $i);
						if($i > setting('actual_budget')) {
							$x1 = '';
							$xb1 = '';
						}else{
							$x1 =  ($contentedit == 'true'  ? number_format($t[$field1]) :'');
							$xb1 =  ($contentedit == 'true'  ? number_format($t[$fieldB]) :'');
						}
							if($i <= setting('actual_budget')) {
								$bgedit = '#F7F7EB';
								$contentedit = "false";
							}
							if(count(@$mst_account[$m1->id]) >=1 ) {
								// $bgedit ="#A9A9A9";
								$bgedit ="";
								$contentedit ="false" ;
							}else{
								$bgedit ="";
								$contentedit ="true" ;
							}
							// Actual Column
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field1.'" data-name="'.$field1.'"  data-value="'.$x1.'">'.$x1.'</td>';
							
							// Budget Column
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'"  data-value="'.$xb1.'">'.$xb1.'</td>';
							
							// Deviation Column
							if($x1 === '' || $xb1 === '') {
								$xdev1 = '';
							} else {
								$xdev1_numeric = str_replace(['.',','],'',$x1) - str_replace(['.',','],'',$xb1);
								$xdev1 = number_format($xdev1_numeric);
							}
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_dev"  data-value="'.$xdev1.'">'.$xdev1.'</td>';
							
							// Percentage Column
								if($x1 === '' || $xb1 === '' || $xdev1 === '') {
									$xpct1 = '';
								} else {
									$xdev1_numeric = str_replace(['.',','],'',$xdev1);
									$xb1_numeric = str_replace(['.',','],'',$xb1);
									$xpct1 = ($xb1_numeric != 0) ? number_format(($xdev1_numeric / $xb1_numeric) * 100, 2) : '';
							}
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_pct"  data-value="'.$xpct1.'">'.$xpct1.''.($xpct1 !== '' ? '%' : '').'</td>';
							}
					}
				}
			}
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right total_le" data-name="total_le" data-id="'.$m1->id_trx.'" data-value="'.$xtotal1.'">'.$xtotal1.'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right total_budget" data-name="total_budget" data-id="'.$m1->id_trx.'" data-value="'.$xtotal_budget1.'">'.$xtotal_budget1.'</td>';
			// BvA Analysis
			if($xtotal1 === '' || $xtotal_budget1 === '') {
				$bva1 = '';
			} else {
				$bva1 = number_format(str_replace(['.',','],'',$xtotal_budget1) - str_replace(['.',','],'',$xtotal1));
			}
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right bva_analysis" data-name="bva_analysis" data-id="'.$m1->id_trx.'" data-value="'.$bva1.'">'.$bva1.'</td>';
		// Ratio Budget
		if($xtotal1 === '' || $xtotal_budget1 === '') {
			$ratio1 = '';
		} else {
			$actual_numeric = str_replace(['.',','],'',$xtotal1);
			$budget_numeric = str_replace(['.',','],'',$xtotal_budget1);
			$ratio1 = ($budget_numeric != 0) ? number_format(($actual_numeric / $budget_numeric) * 100, 2) : '';
		}
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right ratio_budget" data-name="ratio_budget" data-id="'.$m1->id_trx.'" data-value="'.$ratio1.'">'.$ratio1.''.($ratio1 !== '' ? '%' : '').'</td>';

		?>
		</tr>
		<?php 
		foreach($mst_account[$m1->id] as $m2) { 
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
				// Calculate total_le based on actual_budget
				$xtotal2 = 0;
				if($contentedit == 'true' && in_array($m2->account_code,$user_akses_account)) {
					for($j = 1; $j <= setting('actual_budget'); $j++) {
						$fieldTemp = 'EST_' . sprintf('%02d', $j);
						$xtotal2 += $m2->$fieldTemp;
					}
					$xtotal2 = number_format($xtotal2);
				}else{
					$xtotal2 = '';
				}
				$xtotal2Numeric = (int) str_replace(['.',','],'',$xtotal2);
				$gnTotal_le += $xtotal2Numeric;
				$grandTotalLe += $xtotal2Numeric;

				$sTotal_le += $xtotal2Numeric;
				// Total Budget
				$xtotal_budget2 = ($contentedit == 'true' && in_array($m2->account_code,$user_akses_account) ? number_format($m2->total_budget) : '');
				$xtotalBudget2Numeric = (int) str_replace(['.',','],'',$xtotal_budget2);
				$sTotal_budget += $xtotalBudget2Numeric;
				$gnTotal_budget += $xtotalBudget2Numeric;
				$grandTotalBudget += $xtotalBudget2Numeric;

				for ($i = 1; $i <= 12; $i++) { 
					$field2 = 'EST_' . sprintf('%02d', $i);
					$fieldB = 'B_' . sprintf('%02d', $i);
					if($i > setting('actual_budget')) {
						$x2 = '';
						$xb2 = '';
					}else{
						$x2 =  ($contentedit == 'true' && in_array($m2->account_code,$user_akses_account) ? number_format($m2->$field2) : '');
						$xb2 =  ($contentedit == 'true' && in_array($m2->account_code,$user_akses_account) ? number_format($m2->$fieldB) : '');
					}
					
					$sTotal = "sTotal_" . sprintf('%02d', $i);
					$$sTotal += str_replace(['.',','],'',$x2) ;
					
					$sTotalBudget = "sTotalBudget_" . sprintf('%02d', $i);
					$$sTotalBudget += str_replace(['.',','],'',$xb2) ;

					$gnTotal = "gTotal_" . sprintf('%02d', $i);
					$$gnTotal += str_replace(['.',','],'',$x2) ;
					
					$gnTotalBudget = "gTotalBudget_" . sprintf('%02d', $i);
					$$gnTotalBudget += str_replace(['.',','],'',$xb2) ;

					
					if($i <= setting('actual_budget')) {
						$bgedit = '#F7F7EB';
						$contentedit = "false";
					}
					// Actual Column
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field2.'" data-name="'.$field2.'" data-id="'.$m2->id_trx.'" data-value="'.$x2.'">'.$x2.'</td>';
					
					// Budget Column
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'" data-id="'.$m2->id_trx.'" data-value="'.$xb2.'">'.$xb2.'</td>';
					
					// Deviation Column
					if($x2 === '' || $xb2 === '') {
						$xdev2 = '';
					} else {
						$xdev2_numeric = str_replace(['.',','],'',$x2) - str_replace(['.',','],'',$xb2);
						$xdev2 = number_format($xdev2_numeric);
					}
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_dev" data-id="'.$m2->id_trx.'" data-value="'.$xdev2.'">'.$xdev2.'</td>';
					
					// Percentage Column
					if($x2 === '' || $xb2 === '' || $xdev2 === '') {
						$xpct2 = '';
					} else {
						$xdev2_numeric = str_replace(['.',','],'',$xdev2);
						$xb2_numeric = str_replace(['.',','],'',$xb2);
						$xpct2 = ($xb2_numeric != 0) ? number_format(($xdev2_numeric / $xb2_numeric) * 100, 2) : '';
					}
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_pct" data-id="'.$m2->id_trx.'" data-value="'.$xpct2.'">'.$xpct2.''.($xpct2 !== '' ? '%' : '').'</td>';
					
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
						// Calculate total_le for labour based on actual_budget
						$xtotal2 = 0;
						if($contentedit == 'true') {
							for($j = 1; $j <= setting('actual_budget'); $j++) {
								$fieldTemp = 'EST_' . sprintf('%02d', $j);
								$xtotal2 += $t[$fieldTemp];
							}
							$xtotal2 = number_format($xtotal2);
						}else{
							$xtotal2 = '';
						}
						// Total Budget
						$xtotal_budget2 = ($contentedit == 'true'  ? number_format($t['total_budget']):'');
						
						for ($i = 1; $i <= 12; $i++) { 
							$field2 = 'EST_' . sprintf('%02d', $i);
							$fieldB = 'B_' . sprintf('%02d', $i);
							if($i > setting('actual_budget')) {
								$x2 = '';
								$xb2 = '';
							}else{
								$x2 =  ($contentedit == 'true'  ? number_format($t[$field2]) :'');
								$xb2 =  ($contentedit == 'true'  ? number_format($t[$fieldB]) :'');
							}
							if($i <= setting('actual_budget')) {
								$bgedit = '#F7F7EB';
								$contentedit = "false";
							}
							// Actual Column
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field2.'" data-name="'.$field2.'"  data-value="'.$x2.'">'.$x2.'</td>';
							
							// Budget Column
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'"  data-value="'.$xb2.'">'.$xb2.'</td>';
							
							// Deviation Column
							if($x2 === '' || $xb2 === '') {
								$xdev2 = '';
							} else {
								$xdev2_numeric = str_replace(['.',','],'',$x2) - str_replace(['.',','],'',$xb2);
								$xdev2 = number_format($xdev2_numeric);
							}
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_dev"  data-value="'.$xdev2.'">'.$xdev2.'</td>';
							
							// Percentage Column
								if($x2 === '' || $xb2 === '' || $xdev2 === '') {
									$xpct2 = '';
								} else {
									$xdev2_numeric = str_replace(['.',','],'',$xdev2);
									$xb2_numeric = str_replace(['.',','],'',$xb2);
									$xpct2 = ($xb2_numeric != 0) ? number_format(($xdev2_numeric / $xb2_numeric) * 100, 2) : '';
							}
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_pct"  data-value="'.$xpct2.'">'.$xpct2.''.($xpct2 !== '' ? '%' : '').'</td>';
							
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
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right total_le" data-name="total_le" data-id="'.$m2->id_trx.'" data-value="'.$xtotal2.'">'.$xtotal2.'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right total_budget" data-name="total_budget" data-id="'.$m2->id_trx.'" data-value="'.$xtotal_budget2.'">'.$xtotal_budget2.'</td>';
			// BvA Analysis
			if($xtotal2 === '' || $xtotal_budget2 === '') {
				$bva2 = '';
			} else {
				$bva2 = number_format(str_replace(['.',','],'',$xtotal_budget2) - str_replace(['.',','],'',$xtotal2));
			}
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right bva_analysis" data-name="bva_analysis" data-id="'.$m2->id_trx.'" data-value="'.$bva2.'">'.$bva2.'</td>';
		// Ratio Budget
		if($xtotal2 === '' || $xtotal_budget2 === '') {
			$ratio2 = '';
		} else {
			$actual_numeric = str_replace(['.',','],'',$xtotal2);
			$budget_numeric = str_replace(['.',','],'',$xtotal_budget2);
			$ratio2 = ($budget_numeric != 0) ? number_format(($actual_numeric / $budget_numeric) * 100, 2) : '';
		}
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right ratio_budget" data-name="ratio_budget" data-id="'.$m2->id_trx.'" data-value="'.$ratio2.'">'.$ratio2.''.($ratio2 !== '' ? '%' : '').'</td>';

		?>
		</tr>
		<?php 
		foreach($mst_account[$m2->id] as $m3) { 
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
						// Calculate total_le based on actual_budget
						$xtotal3 = 0;
						if($contentedit == 'true' && in_array($m3->account_code,$user_akses_account)) {
							for($j = 1; $j <= setting('actual_budget'); $j++) {
								$fieldTemp = 'EST_' . sprintf('%02d', $j);
								$xtotal3 += $m3->$fieldTemp;
							}
							$xtotal3 = number_format($xtotal3);
						}else{
							$xtotal3 = '';
						}
						// Total Budget
						$xtotal_budget3 = ($contentedit == 'true' && in_array($m3->account_code,$user_akses_account) ? number_format($m3->total_budget) : '');
						
						for ($i = 1; $i <= 12; $i++) { 
							$field3 = 'EST_' . sprintf('%02d', $i);
							$fieldB = 'B_' . sprintf('%02d', $i);
							if($i > setting('actual_budget')) {
								$x3 = '';
								$xb3 = '';
							}else{
								$x3 =  ($contentedit == 'true' && in_array($m3->account_code,$user_akses_account) ? number_format($m3->$field3) : '');
								$xb3 =  ($contentedit == 'true' && in_array($m3->account_code,$user_akses_account) ? number_format($m3->$fieldB) : '');
							}
							$sTotal = "sTotal_" . sprintf('%02d', $i);
							$$sTotal += str_replace(['.',','],'',$x3) ;
							
							$sTotalBudget = "sTotalBudget_" . sprintf('%02d', $i);
							$$sTotalBudget += str_replace(['.',','],'',$xb3) ;

							$gnTotal = "gTotal_" . sprintf('%02d', $i);
							$$gnTotal += str_replace(['.',','],'',$x3) ;
							
							$gnTotalBudget = "gTotalBudget_" . sprintf('%02d', $i);
							$$gnTotalBudget += str_replace(['.',','],'',$xb3) ;

							if($i <= setting('actual_budget')) {
								$bgedit = '#F7F7EB';
								$contentedit = "false";
							}
							// Actual Column
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field3.'" data-name="'.$field3.'" data-id="'.$m3->id_trx.'" data-value="'.$x3.'">'.$x3.'</td>';
							
							// Budget Column
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'" data-id="'.$m3->id_trx.'" data-value="'.$xb3.'">'.$xb3.'</td>';
							
							// Deviation Column
							if($x3 === '' || $xb3 === '') {
								$xdev3 = '';
							} else {
								$xdev3_numeric = str_replace(['.',','],'',$x3) - str_replace(['.',','],'',$xb3);
								$xdev3 = number_format($xdev3_numeric);
							}
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_dev" data-id="'.$m3->id_trx.'" data-value="'.$xdev3.'">'.$xdev3.'</td>';
							
							// Percentage Column
							if($x3 === '' || $xb3 === '' || $xdev3 === '') {
								$xpct3 = '';
							} else {
								$xdev3_numeric = str_replace(['.',','],'',$xdev3);
								$xb3_numeric = str_replace(['.',','],'',$xb3);
								$xpct3 = ($xb3_numeric != 0) ? number_format(($xdev3_numeric / $xb3_numeric) * 100, 2) : '';
							}
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_pct" data-id="'.$m3->id_trx.'" data-value="'.$xpct3.'">'.$xpct3.''.($xpct3 !== '' ? '%' : '').'</td>';
							
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
								// Calculate total_le for labour based on actual_budget
								$xtotal3 = 0;
								if($contentedit == 'true') {
									for($j = 1; $j <= setting('actual_budget'); $j++) {
										$fieldTemp = 'EST_' . sprintf('%02d', $j);
										$xtotal3 += $t[$fieldTemp];
									}
									$xtotal3 = number_format($xtotal3);
								}else{
									$xtotal3 = '';
								}
								// Total Budget
								$xtotal_budget3 = ($contentedit == 'true'  ? number_format($t['total_budget']):'');
								
								for ($i = 1; $i <= 12; $i++) { 
									$field3 = 'EST_' . sprintf('%02d', $i);
									$fieldB = 'B_' . sprintf('%02d', $i);
								if($i > setting('actual_budget')) {
									$x3 = '';
									$xb3 = '';
								}else{
									$x3 =  ($contentedit == 'true'  ? number_format($t[$field3]):'');
									$xb3 =  ($contentedit == 'true'  ? number_format($t[$fieldB]):'');
								}
									if($i <= setting('actual_budget')) {
										$bgedit = '#F7F7EB';
										$contentedit = "false";
									}
									// Actual Column
									echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field3.'" data-name="'.$field3.'"  data-value="'.$x3.'">'.$x3.'</td>';
									
									// Budget Column
									echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'"  data-value="'.$xb3.'">'.$xb3.'</td>';
									
									// Deviation Column
									if($x3 === '' || $xb3 === '') {
										$xdev3 = '';
									} else {
										$xdev3_numeric = str_replace(['.',','],'',$x3) - str_replace(['.',','],'',$xb3);
										$xdev3 = number_format($xdev3_numeric);
									}
									echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_dev"  data-value="'.$xdev3.'">'.$xdev3.'</td>';
									
									// Percentage Column
									if($x3 === '' || $xb3 === '' || $xdev3 === '') {
										$xpct3 = '';
									} else {
										$xdev3_numeric = str_replace(['.',','],'',$xdev3);
										$xb3_numeric = str_replace(['.',','],'',$xb3);
										$xpct3 = ($xb3_numeric != 0) ? number_format(($xdev3_numeric / $xb3_numeric) * 100, 2) : '';
									}
									echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right budget" data-name="'.$fieldB.'_pct"  data-value="'.$xpct3.'">'.$xpct3.''.($xpct3 !== '' ? '%' : '').'</td>';
									
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
					$xtotal3Numeric = (int) str_replace(['.',','],'',$xtotal3);
					$grandTotalLe += $xtotal3Numeric;
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right total_le" data-name="total_le" data-id="'.$m3->id_trx.'" data-value="'.$xtotal3.'">'.$xtotal3.'</td>';
					$xtotalBudget3Numeric = (int) str_replace(['.',','],'',$xtotal_budget3);
					$grandTotalBudget += $xtotalBudget3Numeric;
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right total_budget" data-name="total_budget" data-id="'.$m3->id_trx.'" data-value="'.$xtotal_budget3.'">'.$xtotal_budget3.'</td>';
					// BvA Analysis
					if($xtotal3 === '' || $xtotal_budget3 === '') {
						$bva3 = '';
					} else {
						$bva3 = number_format(str_replace(['.',','],'',$xtotal_budget3) - str_replace(['.',','],'',$xtotal3));
					}
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right bva_analysis" data-name="bva_analysis" data-id="'.$m3->id_trx.'" data-value="'.$bva3.'">'.$bva3.'</td>';
				// Ratio Budget
				if($xtotal3 === '' || $xtotal_budget3 === '') {
					$ratio3 = '';
				} else {
					$actual_numeric = str_replace(['.',','],'',$xtotal3);
					$budget_numeric = str_replace(['.',','],'',$xtotal_budget3);
					$ratio3 = ($budget_numeric != 0) ? number_format(($actual_numeric / $budget_numeric) * 100, 2) : '';
				}
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right ratio_budget" data-name="ratio_budget" data-id="'.$m3->id_trx.'" data-value="'.$ratio3.'">'.$ratio3.''.($ratio3 !== '' ? '%' : '').'</td>';

			?>
			</tr>
			<?php 
		} ?>
	<?php } ?>
	<?php } ?>

<tr>
	<td bgcolor="#778899" style="color: white; font-weight: bold; background-color: #778899 !important;">SUB TOTAL <?php echo strtoupper($m0->account_name);?></td>
	<?php

	$subtotalPrinted = false;
	$field0 = '';
	foreach($total_header as $h => $th){
			if($m0->id == $h) {
				$subtotalPrinted = true;
				for ($i = 1; $i <= 12; $i++) { 
					$sTotalKey = "sTotal_" . sprintf('%02d', $i);
					$sTotalBudgetKey = "sTotalBudget_" . sprintf('%02d', $i);
					$sTotalValue = isset($$sTotalKey) ? $$sTotalKey : 0;
					$sTotalBudgetValue = isset($$sTotalBudgetKey) ? $$sTotalBudgetKey : 0;
					
					// Actual
					echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.number_format($sTotalValue).'</td>';
					
					// Budget
					echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.number_format($sTotalBudgetValue).'</td>';
					
					// Deviation
					$sDeviation = $sTotalValue - $sTotalBudgetValue;
					echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.number_format($sDeviation).'</td>';
					
					// Percentage
					$sPercentage = ($sTotalBudgetValue != 0) ? number_format(($sDeviation / $sTotalBudgetValue) * 100, 2) : '';
					echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.$sPercentage.''.($sPercentage !== '' ? '%' : '').'</td>';
				}
				echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.number_format($sTotal_le).'</td>';
				echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.number_format($sTotal_budget).'</td>';
				$sBva = $sTotal_budget - $sTotal_le;
				echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.number_format($sBva).'</td>';			// Ratio Budget
			$sRatio = ($sTotal_budget != 0) ? number_format(($sTotal_le / $sTotal_budget) * 100, 2) : '';
			echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.$sRatio.''.($sRatio !== '' ? '%' : '').'</td>';				break;
			}
		}

		if(!$subtotalPrinted) {
			foreach($total_labour as $h => $th){
				if($m0->id == $h) {
					$subtotalPrinted = true;
					for ($i = 1; $i <= 12; $i++) { 
						$field00 = 'EST_' . sprintf('%02d', $i);
						$fieldB00 = 'B_' . sprintf('%02d', $i);
						$x00 =  isset($th[$field00]) ? number_format($th[$field00]) : 0;
						$xb00 =  isset($th[$fieldB00]) ? number_format($th[$fieldB00]) : 0;
						
						// Actual
						echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.$x00.'</td>';
						
						// Budget
						echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.$xb00.'</td>';
						
						// Deviation
						$x00_numeric = (float)str_replace([','], '', $x00);
						$xb00_numeric = (float)str_replace([','], '', $xb00);
						$xdev00_numeric = $x00_numeric - $xb00_numeric;
						$xdev00 = number_format($xdev00_numeric);
						echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.$xdev00.'</td>';
						
						// Percentage
						$xpct00 = ($xb00_numeric != 0) ? number_format(($xdev00_numeric / $xb00_numeric) * 100, 2) : '';
						echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.$xpct00.''.($xpct00 !== '' ? '%' : '').'</td>';
					}
					$labourTotalActual = isset($th['total_le']) ? number_format($th['total_le']) : 0;
					$labourTotalBudget = isset($th['total_budget']) ? number_format($th['total_budget']) : 0;
					echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.$labourTotalActual.'</td>';
					echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.$labourTotalBudget.'</td>';
					$bvaLabourNumeric = (float)str_replace([','], '', $labourTotalBudget) - (float)str_replace([','], '', $labourTotalActual);
					echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.number_format($bvaLabourNumeric).'</td>';				// Ratio Budget
				$labourActualNumeric = (float)str_replace([','], '', $labourTotalActual);
				$labourBudgetNumeric = (float)str_replace([','], '', $labourTotalBudget);
				$labourRatio = ($labourBudgetNumeric != 0) ? number_format(($labourActualNumeric / $labourBudgetNumeric) * 100, 2) : '';
				echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.$labourRatio.''.($labourRatio !== '' ? '%' : '').'</td>';					break;
				}
			}
		}

		if(!$subtotalPrinted) {
			for ($i = 1; $i <= 12; $i++) {
				$sTotalKey = "sTotal_" . sprintf('%02d', $i);
				$sTotalBudgetKey = "sTotalBudget_" . sprintf('%02d', $i);
				$value = isset($$sTotalKey) ? $$sTotalKey : 0;
				$valueBudget = isset($$sTotalBudgetKey) ? $$sTotalBudgetKey : 0;
				
				// Actual
				echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.number_format($value).'</td>';
				
				// Budget
				echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.number_format($valueBudget).'</td>';
				
				// Deviation
				$sDeviation = $valueBudget - $value;
				echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.number_format($sDeviation).'</td>';
				
				// Percentage
				$sPercentage = ($valueBudget != 0) ? number_format(($value / $valueBudget) * 100, 2) : '';
				echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.$sPercentage.''.($sPercentage !== '' ? '%' : '').'</td>';
			}
			echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.number_format($sTotal_le).'</td>';
			echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.number_format($sTotal_budget).'</td>';
			$sBvaFallback = $sTotal_budget - $sTotal_le;
		echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.number_format($sBvaFallback).'</td>';
		// Ratio Budget
		$sFallbackRatio = ($sTotal_budget != 0) ? number_format(($sTotal_le / $sTotal_budget) * 100, 2) : '';
		echo '<td class="text-right" bgcolor="#778899" style="color: white; background-color: #778899 !important;">'.$sFallbackRatio.''.($sFallbackRatio !== '' ? '%' : '').'</td>';
	}
	?>
</tr>
<?php } ?>

<tr>
	<th bgcolor="#D2691E" style="color: white; font-weight: bold; background-color: #D2691E !important;" colspan=""><b>GRAND TOTAL</b></th>
	<?php
	for ($i = 1; $i <= 12; $i++) { 
		$gnTotal = "gTotal_" . sprintf('%02d', $i);
		$gnTotalBudget = "gTotalBudget_" . sprintf('%02d', $i);
		$gnValue = isset($$gnTotal) ? $$gnTotal : 0;
		$gnValueBudget = isset($$gnTotalBudget) ? $$gnTotalBudget : 0;
		?>
		<!-- Actual -->
		<td class="text-right" bgcolor="#D2691E" style="color: white; background-color: #D2691E !important;"><?php echo number_format($gnValue);?></td>
		<!-- Budget -->
		<td class="text-right" bgcolor="#D2691E" style="color: white; background-color: #D2691E !important;"><?php echo number_format($gnValueBudget);?></td>
		<!-- Deviation -->
		<?php $gnDeviation = $gnValue - $gnValueBudget; ?>
		<td class="text-right" bgcolor="#D2691E" style="color: white; background-color: #D2691E !important;"><?php echo number_format($gnDeviation);?></td>
		<!-- Percentage -->
		<td class="text-right" bgcolor="#D2691E" style="color: white; background-color: #D2691E !important;"><?php echo ($gnValueBudget != 0) ? number_format(($gnDeviation / $gnValueBudget) * 100, 2) : ''; ?><?php echo ($gnValueBudget != 0) ? '%' : ''; ?></td>
		<?php
	}
	$grandTotalRemaining = $grandTotalBudget - $grandTotalLe;
	echo '<td class="text-right" bgcolor="#D2691E" style="color: white; background-color: #D2691E !important;"><div style="min-height: 10px; overflow: visible;" contenteditable="false" class="edit-value text-right"  data-id="" data-value="">'.number_format($grandTotalLe).'</div></td>';
	echo '<td class="text-right" bgcolor="#D2691E" style="color: white; background-color: #D2691E !important;"><div style="min-height: 10px; overflow: visible;" contenteditable="false" class="edit-value text-right"  data-id="" data-value="">'.number_format($grandTotalBudget).'</div></td>';
	// BvA Analysis
	$gnBva = $grandTotalRemaining;
	echo '<td class="text-right" bgcolor="#D2691E" style="color: white; background-color: #D2691E !important;"><div style="min-height: 10px; overflow: visible;" contenteditable="false" class="edit-value text-right"  data-id="" data-value="">'.number_format($grandTotalRemaining).'</div></td>';
	// Ratio Budget
	$grandRatio = ($grandTotalBudget != 0) ? number_format(($grandTotalLe / $grandTotalBudget) * 100, 2) : '';
	echo '<td class="text-right" bgcolor="#D2691E" style="color: white; background-color: #D2691E !important;"><div style="min-height: 10px; overflow: visible;" contenteditable="false" class="edit-value text-right"  data-id="" data-value="">'.$grandRatio.''.($grandRatio !== '' ? '%' : '').'</div></td>';

	?>	

</tr>
