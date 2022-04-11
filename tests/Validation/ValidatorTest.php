<?php

namespace Tests\Unit\Validation;

use Brain\Monkey;
use Brain\Monkey\Functions;

use function Tests\setupMocks;

use EightshiftForms\Validation\Validator;
use EightshiftForms\Labels\Labels;
use EightshiftForms\Labels\LabelsInterface;
use EightshiftForms\Validation\SettingsValidation;

class ValidatorMock extends Validator {
	public function __construct(LabelsInterface $labels) {
		parent::__construct($labels);
	}

	public function getOptionValue(string $name): string {
		switch($name) {
			case 'validation-patterns': 
				return "Numbers : [0-9]+\nLetters : [a-zA-Z]+\nInvalid-value";
			default:
				return $name;
		}
	}
};


/**
 * Mock before tests.
 */
beforeEach(function () {
	Monkey\setUp();
	setupMocks();

	$labels = new Labels();
	$this->validator = new ValidatorMock($labels);
});

afterAll(function() {
	Monkey\tearDown();
});

test('Validator skips validation on single submit', function() {
	expect($this->validator->validate(['es-form-single-submit' => true]))->toBe([]);
});

test('getValidationPatterns returns local and user patterns', function() {
	$preparedValidationPatterns = [];

	foreach(SettingsValidation::VALIDATION_PATTERNS as $key => $value) {
		$preparedValidationPatterns[] = [
			'value' => $value,
			'label' => $key,
		];
	}

	$preparedValidationPatterns[] = [
		'value' => '[0-9]+',
		'label' => 'Numbers'
	];

	$preparedValidationPatterns[] = [
		'value' => '[a-zA-Z]+',
		'label' => 'Letters'
	];

	expect($this->validator->getValidationPatterns())->toBe(
		array_merge(
			[['value' => '', 'label' => '---']],
			$preparedValidationPatterns,
		));
});
