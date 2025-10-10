<?php 
for ($i = 1; $i <= 12; $i++) { 
	$totalovh = 'totalovh' . 'B_' . sprintf('%02d', $i);
	$$totalovh = 0;
}
$totalovh_1tahun = 0;
foreach($mst_account[0] as $m0) { 
	?>
	<tr>
		<td><b><?php echo strtoupper($m0->grup); ?></b></td>
	</tr>

	<?php 
	for ($i = 1; $i <= 12; $i++) { 
		$total = 'total' . 'B_' . sprintf('%02d', $i);
		$$total = 0;
	}
	$total_1tahun = 0;
	foreach($mst_account[$m0->grup] as $m1) { 
		foreach($total_budget as $v => $t){
			if($m1->account_code == $v) {
		?>
		<tr>
			<td class="sub-1"><b><?php echo $m1->account_code . '-' .$m1->account_name; ?></b></td>
			<?php
			for ($i = 1; $i <= 12; $i++) { 
				$field1 = 'B_' . sprintf('%02d', $i);
				$total = 'total' . 'B_' . sprintf('%02d', $i);
				$totalovh = 'totalovh' . 'B_' . sprintf('%02d', $i);

				// Always show value or 0
				$x1 = ($t[$field1] && $t[$field1] > 0) ? number_format($t[$field1]) : '0';
				
				$$total += ($t[$field1] ?? 0);
				$$totalovh += ($t[$field1] ?? 0);
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" contenteditable="true" class="edit-value text-right budget '.$field1.'" data-name="'.$field1.'" data-id="'.$m1->id_trx.'" data-value="'.$x1.'">'.$x1.'</td>';
			}

			// Always show total or 0
			$xtotal1 = ($t['total'] && $t['total'] > 0) ? number_format($t['total']) : '0';
			$total_1tahun += ($t['total'] ?? 0);
			$totalovh_1tahun += ($t['total'] ?? 0);
			
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" contenteditable="true" class="edit-value text-right total_budget" data-name="total_budget" data-id="'.$m1->id_trx.'" data-value="'.$xtotal1.'">'.$xtotal1.'</td>';

			?>
		</tr>
		<?php 
		 ?>
         
	<?php }}} ?>
    <tr>
        <td><b><?php echo 'TOTAL ' . strtoupper($m0->grup);?></b></td>
        <?php
                for ($i = 1; $i <= 12; $i++) { 
					$total = 'total' . 'B_' . sprintf('%02d', $i);
					$total_value = ($$total && $$total > 0) ? number_format($$total) : '0';
					
                    echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" contenteditable="true" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m0->id_trx.'" data-value="'.$$total.'"><b>'.$total_value.'</b></td>';
                }
                
                $total_1tahun_display = ($total_1tahun && $total_1tahun > 0) ? number_format($total_1tahun) : '0';
                echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" contenteditable="true" class="edit-value text-right total_budget" data-name="total_budget" data-id="'.$m0->id_trx.'" data-value="'.$total_1tahun.'"><b>'.$total_1tahun_display.'</b></td>';        ?>
    </tr>
 
<?php } ?>
<tr>
<td><b><?php echo 'TOTAL OVERHEAD' ;?></b></td>
<?php
	for ($i = 1; $i <= 12; $i++) { 
		$totalovh = 'totalovh' . 'B_' . sprintf('%02d', $i);
		$totalovh_value = ($$totalovh && $$totalovh > 0) ? number_format($$totalovh) : '0';

		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" contenteditable="true" class="edit-value text-right budget '.$field1.'" data-name="'.$field1.'" data-id="'.$m1->id_trx.'" data-value="'.$$totalovh.'"><b>'.$totalovh_value.'</b></td>';
	}
	
	$totalovh_1tahun_display = ($totalovh_1tahun && $totalovh_1tahun > 0) ? number_format($totalovh_1tahun) : '0';
	echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" contenteditable="true" class="edit-value text-right total_budget" data-name="total_budget" data-id="'.$m0->id_trx.'" data-value="'.$totalovh_1tahun.'"><b>'.$totalovh_1tahun_display.'</b></td>';
 
?>
</tr>

	





			
