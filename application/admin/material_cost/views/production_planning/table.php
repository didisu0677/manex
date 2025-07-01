<?php 
//debug($dtx_core2018);die;
	$hno = 0;
	// for ($i = setting('actual_budget'); $i <= 12; $i++) {
	$gt_begining = "";
	for ($i = 1; $i <= 12; $i++) {
		$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
		$$sumtotalfield0 = 0;

		$gt_begining = 'tbegining' . sprintf('%02d', $i);
		$$gt_begining = 0;
		
		$gt_sales = 'tsales' . sprintf('%02d', $i);
		$$gt_sales = 0;

		$gt_prod = 'tprod' . sprintf('%02d', $i);
		$$gt_prod = 0;

		$gt_end = 'tend' . sprintf('%02d', $i);
		$$gt_end = 0;

	}
	$sumstotal_budget = 0;

	foreach($grup[0] as $m0) { ?>
		<tr>
		</tr>
		<tr>
            <?php $colspan = 4; ?>
			<th rowspan = "4" colspan="<?php echo $colspan ; ?>" style="background: #757575; text-align: center; vertical-align: middle;" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff"><?php echo $m0->cost_centre; ?></font></th>
		
		
			<th style="background: #757575; text-align: left" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff">Total Produksi</font></th>
			<?php
			$bgedit ="";
			$contentedit ="true" ;
			$tpord = "";
			for ($i = 1; $i <= 12; $i++) {
				$field0 = 'P_' . sprintf('%02d', $i);
				$field01 = 'EP_' . sprintf('%02d', $i);
				echo '<th class = "text-right" style="background: #757575; text-align: right" style="min-height: 10px; width: 50px; overflow: hidden;">
					<font color="#fff" data-type="grand-total-produksi" data-cost-center="'.$m0->id.'" data-month="'.$i.'">0</font>
				</th>';
			}
			?>
			<th style="background: #757575; text-align: left; color:#fff" class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
				0
			</th>
		</tr>
		<tr>
			<th style="background: #757575; text-align: left" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff">Standar Produksi</font></th>
			<?php
			$bgedit ="";
			$contentedit ="true" ;
			for ($i = 1; $i <= 12; $i++) {
				echo '<th class = "text-right" style="background: #757575; text-align: right" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff">'.number_format($sprod[$m0->id][$i]).'</font></th>';
			}
			?>
			<th style="background: #757575; text-align: left; color:#fff" class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
				0
			</th>
		</tr>
		<tr>
			<th style="background: #757575; text-align: left" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff">Kapasitas Produksi</font></th>
			<?php
			$bgedit ="";
			$contentedit ="true" ;
			for ($i = 1; $i <= 12; $i++) {	
				echo '<th class= "text-right" style="background: #757575; text-align: right" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff">'.number_format($kprod[$m0->id]).'</font></th>';
			}
			?>
			<th style="background: #757575; text-align: left; color:#fff" class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
				0
			</th>

		</tr>
		<tr>
            <?php $colspan = 4 ; ?>
			<th style="background: #757575; text-align: left" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff">Working Days</font></th>
			<?php
			$bgedit ="";
			$contentedit ="true" ;
			for ($i = 1; $i <= 12; $i++) {
				echo '<th class = "text-right" style="background: #757575; text-align: right" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff">'.number_format($wday[$m0->id][$i]).'</font></th>';
			}
			?>
			<th style="background: #757575; text-align: left; color:#fff" class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
				0
			</th>
		</tr>	
  	<?php

	for ($i = 1; $i <= 12; $i++) {
		$totalfield0 = 'TotalB_' . sprintf('%02d', $i);
		$$totalfield0 = 0;
	}
	$stotal_budget = 0;
	foreach($produk[$m0->id] as $m2 => $m1) { 		
		$bgedit ="";
		$contentedit ="false" ;
		?>
		<tr>

			<td rowspan ="6" style="vertical-align: middle; "><?php echo $m1->product_name ; ?></td>
			<td rowspan ="6" style="vertical-align: middle; "><?php echo $m1->code ; ?></td>
			<td rowspan ="6" style="vertical-align: middle; "><?php echo $m1->destination ; ?></td>
			<td class="batch" rowspan ="6" style="vertical-align: middle; "><?php echo number_format($m1->batch_size) ; ?></td>
			<td>Begining Stock</td>
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

				foreach($sto_awal[$m0->id] as $s2 => $s1) { 
					if($s1->product_code == $m1->code) {
						$xxx2 = number_format($s1->$field0) ;
					}
					$$t_begining +=  $s1->$field0 ;
					// $$gt_begining +=  $s1->$field0 ;

					foreach($xprod[$m0->id] as $sp => $sp1) { 
						if($sp1->product_code == $m1->code) {
							$id = $sp1->id ;
						}
					}

				}
				
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'" id="'.$fieldp.$m1->id.'"
				data-type="begining-stock" data-cost-center="'.$m0->id.'" data-month="'.$i.'" data-product-code="'.$m1->code.'">'.$xxx2.'</td>';
			}

			?>
			<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
				0
			</th>

		</tr>
		<tr>
			<td>X Produksi</td>
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

				foreach($xprod[$m0->id] as $sp => $sp1) { 
					if($sp1->product_code == $m1->code) {
						$id = $sp1->id ;
						$xxx5 = $sp1->$field0 ;
					}

					foreach($epr[$m0->id] as $ek => $ev){
						if($ev->product_code == $m1->code && intval($ev->$field0) > 0){
							$xxx5 = $ev->$field0;
						}
					}
				}

				// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right xprod xproduksi '.$field0x.'" data-name="'.$field0x.'" data-id="'.$m1->id.'" data-value="" data-nilai = "'.$m1->batch_size.'" id="id="'.$field0x.$m1->id.'"></td>';
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right xprod xproduksi '.$field0x.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="" data-nilai = "'.$m1->batch_size.'" id="'.$field0x.$m1->id.'"
					data-cost-center="'.$m0->id.'" data-type="x-production" data-month="'.$i.'" data-product-id="'.$id.'" data-product-code="'.$m1->code.'"
				>'.number_format($xxx5, 0).'</td>';

			}
			?>
			<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
				0
			</th>
		</tr>
		<tr>
			<td style="background-color:; color: #0101fd;">Prod</td>
			<?php
			$bgedit ="";
			$contentedit ="true" ;

			// for ($i = setting('actual_budget'); $i <= 12; $i++) {
			for ($i = 1; $i <= 12; $i++) {
				$fieldp = 'prod_' . sprintf('%02d', $i);
				$field0 = 'P_' . sprintf('%02d', $i);
				$xxx5 = 0;
				$edit_produksi = 0;
				foreach($prd[$m0->id] as $s2 => $s1) { 
					if($s1->product_code == $m1->code) {
						// $xxx5 = (($s1->$field0 * -1)  < 1.8 && $s1->$field0 != 0 ? $m1->batch_size : 0) ;
						$xxx5 = $s1->$field0;
						foreach($xprod[$m0->id] as $sp => $sp1) { 
							if($sp1->product_code == $m1->code) {
								$id = $sp1->id ;
							}
						}

					}
				}

				foreach($epd[$m0->id] as $eprk => $eprv){
					if($eprv->product_code == $m1->code){
						if(intval($eprv->$field0) > 0){
							$edit_produksi = 1;
							$xxx5 = $eprv->$field0;
						}
					}
				}

				$stotal_prsn = 0;
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right produksi '.$fieldp.'" data-name="'.$fieldp.'" data-id="'.$m1->id.'" data-value="" id="'.$fieldp.$m1->id.'"
					data-type="production" data-cost-center="'.$m0->id.'" data-month="'.$i.'" data-edit="'.$edit_produksi.'" data-product-code="'.$m1->code.'"
				>'.number_format($xxx5, 0).'</div></td>';	

				// echo '<td class="money-custom" style="background-color: #ffded7; color: #fd0501;"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$xxx4.'">'.$xxx4.'</td>';

			}

			

			?>
			<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
				0
			</th>
			
		</tr>
		<tr>
			<td>Sales</td>
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

					foreach($sales[$m0->id] as $s2 => $s1) { 
						// debug($s1->product_code);die;
						if($s1->product_code == $m1->code) {
							$xxx1 = number_format($s1->$field0) ;
						}
						$$t_sales +=  $s1->$field0 ;
						// $$gt_sales +=  $s1->$field0 ;

						foreach($xprod[$m0->id] as $sp => $sp1) { 
							if($sp1->product_code == $m1->code) {
								$id = $sp1->id ;
							}
						}
					}
					
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'" id="'.$fieldp.$m1->id.'"
					data-type="sales" data-cost-center="'.$m0->id.'" data-month="'.$i.'" data-product-code="'.$m1->code.'">'.$xxx1.'</td>';
				}

				// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_est" data-name="" data-id="'.$m1->id.'" data-value="" id="'.$fieldp.$m1->id.'"><b>'.number_format(0).'</b></td>';
			?>
			<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
				0
			</th>
		</tr>
		<tr>
			<td>End Stock</td>
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

					foreach($sto_end[$m0->id] as $s2 => $s1) { 
						// debug($s1->product_code);die;
						if($s1->product_code == $m1->code) {
							$xxx3 = number_format($s1->$field0, 0) ;
						}
						$$t_end +=  $s1->$field0 ;
						// $$gt_end +=  $s1->$field0 ;

						foreach($xprod[$m0->id] as $sp => $sp1) { 
							if($sp1->product_code == $m1->code) {
								$id = $sp1->id ;
							}
						}
					}
					echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'" id="'.$fieldp.$m1->id.'"
					data-type="end-stock" data-cost-center="'.$m0->id.'" data-month="'.$i.'" data-product-code="'.$m1->code.'">'.$xxx3.'</td>';
				}
	
				// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_est" data-name="" data-id="'.$m1->id.'" data-value="" id="'.$fieldp.$m1->id.'"><b>'.number_format(0).'</b></td>';
	
			?>
			<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
				0
			</th>
		</tr>
		<tr>
			<td>M. Cov</td>
			<?php
				$bgedit ="";
				$contentedit ="false" ;
				// for ($i = setting('actual_budget'); $i <= 12; $i++) {
				for ($i = 1; $i <= 12; $i++) {
					$fieldp = 'm_cov_' . sprintf('%02d', $i);
					$field0 = 'P_' . sprintf('%02d', $i);
					$xxx4 = 0;
					foreach($m_cov[$m0->id] as $s2 => $s1) { 
						// debug($s1->product_code);die;
						if($s1->product_code == $m1->code) {
							$xxx4 = ($s1->$field0) ;
						}

						foreach($xprod[$m0->id] as $sp => $sp1) { 
							if($sp1->product_code == $m1->code) {
								$id = $sp1->id ;
							}
						}
					}
					$stotal_prsn = 0;
					echo '<td class="money-custom" style="background-color: #ffded7; color: #fd0501;"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$xxx4.'" id="'.$fieldp.$m1->id.'"
					data-type="m_cov" data-cost-center="'.$m0->id.'" data-month="'.$i.'" data-product-code="'.$m1->code.'">'.$xxx4.'</td>';
				}
				// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_est" data-name="" data-id="'.$m1->id.'" data-value="" id="'.$fieldp.$m1->id.'"><b>'.number_format(0).'</b></td>';

			?>
			<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
				0
			</th>
		</tr>

	<?php 
	} ?>
	<tr>
		<td style="vertical-align: middle; " rowspan = "6" class="sub-1" colspan="3"><b>TOTAL <?php echo $m0->cost_centre  ?></b></td>
		<td style="vertical-align: middle; " rowspan = "6"></td>
		<td>Begining Stock</td>
		<?php
		$bgedit ="";
		$contentedit ="false" ;
		// for ($i = setting('actual_budget'); $i <= 12; $i++) {
		for ($i = 1; $i <= 12; $i++) {				
			$t_begining = 'begining' . sprintf('%02d', $i);
			$gt_begining = 'tbegining' . sprintf('%02d', $i);
			$$gt_begining +=  $$t_begining ;
			$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sumtotalfield0.'"
				data-type="total-begining-stock" data-cost-center="'.$m0->id.'" data-month="'.$i.'"
			><b>'.number_format($$t_begining).'</b></td>';
		}
		// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';
		?>
		<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
			0
		</th>
	</tr>
	<tr>
		<td>X Produksi</td>
		<?php
		$bgedit ="";
		$contentedit ="false" ;
		// for ($i = setting('actual_budget'); $i <= 12; $i++) {
		for ($i = 1; $i <= 12; $i++) {				
			$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sumtotalfield0.'"><b>'.number_format($$sumtotalfield0).'</b></td>';
		}
		// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';
		?>
		<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
			0
		</th>
	</tr>
	<tr>
		<td>Prod</td>
		<?php
		$bgedit ="";
		$contentedit ="false" ;
		$t_prod = "";
		$field0 = "";
		for ($i = 1; $i <= 12; $i++) {				
			$field0 = 'P_' . sprintf('%02d', $i);
			$t_prod = 'prod_' . sprintf('%02d', $i);
			$gt_prod = 'tprod_' . sprintf('%02d', $i);

			$$t_prod = $prod[$m0->id][$field0];
			$$gt_prod += $prod[$m0->id][$field0];

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$prod[$m0->id][$field0].'"
				data-type="total-produksi" data-cost-center="'.$m0->id.'" data-month="'.$i.'"
			>
				<b>'.number_format($prod[$m0->id][$field0]).'</b>
			</td>';
		}
		// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';
		?>
		<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
			0
		</th>
	</tr>
	<tr>
		<td>Sales</td>
		<?php
		$bgedit ="";
		$contentedit ="false" ;
		// for ($i = setting('actual_budget'); $i <= 12; $i++) {
		for ($i = 1; $i <= 12; $i++) {				
			$t_sales = 'sales' . sprintf('%02d', $i);
			$gt_sales = 'tsales' . sprintf('%02d', $i);
			$$gt_sales += $$t_sales;

			$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$t_sales.'"
				data-type="total-sales" data-cost-center="'.$m0->id.'" data-month="'.$i.'"
			><b>'.number_format($$t_sales).'</b></td>';
		}
		// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';
		?>
		<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
			0
		</th>
	</tr>
	<tr>
		<td>End Stock</td>
		<?php
		$bgedit ="";
		$contentedit ="false" ;
		// for ($i = setting('actual_budget'); $i <= 12; $i++) {
		for ($i = 1; $i <= 12; $i++) {				
			$t_begining = 'begining' . sprintf('%02d', $i);
			$t_prod = 'prod_' . sprintf('%02d', $i);
			$t_sales = 'sales' . sprintf('%02d', $i);

			$t_end = 'end' . sprintf('%02d', $i);
            $gt_end = 'tend' . sprintf('%02d', $i);
			$$gt_end += $$t_end ; 

			$$t_end = ($$t_begining + $$t_prod) - $$t_sales ;

			$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sumtotalfield0.'"
			data-type="total-end-stock" data-cost-center="'.$m0->id.'" data-month="'.$i.'"><b>'.number_format($$t_end).'</b></td>';
		}
		// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';
		?>
		<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
			0
		</th>
	</tr>
	<tr>
		<td>M Coverage</td>
		<?php
		$bgedit ="";
		$contentedit ="false" ;
		// for ($i = setting('actual_budget'); $i <= 12; $i++) {
		for ($i = 1; $i <= 12; $i++) {				
			$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sumtotalfield0.'"
			data-type="total-m-cov" data-cost-center="'.$m0->id.'" data-month="'.$i.'"><b>'.number_format($$sumtotalfield0).'</b></td>';
		}
		// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';
		?>
		<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
			0
		</th>
	</tr>
<?php } ;?>

<tr>
	<td style="vertical-align: middle; " rowspan = "6" class="sub-1" colspan="3"><b>GRAND TOTAL</b></td>
	<td style="vertical-align: middle; " rowspan = "6"></td>
		<td>Begining Stock</td>
		<?php
		$bgedit ="";
		$contentedit ="false" ;
		// for ($i = setting('actual_budget'); $i <= 12; $i++) {
		for ($i = 1; $i <= 12; $i++) {		
			$gt_begining = 'tbegining' . sprintf('%02d', $i);
			// $sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sumtotalfield0.'"
				data-type="grand-begining-stock" data-month="'.$i.'"
			><b>'.number_format($$gt_begining).'</b></td>';
		}
		// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';
		?>
		<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
			0
		</th>
</tr>
<tr>
	<td>X Produksi</td>
	<?php
	$bgedit ="";
	$contentedit ="false" ;
	// for ($i = setting('actual_budget'); $i <= 12; $i++) {
	for ($i = 1; $i <= 12; $i++) {				
		$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sumtotalfield0.'"><b>'.number_format($$sumtotalfield0).'</b></td>';
	}
	// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';
	?>
	<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
		0
	</th>
</tr>
<tr>
	<td>Prod</td>
	<?php
	$bgedit ="";
	$contentedit ="false" ;
	// for ($i = setting('actual_budget'); $i <= 12; $i++) {
	for ($i = 1; $i <= 12; $i++) {		
		$gt_prod = 'tprod_' . sprintf('%02d', $i);
		$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$gtprod.'"
			data-type="grand-production" data-month="'.$i.'"
		><b>'.number_format($$gt_prod).'</b></td>';
	}
	// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';
	?>
	<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
		0
	</th>
</tr>
<tr>
	<td>Sales</td>
	<?php
	$bgedit ="";
	$contentedit ="false" ;
	// for ($i = setting('actual_budget'); $i <= 12; $i++) {
	for ($i = 1; $i <= 12; $i++) {			
		$gt_sales = 'tsales' . sprintf('%02d', $i);

		$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sumtotalfield0.'"
		data-type="grand-sales" data-month="'.$i.'"><b>'.number_format($$gt_sales).'</b></td>';
	}
	// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';
	?>
	<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
		0
	s</th>
</tr>
<tr>
	<td>End Stock</td>
	<?php
	$bgedit ="";
	$contentedit ="false" ;
	// for ($i = setting('actual_budget'); $i <= 12; $i++) {
	for ($i = 1; $i <= 12; $i++) {			
		$gt_begining = 'tbegining' . sprintf('%02d', $i);
		$gt_prod = 'tprod_' . sprintf('%02d', $i);
		$gt_sales = 'tsales' . sprintf('%02d', $i);
		$gt_end = 'tend' . sprintf('%02d', $i);

		$$gt_end = ($$gt_begining + $$gt_prod) - $$gt_sales ;

		$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sumtotalfield0.'"
		data-type="grand-end-stock" data-month="'.$i.'"><b>'.number_format($$gt_end).'</b></td>';
	}
	// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';
	?>
	<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
		0
	</th>
</tr>
<tr>
	<td>M Coverage</td>
	<?php
	$bgedit ="";
	$contentedit ="false" ;
	// for ($i = setting('actual_budget'); $i <= 12; $i++) {
	for ($i = 1; $i <= 12; $i++) {				
		$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sumtotalfield0.'"
		data-type="grand-m-cov" data-month="'.$i.'"><b>'.number_format($$sumtotalfield0).'</b></td>';
	}
	// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';
	?>
	<th class="text-right" data-type="left-total" data-cost-center="<?=$m0->id?>">
		0
	</th>
</tr>