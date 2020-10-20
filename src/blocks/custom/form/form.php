<?php
/**
 * Template for the Form Block view.
 *
 * @package Eightshift_Forms\Blocks.
 */

namespace Eightshift_Forms\Blocks;

use Eightshift_Forms\Helpers\Components;

use Eightshift_Forms\Core\Config;
use Eightshift_Forms\Rest\Dynamics_Crm_Route;
use Eightshift_Forms\Rest\Send_Email_Route;
use Eightshift_Forms\Rest\Base_Buckaroo_Route as Buckaroo_Route;
use Eightshift_Forms\Rest\Buckaroo_Emandate_Route;
use Eightshift_Forms\Rest\Mailchimp_Route;

$block_class                  = $attributes['blockClass'] ?? '';
$form_action                  = $attributes['action'] ?? '';
$form_method                  = $attributes['method'] ?? '';
$form_target                  = $attributes['target'] ?? '';
$form_classes                 = $attributes['classes'] ?? '';
$form_id                      = $attributes['id'] ?? '';
$form_type                    = $attributes['type'] ?? '';
$form_theme                   = $attributes['theme'] ?? '';
$success_message              = $attributes['successMessage'] ?? '';
$error_message                = $attributes['errorMessage'] ?? '';
$dynamics_crm_entity          = $attributes['dynamicsEntity'] ?? '';
$email_to                     = $attributes['emailTo'] ?? '';
$email_subject                = $attributes['emailSubject'] ?? '';
$email_message                = $attributes['emailMessage'] ?? '';
$email_additional_headers     = $attributes['emailAdditionalHeaders'] ?? '';
$buckaroo_redirect_url        = $attributes['buckarooRedirectUrl'] ?? '';
$buckaroo_redirect_url_cancel = $attributes['buckarooRedirectUrlCancel'] ?? '';
$buckaroo_redirect_url_error  = $attributes['buckarooRedirectUrlError'] ?? '';
$buckaroo_redirect_url_reject = $attributes['buckarooRedirectUrlReject'] ?? '';
$buckaroo_service             = $attributes['buckarooService'] ?? '';
$buckaroo_emandate_desc       = $attributes['buckarooEmandateDescription'] ?? '';
$buckaroo_sequence_type       = $attributes['buckarooSequenceType'] ?? '';
$mailchimp_list_id            = $attributes['mailchimpListId'] ?? '';
$mailchimp_tags               = $attributes['mailchimpTags'] ?? [];
$custom_event_names           = $attributes['eventNames'] ?? [];

$block_classes = Components::classnames([
  $block_class,
  $form_classes,
  'js-form',
  "js-form__type--{$form_type}",
  ! empty( $form_theme ) ? "{$block_class}__theme--{$form_theme}" : '',
]);

?>

<div class="<?php echo esc_attr( $block_classes ); ?>">
  <form
    class="<?php echo esc_attr( "{$block_class}__form js-{$block_class}-form" ); ?>"
    action="<?php echo esc_attr( $form_action ); ?>"
    method="<?php echo esc_attr( $form_method ); ?>"
    target="<?php echo esc_attr( $form_target ); ?>"
    data-form-type="<?php echo esc_attr( $form_type ); ?>"
    <?php ! empty( $form_id ) ? printf( 'id="%s"', esc_attr( $form_id ) ) : ''; ?>
    <?php ! empty( $buckaroo_service ) ? printf( 'data-buckaroo-service="%s"', esc_attr( $buckaroo_service ) ) : ''; ?>
  >
    <?php echo wp_kses_post( $inner_block_content ); ?>

    <?php if ( $form_type === Config::DYNAMICS_CRM_METHOD ) { ?>
      <input type="hidden" name="<?php echo esc_attr( Dynamics_Crm_Route::ENTITY_PARAM ); ?>" value="<?php echo esc_attr( $dynamics_crm_entity ); ?>" />
    <?php } ?>

    <?php if ( $form_type === Config::EMAIL_METHOD ) { ?>
      <input type="hidden" name="<?php echo esc_attr( Send_Email_Route::TO_PARAM ); ?>" value="<?php echo esc_attr( $email_to ); ?>" />
      <input type="hidden" name="<?php echo esc_attr( Send_Email_Route::SUBJECT_PARAM ); ?>" value="<?php echo esc_attr( $email_subject ); ?>" />
      <input type="hidden" name="<?php echo esc_attr( Send_Email_Route::MESSAGE_PARAM ); ?>" value="<?php echo esc_attr( $email_message ); ?>" />
      <input type="hidden" name="<?php echo esc_attr( Send_Email_Route::ADDITIONAL_HEADERS_PARAM ); ?>" value="<?php echo esc_attr( $email_additional_headers ); ?>" />
    <?php } ?>

    <?php if ( $form_type === Config::BUCKAROO_METHOD ) { ?>
      <input type="hidden" name="<?php echo esc_attr( Buckaroo_Route::REDIRECT_URL_PARAM ); ?>" value="<?php echo esc_attr( $buckaroo_redirect_url ); ?>" />
      <input type="hidden" name="<?php echo esc_attr( Buckaroo_Route::REDIRECT_URL_CANCEL_PARAM ); ?>" value="<?php echo esc_attr( $buckaroo_redirect_url_cancel ); ?>" />
      <input type="hidden" name="<?php echo esc_attr( Buckaroo_Route::REDIRECT_URL_ERROR_PARAM ); ?>" value="<?php echo esc_attr( $buckaroo_redirect_url_error ); ?>" />
      <input type="hidden" name="<?php echo esc_attr( Buckaroo_Route::REDIRECT_URL_REJECT_PARAM ); ?>" value="<?php echo esc_attr( $buckaroo_redirect_url_reject ); ?>" />

      <?php if ( $buckaroo_service === 'emandate' ) { ?>
        <input type="hidden" name="<?php echo esc_attr( Buckaroo_Emandate_Route::SEQUENCE_TYPE_PARAM ); ?>" value="<?php echo esc_attr( $buckaroo_sequence_type ); ?>" />
        <input type="hidden" name="<?php echo esc_attr( Buckaroo_Emandate_Route::EMANDATE_DESCRIPTION_PARAM ); ?>" value="<?php echo esc_attr( $buckaroo_emandate_desc ); ?>" />
      <?php } ?>
    <?php } ?>

    <?php if ( $form_type === Config::MAILCHIMP_METHOD ) { ?>
      <input type="hidden" name="<?php echo esc_attr( Mailchimp_Route::LIST_ID_PARAM ); ?>" value="<?php echo esc_attr( $mailchimp_list_id ); ?>" />

      <?php foreach ( $mailchimp_tags as $mailchimp_tag ) { ?>
        <input type="hidden" name="<?php echo esc_attr( Mailchimp_Route::TAGS_PARAM ); ?>[]" value="<?php echo esc_attr( $mailchimp_tag ); ?>" />
      <?php } ?>
    <?php } ?>

    <?php if ( $form_type === Config::CUSTOM_EVENT_METHOD ) { ?>
      <?php foreach ( $custom_event_names as $custom_event_name ) { ?>
        <input type="hidden" name="custom-events[]" value="<?php echo esc_attr( $custom_event_name ); ?>" />
      <?php } ?>
    <?php } ?>

  </form>

  <?php echo wp_kses_post( Components::render( 'form-overlay' ) ); ?>
  <?php echo wp_kses_post( Components::render( 'spinner' ) ); ?>
  <?php echo wp_kses_post( Components::render( 'form-message', [ 'message' => $success_message, 'type' => 'success' ] ) ); ?>
  <?php echo wp_kses_post( Components::render( 'form-message', [ 'message' => $error_message, 'type' => 'error' ] ) ); ?>
</div>

