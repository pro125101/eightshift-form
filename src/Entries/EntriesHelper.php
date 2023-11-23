<?php

/**
 * EntriesHelper class.
 *
 * @package EightshiftForms\Entries
 */

declare(strict_types=1);

namespace EightshiftForms\Entries;

use EightshiftForms\Helpers\Helper;

/**
 * EntriesHelper class.
 */
class EntriesHelper
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	public const TABLE_NAME = 'es_forms_entries';

	/**
	 * Get entry by form data reference.
	 *
	 * @param array<string, mixed> $values Values to store.
	 * @param string $formId Form Id.
	 *
	 * @return boolean
	 */
	public static function setEntryByFormDataRef(array $formDataReference, string $formId): bool
	{
		$params = $formDataReference['params'] ?? [];

		$output = [];

		$params = Helper::removeUneceseryParamFields($params);

		foreach ($params as $param) {
			$name = $param['name'] ?? '';
			$value = $param['value'] ?? '';

			if (!$name || !$value) {
				continue;
			}

			$output[$name] = $value;
		}

		if (!$output) {
			return false;
		}

		return self::setEntry($output, $formId);
	}

	/**
	 * Get entry by ID.
	 *
	 * @param string $id Entry Id.
	 * 
	 * @return array<string, mixed>
	 */
	public static function getEntry(string $id): array
	{
		global $wpdb;

		$tableName = self::getFullTableName();

		$output = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->prepare(
				"SELECT * FROM {$tableName} WHERE id = %d",
				(int) $id
			),
			ARRAY_A
		);

		if (\is_wp_error($output) || !$output) {
			return [];
		}

		return self::prepareEntryOutput($output);
	}

	/**
	 * Get entries by form ID.
	 *
	 * @param string $formId Form Id.
	 * 
	 * @return array<string, mixed>
	 */
	public static function getEntries(string $formId): array
	{
		global $wpdb;

		$tableName = self::getFullTableName();

		$output = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->prepare(
				"SELECT * FROM {$tableName} WHERE form_id = %d",
				(int) $formId
			),
			ARRAY_A
		);

		if (\is_wp_error($output) || !$output) {
			return [];
		}

		$results = [];

		foreach ($output as $value) {
			$results[] = self::prepareEntryOutput($value);
		}

		return $results;
	}

	/**
	 * Set entry.
	 *
	 * @param array<string, mixed> $values Values to store.
	 * @param string $formId Form Id.
	 *
	 * @return boolean
	 */
	public static function setEntry(array $data, string $formId): bool
	{
		global $wpdb;
		error_log( print_r( ( $data ), true ) );

		$output = \wp_json_encode($data);

		$result = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			self::getFullTableName(),
			[
				'form_id' => (int) $formId,
				'entry_value' => $output,
			],
			[
				'%d',
				'%s',
			]
		);

		error_log( print_r( ( $result ), true ) );

		if (\is_wp_error($result)) {
			return false;
		}

		return true;
	}

	/**
	 * Prepare entry output from DB.
	 *
	 * @param array<string, mixed> $data Data to prepare.
	 *
	 * @return array<string, mixed>
	 */
	private static function prepareEntryOutput(array $data): array
	{
		return [
			'id' => $data['id'] ?? '',
			'formId' => $data['form_id'] ?? '',
			'entryValue' => isset($data['entry_value']) ? json_decode($data['entry_value'], true) : [],
		];
	}

	/**
	 * Get full table name.
	 *
	 * @return string
	 */
	private static function getFullTableName(): string
	{
		global $wpdb;
		return $wpdb->prefix . self::TABLE_NAME;
	}
}
