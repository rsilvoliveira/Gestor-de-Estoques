<div class="ffp_customer_details">
    <h4><?php _e('Customer Details', 'wppayform'); ?></h4>
    <div  class="ffp_submission_details">
        <ul>
            <li><?php _e('Customer Name:', 'fluentformpro');?> <?php echo $transaction->payer_name; ?></li>
            <li><?php _e('Customer Email:', 'fluentformpro');?> <?php echo $transaction->payer_email; ?></li>
            <?php if($transaction->billing_address && strlen($transaction->billing_address) > 2): ?>
            <li><?php _e('Billing Address:', 'fluentformpro');?> <?php echo $transaction->billing_address; ?></li>
            <?php endif; ?>
            <?php if($transaction->shipping_address && strlen($transaction->shipping_address) > 2): ?>
            <li><?php _e('Shipping Address:', 'fluentformpro');?> <?php echo $transaction->shipping_address; ?></li>
            <?php endif; ?>
        </ul>
    </div>
</div>