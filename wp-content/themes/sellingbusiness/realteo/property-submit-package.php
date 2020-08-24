<?php
/**
 * property Submission Form
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$fields = array();
if(isset($data)) :
	$fields	 	= (isset($data->fields)) ? $data->fields : '' ;
endif;
if(isset($_GET["action"])) {
	$form_type = $_GET["action"];
} else {
	$form_type = 'submit';
}
$packages = $data->packages;
$user_packages = $data->user_packages;
$current = '';
if(isset($data)) :
	$current	 	= (isset($data->current)) ? $data->current : '' ;
endif;

$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role = array_shift( $roles );
?>
<form method="post" id="package_selection">
<?php if ( $packages || $user_packages ) :
	$checked = 1;
	?>

    <div class="row">
        <div class="col-12 col-lg-6">
        <?php if ( $user_packages ) : ?>
             <h4 class="headline margin-bottom-20"><?php _e( 'Choose your Package:', 'realteo' ); ?></h4>
            <ul class="products user-packages">
                <?php
                foreach ( $user_packages as $key => $package ) :
                    $package = realteo_get_package( $package );
                    ?>
                    <li class="user-job-package">
                    <input type="radio" <?php checked( $checked, 1 ); ?> name="package" value="user-<?php echo $key; ?>" id="user-package-<?php echo $package->get_id(); ?>" />
                    <label for="user-package-<?php echo $package->get_id(); ?>"><?php echo $package->get_title(); ?>                    <p>
                        <?php
                        if ( $package->get_limit() ) {
                            printf( _n( 'You have %1$s properties posted out of %2$d', 'You have %1$s properties posted out of %2$d', $package->get_count(), 'realteo' ), $package->get_count(), $package->get_limit() );
                        } else {
                            printf( _n( 'You have %s properties posted', 'You have %s properties posted', $package->get_count(), 'realteo' ), $package->get_count() );
                        }

                        if ( $package->get_duration() ) {
                            printf( ', ' . _n( 'listed for %s day', 'listed for %s days', $package->get_duration(), 'realteo' ), $package->get_duration() );
                        }

                        $checked = 0;
                    ?>
                    </p></label>

                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        </div>
        <?php if ( $user_packages ) : ?>
        <div class="col-12 col-lg-6">
            <?php else: ?>
        <div>        
        <?php endif; if ( $packages ) : ?>

            <h4 class="headline margin-bottom-25">
                <?php
                if ( $user_packages ) :
                    esc_html_e('Or Purchase New Package:','realteo');
                else:
                    esc_html_e( 'Choose Package:', 'realteo' ); ?>
                <?php endif; ?>
            </h4>
            <div class="clearfix"></div>
            <div class="pricing-container margin-top-30">

            <?php foreach ( $packages as $key => $package ) :
            $counter = 0;
                $product = wc_get_product( $package );
                if ( ! $product->is_type( array( 'property_package' ) ) || ! $product->is_purchasable() ) {
                    continue;
                }
                ?>


                <div class="plan <?php echo ($product->is_featured()) ? 'featured' : '' ; ?>">
                    <?php if( $product->is_featured() ) : ?>
                        <div class="listing-badges">
                            <span class="featured"><?php esc_html_e('Featured','realteo'); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="plan-price">
                        <div class="inner-plan-price">
                        <h3><?php echo $product->get_title();?></h3>
                           <span class="value"> <?php echo $product->get_price_html(); ?></span>
                        <span class="period"><?php echo $product->get_short_description(); ?></span>
                        </div>
                    </div>

                <div class="plan-features">
                    <ul>
                        <?php
                        $propertieslimit = $product->get_limit();
                        if(!$propertieslimit){
                            echo "<li>";
                             esc_html_e('Unlimited number of business listings','realteo');
                             echo "</li>";
                        } else { ?>
                            <li>
                                <?php esc_html_e('This plan includes ','realteo'); printf( _n( '%d businesses', '%s businesses', $propertieslimit, 'realteo' ) . ' ', $propertieslimit ); ?>
                            </li>
                        <?php } ?>
                        <?php if( $product->get_duration() ) { ?>
                        <li>
                            <?php esc_html_e('Business listings are visible ','realteo'); printf( _n( 'for %s day', 'for %s days', $product->get_duration(), 'realteo' ), $product->get_duration() ); ?>
                        </li>
                        <?php } ?>

                    </ul>
                    <?php

                        //echo $product->get_description();

                    ?>

                    <div><input type="radio" name="package" value="<?php echo $product->get_id(); ?>" id="package-<?php echo $product->get_id(); ?>" />
                      <label for="package-<?php echo $product->get_id(); ?>"> <?php esc_html_e('Buy this package','realteo'); ?></label></div>

                </div>
            </div>

            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </ul>
<?php else : ?>

    <p><?php _e( 'No packages found', 'realteo' ); ?></p>

<?php endif; ?>
        </div>
    </div>

<div class="submit-page">
	<p>
		<input type="hidden" 	name="realteo_form" value="<?php echo $data->form; ?>" />
		<input type="hidden" 	name="property_id" value="<?php echo esc_attr( $data->property_id ); ?>" />
		<input type="hidden" 	name="step" value="<?php echo esc_attr( $data->step ); ?>" />
		<div class="input-with-icon big  margin-top-10 margin-bottom-30"><i class="sl sl-icon-plus" style="margin-top: 1px;"></i><input type="submit" name="continue" class="button" value="Submit Business" /></div>
	</p>

</form>
</div>