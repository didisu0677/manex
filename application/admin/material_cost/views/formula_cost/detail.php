<div class="card mb-2">
    <div class="card-header"><?php echo lang('item_product'); ?></div>
    <div class="card-body p-1">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-app table-detail table-normal">
                <tr>
                    <th width="200"><?php echo lang('product_code'); ?></th>
                    <td colspan="3"><?php echo $code; ?></td>
                </tr>
                <tr>
                    <th width="200"><?php echo lang('product_name'); ?></th>
                    <td colspan="3"><?php echo $product_name; ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="card mb-2">
    <div class="card-header"><?php echo lang('material_detail'); ?></div>
    <div class="card-body p-1">
<div class="table-responsive mb-2">
    <table class="table table-bordered table-app table-detail table-normal">
        <thead> 
            <tr>
                <th style="background-color: #CC0000; color: white;" width =""><font color="#fff"><?php echo lang('material_code'); ?></th>
                <th style="background-color: #CC0000; color: white;" width ="" class="text-center"><font color="#fff"><?php echo lang('material_name'); ?></th>
                <th style="background-color: #CC0000; color: white;"><font color="#fff"><?php echo lang('um'); ?></th>
                <th style="background-color: #CC0000; color: white;"><font color="#fff"><?php echo lang('quantity'); ?></th>
                <th style="background-color: #CC0000; color: white;"><font color="#fff"><?php echo lang('group_formula'); ?></th>
                <th style="background-color: #CC0000; color: white;"><font color="#fff"><?php echo lang('price'); ?></th>
                <th style="background-color: #CC0000; color: white;"><font color="#fff"><?php echo lang('curr'); ?></th>
                <th style="background-color: #CC0000; color: white;"><font color="#fff"><?php echo lang('rates'); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($detail as $p) { ?>
            <tr>
                <?php 
                    $group_formula = '';
                    if($p->group_formula == 'A') {
                        $group_formula = 'Bottle';
                    }elseif($p->group_formula=='B') {
                        $group_formula = 'Content';
                    }elseif($p->group_formula=='C'){
                        $group_formula = 'Packing';
                    }elseif($p->group_formula == 'D'){
                        $group_formula = 'Set';
                    }else{
                         $group_formula = '';
                    }

                $bm_amt = $p->total_price * ($p->bm/100);
                $pph = ($bm_amt + $p->total_price) * ($p->pph/100);
                $ppn = ($bm_amt + $p->total_price) * ($p->ppn/100);
                $price_budget = $p->total_price + $bm_amt + $p->bank_charges + $p->handling_charges ;

                ?>
                <td><?php echo $p->component_item; ?></td>
                <td><?php echo $p->material_name; ?> </td>
                <td><?php echo $p->um; ?> </td>
                <td><?php echo $p->quantity; ?> </td>
                <td><?php echo $group_formula; ?> </td>
                <td><?php echo number_format($p->price_us,5); ?> </td>
                <td><?php echo $p->curr; ?> </td>
                <td><?php echo number_format($p->rates,2); ?> </td>
            </tr>
            <?php } ?>
        </tbody>


    </table>
</div>
        </div>
    </div>
</div
