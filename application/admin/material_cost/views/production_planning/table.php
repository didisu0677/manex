<?php 
//debug($dtx_core2018);die;
	$hno = 0;
	// for ($i = setting('actual_budget'); $i <= 12; $i++) {

	for ($i = 1; $i <= 12; $i++) {
		$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
		$$sumtotalfield0 = 0;
	}
	$sumstotal_budget = 0;

	foreach($grup[0] as $m0) { ?>
		<tr>
            <?php $colspan = 6 + (12 ); ?>
			<th colspan="<?php echo $colspan ; ?>" style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff"><?php echo $m0->cost_centre; ?></font></th>
		</tr>		
  	<?php

	for ($i = 1; $i <= 12; $i++) {
		$totalfield0 = 'TotalB_' . sprintf('%02d', $i);
		$$totalfield0 = 0;
	}
	$stotal_budget = 0;

	foreach($produk[$m0->id] as $m2 => $m1) { 
		// debug($m1->product_name);die;
		// debug(isset($m1['product_name']) ? $m1['product_name'] : '');die;
			$no++;
						
		$bgedit ="";
		$contentedit ="false" ;
		?>
		<tr>

			<td rowspan ="5" style="vertical-align: middle; "><?php echo isset($m1->product_name) ? $m1->product_name : ''; ?></td>
			<td rowspan ="5" style="vertical-align: middle; "><?php echo isset($m1->code) ? $m1->code : ''; ?></td>
			<td rowspan ="5" style="vertical-align: middle; "></td>
			<td rowspan ="5" style="vertical-align: middle; "></td>
			<td>Begining Stock</td>
			<?php

		
			$bgedit ="";
			$contentedit ="true" ;
			// for ($i = setting('actual_budget'); $i <= 12; $i++) {
			for ($i = 1; $i <= 12; $i++) {
				$x1 = number_format(0) ;
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'">'.$x1.'</td>';
			}

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_est" data-name="" data-id="'.$m1->id.'" data-value=""><b>'.number_format(0).'</b></td>';

			?>

		</tr>
		<tr>
			<td>Prod</td>
			<?php
			$bgedit ="";
			$contentedit ="true" ;
			// for ($i = setting('actual_budget'); $i <= 12; $i++) {
			for ($i = 1; $i <= 12; $i++) {
				$x1 = number_format(0) ;
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'">'.$x1.'</td>';
			}

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_est" data-name="" data-id="'.$m1->id.'" data-value=""><b>'.number_format(0).'</b></td>';

			?>
			
		</tr>
		<tr>
			<td>Sales</td>
			<?php
				$bgedit ="";
				$contentedit ="true" ;
				// for ($i = setting('actual_budget'); $i <= 12; $i++) {
				for ($i = 1; $i <= 12; $i++) {
					$x1 = number_format(0) ;
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'">'.$x1.'</td>';
				}
	
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_est" data-name="" data-id="'.$m1->id.'" data-value=""><b>'.number_format(0).'</b></td>';
	
			?>
		</tr>
		<tr>
			<td>End Stock</td>
			<?php
				$bgedit ="";
				$contentedit ="true" ;
				// for ($i = setting('actual_budget'); $i <= 12; $i++) {
				for ($i = 1; $i <= 12; $i++) {
					$x1 = number_format(0) ;
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'">'.$x1.'</td>';
				}
	
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_est" data-name="" data-id="'.$m1->id.'" data-value=""><b>'.number_format(0).'</b></td>';
	
			?>
		</tr>
		<tr>
			<td>M. Cov</td>
			<?php
			$bgedit ="";
			$contentedit ="true" ;
			// for ($i = setting('actual_budget'); $i <= 12; $i++) {
			for ($i = 1; $i <= 12; $i++) {
				$x1 = number_format(0) ;
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'">'.$x1.'</td>';
			}

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_est" data-name="" data-id="'.$m1->id.'" data-value=""><b>'.number_format(0).'</b></td>';

			?>
		</tr>

	<?php 
	} ?>
	<tr>
		<td class="sub-1" colspan="5"><b>TOTAL <?php echo $m0->cost_centre  ?></b></td>
		<?php
			$bgedit ="";
			$contentedit ="false" ;
			// for ($i = setting('actual_budget'); $i <= 12; $i++) {
			for ($i = 1; $i <= 12; $i++) {
				$totalfield0 = 'TotalB_' . sprintf('%02d', $i);
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$totalfield0.'"><b>'.number_format($$totalfield0).'</b></td>';
			}
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$stotal_est.'"><b>'.number_format( $stotal_budget).'</b></td>';
		?>
	</tr
	
<?php } ;?>
?>
<tr>
	<td class="sub-1" colspan="5"><b>GRAND TOTAL</b></td>
	<?php
		$bgedit ="";
		$contentedit ="false" ;
		// for ($i = setting('actual_budget'); $i <= 12; $i++) {
		for ($i = 1; $i <= 12; $i++) {				
			$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sumtotalfield0.'"><b>'.number_format($$sumtotalfield0).'</b></td>';
		}
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';
	?>
</tr