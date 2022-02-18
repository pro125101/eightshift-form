<?php

namespace Tests\Unit\Mailer;

use Brain\Monkey;
use EightshiftForms\Mailer\SettingsMailer;
use EightshiftForms\Labels\LabelsInterface;

use function Tests\setupMocks;

/**
 * Mock before tests.
 */
beforeEach(function () {
	Monkey\setUp();
	setupMocks();

	$this->mailerSettings = new SettingsMailer();
});

afterAll(function() {
	Monkey\tearDown();
});

test('Register method will call sidebar hook', function () {
	$this->mailerSettings->register();

	$this->assertSame(10, has_filter(SettingsMailer::FILTER_SETTINGS_SIDEBAR_NAME, 'EightshiftForms\Mailer\SettingsMailer->getSettingsSidebar()'), 'The callback getSettingsSidebar should be hooked to custom filter hook with priority 10.');
	$this->assertSame(10, has_filter(SettingsMailer::FILTER_SETTINGS_NAME, 'EightshiftForms\Mailer\SettingsMailer->getSettingsData()'), 'The callback getSettingsData should be hooked to custom filter hook with priority 10.');
	$this->assertSame(10, has_filter(SettingsMailer::FILTER_SETTINGS_IS_VALID_NAME, 'EightshiftForms\Mailer\SettingsMailer->isSettingsValid()'), 'The callback isSettingsValid should be hooked to custom filter hook with priority 10.');
});