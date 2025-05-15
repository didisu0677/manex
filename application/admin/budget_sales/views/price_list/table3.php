<?php 
//debug($dtx_core2018);die;
$hno = 0;
for ($i = 1; $i <= 10; $i++) {
	$totalfield0 = 'TotalTHN_' . sprintf('%02d', $i);
	$$totalfield0 = 0;
}
foreach($grup[0] as $m0) { ?>
	<tr>
		<?php $colspan = 15; ?>
		<th colspan="<?php echo $colspan ; ?>" style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff"><?php echo $m0->sub_product; ?></font></th>
	</tr>		
	<?php
	foreach($produk[$m0->product_line] as $m2 => $m1) { 
		// debug($m1->product_name);die;
		// debug(isset($m1['product_name']) ? $m1['product_name'] : '');die;

		$bgedit ="";
		$contentedit ="false" ;
		?>
		<tr>

			<td><?php echo isset($m1->product_name) ? $m1->product_name : ''; ?></td>
			<td><?php echo isset($m1->code) ? $m1->code : ''; ?></td>
			<td><?php echo isset($m1->segment) ? $m1->segment : ''; ?></td>
			<?php

			$bgedit ="";
			$contentedit ="true" ;
			$actual =  'EST_' . sprintf('%02d', setting('actual_budget'));
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget actual" data-name="actual" data-id="'.$m1->id.'" data-value="">'.number_format($m1->$actual).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget actual" data-name="actual" data-id="'.$m1->id.'" data-value="">'.number_format($m1->B_12).'</td>';

			for ($i = 1; $i <= 10; $i++) {
				$field0 = 'THN_' . sprintf('%02d', $i);
				$totalfield0 = 'TotalTHN_' . sprintf('%02d', $i);
				$$totalfield0 += $m1->$field0;
				
				$x1 = ($contentedit == 'true' ? number_format($m1->$field0) : '');
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'">'.$x1.'</td>';
			}
            // echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_budgetthn" data-name="total_budgetthn" data-id="'.$m1->id.'" data-value=""></td>';

			?>

		</tr>
	<?php 
	} 	?>
	<tr>
		<td class="sub-1" colspan="3"><b>TOTAL <?php echo $m0->sub_product  ?></b></td>
		<?php
			$bgedit ="";
			$contentedit ="false" ;
			// for ($i = setting('actual_budget'); $i <= 12; $i++) {
			for ($i = 1; $i <= 12; $i++) {
				if($i <= setting('actual_budget')) {       
					$bgedit = '#F7F7EB';
					$contentedit = "false";
				}else{
					$bgedit = '';
					$contentedit = "true";
				}

				$totalfield0 = 'TotalB_' . sprintf('%02d', $i);
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$totalfield0.'"><b>'.number_format(0).'</b></td>';
			}
			?>
	</tr>

<?php
 }  ?>

	<tr>
		<td class="sub-1" colspan="3"><b>GRAND TOTAL</b></td>
		<?php
			$bgedit ="";
			$contentedit ="false" ;
			// for ($i = setting('actual_budget'); $i <= 12; $i++) {
			for ($i = 1; $i <= 12; $i++) {
				if($i <= setting('actual_budget')) {
					$bgedit = '#F7F7EB';
					$contentedit = "false";
				}else{
					$bgedit = '';
					$contentedit = "true";
				}
				
				$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sumtotalfield0.'"><b>'.number_format(0).'</b></td>';
			}
			?>
	</tr>