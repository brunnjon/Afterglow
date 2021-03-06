<?php
/**
 * Checkbox Acceptance Type
 */
if (!class_exists('Bxcft_Field_Type_CheckboxAcceptance')) 
{
    class Bxcft_Field_Type_CheckboxAcceptance extends BP_XProfile_Field_Type
    {
        public function __construct() {
            parent::__construct();

            $this->name             = _x( 'Checkbox Acceptance', 'xprofile field type', 'bxcft' );
            
            $this->accepts_null_value   = true;
            $this->supports_options     = true;
            
            $this->set_format( '/^.+$/', 'replace' );
            do_action( 'bp_xprofile_field_type_checkbox_acceptance', $this );
        }

        public function admin_field_html (array $raw_properties = array ())
        {
            global $field;
            
            $options = $field->get_children( true );
            $text = '';
            foreach ($options as $option) {
                $text .= rawurldecode($option->name);
            }
            
            $html = $this->get_edit_field_html_elements( array_merge(
                array( 'type' => 'checkbox' ),
                $raw_properties
            ) );
        ?>
            <label for="<?php bp_the_profile_field_input_name(); ?>">
                <input <?php echo $html; ?>>
                <?php echo $text; ?>
            </label>
        <?php
        }

        public function edit_field_html (array $raw_properties = array ())
        {
            $user_id = bp_displayed_user_id();
            
            if ( isset( $raw_properties['user_id'] ) ) {
                $user_id = (int) $raw_properties['user_id'];
                unset( $raw_properties['user_id'] );
            }
            
            // HTML5 required attribute.
            if ( bp_get_the_profile_field_is_required() ) {
                $raw_properties['required'] = 'required';
                $required = true;
            } else {
                $required = false;
            }
        ?>
            <div class="checkbox_acceptance">

                <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php esc_html_e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
                <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
                <?php bp_the_profile_field_options( "user_id={$user_id}&required={$required}" ); ?>

            </div>
        <?php
        }
        
        public function admin_new_field_html (\BP_XProfile_Field $current_field, $control_type = '')
        {
            $type = array_search( get_class( $this ), bp_xprofile_get_field_types() );
            if ( false === $type ) {
                return;
            }
            
            $class            = $current_field->type != $type ? 'display: none;' : '';
            $current_type_obj = bp_xprofile_create_field_type( $type );
            
            $text = '';
            $options = $current_field->get_children( true );
            if ( ! $options ) {
                $options = array();
                $i       = 1;
                while ( isset( $_POST[$type . '_option'][$i] ) ) {
                    if ( $current_type_obj->supports_options && ! $current_type_obj->supports_multiple_defaults && isset( $_POST["isDefault_{$type}_option"][$i] ) && (int) $_POST["isDefault_{$type}_option"] === $i ) {
                        $is_default_option = true;
                    } elseif ( isset( $_POST["isDefault_{$type}_option"][$i] ) ) {
                        $is_default_option = (bool) $_POST["isDefault_{$type}_option"][$i];
                    } else {
                        $is_default_option = false;
                    }

                    $options[] = (object) array(
                        'id'                => -1,
                        'is_default_option' => $is_default_option,
                        'name'              => sanitize_text_field( stripslashes( $_POST[$type . '_option'][$i] ) ),
                    );

                    $text .= sanitize_text_field( stripslashes( $_POST[$type . '_option'][$i] ) );
                    ++$i;
                }

                if ( ! $options ) {
                    $options[] = (object) array(
                        'id'                => -1,
                        'is_default_option' => false,
                        'name'              => '',
                    );
                }
            } else {
                foreach ($options as $option) {
                    $text .= rawurldecode($option->name);
                }
            }
        ?>
            <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
                <h3><?php esc_html_e( 'Use this field to write a text that should be displayed beside the checkbox:', 'bxcft' ); ?></h3>
                <div class="inside">
                    <p>
                        <textarea name="<?php echo esc_attr( "{$type}_text" ); ?>" 
                                  id="<?php echo esc_attr( "{$type}_text" ); ?>" rows="5" cols="60"><?php echo $text; ?></textarea>
                    </p>
                </div>
                <?php if ($options):$i=1; ?>
                    <?php foreach ($options as $option): ?>
                    <input type="hidden" name="<?php echo esc_attr( "{$type}_option[{$i}]"); ?>"
                           id ="<?php echo esc_attr( "{$type}_option{$i}"); ?>" value="<?php echo $option->name; ?>" />
                    <?php $i++; endforeach; ?>
                <?php endif; ?>
            </div>
        <?php  
        }
        
        public function edit_field_options_html( array $args = array() ) 
        {
            $options                = $this->field_obj->get_children();
            $checkbox_acceptance    = maybe_unserialize(BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] ));
            
            if ( !empty($_POST['field_' . $this->field_obj->id]) ) {
                $new_checkbox_acceptance = $_POST['field_' . $this->field_obj->id];
                $checkbox_acceptance = ( $checkbox_acceptance != $new_checkbox_acceptance ) ? $new_checkbox_acceptance : $checkbox_acceptance;
            }
            
            $html = '<input type="checkbox" name="check_acc_'.bp_get_the_profile_field_input_name().'" id="check_acc_'.bp_get_the_profile_field_input_name().'"';
            if ($checkbox_acceptance == 1) {
                $html .= ' checked="checked"';
            }
            if (isset($args['required']) && $args['required']) {
                $html .= ' required="required" aria-required="true"';
            }
            $html .= ' value="1" /> ';
            
            $html .= '<input type="hidden" name="'.bp_get_the_profile_field_input_name().'" id="'.bp_get_the_profile_field_input_name().'"';
            if ($checkbox_acceptance == 1) {
                $html .= ' value="1" /> ';
            } else {
                $html .= ' value="-1" /> ';
            }
            if ($options) {
                foreach ($options as $option) {
                    $html .= rawurldecode($option->name);
                }
            }
            
            // Javascript.
            $html .= '
                <script>
                    jQuery(document).ready(function() {
                        jQuery("#check_acc_'.bp_get_the_profile_field_input_name().'").click(function() {
                            if (jQuery(this).is(":checked")) {
                                jQuery("#'.bp_get_the_profile_field_input_name().'").val("1");
                            } else {
                                jQuery("#'.bp_get_the_profile_field_input_name().'").val("-1");
                            }
                        });
                    });
                </script>
            ';
            
            echo apply_filters( 'bp_get_the_profile_field_checkbox_acceptance', $html, $args['type'], $this->field_obj->id, $checkbox_acceptance );
        }
        
        public function is_valid( $values ) {
            $validated = false;

            // Some types of field (e.g. multi-selectbox) may have multiple values to check
            foreach ( (array) $values as $value ) {

                // Validate the $value against the type's accepted format(s).
                foreach ( $this->validation_regex as $format ) {
                    if ( 1 === preg_match( $format, $value ) ) {
                        $validated = true;
                        continue;

                    } else {
                        $validated = false;
                    }
                }
            }

            // Handle field types with accepts_null_value set if $values is an empty array
            if ( ! $validated && is_array( $values ) && empty( $values ) && $this->accepts_null_value ) {
                $validated = true;
            }

            return (bool) apply_filters( 'bp_xprofile_field_type_is_valid', $validated, $values, $this );
        }

    }
}
