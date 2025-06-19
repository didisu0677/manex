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
            </tr>
        </thead>

        <tbody>
            <?php foreach($detail as $p) { ?>
            <tr>
                <td><?php echo $p->component_item; ?></td>
                <td><?php echo $p->material_name; ?> </td>
                <td><?php echo $p->um; ?> </td>
                <td><?php echo $p->quantity; ?> </td>
                <td><?php echo $p->group_formula; ?> </td>
                <td><?php echo number_format($p->price_us,5); ?> </td>
            </tr>
            <?php } ?>
        </tbody>


    </table>
</div>
        </div>
    </div>
</div
