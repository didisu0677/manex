<?php 

	foreach($material as $m2 => $m1) { 
		// debug($m1->product_name);die;
		// debug(isset($m1['product_name']) ? $m1['product_name'] : '');die;
						
		$bgedit ="";
		$contentedit ="false" ;
		?>
		<tr>

			<td><?php echo isset($m1['material_name']) ? $m1['material_name'] : ''; ?></td>
			<td><?php echo isset($m1['component_item']) ? $m1['component_item'] :'';?></td>
			<?php


			$bgedit ="";
			$contentedit ="true" ;
			// for ($i = setting('actual_budget'); $i <= 12; $i++) {
			$total = 0;
			for ($i = 1; $i <= 12; $i++) {

				$field0 = 'B_' . sprintf('%02d', $i);
				$total += $m1[$field0];

				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="" data-value="">'.number_format($m1[$field0]).'</td>';
			
			}
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_est" data-name="" data-id="'.$m1->id.'" data-value=""><b>'.number_format($total).'</b></td>';

			?>

		</tr>
	<?php 
	} ?>
