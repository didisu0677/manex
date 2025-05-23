<?php 
//debug($dtx_core2018);die;
	$hno = 0;
	// for ($i = setting('actual_budget'); $i <= 12; $i++) {

	for ($i = 1; $i <= 12; $i++) {
		$totalfield0 = 'TotalB_' . sprintf('%02d', $i);
		$$totalfield0 = 0;
	}
	$stotal_budget = 0;
	foreach($produk as $m2 => $m1) { 
						
		$bgedit ="";
		$contentedit ="false" ;
		?>
		<tr>
			<td style="width: 150px;"><b><?php echo $m1->material_name; ?></b></td>
		</tr>	
		<tr>
			<td style="width: 150px;">Number of units in inventory—beginning of period</td>
			<?php

		
			$bgedit ="";
			$contentedit ="false" ;
			$t_begining = "";

			for ($i = 1; $i <= 12; $i++) {
				$t_begining = 'begining' . sprintf('%02d', $i);
				$$t_begining = 0;
			}
			
			for ($i = 1; $i <= 12; $i++) {
				$fieldp = 'begining_stock_' . sprintf('%02d', $i);
				$field0 = 'P_' . sprintf('%02d', $i);
				$xxx2 =0;
				$t_begining = 'begining' . sprintf('%02d', $i);
				$gt_begining = 'tbegining' . sprintf('%02d', $i);

				// foreach($sto_awal[$m0->id] as $s2 => $s1) { 
				// 	if($s1->product_code == $m1->code) {
				// 		$xxx2 = number_format($s1->$field0) ;
				// 	}
				// 	$$t_begining +=  $s1->$field0 ;
				// 	// $$gt_begining +=  $s1->$field0 ;

				// 	foreach($xprod[$m0->id] as $sp => $sp1) { 
				// 		if($sp1->product_code == $m1->code) {
				// 			$id = $sp1->id ;
				// 		}
				// 	}

				// }
				
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'" id="'.$fieldp.$id.'">'.$xxx2.'</td>';
			}

	
			?>

		</tr>
		<tr>
			<td>Arrival quantity</td>
			<?php
			$bgedit ="#CCCCFF";
			$contentedit ="true" ;

			$t_xprod = "";
			for ($i = 1; $i <= 12; $i++) {
				$t_xprod = 'xprod' . sprintf('%02d', $i);
				$$t_xprod = 0;
			}
			
			for ($i = 1; $i <= 12; $i++) {

				$field0 = 'P_' . sprintf('%02d', $i);
				$xxx5 =0;
				$t_xprod = 'xprod' . sprintf('%02d', $i);
				$gt_xprod = 'txprod' . sprintf('%02d', $i);
				$field0x = 'xproduksi_' . sprintf('%02d', $i);

				// foreach($xprod[$m0->id] as $sp => $sp1) { 
				// 	if($sp1->product_code == $m1->code) {
				// 		$id = $sp1->id ;
				// 		$xxx5 = $sp1->$field0 ;
				// 	}
				// }

				// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right xprod xproduksi '.$field0x.'" data-name="'.$field0x.'" data-id="'.$m1->id.'" data-value="" data-nilai = "'.$m1->batch_size.'" id="id="'.$field0x.$m1->id.'"></td>';
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right xprod xproduksi '.$field0x.'" data-name="'.$field0x.'" data-id="'.$id.'" data-value="" data-nilai = "'.$m1->batch_size.'" id="'.$field0x.$m1->id.'">'.$xxx5.'</td>';

			}
			?>
		</tr>
		<tr>
			<td style="background-color:; color: #0101fd;">Units available for use</td>
			<?php
			$bgedit ="";
			$contentedit ="false" ;

			// for ($i = setting('actual_budget'); $i <= 12; $i++) {
			for ($i = 1; $i <= 12; $i++) {
				$fieldp = 'prod_' . sprintf('%02d', $i);
				$field0 = 'P_' . sprintf('%02d', $i);
				$xxx5 = 0;
				// foreach($m_cov[$m0->id] as $s2 => $s1) { 
				// 	if($s1->product_code == $m1->code) {
				// 		$xxx5 = (($s1->$field0 * -1)  < 1.98 && $s1->$field0 != 0 ? $m1->batch_size : 0) ;
				// 		foreach($xprod[$m0->id] as $sp => $sp1) { 
				// 			if($sp1->product_code == $m1->code) {
				// 				$id = $sp1->id ;
				// 			}
				// 		}
				// 	}

				// }

				$stotal_prsn = 0;
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right produksi '.$fieldp.'" data-name="'.$fieldp.'" data-id="'.$m1->id.'" data-value="" id="'.$fieldp.$id.'">'.$xxx5.'</div></td>';	

				// echo '<td class="money-custom" style="background-color: #ffded7; color: #fd0501;"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$xxx4.'">'.$xxx4.'</td>';

			}


			?>
			
		</tr>
		<tr>
			<td>Units used in production</td>
			<?php
				$bgedit ="";
				$contentedit ="false" ;

				for ($i = 1; $i <= 12; $i++) {
					$t_sales = 'sales' . sprintf('%02d', $i);
					$$t_sales = 0;
				}

				// for ($i = setting('actual_budget'); $i <= 12; $i++) {
				for ($i = 1; $i <= 12; $i++) {
					$fieldp = 'sales_' . sprintf('%02d', $i);
					$field0 = 'P_' . sprintf('%02d', $i);
					$xxx1 = 0;
					$t_sales = 'sales' . sprintf('%02d', $i);
					$gt_sales = 'tsales' . sprintf('%02d', $i);

					// foreach($sales[$m0->id] as $s2 => $s1) { 
					// 	// debug($s1->product_code);die;
					// 	if($s1->product_code == $m1->code) {
					// 		$xxx1 = number_format($s1->$field0) ;
					// 	}
					// 	$$t_sales +=  $s1->$field0 ;
					// 	// $$gt_sales +=  $s1->$field0 ;

					// 	foreach($xprod[$m0->id] as $sp => $sp1) { 
					// 		if($sp1->product_code == $m1->code) {
					// 			$id = $sp1->id ;
					// 		}
					// 	}
					// }
					
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'" id="'.$fieldp.$id.'">'.$xxx1.'</td>';
				}

			?>
		</tr>
		<tr>
			<td>Number of units in inventory—end of period</td>
			<?php
				$bgedit ="";
				$contentedit ="false" ;
				for ($i = 1; $i <= 12; $i++) {
					$t_end = 'end' . sprintf('%02d', $i);
					$$t_end = 0;
				}

				// for ($i = setting('actual_budget'); $i <= 12; $i++) {
				for ($i = 1; $i <= 12; $i++) {
					$fieldp = 'end_stock_' . sprintf('%02d', $i);
					$field0 = 'P_' . sprintf('%02d', $i);
					$xxx3 = 0;

					$t_end = 'end' . sprintf('%02d', $i);
					$gt_end = 'tend' . sprintf('%02d', $i);

					// foreach($sto_end[$m0->id] as $s2 => $s1) { 
					// 	// debug($s1->product_code);die;
					// 	if($s1->product_code == $m1->code) {
					// 		$xxx3 = number_format($s1->$field0) ;
					// 	}
					// 	$$t_end +=  $s1->$field0 ;
					// 	// $$gt_end +=  $s1->$field0 ;

					// 	foreach($xprod[$m0->id] as $sp => $sp1) { 
					// 		if($sp1->product_code == $m1->code) {
					// 			$id = $sp1->id ;
					// 		}
					// 	}
					// }
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'" id="'.$fieldp.$id.'">'.$xxx3.'</td>';
				}
	
	
			?>
		</tr>
		<tr>
			<td>Month Coverage</td>
			<?php
				$bgedit ="";
				$contentedit ="false" ;
				// for ($i = setting('actual_budget'); $i <= 12; $i++) {
				for ($i = 1; $i <= 12; $i++) {
					$fieldp = 'm_cov_' . sprintf('%02d', $i);
					$field0 = 'P_' . sprintf('%02d', $i);
					$xxx4 = 0;
					// foreach($m_cov[$m0->id] as $s2 => $s1) { 
					// 	// debug($s1->product_code);die;
					// 	if($s1->product_code == $m1->code) {
					// 		$xxx4 = ($s1->$field0) ;
					// 	}

					// 	foreach($xprod[$m0->id] as $sp => $sp1) { 
					// 		if($sp1->product_code == $m1->code) {
					// 			$id = $sp1->id ;
					// 		}
					// 	}
					// }
					$stotal_prsn = 0;
					echo '<td class="money-custom" style="background-color: #ffded7; color: #fd0501;"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$xxx4.'" id="'.$fieldp.$id.'">'.$xxx4.'</td>';
				}

			?>
		</tr>

	<?php 
	} ?>


