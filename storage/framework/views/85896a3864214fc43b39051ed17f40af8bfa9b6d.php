<?php $__currentLoopData = $variations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr <?php if(!empty($purchase_order_line)): ?> data-purchase_order_id="<?php echo e($purchase_order_line->transaction_id, false); ?>" <?php endif; ?> <?php if(!empty($purchase_requisition_line)): ?> data-purchase_requisition_id="<?php echo e($purchase_requisition_line->transaction_id, false); ?>" <?php endif; ?>>
        <td><span class="sr_number"></span></td>
        <td>
            <?php echo e($product->name, false); ?> (<?php echo e($variation->sub_sku, false); ?>)
            <?php if( $product->type == 'variable' ): ?>
                <br/>
                (<b><?php echo e($variation->product_variation->name, false); ?></b> : <?php echo e($variation->name, false); ?>)
            <?php endif; ?>
            <?php if($product->enable_stock == 1): ?>
                <br>
                <small class="text-muted" style="white-space: nowrap;"><?php echo app('translator')->get('report.current_stock'); ?>: <?php if(!empty($variation->variation_location_details->first())): ?> <?php echo e(number_format($variation->variation_location_details->first()->qty_available, session('business.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']), false); ?> <?php else: ?> 0 <?php endif; ?> <?php echo e($product->unit->short_name, false); ?></small>
            <?php endif; ?>
            
        </td>
        <td>
            <?php if(!empty($purchase_order_line)): ?>
                <?php echo Form::hidden('purchases[' . $row_count . '][purchase_order_line_id]', $purchase_order_line->id ); ?>

            <?php endif; ?>

            <?php if(!empty($purchase_requisition_line)): ?>
                <?php echo Form::hidden('purchases[' . $row_count . '][purchase_requisition_line_id]', $purchase_requisition_line->id ); ?>

            <?php endif; ?>

            <?php echo Form::hidden('purchases[' . $row_count . '][product_id]', $product->id ); ?>

            <?php echo Form::hidden('purchases[' . $row_count . '][variation_id]', $variation->id , ['class' => 'hidden_variation_id']); ?>


            <?php
                $check_decimal = 'false';
                if($product->unit->allow_decimal == 0){
                    $check_decimal = 'true';
                }
                $currency_precision = session('business.currency_precision', 2);
                $quantity_precision = session('business.quantity_precision', 2);

                $quantity_value = !empty($purchase_order_line) ? $purchase_order_line->quantity : 1;

                $quantity_value = !empty($purchase_requisition_line) ? $purchase_requisition_line->quantity - $purchase_requisition_line->po_quantity_purchased : $quantity_value;
                $max_quantity = !empty($purchase_order_line) ? $purchase_order_line->quantity - $purchase_order_line->po_quantity_purchased : 0;

                $max_quantity = !empty($purchase_requisition_line) ? $purchase_requisition_line->quantity - $purchase_requisition_line->po_quantity_purchased : $max_quantity;

                $quantity_value = !empty($imported_data) ? $imported_data['quantity'] : $quantity_value;
            ?>
            
            <input type="text" 
                name="purchases[<?php echo e($row_count, false); ?>][quantity]" 
                value="<?php echo e(number_format($quantity_value, session('business.quantity_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']), false); ?>"
                class="form-control input-sm purchase_quantity input_number mousetrap"
                required
                data-rule-abs_digit=<?php echo e($check_decimal, false); ?>

                data-msg-abs_digit="<?php echo e(__('lang_v1.decimal_value_not_allowed'), false); ?>"
                <?php if(!empty($max_quantity)): ?>
                    data-rule-max-value="<?php echo e($max_quantity, false); ?>"
                    data-msg-max-value="<?php echo e(__('lang_v1.max_quantity_quantity_allowed', ['quantity' => $max_quantity]), false); ?>" 
                <?php endif; ?>
            >


            <input type="hidden" class="base_unit_cost" value="<?php echo e($variation->default_purchase_price, false); ?>">
            <input type="hidden" class="base_unit_selling_price" value="<?php echo e($variation->sell_price_inc_tax, false); ?>">

            <input type="hidden" name="purchases[<?php echo e($row_count, false); ?>][product_unit_id]" value="<?php echo e($product->unit->id, false); ?>">
            <?php if(!empty($sub_units)): ?>
                <br>
                <select name="purchases[<?php echo e($row_count, false); ?>][sub_unit_id]" class="form-control input-sm sub_unit">
                    <?php $__currentLoopData = $sub_units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key, false); ?>" data-multiplier="<?php echo e($value['multiplier'], false); ?>">
                            <?php echo e($value['name'], false); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            <?php else: ?> 
                <?php echo e($product->unit->short_name, false); ?>

            <?php endif; ?>

            <?php if(!empty($product->second_unit)): ?>
                <?php
                    $secondary_unit_quantity = !empty($purchase_requisition_line) ? $purchase_requisition_line->secondary_unit_quantity : "";
                ?>
                <br>
                <span style="white-space: nowrap;">
                <?php echo app('translator')->get('lang_v1.quantity_in_second_unit', ['unit' => $product->second_unit->short_name]); ?>*:</span><br>
                <input type="text" 
                name="purchases[<?php echo e($row_count, false); ?>][secondary_unit_quantity]" 
                <?php if($secondary_unit_quantity !== ''): ?>value="<?php echo e(number_format($secondary_unit_quantity, session('business.quantity_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']), false); ?>" <?php endif; ?>
                class="form-control input-sm input_number"
                required>
            <?php endif; ?>
        </td>
        <td>
            <?php
                $pp_without_discount = !empty($purchase_order_line) ? $purchase_order_line->pp_without_discount/$purchase_order->exchange_rate : $variation->default_purchase_price;

                $discount_percent = !empty($purchase_order_line) ? $purchase_order_line->discount_percent : 0;

                $purchase_price = !empty($purchase_order_line) ? $purchase_order_line->purchase_price/$purchase_order->exchange_rate : $variation->default_purchase_price;

                $tax_id = !empty($purchase_order_line) ? $purchase_order_line->tax_id : $product->tax;

                $tax_id = !empty($imported_data['tax_id']) ? $imported_data['tax_id'] : $tax_id;

                $pp_without_discount = !empty($imported_data['unit_cost_before_discount']) ? $imported_data['unit_cost_before_discount'] : $pp_without_discount;

                $discount_percent = !empty($imported_data['discount_percent']) ? $imported_data['discount_percent'] : $discount_percent;
            ?>
            <?php echo Form::text('purchases[' . $row_count . '][pp_without_discount]',
            number_format($pp_without_discount, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost_without_discount input_number', 'required']); ?>


            <?php if(!empty($last_purchase_line)): ?>
                <br>
                <small class="text-muted"><?php echo app('translator')->get('lang_v1.prev_unit_price'); ?>: <?php 
            $formated_number = "";
            if (session("business.currency_symbol_placement") == "before") {
                $formated_number .= session("currency")["symbol"] . " ";
            } 
            $formated_number .= number_format((float) $last_purchase_line->pp_without_discount, session("business.currency_precision", 2) , session("currency")["decimal_separator"], session("currency")["thousand_separator"]);

            if (session("business.currency_symbol_placement") == "after") {
                $formated_number .= " " . session("currency")["symbol"];
            }
            echo $formated_number; ?></small>
            <?php endif; ?>
        </td>
        <td>
            <?php echo Form::text('purchases[' . $row_count . '][discount_percent]', number_format($discount_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm inline_discounts input_number', 'required']); ?>


            <?php if(!empty($last_purchase_line)): ?>
                <br>
                <small class="text-muted">
                    <?php echo app('translator')->get('lang_v1.prev_discount'); ?>: 
                    <?php echo e(number_format($last_purchase_line->discount_percent, session('business.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']), false); ?>%
                </small>
            <?php endif; ?>
        </td>
        <td>
            <?php echo Form::text('purchases[' . $row_count . '][purchase_price]',
            number_format($purchase_price, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost input_number', 'required']); ?>

        </td>
        <td class="<?php echo e($hide_tax, false); ?>">
            <span class="row_subtotal_before_tax display_currency">0</span>
            <input type="hidden" class="row_subtotal_before_tax_hidden" value=0>
        </td>
        <td class="<?php echo e($hide_tax, false); ?>">
            <div class="input-group">
                <select name="purchases[<?php echo e($row_count, false); ?>][purchase_line_tax_id]" class="form-control select2 input-sm purchase_line_tax_id" placeholder="'Please Select'">
                    <option value="" data-tax_amount="0" <?php if( $hide_tax == 'hide' ): ?>
                    selected <?php endif; ?> ><?php echo app('translator')->get('lang_v1.none'); ?></option>
                    <?php $__currentLoopData = $taxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tax->id, false); ?>" data-tax_amount="<?php echo e($tax->amount, false); ?>" <?php if( $tax_id == $tax->id && $hide_tax != 'hide'): ?> selected <?php endif; ?> ><?php echo e($tax->name, false); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php echo Form::hidden('purchases[' . $row_count . '][item_tax]', 0, ['class' => 'purchase_product_unit_tax']); ?>

                <span class="input-group-addon purchase_product_unit_tax_text">
                    0.00</span>
            </div>
        </td>
        <td class="<?php echo e($hide_tax, false); ?>">
            <?php
                $dpp_inc_tax = number_format($variation->dpp_inc_tax, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);
                if($hide_tax == 'hide'){
                    $dpp_inc_tax = number_format($variation->default_purchase_price, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator);
                }

                $dpp_inc_tax = !empty($purchase_order_line) ? number_format($purchase_order_line->purchase_price_inc_tax/$purchase_order->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator) : $dpp_inc_tax;

            ?>
            <?php echo Form::text('purchases[' . $row_count . '][purchase_price_inc_tax]', $dpp_inc_tax, ['class' => 'form-control input-sm purchase_unit_cost_after_tax input_number', 'required']); ?>

        </td>
        <td>
            <span class="row_subtotal_after_tax display_currency">0</span>
            <input type="hidden" class="row_subtotal_after_tax_hidden" value=0>
        </td>
        <td class="<?php if(!session('business.enable_editing_product_from_purchase') || !empty($is_purchase_order)): ?> hide <?php endif; ?>">
            <?php echo Form::text('purchases[' . $row_count . '][profit_percent]', number_format($variation->profit_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm input_number profit_percent', 'required']); ?>

        </td>
        <?php if(empty($is_purchase_order)): ?>
        <td>
            <?php if(session('business.enable_editing_product_from_purchase')): ?>
                <?php echo Form::text('purchases[' . $row_count . '][default_sell_price]', number_format($variation->sell_price_inc_tax, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm input_number default_sell_price', 'required']); ?>

            <?php else: ?>
                <?php echo e(number_format($variation->sell_price_inc_tax, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), false); ?>

            <?php endif; ?>
        </td>
        <?php if(session('business.enable_lot_number')): ?>
            <?php
                $lot_number = !empty($imported_data['lot_number']) ? $imported_data['lot_number'] : null;
            ?>
            <td>
                <?php echo Form::text('purchases[' . $row_count . '][lot_number]', $lot_number, ['class' => 'form-control input-sm']); ?>

            </td>
        <?php endif; ?>
        <?php if(session('business.enable_product_expiry')): ?>
            <td style="text-align: left;">

                
                <?php
                    $expiry_period_type = !empty($product->expiry_period_type) ? $product->expiry_period_type : 'month';
                ?>
                <?php if(!empty($expiry_period_type)): ?>
                <input type="hidden" class="row_product_expiry" value="<?php echo e($product->expiry_period, false); ?>">
                <input type="hidden" class="row_product_expiry_type" value="<?php echo e($expiry_period_type, false); ?>">

                <?php if(session('business.expiry_type') == 'add_manufacturing'): ?>
                    <?php
                        $hide_mfg = false;
                    ?>
                <?php else: ?>
                    <?php
                        $hide_mfg = true;
                    ?>
                <?php endif; ?>

                <?php
                    $mfg_date = !empty($imported_data['mfg_date']) ? $imported_data['mfg_date'] : null;
                    $exp_date = !empty($imported_data['exp_date']) ? $imported_data['exp_date'] : null;
                ?>

                <b class="<?php if($hide_mfg): ?> hide <?php endif; ?>"><small><?php echo app('translator')->get('product.mfg_date'); ?>:</small></b>
                <div class="input-group <?php if($hide_mfg): ?> hide <?php endif; ?>">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <?php echo Form::text('purchases[' . $row_count . '][mfg_date]', $mfg_date, ['class' => 'form-control input-sm expiry_datepicker mfg_date', 'readonly']); ?>

                </div>
                <b><small><?php echo app('translator')->get('product.exp_date'); ?>:</small></b>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <?php echo Form::text('purchases[' . $row_count . '][exp_date]', $exp_date, ['class' => 'form-control input-sm expiry_datepicker exp_date', 'readonly']); ?>

                </div>
                <?php else: ?>
                <div class="text-center">
                    <?php echo app('translator')->get('product.not_applicable'); ?>
                </div>
                <?php endif; ?>
            </td>
        <?php endif; ?>
        <?php endif; ?>
        <?php $row_count++ ;?>

        <td><i class="fa fa-times remove_purchase_entry_row text-danger" title="Remove" style="cursor:pointer;"></i></td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<input type="hidden" id="row_count" value="<?php echo e($row_count, false); ?>"><?php /**PATH C:\xampp\htdocs\pos\resources\views/purchase/partials/purchase_entry_row.blade.php ENDPATH**/ ?>