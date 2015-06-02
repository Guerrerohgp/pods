<?php
$date_format = array(
	'mdy'       => 'mm/dd/yy',
	'mdy_dash'  => 'mm-dd-yy',
	'mdy_dot'   => 'mm.dd.yy',
	'dmy'       => 'dd/mm/yy',
	'dmy_dash'  => 'dd-mm-yy',
	'dmy_dot'   => 'dd.mm.yy',
	'ymd_slash' => 'yy/mm/dd',
	'ymd_dash'  => 'yy-mm-dd',
	'ymd_dot'   => 'yy.mm.dd',
	'dMy'       => 'dd/M/yy',
	'dMy_dash'  => 'dd-M-yy',
	'fjy'       => 'MM d, yy',
	'fjsy'      => 'MM d, yy',
	'Dfjy'      => 'DD MM d, yy',
	'lfjsy'     => 'D MM d, yy',
	'y'         => 'yy'
);

$date_format = apply_filters( 'pods_form_ui_field_date_js_formats', $date_format );

wp_enqueue_script( 'jquery-ui-datepicker' );
wp_enqueue_style( 'jquery-ui' );

$attributes = array();

$type = 'text';

if ( 1 == pods_v( $form_field_type . '_html5', $options ) ) {
	$type = $form_field_type;
}

$attributes[ 'type' ] = $type;
$attributes[ 'tabindex' ] = 2;

$format = Pods_Form::field_method( 'date', 'format', $options );

$method = 'datepicker';

$args = array(
	'dateFormat'  => $date_format[ pods_v( $form_field_type . '_format', $options, 'mdy', true ) ],
	'changeMonth' => true,
	'changeYear'  => true,
	'yearRange'   => pods_v( $form_field_type . '_year_range', $options, 'c-10:c+10', true ),
	'minDate'     => pods_v( $form_field_type . '_min_date', $options ),
	'maxDate'     => pods_v( $form_field_type . '_max_date', $options )
);

$html5_format = 'Y-m-d';

$date = Pods_Form::field_method( 'date', 'createFromFormat', $format, (string) $value );
$date_default = Pods_Form::field_method( 'date', 'createFromFormat', 'Y-m-d', (string) $value );

$formatted_date = $value;

if ( 1 == pods_v( $form_field_type . '_allow_empty', $options, 1 ) && in_array( $value, array(
			'',
			'0000-00-00',
			'0000-00-00 00:00:00',
			'00:00:00'
		) )
) {
	$formatted_date = $value = '';
} elseif ( 'text' != $type ) {
	$formatted_date = $value;

	if ( false !== $date ) {
		$value = $date->format( $html5_format );
	} elseif ( false !== $date_default ) {
		$value = $date_default->format( $html5_format );
	} elseif ( ! empty( $value ) ) {
		$value = date_i18n( $html5_format, strtotime( (string) $value ) );
	} else {
		$value = date_i18n( $html5_format );
	}
}

$args = apply_filters( 'pods_form_ui_field_date_args', $args, $type, $options, $attributes, $name, $form_field_type );

$attributes[ 'value' ] = $value;

$attributes = Pods_Form::merge_attributes( $attributes, $name, $form_field_type, $options );
?>
<input<?php Pods_Form::attributes( $attributes, $name, $form_field_type, $options ); ?> />

<script>
	jQuery( function () {
		var <?php echo esc_js( pods_clean_name( $attributes[ 'id' ] ) ); ?>_args = <?php echo json_encode( $args ); ?>;

		<?php
			if ( 'text' != $type ) {
		?>
		if ( 'undefined' == typeof pods_test_date_field_<?php echo esc_js( $type ); ?> ) {
			// Test whether or not the browser supports date inputs
			function pods_test_date_field_<?php echo esc_js( $type ); ?> () {
				var input = jQuery( '<input/>', {
					'type' : '<?php echo esc_js( $type ); ?>',
					css : {
						position : 'absolute',
						display : 'none'
					}
				} );

				jQuery( 'body' ).append( input );

				var bool = input.prop( 'type' ) !== 'text';

				if ( bool ) {
					var smile = ":)";
					input.val( smile );

					return (input.val() != smile);
				}
			}
		}

		if ( !pods_test_date_field_<?php echo esc_js( $type ); ?>() ) {
			jQuery( 'input#<?php echo esc_js( $attributes[ 'id' ] ); ?>' ).val( '<?php echo esc_js( $formatted_date ); ?>' );
			jQuery( 'input#<?php echo esc_js( $attributes[ 'id' ] ); ?>' ).<?php echo esc_js( $method ); ?>( <?php echo esc_js( pods_clean_name( $attributes[ 'id' ] ) ); ?>_args );
		}
		<?php
			}
			else {
		?>
		jQuery( 'input#<?php echo esc_js( $attributes[ 'id' ] ); ?>' ).<?php echo esc_js( $method ); ?>( <?php echo esc_js( pods_clean_name( $attributes[ 'id' ] ) ); ?>_args );
		<?php
			}
		?>
	} );
</script>
