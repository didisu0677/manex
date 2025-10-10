<?php 
$gnTotal = "";
$i = 0;
foreach($production as $p) { 
	$i++;
	$gnTotal = "gTotal_" . $p->kode;
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
				$xtotal0 = ($contentedit == 'true'  ? 0 : '');
				$xtotal00 = ($contentedit == 'true'  ? 0 : '');
				$gnTotal ="";
				foreach($production as $p) { 
					$x0 = ($contentedit == 'true'  ? 0 : '');
					$x00 = ($contentedit == 'true'  ? 0 : '');
					foreach($tbudget as $t) {
						if($p->kode == $t->cost_centre && $t->account_code == $m0->account_code) {
							$x0 = ($contentedit == 'true'  ? number_format($t->total_budget) : '');
							$x00 = ($contentedit == 'true'  ? number_format($t->total_le) : '');
						}
					}

					$xtotal0 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x0) :'');
					$xtotal00 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x00) :'');

					$sTotal = "sTotal_" . $p->kode;
					$$sTotal += str_replace(['.',','],'',$x0) ;
					
					$gnTotal = "gTotal_" . $p->kode;
					$$gnTotal += str_replace(['.',','],'',$x0);

					$incr0 = ($contentedit == 'true' || $m0->total_budget !=0 ? (($m0->total_le / $m0->total_budget) - 1) * 100 : '');
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'"  data-value="'.$x0.'">'.$x0.'</td>';
				}
				$gnTotal_budget += str_replace(['.',','],'',$xtotal0) ;
				$gnTotal_le += str_replace(['.',','],'',$xtotal00) ;
			}else{
				$xtotal0 = ($contentedit == 'true'  ? 0 : '');
				$xtotal00 = ($contentedit == 'true'  ? 0 : '');
				foreach($total_labour as $v => $t){
					if($m0->id == $v) {
						foreach($production as $p) { 
							$x0 = ($contentedit == 'true'  ? 0 : '');
							$x00 = ($contentedit == 'true'  ? 0 : '');
							foreach($t as $tv => $k) {
								if($p->kode == $tv) {
									$x0 =  ($contentedit == 'true'  ? number_format($k['total_budget']) :'');
									$x00 = ($contentedit == 'true'  ? number_format($k['total_le']) :'');
									$incr0 = ($contentedit == 'true' || $t['total_le'] !=0 ? (($t['total'] / $t['total_le'])-1) * 100 : 0);
								}
							}

							$xtotal0 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x0) :'');
							$xtotal00 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x00) :'');

							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'"  data-value="'.$x0.'">'.$x0.'</td>';
							
						}
					}
				}
			}
			$incr0 = ($xtotal00 !=0 ? (($xtotal0 / $xtotal00)-1) * 100 : 0);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_budget" data-name="total_budget"  data-value="'.$xtotal0.'">'.($contentedit == 'true'  ? number_format($xtotal0) : '').'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_le" data-name="total_le"  data-value="'.$xtotal00.'">'.($contentedit == 'true'  ? number_format($xtotal00) : '').'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right increase money-custom" data-name="increase"  data-value="">'.($contentedit == 'true'  ? number_format($incr0,2) : '').'</td>';

			?>
	</tr>
	<?php 

	$sTotal = "";
	foreach($production as $p) { 
		$sTotal = "sTotal_" . $p->kode;
		$$sTotal = 0;
	}
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
	
					$sTotal = "";
					$xtotal1 = ($contentedit == 'true'  ? 0 : '');
					$xtotal11 = ($contentedit == 'true'  ? 0 : '');
					foreach($production as $p) { 
						$x1 = ($contentedit == 'true'  ? 0 : '');
						$x11 = ($contentedit == 'true'  ? 0 : '');
						foreach($tbudget as $t) {
							if($p->kode == $t->cost_centre && $t->account_code == $m1->account_code) {
								$x1 = ($contentedit == 'true'  ? number_format($t->total_budget) : '');

								$x11 = ($contentedit == 'true'  ? number_format($t->total_le) : '');
		

							}
						}

						$xtotal1 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x1) :'');
						$xtotal11 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x11) :'');

						$sTotal = "sTotal_" . $p->kode;
						$$sTotal += str_replace(['.',','],'',$x1) ;

	
						$gnTotal = "gTotal_" . $p->kode;
						$$gnTotal += str_replace(['.',','],'',$x1) ;

						$incr1 = ($contentedit == 'true' && $m1->total_le !=0 ? (($m1->total_budget / $m1->total_le) -1) * 100 : '');

						echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field1.'" data-name="'.$field1.'" data-id="'.$m1->id_trx.'" data-value="'.$x1.'">'.$x1.'</td>';
					}

					$gnTotal_budget += str_replace(['.',','],'',$xtotal1) ;
					$sTotal_le += str_replace(['.',','],'',$xtotal11) ;
					$gnTotal_le += str_replace(['.',','],'',$xtotal11) ;

			}else{
				foreach($total_labour as $v => $t){
					if($m1->id == $v) {
						$xtotal1 = ($contentedit == 'true'  ? 0 : '');
						$xtotal11 = ($contentedit == 'true'  ? 0 : '');
						foreach($production as $p) { 
							$x1 = ($contentedit == 'true'  ? 0 : '');
							foreach($t as $tv => $k) {
								if($p->kode == $tv) {
									$x1 =  ($contentedit == 'true'  ? number_format($k['total_budget']) :'');
									$x11 = ($contentedit == 'true'  ? number_format($k['total_le']) :'');
									$incr1 = ($contentedit == 'true' || $t['total_le'] !=0 ? (($t['total'] / $t['total_le'])-1) * 100 : 0);
								}
							}

							$xtotal1 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x1) :'');
							$xtotal11 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x11) :'');

							$sTotal = "sTotal_" . $p->kode;
							$$sTotal += str_replace(['.',','],'',$x1) ;

							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field1.'" data-name="'.$field1.'"  data-value="'.$x1.'">'.$x1.'</td>';
							
						}
					}
				}
				$sTotal_le += str_replace(['.',','],'',$xtotal11) ;
			}
			$incr1 = ($xtotal11 !=0 ? (($xtotal1 / $xtotal11)-1) * 100 : 0);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_budget" data-name="total_budget" data-id="'.$m1->id_trx.'" data-value="'.$xtotal1.'">'.($contentedit == 'true'  ? number_format($xtotal1) : '').'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_le" data-name="total_le"  data-value="'.$xtotal11.'">'.($contentedit == 'true'  ? number_format($xtotal11) : '').'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right increase money-custom" data-name="increase"  data-value="">'.($contentedit == 'true'  ? number_format($incr1,2) : '')  . ' ' .$prsn.'</td>';

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
				$xtotal2 = ($contentedit == 'true'  ? 0 : '');
				$xtotal22 = ($contentedit == 'true'  ? 0 : '');
				$sTotal ="";
				$gnTotal = "";

				foreach($production as $p) { 
					$x2 = ($contentedit == 'true'  ? 0 : '');
					$x22 = ($contentedit == 'true'  ? 0 : '');
					foreach($tbudget as $t) {
						if($p->kode == $t->cost_centre && $t->account_code == $m2->account_code) {
							$x2 = ($contentedit == 'true'  ? number_format($t->total_budget) : '');
							$x22 = ($contentedit == 'true' ? number_format($t->total_le) : '');
							
							$sTotal_budget += str_replace(['.',','],'',$xtotal2) ;
			
						}
					}


					$xtotal2 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x2):'');
					$xtotal22 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x22):'');

					$sTotal = "sTotal_" . $p->kode;
					$$sTotal += str_replace(['.',','],'',$x2) ;

					$gnTotal = "gTotal_" . $p->kode;
					$$gnTotal += str_replace(['.',','],'',$x2) ;

					$incr2 = ($contentedit == 'true' && $m2->total_le !=0 ? (($m2->total_budget / $m2->total_le)-1) * 100 : '');
		
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field2.'" data-name="'.$field2.'" data-id="'.$m2->id_trx.'" data-value="'.$x2.'">'.$x2.'</td>';
				}
				$gnTotal_budget += str_replace(['.',','],'',$xtotal2) ;
				$sTotal_le += str_replace(['.',','],'',$xtotal22) ;
				$gnTotal_le += str_replace(['.',','],'',$xtotal22) ;
			}else{
				foreach($total_labour as $v => $t){
					if($m2->id == $v) {

						$xtotal2 = ($contentedit == 'true'  ? 0 : '');
						$xtotal22 = ($contentedit == 'true'  ? 0 : '');
						foreach($production as $p) { 
							$x2 = ($contentedit == 'true'  ? 0 : '');
							$x22 = ($contentedit == 'true'  ? 0 : '');
							foreach($t as $tv => $k) {
								if($p->kode == $tv) {
									$x2 =  ($contentedit == 'true'  ? number_format($k['total_budget']) :'');
									$x22 = ($contentedit == 'true'  ? number_format($k['total_le']) :'');
									$incr2 = ($contentedit == 'true' || $t['total_le'] !=0 ? (($t['total'] / $t['total_le'])-1) * 100 : 0);
								}
							}
							$xtotal2 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x2) :'');
							$xtotal22 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x22) :'');


							$sTotal = "sTotal_" . $p->kode;
							$$sTotal += str_replace(['.',','],'',$x2) ;
	
							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field2.'" data-name="'.$field2.'"  data-value="'.$x2.'">'.$x2.'</td>';
							
						}
					}
				}
				$sTotal_le += str_replace(['.',','],'',$xtotal22) ;
			}
			$incr2 = ($xtotal22 !=0 ? (($xtotal2 / $xtotal22)-1) * 100 : 0);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_budget" data-name="total_budget" data-id="'.$m2->id_trx.'" data-value="'.$xtotal2.'">'.($contentedit == 'true'  ? number_format($xtotal2) : '').'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_le" data-name="total_le"  data-value="'.$xtotal22.'">'.($contentedit == 'true'  ? number_format($xtotal22) : '').'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right increase money-custom" data-name="increase"  data-value="">'.($contentedit == 'true'  ? number_format($incr2,2) : '') . ' ' .$prsn.'</td>';

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
						if(!in_array($m3->id,$id_labour)) {
						$sTotal="";
						$xtotal3 = ($contentedit == 'true'  ? 0 : '');
						$xtotal33 = ($contentedit == 'true'  ? 0 : '');
						foreach($production as $p) { 
							$x3 = ($contentedit == 'true'  ? 0 : '');
							$x33 = ($contentedit == 'true'  ? 0 : '');
							foreach($tbudget as $t) {
								if($p->kode == $t->cost_centre && $t->account_code == $m3->account_code) {
									$x3 = ($contentedit == 'true'  ? number_format($t->total_budget) : '');

									$x33 = ($contentedit == 'true'   ? number_format($t->total_le) : '');
			
								}
							}

							$xtotal3 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x3) :'');
							$xtotal33 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x33) :'');

							$sTotal = "sTotal_" . $p->kode;
							$$sTotal += str_replace(['.',','],'',$x3) ;

							$gnTotal = "gTotal_" . $p->kode;
							$$gnTotal += str_replace(['.',','],'',$x3) ;
	
							$incr3 = ($contentedit == 'true' || $m2->total_le !=0 ? (($m2->total_budget / $m2->total_le)-1) * 100 : '');


							echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field3.'" data-name="'.$field3.'" data-id="'.$m3->id_trx.'" data-value="'.$x3.'">'.$x3.'</td>';
						}
						$gnTotal_budget += str_replace(['.',','],'',$xtotal3) ;
						$gnTotal_le += str_replace(['.',','],'',$xtotal33) ;
					}else{
						foreach($total_labour as $v => $t){
							if($m3->id == $v) {
								$xtotal3 = ($contentedit == 'true'  ? 0 : '');
								$xtotal33 = ($contentedit == 'true'  ? 0 : '');
								foreach($production as $p) { 
									$x3 = ($contentedit == 'true'  ? 0 : '');
									$x33 = ($contentedit == 'true'  ? 0 : '');
									foreach($t as $tv => $k) {
										if($p->kode == $tv) {
											$x3 =  ($contentedit == 'true'  ? number_format($k['total_budget']) :'');
											$x33 = ($contentedit == 'true'  ? number_format($k['total_le']) :'');
											$incr2 = ($contentedit == 'true' || $t['total_le'] !=0 ? (($t['total'] / $t['total_le'])-1) * 100 : 0);
										}
									}

									$xtotal3 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x3) :'');
									$xtotal33 += ($contentedit == 'true'  ? str_replace(['.',','],'',$x33) :'');

									$sTotal = "sTotal_" . $p->kode;
									$$sTotal += str_replace(['.',','],'',$x3) ;
			
									echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right budget '.$field3.'" data-name="'.$field3.'"  data-value="'.$x3.'">'.$x3.'</td>';
									
								}
							}
						}
					}

					$incr3 = ($xtotal33 !=0 ? (($xtotal3 / $xtotal33)-1) * 100 : 0);
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_budget" data-name="total_budget" data-id="'.$m3->id_trx.'" data-value="'.$xtotal3.'">'.($contentedit == 'true'  ? number_format($xtotal3) : '').'</td>';
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right total_le" data-name="total_le"  data-value="'.$xtotal33.'">'.($contentedit == 'true'  ? number_format($xtotal33) : '').'</td>';
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false"  class="edit-value text-right increase money-custom" data-name="increase"  data-value="">'.($contentedit == 'true'  ? number_format($incr3,2) : '')  . ' ' .$prsn.'</td>';		


			?>
				</tr>
			<?php } ?>
		<?php } ?>
	<?php } ?>
	<tr>
		<td bgcolor="#778899" style="color: white;">SUB TOTAL <?php echo strtoupper($m0->account_name);?></td>
		<?php
		$field0 = '';


				$sTotal = "";
				$sTotal_budget = 0;
				foreach($production as $p) { 
					$sTotal = "sTotal_" . $p->kode;
	
					echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.number_format($$sTotal).'</td>';
					$sTotal_budget += $$sTotal;
				}

				$sub_total_incr_ = ($sTotal_le !=0 ? (($sTotal_budget / $sTotal_le)-1) * 100 : 0);
				echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.number_format($sTotal_budget).'</td>';
				echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.number_format($sTotal_le).'</td>';
				echo '<td class="text-right" bgcolor="#778899" style="color: white;">'.number_format($sub_total_incr_,2).' %</td>';

	?>
	</tr>
<?php } ?>


	<tr>
		<th bgcolor="#D2691E" style="color: white;" colspan=""><b>GRAND TOTAL</b></th>
		<?php
		$gnTotal = '';
		foreach($production as $p) { 
			$gnTotal = "gTotal_" . $p->kode;

			?>
			<td class="text-right" bgcolor="#D2691E" style="color: white;"><?php echo number_format($$gnTotal);?></td>
			<?php
		}

		$total_incr = ($gnTotal_le !=0 ? (($gnTotal_budget / $gnTotal_le)-1) * 100 : 0);
		echo '<td class="text-right" bgcolor="#D2691E" style="color: white;"><div style="background:" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right"  data-id="" data-value="">'.number_format($gnTotal_budget).'</td>';
		echo '<td class="text-right" bgcolor="#D2691E" style="color: white;"><div style="background:" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right"  data-id="" data-value="">'.number_format($gnTotal_le).'</td>';
		echo '<td class="text-right" bgcolor="#D2691E" style="color: white;"><div style="background:" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="edit-value text-right money-custom2"  data-id="" data-value="">'.number_format($total_incr,2).' %</td>';

		?>	
	
	</tr>





			
