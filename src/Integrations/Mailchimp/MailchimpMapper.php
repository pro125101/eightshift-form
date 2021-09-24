<?php

/**
 * Mailchimp Mapper integration class.
 *
 * @package EightshiftForms\Integrations\Mailchimp
 */

declare(strict_types=1);

namespace EightshiftForms\Integrations\Mailchimp;

use EightshiftForms\Helpers\Helper;
use EightshiftFormsPluginVendor\PHPHtmlParser\Dom;
use EightshiftForms\Helpers\TraitHelper;
use EightshiftForms\Settings\Settings\AbstractFormBuilder;
use EightshiftFormsPluginVendor\EightshiftLibs\Services\ServiceInterface;

/**
 * MailchimpMapper integration class.
 */
class MailchimpMapper extends AbstractFormBuilder implements ServiceInterface
{
	/**
	 * Use General helper trait.
	 */
	use TraitHelper;

	// Filter name.
	public const MAILCHIMP_MAPPER_FILTER_NAME = 'es_mailchimp_mapper_filter';

	public const MAILCHIMP_MAPPER_TRANSIENT_NAME = 'es_mailchimp_mapper_cache';

	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{

		// Blocks string to value filter name constant.
		\add_filter(static::MAILCHIMP_MAPPER_FILTER_NAME, [$this, 'getMapper'], 10, 2);
	}

	/**
	 * Map Mailchimp form to our components.
	 *
	 * @param string $formId Form ID.
	 * @param string $redirect Redirect Url.
	 *
	 * @return string
	 */
	public function getMapper(string $formId, string $redirect = ''): string
	{
		$build = get_transient(self::MAILCHIMP_MAPPER_TRANSIENT_NAME);

		$formId = Helper::encryptor('decrypt', $formId);

		if (empty($build)) {

			$form = $this->getIntegrationRemoteForm(\get_post_meta($formId, $this->getSettingsName(SettingsMailchimp::MAILCHIMP_FORM_URL_KEY), true));

			$build = $this->getForm($form);

			set_transient(self::MAILCHIMP_MAPPER_TRANSIENT_NAME, $build, 3600);
		}

		return $this->buildForm($build, $formId, false, !empty($redirect));
	}

	/**
	 * Build form
	 *
	 * @param string $form Form String.
	 *
	 * @return string
	 */
	private function getForm(string $form): array
	{
		$dom = new Dom;
		$dom->loadStr($form);
		$fields = $dom->find('.mergeRow');

		$output = [];

		if ($fields) {
			foreach ($fields as $field) {
				$input = $field->find('input, select, textarea');
				$label = $field->find('> label')->innerHtml;
				$tagName = $input->tag->name();
				$name = $input->getAttribute('name');
				$value = $input->getAttribute('value');
				$type = $input->getAttribute('type');
				$id = $input->getAttribute('id');
				$isRequired = strpos($label, '<span class="req asterisk">*</span>') !== false;
				$label = str_replace('<span class="req asterisk">*</span>', '', $label);

				if ($tagName === 'input') {
					switch ($type) {
						case 'radio':
							$children = $field->find('.radio-group input');

							$childrenItems = [];

							if ($children) {
								foreach ($children as $child) {
									$childrenItems[] = [
										'component' => 'radio',
										'radioId' => $child->getAttribute('id'),
										'radioLabel' => $child->nextSibling()->innerHtml,
										'radioValue' => $child->getAttribute('value'),
									];
								}
							}

							$output[] = [
								'component' => 'radios',
								'radiosName' => $name,
								'radiosFieldLabel' => $label,
								'radiosIsRequired' => $isRequired,
								'radiosContent' => $childrenItems,
							];
							break;

						case 'checkbox':
							$children = $field->find('.checkbox-group input');

							$childrenItems = [];

							if ($children) {
								foreach ($children as $child) {
									$childrenItems[] = [
										'component' => 'checkbox',
										'checkboxId' => $child->getAttribute('id'),
										'checkboxName' => $child->getAttribute('name'),
										'checkboxLabel' => $child->nextSibling()->innerHtml,
										'checkboxValue' => $child->getAttribute('value'),
									];
								}
							}

							$output[] = [
								'component' => 'checkboxes',
								'checkboxesFieldLabel' => $label,
								'checkboxesContent' => $childrenItems,
							];
							break;

						default:
							$output[] = [
								'component' => $tagName,
								"${tagName}Name" => $name,
								"${tagName}FieldLabel" => $label,
								"${tagName}Id" => $id,
								"${tagName}Value" => $value,
								"${tagName}Type" => $type,
								"${tagName}IsRequired" => $isRequired,
							];
							break;
					}
				}

				if ($tagName === 'select') {
					$children = $field->find('option');

					$childrenItems = [];

					if ($children) {
						foreach ($children as $child) {
							$childrenItems[] = [
								'component' => 'select-option',
								'selectOptionLabel' => $child->innerHtml,
								'selectOptionValue' => $child->getAttribute('value'),
							];
						}
					}

					$output[] = [
						'component' => 'select',
						'selectName' => $name,
						'selectId' => $id,
						'selectFieldLabel' => $label,
						'selectOptions' => $childrenItems,
						'selectIsRequired' => $isRequired,
					];
				}
			}
		}

		$hiddenItems = $dom->find('input[type="hidden"]');
		if ($hiddenItems) {
			foreach ($hiddenItems as $hidden) {
				$output[] = [
					'component' => 'input',
					'inputName' => $hidden->getAttribute('name'),
					'inputValue' => $hidden->getAttribute('value'),
					'inputId' => $hidden->getAttribute('id'),
					'inputType' => 'hidden',
					'inputFieldUseError' => false
				];
			}
		}


		$submit = $dom->find('input[type="submit"]');
		if ($submit) {
			$output[] = [
				'component' => 'submit',
				'submitName' => $submit->getAttribute('name'),
				'submitValue' => $submit->getAttribute('value'),
				'submitType' => 'input',
				'submitFieldUseError' => false
			];
		}

		return $output;
	}
}