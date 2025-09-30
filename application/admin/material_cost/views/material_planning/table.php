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
			<td style="width: 150px;" colspan ="13"><b><?php echo $m1->material_name; ?></b></td>
		</tr>	
		<tr>
			<td style="width: 150px;" colspan ="13"><b><?php echo 'Code : ' . $m1->material_code .', Min. Order: ' . $m1->moq . ', Order Multiple :' . $m1->order_multiple; ?></b></td>
		</tr>
		<tr>
			<td style="width: 150px;" class="sub-1">Beginning Stock</td>
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

			
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="" id="'.$fieldp.$m1->id.'">'.number_format($m1->$field0).'</div></td>';
			}

	
			?>

		</tr>
		<tr>
			<td class="sub-1">Arrival quantity</td>
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
				$fieldx = 'xproduksi_' . sprintf('%02d', $i);

				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right '.$fieldx.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-nilai="1" id="'.$fieldx.$m1->id.'">'.number_format($prod[$m1->material_code][$field0]).'</div></td>';
			}
			?>
		</tr>
		<tr>
			<td style="background-color:; color: #0101fd;" class="sub-1">Units available for use</td>
			<?php
			$bgedit ="";
			$contentedit ="false" ;

			for ($i = 1; $i <= 12; $i++) {
				$fieldp = 'unit_available_' . sprintf('%02d', $i);
				$field0 = 'P_' . sprintf('%02d', $i);
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="" id="'.$fieldp.$m1->id.'">'.number_format($available[$m1->material_code][$field0]).'</div></td>';
			}
			?>
			
		</tr>
		<tr>
			<td class="sub-1">Units used in production</td>
			<?php
				$bgedit ="";
				$contentedit ="false" ;

				for ($i = 1; $i <= 12; $i++) {
					$fieldp = 'sales_' . sprintf('%02d', $i);
					$field0 = 'P_' . sprintf('%02d', $i);
					
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="" id="'.$fieldp.$m1->id.'">'.number_format($pakai[$m1->material_code][$field0]).'</div></td>';
				}

			?>
		</tr>
		<tr>
			<td class="sub-1">End Stock</td>
			<?php
				$bgedit ="";
				$contentedit ="false" ;
				for ($i = 1; $i <= 12; $i++) {
					$t_end = 'end' . sprintf('%02d', $i);
					$$t_end = 0;
				}

				for ($i = 1; $i <= 12; $i++) {
					$fieldp = 'end_stock_' . sprintf('%02d', $i);
					$field0 = 'P_' . sprintf('%02d', $i);
					$xxx3 = 0;

					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="" id="'.$fieldp.$m1->id.'">'.number_format($iventory[$m1->material_code][$field0]).'</div></td>';
				}
	
	
			?>
		</tr>
		<tr>
			<td class="sub-1">Month Coverage</td>
			<?php
				$bgedit ="";
				$contentedit ="false" ;
				for ($i = 1; $i <= 12; $i++) {
					$fieldp = 'm_cov_' . sprintf('%02d', $i);
					$field0 = 'P_' . sprintf('%02d', $i);
					$xxx4 = 0;

					$stotal_prsn = 0;
					echo '<td class="money-custom" style="background-color: #ffded7; color: #fd0501;"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="" id="'.$fieldp.$m1->id.'">'.$cov[$m1->material_code][$field0].'</div></td>';
				}

			?>
		</tr>

	<?php 
	} ?>


