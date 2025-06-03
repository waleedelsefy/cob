<div class="right-landing-flat">
    <h5><?php echo esc_html__( 'Facilities and services', 'cob_theme' ); ?></h5>
    <div class="underline"></div>
    <div class="tags">
		<?php get_template_part( 'template-parts/properties-post/project-facilities' ); ?>
    </div>
	<?php
	$post_id = get_the_ID();
	$payment_plans = get_post_meta( $post_id, 'cob_payment_plans', true );
	if ( ! is_array( $payment_plans ) ) {
		$payment_plans = array();
	}
	if ( ! empty( $payment_plans ) ) :
		?>
        <h5><?php echo esc_html__( 'Payment systems', 'cob_theme' ); ?></h5>
        <div class="underline"></div>
        <div id="cob-payment-plans" class="payment-plans">
			<?php foreach ( $payment_plans as $plan ) : ?>
                <div class="plan">
                    <h6><?php echo number_format( floatval( $plan['down_payment_percentage'] ), 0 ) . '%'; ?></h6>
                    <p><?php echo esc_html__( 'Deposit', 'cob_theme' ); ?></p>
                    <p><?php echo esc_attr( $plan['years'] ) . ' ' . esc_html__( 'Years', 'cob_theme' ); ?></p>
                </div>
			<?php endforeach; ?>
        </div>
	<?php endif; ?>

    <h5><?php echo esc_html__( 'About the property', 'cob_theme' ); ?></h5>
    <div class="underline"></div>
	<?php the_content(); ?>
</div>
