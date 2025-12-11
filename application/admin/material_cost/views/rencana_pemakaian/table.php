<?php 
if(empty($material)) {
    echo '<tr><td colspan="15" class="text-center">No data found for selected criteria</td></tr>';
    return;
}

foreach($material as $m2 => $m1) { 
    $bgedit ="#f0f0f0";
    $contentedit ="false" ;
    ?>
    <tr>
        <td style="background-color: #f8f9fa; font-weight: bold;"><?php echo isset($m1['material_name']) ? $m1['material_name'] : ''; ?></td>
        <td style="background-color: #f8f9fa; font-weight: bold;"><?php echo isset($m1['component_item']) ? $m1['component_item'] :'';?></td>
        <?php

        $total = 0;
        // Pre-calculate values untuk semua bulan sekaligus untuk efisiensi
        $monthly_values = [];
        for ($i = 1; $i <= 12; $i++) {
            $field0 = 'B_' . sprintf('%02d', $i);
            $value = isset($m1[$field0]) ? $m1[$field0] : 0;
            $total += $value;
            $monthly_values[$i] = ($value > 0) ? number_format($value) : '-';
        }

        // Output semua cell sekaligus
        for ($i = 1; $i <= 12; $i++) {
            echo '<td style="background: '.$bgedit.';" class="text-right">'.$monthly_values[$i].'</td>';
        }
        
        // Total column
        $total_display = ($total > 0) ? number_format($total) : '-';
        echo '<td style="background: '.$bgedit.';" class="text-right"><b>'.$total_display.'</b></td>';
        ?>
    </tr>
<?php 
} ?>
