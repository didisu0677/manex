<?php 
//debug($dtx_core2018);die;
	$hno = 0;
	foreach($grup[0] as $m0) { ?>
		<tr>
            <?php $colspan = 4 + 12 ; ?>
			<th colspan="<?php echo $colspan ; ?>" style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff"><?php echo $m0->sub_product; ?></font></th>
		</tr>		
  	<?php

	foreach($produk[$m0->product_line] as $m2 => $m1) { 
		// debug($m1->product_name);die;
		// debug(isset($m1['product_name']) ? $m1['product_name'] : '');die;
			$no++;
						
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
            for ($i = 1; $i <= 12; $i++) {
				$field0 = 'EST_' . sprintf('%02d', $i);
				if($i <= setting('actual_budget')) {
					$bgedit = '#F7F7EB';
					$contentedit = "false";
				}else{
					$bgedit = '';
					$contentedit = "true";
				}

				$x1 = number_format($m1->$field0);
			    echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'">'.$x1.'</td>';
            }
 
			?>

		</tr>
	<?php 
	} ?>

<?php } ?>